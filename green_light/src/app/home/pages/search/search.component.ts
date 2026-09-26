import { Component, Injectable, OnDestroy, OnInit, Renderer2 } from '@angular/core';
import { NgbDateAdapter, NgbDateStruct } from '@ng-bootstrap/ng-bootstrap';
import { FormControl, NgForm, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router }    from '@angular/router';
import { first } from 'rxjs/operators';
import { CommonApplicationService,CommonActivityService } from '../../../shared/_services';
import { apiUrl } from '../../../config/api-url';

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
  selector: 'search',
  templateUrl: './search.component.html',
  providers: [{provide: NgbDateAdapter, useClass: NgbDateNativeAdapter}],
})

export class SearchComponent  implements OnInit{
  constructor(private commonActivityService :CommonActivityService,
              private commonApplicationService:CommonApplicationService) { }

  submitted = false;
  // ngbdatepickercma_arv_datecma_arv_date
  model1: Date;
  model2: Date;
  searchResult:any;
  offset: number = 1;
  totalRecord:number;
  searchField:any;
  limit:number=10;
  loading:boolean=true;
  map_data:any[]=[];

  get today() {
    return new Date();
  }

  // ngbtimepicker
  
  ngOnInit() {
  
  }
  
  intilize(){
    // this.searchForm = this.formBuilder.group({
    //   property_address: ['', ''],
    //   city: ['', ''],
    //   county: ['', ''],
    //   state: ['NC', ''],
    //   zip_code: ['', ''],
    //   case_number: ['', ''],
    //   trustee:['', ''],
    //   owner_name:['', ''],
    //   before_sale_spread: ['', ''],
    //   entity_llc_name: ['', ''],
    //   sub_devision:['', ''],
    //   legal_desc:['', ''],
    //   loan_type: ['', ''],
    //   sale_type:['', ''],
    //   bidder_name: ['', ''],
    //   potential_buy: ['', ''],
    //   opening_bid: ['', ''],
    //   low_first: ['', ''],
    //   low_first_w_lien: ['', ''],
    //   no_cma_arv: ['', ''],
    //   upset_bid: ['', ''],
    //   sale_date_from: ['', ''],
    //   sale_date_to: ['', ''],
    //   within_days: ['', ''],
    //   first_sort_by: ['', ''],
    //   first_sort_order: ['', ''],
    //   second_sort_by: ['', ''],
    //   second_sort_order: ['', ''],
    //   third_sort_by: ['', ''],
    //   third_sort_order: ['', ''],
    //   limit: ['', ''],
    //   //taxes_assessed: ['', Validators.required],

            
    // });
  }
    // Property Validation
  validateForm(data: any) { 
      let result = this.commonActivityService.getFullFormData(data);
      let url = apiUrl.search;
      console.log(url);
      this.commonApplicationService.get(url, data)
          .subscribe(
              data => {
                this.searchResult=data;
              },
              error => {
                  //this.error_message=true;
                  //this.message=error['message'];
                  //this.alertService.error(error);
                  //this.loading = false;
              }
          ); 
      // do something else
  }

  //get f() { return this.searchForm.controls; }

  seachProperty(data: any){
    this.loading=false;
    let result = this.commonActivityService.getFullFormDataWithDateFormatted(data);
    this.searchField=result;
    let url = apiUrl.search;
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
