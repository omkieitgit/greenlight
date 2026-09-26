import { Component, OnInit,Input,Output,EventEmitter,OnDestroy,OnChanges, SimpleChange } from '@angular/core';
import { CommonApplicationService,CommunicationService } from '../../../../shared/_services';
import {HomebuyerPropertyService} from '../homebuyer-property.service';
import { Observable, Subscription } from 'rxjs';
import { apiUrl } from '../../../../config/api-url';

@Component({
  selector: 'app-homebuyer-property-detail',
  templateUrl: './homebuyer-property-detail.component.html',
  styleUrls: ['./homebuyer-property-detail.component.css']
})
export class HomebuyerPropertyDetailComponent implements OnInit,OnChanges {
  moreBtn:boolean=true;
  propertyInfo:any=[];
  private subscription:Subscription;
  propErrMsg:any;
  house_ids:any=[];
  loadingContent:boolean=false;
  loader:boolean=false;
  @Input() limit;
  offset=0;
  records: any=[];
  @Input() filter_type;
  loadInfoType:any=[];
  @Output() selectedProperty: EventEmitter<any> = new EventEmitter();
  @Output() unSelectedProperty: EventEmitter<any> = new EventEmitter();
  communicateSubscription:Subscription;

  constructor(private homebuyerPropertyService:HomebuyerPropertyService,
              private commonApplicationService:CommonApplicationService,
              private communicationService:CommunicationService) { }

  ngOnInit() {
    this.communicateSubscription=this.communicationService.getHomeBuyerPropetyType().subscribe(response=>{
      if(this.filter_type==response.filter_type && !this.loadInfoType.some((item) => item == response)){
        this.propertyInfo=[];
        this.loadInfoType.push(response);
        this.loadingContent=true;
        this.get_wholesale_info(response);
      }
        
    })
    
  }

  ngOnChanges(){
    console.log(this.limit);
  }

  get_wholesale_info(request){

    // this.homebuyerPropertyService.get_wholesale_info(request);
    // this.subscription=this.homebuyerPropertyService.getSearchDetail().subscribe(response=>{
    //   this.propertyInfo = response;
    //   this.loadingContent=false;
    // });
   
    let url = apiUrl.wholesale_buyer_list;
    this.commonApplicationService.getSearch(url, { params: request }).subscribe(response => {
     
      if(response.status=='success'){

        for(let i=0; response.data.length>i; i++){
          this.propertyInfo.push(response.data[i]);
        }

        if(response.data.length!=this.limit){
          this.moreBtn=false;
        }  
        this.offset=(this.offset+response.data.length);

        this.loader=false;
        this.loadingContent=false;
      }
    },
    (err: any) => {
      this.loader=false;
      this.loadingContent=false;
       "Error occured, Please try again later!";
    })
    
  }

  displayCounter($event){
    this.house_ids.push($event);
    this.selectedProperty.emit(this.house_ids);
  }

  removeHouseIds($event){
    this.house_ids.pop($event);
    this.unSelectedProperty.emit(this.house_ids);
  }

  ngOnDestroy() {
    if(this.subscription) { 
      this.subscription.unsubscribe();
    }
    if(this.communicateSubscription){
      this.communicateSubscription.unsubscribe();
    }
  }
  getloadmorepages(){
    this.loader=true;
    let request={'filter_type':this.filter_type,'limit':this.limit,'offset':this.offset};
    this.get_wholesale_info(request);
  }
}
