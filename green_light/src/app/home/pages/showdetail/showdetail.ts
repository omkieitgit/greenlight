import { Component,Injectable, OnInit } from '@angular/core';
import { NgbDateAdapter, NgbDateStruct } from '@ng-bootstrap/ng-bootstrap';
import { Router,ActivatedRoute, NavigationEnd }    from '@angular/router';

import { Title }     from '@angular/platform-browser';
import { CommonApplicationService, CommunicationService } from '@shared-service/_services';
import { environment } from '../../../../environments/environment';
import {apiUrl} from '@config/api-url';
//import pageSettings from '../../../config/page-settings';
//import { AlertService, UserService } from '../../shared/_services';

// for ngb datepicker adapter
@Injectable()
export class NgbDateNativeAdapter extends NgbDateAdapter<Date> {

  fromModel(date: Date): NgbDateStruct {
    return (date && date.getFullYear) ? {year: date.getFullYear(), month: date.getMonth() + 1, day: date.getDate()} : null;
  }

  toModel(date: NgbDateStruct): Date {
    return date ? new Date(date.year, date.month - 1, date.day) : null;
  }
}


@Component({
  selector: 'showdetail',
  templateUrl: './showdetail.html',
  providers: [{provide: NgbDateAdapter, useClass: NgbDateNativeAdapter}],
})

export class Showdetail  implements OnInit{
  [x: string]: any;
  constructor(private router: Router,
              private route: ActivatedRoute,
              private commonApplicationService:CommonApplicationService,
              private communicationService:CommunicationService) { }

  submitted = false;
  property_id: string;
  scrollFlag:boolean;
  topPropertyInfo:any;
  oldWebsiteUrl:any;
  slug_address:any;
  activeClass:any;

  //propInfo$=new subject.asObservable();
 
  meridian = true;
  toggleMeridian() {
      this.meridian = !this.meridian;
  }
  ngOnInit() {
    this.property_id = this.route.snapshot.paramMap.get('property_id');
    this.slug_address = this.route.snapshot.paramMap.get('slug_address');
    
    let propertyTab='property';
    if(this.route.snapshot.firstChild.url.length>0)
      this.activeClass=this.route.snapshot.firstChild.url[0].path;
    else
      this.activeClass=propertyTab;

    this.router.events.subscribe((route) => {
      if(route instanceof NavigationEnd){
        if(route.url && route.url.length > 0){
          let propertyUrl=route.url.split('/');
          this.activeClass=propertyTab;
          if(propertyUrl.length>5){
            this.activeClass=propertyUrl[5];
          }
        }
         
      }
    });

    this.getPropertyInfo();
    this.communicationService.getGoogleMapUrl().subscribe(res=>{
      this.topPropertyInfo.map_video.google_map_url=res;
    });
  }
 
  
  getPropertyInfo(){
      let url = apiUrl.home_common+'/'+this.property_id;
      this.commonApplicationService.get(url).subscribe(response =>{
        this.topPropertyInfo=response.row;
      },error=>{

      })
  }
}
