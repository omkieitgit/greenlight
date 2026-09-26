import { Component, Injectable, OnDestroy, OnInit, Renderer2 } from '@angular/core';
import { NgbDateAdapter, NgbDateStruct } from '@ng-bootstrap/ng-bootstrap';
import { FormControl, NgForm, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { CommonApplicationService,CommonActivityService } from '../../../../shared/_services';
import {apiUrl} from '../../../../config/api-url';

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
  selector: 'advancedsearch',
  templateUrl: './advancedsearch.html',
  providers: [{provide: NgbDateAdapter, useClass: NgbDateNativeAdapter}]
})

export class AdvancedSearchComponent  implements OnInit{
  constructor(private formBuilder: FormBuilder,
              private commonApplicationService:CommonApplicationService,
              private commonActivityService:CommonActivityService) { }

  advancedsearchForm: FormGroup;
  submitted = false;
  searchResult:any;
  offset: number = 1;
  totalRecord:number;
  searchField:any;
  limit:number=10;
  loading:boolean=true;
  map_data:any[]=[];
  // ngbdatepicker
  model1: Date;
  model2: Date;

  get today() {
    return new Date();
  }

  
  meridian = true;
  toggleMeridian() {
      this.meridian = !this.meridian;
  }
  ngOnInit() {
    this.intilize();
  }
  
  intilize()
  {
    this.advancedsearchForm = this.formBuilder.group({
      as_owner_name: ['', ''],
      as_owner_email: ['', ''],
      as_owner_phone: ['', ''], 
      as_owner_address: ['', ''],  
      as_borrower_name: ['', ''],
      as_borrower_phone: ['', ''],
      as_borrower_address: ['', ''],
      as_fb_username: ['', ''],
      as_parcel: ['', ''],
      as_properties_owned: ['', ''],
      as_strategy: ['', ''],
      as_mls: ['', ''],
      as_agent: ['', ''],
      as_property_address: ['', ''],
      as_state: ['', ''],
      as_late: ['', ''],
      as_auction_place: ['', ''],

      as_auction_spread: ['', ''],
      as_square_feet: ['', ''],

      as_amenties_pool: ['', ''],
      as_amenties_spa: ['', ''],
      as_beds: ['', ''],
      as_garages: ['', ''],
      limit: ['', ''],
      
      //taxes_assessed: ['', ''],

            
    });
  } 
  get f() { return this.advancedsearchForm.controls; }

  seachProperty(data: any){
    this.loading=false;
    let result = this.commonActivityService.getFullFormDataWithDateFormatted(data);
    this.searchField=result;
    let url = apiUrl.advance;
    this.commonApplicationService.getSearch(url, { params: result })
      .subscribe(
          data => {
            this.loading=true;
            this.searchResult=data.data;
            this.totalRecord=data.total;
            this.map_info(this.searchResult);
          },
          error => {
              //this.error_message=true;
              //this.message=error['message'];
              //this.alertService.error(error);
              //this.loading = false;
          }
      ); 
  }

  rest_search(){
    this.searchResult='';
    this.intilize();
    }

  changeLimit($event){
    this.limit=$event.target.value;
  }

  map_info(search_info){

    for(var i=0; i<search_info.length; i++){
      var  newDoc = {
                  data: search_info[i].address+' '+search_info[i].city+' '+search_info[i].state+' '+search_info[i].zip,
                  lat: search_info[i].geo?search_info[i].geo.latitude:'',
                  lng: search_info[i].geo?search_info[i].geo.longitude:'',
              };

      this.map_data.push(newDoc);
      
    }
  }
  

}
