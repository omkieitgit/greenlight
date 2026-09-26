import { Component, OnInit } from '@angular/core';
import {HomebuyerPropertyService} from './homebuyer-property.service';
import { CommunicationService, AlertService} from '../../../shared/_services';
import { Subscription } from 'rxjs';


@Component({
  selector: 'app-homebuyer-dashboard',
  templateUrl: './homebuyer-dashboard.component.html',
  styleUrls: ['./homebuyer-dashboard.component.css']
})
export class HomebuyerDashboardComponent implements OnInit {

  propertyInfo:any;
  propErrMsg:any;
  house_ids:any=[];
  loading:boolean=false;
  loadingContent:boolean=false;
  totalResult:number=0;
  propetyCountInfo:any=new Object;
  searchRequestParam:any;
  countSubsriber:Subscription;
  selectedAll: any;
  selectPropertFlag:boolean=false;

  result:any;
  limit:number=100;
  collapse:boolean=true;

  constructor(private communicationService:CommunicationService,
              private homebuyerPropertyService:HomebuyerPropertyService,
              private alertService:AlertService) { }

  ngOnInit() {
    this.getWhPropertyCount();
    
  }
  asIsOrder(a, b) {
    return 1;
  }
  getWhPropertyCount(){
    this.loading=true;
    this.result=this.homebuyerPropertyService.getWhPropertyCount(this.searchRequestParam);
    this.countSubsriber=this.homebuyerPropertyService.getSearchResult().subscribe(response=>{
      this.loading=false;
      this.propetyCountInfo = response['data'];
      if(this.propetyCountInfo.buyit>0 || this.propetyCountInfo.passit>0 || this.propetyCountInfo.property_closed 
          ||  this.propetyCountInfo.property_purchased_acq_a_to_b >0 
          || this.propetyCountInfo.total_invite >0
        ){
          this.totalResult=1;
        }
      this.communicationService.clearExpand();
    })
  }
  

  panelExpand($event,propertyType){
    if(!this.searchRequestParam){
      this.searchRequestParam={'filter_type':propertyType};
    }else{
      this.searchRequestParam['filter_type']=propertyType;
    }
    this.communicationService.setHomeBuyerPropetyType(this.searchRequestParam);
  }

  searchResult($event){
    this.searchRequestParam=$event;
    if($event.hasOwnProperty('limit')){
      this.limit=$event.limit;
    }
    this.un_select_all();
    this.getWhPropertyCount();
  }

  displayCounter($event){
    this.house_ids=$event;
  }

  removeHouseIds($event){
    this.house_ids=$event;
  }

  ngOnDestroy() {
    if(this.countSubsriber) { 
      this.countSubsriber.unsubscribe();
    }
  }
  select_all(){
    
    var searchPorpertyInfo=document.getElementsByClassName('search_property_info');
    for (var i = 0; i < searchPorpertyInfo.length; i++) {
       if (searchPorpertyInfo[i].querySelector('input').type=='checkbox'){
          searchPorpertyInfo[i].querySelector('input').checked= true;
          this.house_ids.push(searchPorpertyInfo[i].querySelector('input').value);
       }
          
    }
    if(this.house_ids.length>0){
      this.selectPropertFlag=true;
    }else{
      this.alertService.error('There is no property');
    }

  }

  un_select_all(){
    this.selectPropertFlag=false;
    var searchPorpertyInfo=document.getElementsByClassName('search_property_info');
    for (var i = 0; i < searchPorpertyInfo.length; i++) {
       if (searchPorpertyInfo[i].querySelector('input').type=='checkbox'){
          searchPorpertyInfo[i].querySelector('input').checked= false;
          this.house_ids.pop(searchPorpertyInfo[i].querySelector('input').value);
       }
          
    }
  }

}
