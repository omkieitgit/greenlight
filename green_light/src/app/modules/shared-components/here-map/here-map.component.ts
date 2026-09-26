import { Component, OnInit, ViewChild, ElementRef, Input } from '@angular/core';
import {map_api} from '../../../config/api-url';
import { map } from 'rxjs/operators';
declare var H: any;


@Component({
  selector: 'app-here-map',
  templateUrl: './here-map.component.html',
  styleUrls: ['./here-map.component.css']
})
export class HereMapComponent implements OnInit {

    @ViewChild("map")
    public mapElement: ElementRef;

    @Input()  public appId: any=map_api.api_id;

    @Input()  public appCode: any=map_api.api_code;

    @Input()  public lat: any;

    @Input()  public lng: any;

    @Input()  public width: any;

    @Input()  public height: any;

    @Input() public zoom:number=16;

    @Input() public data_info:any;

    @Input() public search_info:any;
      
    @Input() public show_route_info:any;


    private platform: any;
    private map: any;
    private ui: any;
    private search: any;

    public directions: any;
    private router: any;


    public constructor() { }

    public ngOnInit() {

        let map_key=this.get_here_map_key();
        this.appId=map_key['app_id'];
        this.appCode=map_key['app_code'];

        this.platform = new H.service.Platform({
            "app_id": this.appId,
            "app_code": this.appCode,
            "useHTTPS":true
        });
        this.directions = [];
        this.router = this.platform.getRoutingService();
        
    }

    public ngAfterViewInit() {
        
        if(this.data_info){
            this.drowMap();
            this.dropMarker({ "lat": this.lat, "lng": this.lng },this.data_info);
        }
          
        
        if(this.search_info){
            this.drowMap();
            this.searchPageMap();
        }

        if(this.show_route_info){
            this.drowMap();
           
            this.route();
        }

    }

    drowMap(){
        //let defaultLayers = this.platform.createDefaultLayers();
        var defaultLayers = this.platform.createDefaultLayers({
            tileSize: pixelRatio === 1 ? 256 : 512,
            ppi: pixelRatio === 1 ? undefined : 320
        });
        var pixelRatio = window.devicePixelRatio || 1;
        this.map = new H.Map(
            this.mapElement.nativeElement,
            defaultLayers.normal.map,
            {
                zoom: this.zoom,
                center: { lat: this.lat, lng: this.lng },
                pixelRatio: pixelRatio
            }
        );

            
        let behavior = new H.mapevents.Behavior(new H.mapevents.MapEvents(this.map));
        this.ui = H.ui.UI.createDefault(this.map, defaultLayers);
    }


    public places(query: string) {
        this.map.removeObjects(this.map.getObjects());
        this.search.request({ "q": query, "at": this.lat + "," + this.lng }, {}, data => {
            for(let i = 0; i < data.results.items.length; i++) {
                this.dropMarker({ "lat": data.results.items[i].position[0], "lng": data.results.items[i].position[1] }, data.results.items[i]);
            }
        }, error => {
            console.error(error);
        });
    }

    private dropMarker(coordinates: any, data: any) {
        let marker = new H.map.Marker(coordinates);
        marker.setData(data);
        marker.addEventListener('tap', event => {
            let bubble =  new H.ui.InfoBubble(event.target.getPosition(), {
                content: event.target.getData()
            });
            this.ui.addBubble(bubble);
        }, false);
        this.map.addObject(marker);
    }

    public searchPageMap(){
        console.log(this.search_info);
        this.map.removeObjects(this.map.getObjects());
        for(let i = 0; i < this.search_info.length; i++) {
            this.dropMarker({ "lat": this.search_info[i].lat, "lng": this.search_info[i].lng }, this.search_info[i].data);
        }
    }

    public route() {
        // let params = {
        //     "mode": "fastest;car",
        //     "waypoint0": "geo!" + this.start,
        //     "waypoint1": "geo!" + this.finish,
        //     "representation": "display"
        // }
        var i=0;
        var waypoint:any='';
        
        //console.log(abc);

        let routeRequestParams = {
            mode: 'shortest;pedestrian',
            representation: 'display',
            // waypoint0: '51.51326,-0.0968752', // St Paul's Cathedral
            // waypoint1: '51.5081,-0.0985',  // Tate Modern
            routeattributes: 'waypoints,summary,shape,legs',
            maneuverattributes: 'direction,action'
        };

        this.show_route_info.forEach(element => {
            routeRequestParams['waypoint'+i]="geo!"+element.latitude+','+element.longitude;
             i++;
        });
        console.log(routeRequestParams);
        this.map.removeObjects(this.map.getObjects());
        this.router.calculateRoute(routeRequestParams, data => {
            if(data.response) {
                console.log(data);
                this.directions = data.response.route[0].leg[0].maneuver;
                data = data.response.route[0];
                // let lineString = new H.geo.LineString();
                // data.shape.forEach(point => {
                //     let parts = point.split(",");
                //     lineString.pushLatLngAlt(parts[0], parts[1]);
                // });
                // let routeLine = new H.map.Polyline(lineString, {
                //     style: { strokeColor: "blue", lineWidth: 5 }
                // });
                
                

                // let startMarker = new H.map.Marker({
                //     lat: this.start.split(",")[0],
                //     lng: this.start.split(",")[1]
                // });
                // let finishMarker = new H.map.Marker({
                //     lat: this.finish.split(",")[0],
                //     lng: this.finish.split(",")[1]
                // });

                this.addRouteShapeToMap(data);
                this.addManueversToMap(data);
                this.addWaypointsToPanel(data.waypoint);
                this.addSummaryToPanel(data.summary);
                //this.map.addObjects([routeLine, startMarker, finishMarker]);
                //this.map.setViewBounds(routeLine.getBounds());
            }
        }, error => {
            console.error(error);
        });
    }


