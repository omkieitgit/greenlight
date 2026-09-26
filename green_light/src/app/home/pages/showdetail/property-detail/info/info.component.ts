
import { Component,Input,ViewChild, OnInit, Output, ComponentFactoryResolver, ViewContainerRef, EventEmitter } from '@angular/core';
import { InfoModel } from './info.model';
import { Router,ActivatedRoute } from '@angular/router';
import { Title }     from '@angular/platform-browser';
import { CommonApplicationService,CommonActivityService } from '@shared-service/_services';
import { apiUrl } from '@config/api-url';

@Component({
  selector: 'info',
  templateUrl: './info.html',
})

export class InfoComponent  implements OnInit{ 
  _ref:any;
  public infoData : InfoModel;
  public infoDataBlankModel : InfoModel;
  property_id: string;
  propErr: boolean = false;
  propErrMsg: any = "";
  result: any;
  openPanel:boolean = true;
  map_data:any;
  @Input() quickInput;
  isFavourite:boolean;
  manager_notes:string='manager';
  buyer_notes:string='buyer';
  realStateData:any;
  priceHistoryData:any;
  schoolData:any;
  assessmentData:any;

  @Output() propertyTopInfo=new EventEmitter<any>();

   @ViewChild('infoSections', { read: ViewContainerRef }) container: ViewContainerRef;
  constructor(private router: Router,
          private route: ActivatedRoute,
          private commonApplicationService: CommonApplicationService,
          private commonActivityService: CommonActivityService,
          private _cfr: ComponentFactoryResolver,
          private titleService: Title) {}

          ngOnInit() {
            this.infoDataBlankModel = new InfoModel();
            console.log(this.route);
             this.property_id = this.route.snapshot.paramMap.get('property_id');
            if(this.property_id !== undefined){
              if(this.openPanel){
                  this.getInfoDetails();    
              }
            }
          }

          panelExpand(flag){
            if(!this.openPanel){
              this.getInfoDetails();
              this.openPanel = true;
            }
          }

        /*----------------------------- Get property Info Details --------------------------------*/
          getInfoDetails(){
            //this.commonApplicationService.get(url,data,sucess_message,error_message);
              let url = apiUrl.property_info+"/"+this.property_id+"/all";
              this.commonApplicationService.get(url).subscribe(response => {
                this.result = response['row'];
                if(this.result !== undefined ){
                    
                    this.infoData = this.result.property_info;
                    if(this.infoData!== undefined && this.infoData !==null){
                      this.isFavourite=this.result.isFavourite;
                      this.map_data=this.infoData.address+' '+this.infoData.city+' '+this.infoData.state+' '+this.infoData.zip;
                      let cma_arv=this.infoData['last_cma_arv_recommendations']?this.infoData['last_cma_arv_recommendations'].recommended_cma_arv:0;
                      this.propertyTopInfo.emit({'address':this.map_data,'cma_arv':cma_arv})
                      this.titleService.setTitle( this.map_data);
                      
                    }
                    // Real State Form
                    if(this.infoData && this.infoData.local_real_estate_details !== undefined && this.infoData.local_real_estate_details !== null ){
                      this.realStateData  =  this.infoData.local_real_estate_details;   
                    }else{
                      this.realStateData=this.infoDataBlankModel.local_real_estate_details;
                    }
                    
                    // Price History
                   
                    if(this.infoData && this.infoData.price_history !== undefined && this.infoData.price_history[0] !== undefined ){
                      this.priceHistoryData  =  this.infoData.price_history;   
                    }else{
                      this.priceHistoryData  = this.infoDataBlankModel.price_history;
                    }
                    
                    // School Form
                    if(this.infoData && this.infoData.schools_and_neighborhood !== undefined && this.infoData.schools_and_neighborhood !== null ){
                      this.schoolData  =  this.infoData.schools_and_neighborhood;   
                    }else{
                      this.schoolData  = this.infoDataBlankModel.schools_and_neighborhood;
                    }
                    if(this.infoData && this.infoData.assessment !== null && this.infoData.assessment !== undefined &&  this.infoData.assessment[0] !== undefined ){
                      this.assessmentData  =  this.infoData.assessment;
                    }else{
                      this.assessmentData  = this.infoDataBlankModel.assessment;
                    }
                    

                      
                    

                }
              },
                (err: any) => {
                  this.propErrMsg = "Error occured, Please try again later!";
                  this.propErr = true;
                })
            }
        /*----------------------------- Get property Info Details --------------------------------*/

}