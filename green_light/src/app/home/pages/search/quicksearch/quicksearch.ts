
import { Component, OnInit,HostListener,ElementRef,ViewChild } from '@angular/core';
import { FormControl, NgForm, FormBuilder, FormGroup, Validators } from '@angular/forms';
import {IMyDpOptions, IMyDateModel} from 'mydatepicker';
import { formConstants } from '@config/forms-constants';
import jsonData from '@config/state_county';
import { StorageService } from '../../../../shared/_services/storage.service';
import { CommonApplicationService,CommonActivityService, AlertService } from '../../../../shared/_services';
import {apiUrl,map_api} from '../../../../config/api-url';
import { Response } from 'selenium-webdriver/http';

@Component({
  selector: 'quicksearch',
  templateUrl: './quicksearch.html'
})

export class QuickSearchComponent  implements OnInit{
  countries: any [];
  states: any;
  loan_type_list: string[];
  property_config: any= {};
  sale_type_option: string [];
  property_type: string [];

  todayDate:string;
  submitted = false;
  quickSearchForm: FormGroup;

  searchResult:any;
  offset: number = 1;
  totalRecord:number;
  searchField:any;
  limit:number=20;
  loading:boolean=true;
  map_data:any[]=[];
  firstSortBy:any;

  state:string='';

  searchProAddress:any=[];

  minChar:number=3;
  filteredOptions:any;
  loadingSearch:boolean=false;
  noResult:boolean=false;
 // searchResult:any;
  isSearch:boolean=false;


  constructor(private formBuilder: FormBuilder,
              private storageService:StorageService,
              private commonApplicationService:CommonApplicationService,
              private alertService:AlertService,
              private commonActivityService:CommonActivityService,
              private eRef: ElementRef
              ) { }

  checkClickEvent($event){
    if($event.target.nodeName=='DIV'){
      this.seachProperty(this.quickSearchForm);
    }
  }

  public myDatePickerOptions: IMyDpOptions = { dateFormat: 'yyyy-mm-dd',firstDayOfWeek : 'su'};

  get today() {
    return new Date();
  }

  meridian = true;
  toggleMeridian() {
    this.meridian = !this.meridian;
  }

  
  ngOnInit() {
    
    this.state='NC';

    this.countries         = formConstants.countries;
    this.states             = formConstants.states;
     this.property_config =  this.storageService.get("property_config");
    if(this.property_config !== null){
      this.sale_type_option = this.property_config.sale_type;
      this.loan_type_list = this.property_config.loan_type;
      this.firstSortBy=this.property_config.first_sort_by;
      this.property_type    = this.property_config.property_types;
      
    }
    this.todayDate = new Date().toJSON().slice(0,10).replace(/-/g,'-');
    
    this.intilize();

    this.quickSearchForm.get('property_address').valueChanges.subscribe(value => { 
      if(value && value.length>=this.minChar){
        this.onChangeSearch(value);
      }else{
        this.filteredOptions=[];
      }
    });
  }
 
  getCountyList(state){
    //var selectedState = JSON.parse(this.stateCountyList)
    this.countries = jsonData[state];
  }

  

  onDateChanged(event: IMyDateModel) {
    return event.formatted;
  }

  intilize(){

    this.quickSearchForm = this.formBuilder.group({
          property_address: ['', ''],
          city: ['', ''],
          county: ['', ''],
          state: ['', ''],
          zip_code: ['', ''],
          case_number: ['', ''],
          trustee:['', ''],
          owner_name:['', ''],
          before_sale_spread: ['', ''],
          entity_llc_name: ['', ''],
          sub_devision:['', ''],
          legal_desc:['', ''],
          loan_type: ['', ''],
          sale_type:['', ''],
          bidder_name: ['', ''],
          winning_bidder_name: ['', ''],
          potential_buy: ['', ''],
          opening_bid: ['', ''],
          low_first: ['', ''],
          low_first_w_lien: ['', ''],
          no_cma_arv: ['', ''],
          upset_bid: ['', ''],
          sale_date_from: ['', ''],
          sale_date_to: ['', ''],
          within_days: ['', ''],
          redemption_date_from:['',''],
          redemption_date_to:['',''],
          first_sort_by: ['', ''],
          first_sort_order: ['', ''],
          second_sort_by: ['', ''],
          second_sort_order: ['', ''],
          third_sort_by: ['', ''],
          third_sort_order: ['', ''],
          limit: [this.limit, ''],
          order_by:['asc', ''],
          year_built: ['', ''], 
          property_type: ['', ''],
          from_lot_acr_sqft:[''],
          to_lot_acr_sqft:[''],
          from_living_sqft:[''],
          to_living_sqft:[''],
          w_bids:['']
          //taxes_assessed: ['', Validators.required],
    });
  }


  get f() { return this.quickSearchForm.controls; }

    // Property Validation
  validateForm(data: any) { 
    if(data.within_days>0){
      var dte = new Date();
      data.sale_date_from=dte.setDate(dte.getDate() - data.within_days);
      data.sale_date_to=dte;

    }
      let result = this.commonActivityService.getFullFormData(data);
      let url = apiUrl.search;
      this.commonApplicationService.get(url, 
        data)
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

  seachProperty(data: any){

    window.scrollTo(0, 700);
    if(data.controls.within_days.value>0){
      var dte = new Date();
      dte.setDate(dte.getDate() - data.controls.within_days.value);
      data.controls.sale_date_from.setValue(dte.getFullYear()+'-'+(dte.getMonth()+1)+'-'+dte.getDate());
      data.controls.sale_date_to.setValue(this.todayDate);
     
    }
    this.loading=false;
    let result = this.commonActivityService.getFullFormDataWithDateFormatted(data);
    result['w_bids']=result['w_bids']?1:0;
    this.storageService.setHard('search_fields',result);
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
    this.state='';
    this.countries=[];
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
 
 
 
  selectEvent(item) {
    this.quickSearchForm.controls.property_address.setValue(item);
    this.seachProperty(this.quickSearchForm);
  
  }
 
  onChangeSearch(val: string) {
    this.loadingSearch=true;
    let url=map_api.map_api_url+'?apiKey='+map_api.api_key+'&in=countryCode%3AUSA&limit=10&q='+val;
    this.commonApplicationService.get(url)
      .subscribe(
          response => {
            this.filteredOptions=[];
            if(response.items.length>0){
              for(var i=0; i<response.items.length; i++){
                var strArr = response.items[i].title.split(',');
                var key=strArr[strArr.length-1].replace(/^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g, '');
                this.filteredOptions.push(key);
              }
            }else{
                this.noResult=true;
            }
            this.loadingSearch=false;
          },
          error => {
            this.noResult=true;
            this.loadingSearch=false;
            //this.alertService.common(error);
          }
      ); 
  }
  
  onFocused(e){
    // do something when input is focused
  }

}