     addManueversToMap(route){
        var svgMarkup = '',
        dotIcon = new H.map.Icon(svgMarkup, {anchor: {x:8, y:8}}),
        group = new  H.map.Group(),
        i,j;
        // Add a marker for each maneuver
        for (i = 0;  i < route.leg.length; i += 1) {
          for (j = 0;  j < route.leg[i].maneuver.length; j += 1) {
            // Get the next maneuver.
            var maneuver = route.leg[i].maneuver[j];
            // Add a marker to the maneuvers group
            var marker =  new H.map.Marker({
              lat: maneuver.position.latitude,
              lng: maneuver.position.longitude} ,
              {icon: dotIcon});
            marker.instruction = maneuver.instruction;
            group.addObject(marker);
          }
        }
      
        group.addEventListener('tap', function (evt) {
          this.map.setCenter(evt.target.getGeometry());
          this.openBubble(evt.target.getGeometry(), evt.target.instruction);
        }, false);
      
        // Add the maneuvers group to the map
        this.map.addObject(group);
      }
      
      

    openBubble(position, text){
      var bubble;

      if(!bubble){
          bubble =  new H.ui.InfoBubble(
            position,
            // The FO property holds the province name.
            {content: text});
          this.ui.addBubble(bubble);
        } else {
          bubble.setPosition(position);
          bubble.setContent(text);
          bubble.open();
        }
      }

    addRouteShapeToMap(route){
      var lineString = new H.geo.LineString(),
        routeShape = route.shape,
        polyline;
    
      routeShape.forEach(function(point) {
        var parts = point.split(',');
        lineString.pushLatLngAlt(parts[0], parts[1]);
      });
    
      polyline = new H.map.Polyline(lineString, {
        style: {
          lineWidth: 4,
          strokeColor: 'rgba(0, 128, 255, 0.7)'
        }
      });
      // Add the polyline to the map
      this.map.addObject(polyline);
      // And zoom to its bounding rectangle
      this.map.setViewBounds(polyline.getBounds());
      //this.map.getViewModel().setLookAtData(polyline.getBounds());

      // this.map.addObjects([routeLine, startMarker, finishMarker]);
      //         this.map.setViewBounds(routeLine.getBounds());
    }
    
    addWaypointsToPanel(waypoints){

      let  routeInstructionsContainer = document.getElementById('panel');
      var nodeH3 = document.createElement('h3'),
      waypointLabels = [],
      i;
      for (i = 0;  i < waypoints.length; i += 1) {
          waypointLabels.push(waypoints[i].label)
      }
    
      nodeH3.textContent = waypointLabels.join(' - ');
      routeInstructionsContainer.innerHTML = '';
      routeInstructionsContainer.appendChild(nodeH3);
    }
    
    addSummaryToPanel(summary){

      let routeInstructionsContainer = document.getElementById('panel');
      var summaryDiv = document.createElement('div'),
        content = '';
        content += 'Total distance: ' + summary.distance  + 'm.';
        content += 'Travel Time: ' + summary.travelTime.toMMSS() + ' (in current traffic)';
    
    
      summaryDiv.style.fontSize = 'small';
      summaryDiv.style.marginLeft ='5%';
      summaryDiv.style.marginRight ='5%';
      summaryDiv.innerHTML = content;
      routeInstructionsContainer.appendChild(summaryDiv);

    }

    addManueversToPanel(route){

      let routeInstructionsContainer = document.getElementById('panel');
      var nodeOL = document.createElement('ol'),
      i, j;
    
      nodeOL.style.fontSize = 'small';
      nodeOL.style.marginLeft ='5%';
      nodeOL.style.marginRight ='5%';
      nodeOL.className = 'directions';
    
          // Add a marker for each maneuver
      for (i = 0;  i < route.leg.length; i += 1) {
        for (j = 0;  j < route.leg[i].maneuver.length; j += 1) {
          // Get the next maneuver.
          let maneuver = route.leg[i].maneuver[j];
    
          var li = document.createElement('li'),
            spanArrow = document.createElement('span'),
            spanInstruction = document.createElement('span');
    
          spanArrow.className = 'arrow '  + maneuver.action;
          spanInstruction.innerHTML = maneuver.instruction;
          li.appendChild(spanArrow);
          li.appendChild(spanInstruction);
    
          nodeOL.appendChild(li);
        }
      }
    
      routeInstructionsContainer.appendChild(nodeOL);
    }
      
    get_here_map_key(){
      let here_map_key=map_api.herewego_common_key;
      let map_key = here_map_key[Math.floor(Math.random() * here_map_key.length)];
      console.log(map_key);
      return map_key;

    }
      

}
