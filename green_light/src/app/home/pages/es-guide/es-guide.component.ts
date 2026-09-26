import { Component, OnInit } from '@angular/core';
import { apiUrl } from '../../../config/api-url';
import { CommonApplicationService, AlertService } from '../../../shared/_services';
import {TruncatePipe} from '../../../modules/directives/truncate.pipe';
import { MatDialog } from '@angular/material/dialog';
import { EsGuideDetailComponent } from './es-guide-detail/es-guide-detail.component';

@Component({
  selector: 'app-es-guide',
  templateUrl: './es-guide.component.html',
  styleUrls: ['./es-guide.component.css']
})
export class EsGuideComponent implements OnInit {

  loading:boolean=false;
  public columns:Array<any> = [
    {title: 'DATE', name: 'title', sort: '' },
    {title: 'TOPIC', name: 'sort_desc', sort: '' },
    {title: 'VIEW', name:'view'}
  ];

  public page:number = 1;
  public itemsPerPage:number = 10;
  public maxSize:number = 5;
  public numPages:number = 1;
  public length:number = 0;
  public rows:Array<any> = [];

  listData:any=[];
  guideResult:any;
  scrollBarHorizontal = (window.innerWidth < 1200);

  constructor(private commonApplicationService:CommonApplicationService,
              private alertService:AlertService,
              private truncate:TruncatePipe,
              private dialog:MatDialog) { }

  ngOnInit(): void {
    window.onresize = () => {
      this.scrollBarHorizontal = (window.innerWidth < 1200);
    };
    this.getEsGuidance();
  }

  getEsGuidance(){
    this.loading=false;
    let url = apiUrl.es_guide;
    this.commonApplicationService.get(url).subscribe(response => {
      if(response !== undefined){             
          this.guideResult=response['data'];
          this.esGuideView();
      }
    },
      (err: any) => {
        this.alertService.common(err);    
      })
  }

  esGuideView(){
    let esGuide =  this.guideResult;
    if(esGuide && esGuide.length>0){
      for(var i =0; i<esGuide.length; i++){
        const newDoc = {
          id:esGuide[i].id,
          title: esGuide[i].title,
          sort_desc:this.truncate.transform(esGuide[i].sort_desc,['50', '...']),
          view:'<a  href="javascript:;" >View</a>'
        };
        this.listData.push(newDoc);
      }
      this.rows=this.listData;
      //this.onChangeTable(this.config);

    }  
  }

  public onCellClick(data: any): any {

    if(data.row && data.type=='click'){
      console.log(data);
      let detailGuideData=this.guideResult.filter(x => x.id ==  data.row.id);
      this.dialog.open(EsGuideDetailComponent,{width: '800px',data:detailGuideData[0]});

     // this.router.navigate(['/home/showdetail/'+data.row.house_id]);  
    }

  }

  viewDesc(event){
    console.log(event);
  }
}
