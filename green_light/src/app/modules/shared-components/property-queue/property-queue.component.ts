import { Component, OnInit,Inject } from '@angular/core';
import { CommonApplicationService,CommonActivityService ,AlertService} from '../../../shared/_services';
import { apiUrl } from '../../../config/api-url';
import { DOCUMENT } from '@angular/common'; 
import {StorageService} from '../../../shared/_services/storage.service';
import { FormBuilder,FormGroup } from '@angular/forms';

@Component({
  selector: 'app-property-queue',
  templateUrl: './property-queue.component.html',
  styleUrls: ['./property-queue.component.css']
})
export class PropertyQueueComponent implements OnInit {
  val:any;
  list_data:any;
  property_queue_list:any;
  listFlag:boolean=false;
  queueFlag:boolean=false;
  saveFlag:boolean=false;
  removeFlag:boolean=false;
  property_queue_list_houses_id:any=[];
  selectPropertFlag:boolean=false;
  route_list:any=[];
  house_ids:any=[];
  selectedPropertyQueue:number;
  propertyQueueForm:FormGroup;

  constructor(private commonActivityService :CommonActivityService,
              private commonApplicationService:CommonApplicationService,
              private alertService:AlertService,
              private storageService:StorageService,
              @Inject(DOCUMENT) private document: any,
              private formBuilder:FormBuilder) { }

  ngOnInit() {
    this.get_list();
    this.selectedPropertyQueue=this.storageService.get('property_queue');
    if(this.selectedPropertyQueue){
      this.set_list(this.selectedPropertyQueue);
    }

    this.propertyQueueForm=this.formBuilder.group({
      prop_queue_list:[this.selectedPropertyQueue]
    });

  }

  save_me(list_name){

    let listname = list_name.value
    let url = apiUrl.property_queue;
    var data={'name':listname};
    this.saveFlag=true;
    this.commonApplicationService.post(url,data)
      .subscribe(
          data => {
            this.get_list();
            list_name.value='';
            this.saveFlag=false;
            this.alertService.common(data);  
          },
          error => {
            this.alertService.common(error);  
          }
      ); 
  }
  get_list(){
  
    let url = apiUrl.property_queue;
    this.commonApplicationService.get(url)
      .subscribe(
          data => {
            this.list_data=data.data;
          },
          error => {
          }
      ); 
  }

  set_list(val){
    
      this.listFlag=true;
      let url = apiUrl.queue_list+'/'+val;
      this.commonApplicationService.get(url)
      .subscribe(
          response => {
            if(response.data.length>0){
              this.queueFlag=false;
              this.property_queue_list=response.data;
            }else{
              this.queueFlag=true;
            }
            this.selectPropertFlag=false;
            this.listFlag=false;
          },
          error => {
            
          }
      ); 
    
  }

  showList(list){
    if(list.target.value!=""){
      this.listFlag=true;
      let url = apiUrl.queue_list+'/'+list.target.value;
      this.commonApplicationService.get(url)
      .subscribe(
          response => {
            if(response.data.length>0){
              this.queueFlag=false;
              this.property_queue_list=response.data;
            }else{
              this.queueFlag=true;
            }
            this.storageService.set('property_queue', list.target.value);    
            this.selectPropertFlag=false;
            this.listFlag=false;
          },
          error => {
            
          }
      ); 
    }
  }

  delete_whole_list(list){
    if(list.value!=""){
      if(confirm("Are you sure to delete")) {
        this.removeFlag=true;
        if(this.property_queue_list_houses_id.length>0){
          for(var i=0; i<this.property_queue_list_houses_id.length;i++){
            this.remove_property_queue_house(this.property_queue_list_houses_id[i]);
          }
        }else{
          this.remove_property_queue(list);
        }
        
      }
    }
  }

  remove_property_queue(list){
    let url = apiUrl.remove_property_queue+list.value;
    this.commonApplicationService.delete(url)
    .subscribe(
        response => {
          this.removeFlag=false;
          if(response.status=='failed'){
            this.alertService.error(response.message); 
          }else{
            this.alertService.success(response.message); 
            this.get_list();
            if(this.property_queue_list){
              for(var i=0; i<this.property_queue_list.length; i++){
                let element = this.document.getElementById('property_queue_'+this.property_queue_list[i].property_queue_list_houses_id);      
                element.parentNode.removeChild(element);
              }
            }else{
              
              let element = this.document.getElementById('remove_not_available');      
              element.parentNode.removeChild(element);
            }
            
          }
        },
        error => {
          this.alertService.common(error);
        }
    ); 
  }

  remove_property_queue_house(property_queue_list_houses_id){

    let url = apiUrl.remove_property_queue_house+property_queue_list_houses_id;
    this.commonApplicationService.delete(url)
    .subscribe(
        response => {
          this.removeFlag=false;
          if(response.status=='failed'){
            this.alertService.error(response.message); 
          }else{
            this.alertService.success(response.message);
            let element = this.document.getElementById('property_queue_'+property_queue_list_houses_id);      
            element.parentNode.removeChild(element);
            this.property_queue_list_houses_id.pop(property_queue_list_houses_id);
          }
        },
        error => {
        }
    ); 
  }


  clickEvt(e) {
    e.preventDefault();
    e.stopPropagation();
    if(e.target.checked){
      this.property_queue_list_houses_id.push(e.target.value);
      this.pushHouseId(e.target.value);
    }
    else{
      this.property_queue_list_houses_id.pop(e.target.value);
      this.popHouseId(e.target.value);
    }   
    
  }

  select_all(){
    this.selectPropertFlag=true;
    var searchPorpertyInfo=document.getElementsByClassName('property_queue');
    for (var i = 0; i < searchPorpertyInfo.length; i++) {
       if (searchPorpertyInfo[i].querySelector('input').type=='checkbox'){
          searchPorpertyInfo[i].querySelector('input').checked= true;
          let listId=searchPorpertyInfo[i].querySelector('input').value;
          this.property_queue_list_houses_id.push(listId);
          this.pushHouseId(listId);
        }
          
    }
  }

  un_select_all(){
    this.selectPropertFlag=false;
    var searchPorpertyInfo=document.getElementsByClassName('property_queue');
    for (var i = 0; i < searchPorpertyInfo.length; i++) {
       if (searchPorpertyInfo[i].querySelector('input').type=='checkbox'){
          searchPorpertyInfo[i].querySelector('input').checked= false;
          let listId=searchPorpertyInfo[i].querySelector('input').value;
          this.property_queue_list_houses_id.pop(listId);
          this.popHouseId(listId);
        }
          
    }
  }

  show_route(){
    //console.log(this.property_queue_list);
    this.route_list=[];
    this.property_queue_list.forEach(key => {
      this.property_queue_list_houses_id.find(
        id=>{
          if(id==key.property_queue_list_houses_id && key.house.geo){
            this.route_list.push(key.house.geo);
          }
        });
      console.log(this.route_list);
    });

  }

  pushHouseId(queue_list_id){
    this.property_queue_list.find(queue_list=>{
      if(queue_list.property_queue_list_houses_id==queue_list_id){
        this.house_ids.push(queue_list.house_id);
      }
    });
  }

  popHouseId(queue_list_id){
    this.property_queue_list.find(queue_list=>{
      if(queue_list.property_queue_list_houses_id===queue_list_id){
        this.house_ids.pop(queue_list.house_id);
      }
    });
  }

}
