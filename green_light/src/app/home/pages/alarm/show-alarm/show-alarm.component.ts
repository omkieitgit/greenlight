import { Component, OnInit } from '@angular/core';

import { CommonApplicationService,AlertService } from '@shared-service/_services';
import { apiUrl } from '@config/api-url';
import { StorageService } from '@shared-service/_services/storage.service';
import { Router, ActivatedRoute } from '@angular/router';


@Component({
  selector: 'app-show-alarm',
  templateUrl: './show-alarm.component.html',
  styleUrls: ['./show-alarm.component.css']
})
export class ShowAlarmComponent implements OnInit {

  alarm_list:any;
  property_config: any= {};
  sale_type_option: string [];
  remove_loading:boolean=false;


  propertyImg:string='/assets/img/no-image.gif';

  offset: number = 1;
  totalRecord:number;
  searchField:any;
  limit:number=10;
  loading:boolean=true;
  isFilterShow:boolean=false;
  removedAlarm:boolean=true;


  constructor(private storageService:StorageService,
    private alertService:AlertService,
    private router: Router,
    private commonApplicationService:CommonApplicationService) { 
      //this.length = this.data.length;

    }

  ngOnInit() {
    this.property_config =  this.storageService.get("property_config");
    if(this.property_config !== null){
        this.sale_type_option = this.property_config.sale_type;
    }
    this.get_alarm_list();
    
  }

  get_alarm_list(){

    this.loading=false;
    this.searchField={'limit':this.limit};
    let url = apiUrl.alarm;
    this.commonApplicationService.getSearch(url, { params: this.searchField }).subscribe(response => {
      if(response !== undefined){             
       this.alarm_list=response.data;  
       this.totalRecord=response.total;      
       this.loading=true;
      }
    },
      (err: any) => {
        this.alertService.common(err);     
      })
  }

  removeAlarm(alarm_id){
    this.remove_loading=true;
    let url = apiUrl.alarm+'/'+alarm_id;
    this.commonApplicationService.delete(url).subscribe(response => {
      if(response !== undefined){  
        this.alarm_list = this.alarm_list.filter(item => item.alarm_id != alarm_id);
        this.remove_loading=false;
        this.alertService.success(response.message);
      }
    },
      (err: any) => {
        this.alertService.common(err);    
      })
  }

  pageChanged(data){
    this.loading=false;
    this.offset=data;
    this.searchField={'offset':(data-1)*this.limit,'limit':this.limit};
    let url = apiUrl.alarm;
    this.commonApplicationService.getSearch(url, { params: this.searchField })
        .subscribe(
          response => {
              this.loading=true;
              this.alarm_list=response.data;  
            },
            error => {
                
            }
        ); 
  }

  showAll(total){
    this.limit=total;
    this.get_alarm_list();
  }
  

}
