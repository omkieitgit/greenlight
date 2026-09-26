import { Component, OnInit,EventEmitter,Output,Input } from '@angular/core';
import { FormControl, NgForm, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { formConstants } from '@config/forms-constants';
import { CommonApplicationService,CommonActivityService, AlertService } from '../../shared/_services';
import { apiUrl,map_api } from '../../config/api-url';
import jsonData from '@config/state_county';
import {IMyDpOptions, IMyDateModel} from 'mydatepicker';
import { StorageService } from '../../shared/_services/storage.service';


@Component({
  selector: 'app-property-search',
  templateUrl: './property-search.component.html',
  styleUrls: ['./property-search.component.css']
})
export class PropertySearchComponent implements OnInit {
  
  propertySearchForm: FormGroup;
  submitted:boolean=false;
  countries: any [];
  states: any;
  searchProAddress:any=[];
  loading:boolean=true;
  request_type:any;

  minChar:number=3;
  filteredOptions:any;
  loadingSearch:boolean=false;
  noResult:boolean=false;
  isSearch:boolean=false;

  @Input() limit:number;
  sale_type:any;

  @Output() propertyResult: EventEmitter<any> = new EventEmitter();


  constructor(private formBuilder: FormBuilder,
              private commonApplicationService: CommonApplicationService,
              private commonActivityService:CommonActivityService,
              private alertService:AlertService,
              private storageService:StorageService ) { }

  ngOnInit() {
   // this.countries  = formConstants.countries;
   let property_config =  this.storageService.get("property_config");
    if(property_config !== null){
        this.sale_type= property_config.sale_type;
    }

    this.states     = formConstants.states;
    this.request_type=formConstants.request_type;
    this.intilize();

    this.propertySearchForm.get('property_address').valueChanges.subscribe(value => { 
      if(value && value.length>=this.minChar){
        this.onChangeSearch(value);
      }else{
        this.filteredOptions=[];
      }
    });

  }
  
  public myDatePickerOptions: IMyDpOptions = { dateFormat: 'yyyy-mm-dd',firstDayOfWeek : 'su'};
  public myYearPickerOptions: IMyDpOptions = { dateFormat: 'yyyy'};

  onDateChanged(event: IMyDateModel) {
    return event.formatted;
  }

  onYearChanged(event: IMyDateModel) {
    console.log(event);
    return event.formatted;
  }

  intilize(){

    this.propertySearchForm = this.formBuilder.group({
          property_address: ['', ''],
          city: ['', ''],
          county: ['', ''],
          state: ['', ''],
          zip_code: ['', ''],
          sale_date_from: ['', ''],
          sale_date_to: ['', ''],
          close_date_from: ['', ''],
          close_date_to:['', ''],
          sale_type:[],
          build_year_from: ['', ''],
          build_year_to: ['', ''],
          filter_type: ['', ''],
          equity: ['', ''],
          redemption_expires_from:['',''],
          redemption_expires_to:['',''],
          limit:[this.limit],
          case_number:['',''],
    });
  }

  getCountyList(state){
    //var selectedState = JSON.parse(this.stateCountyList)
    this.countries = jsonData[state];
  }

  get f() { return this.propertySearchForm.controls; }

  selectEvent(item) {
    this.propertySearchForm.controls.property_address.setValue(item);
  
  }
 
  onChangeSearch(val: string) {
    this.loadingSearch=true;
    let url=map_api.map_api_url+'?apiKey='+map_api.api_key+'&in=countryCode%3AUSA&limit=10&q='+val;
  //  let url=map_api.map_api_url+'?app_id='+map_api.api_id+'&app_code='+map_api.api_code+'&country=USA&maxresults=10&query='+val;
    this.commonApplicationService.get(url)
      .subscribe(
          response => {
            this.filteredOptions=[];
            if(response.suggestions.length>0){
              for(var i=0; i<response.suggestions.length; i++){
                var strArr = response.suggestions[i].label.split(',');
                var key=strArr[strArr.length-1].replace(/^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g, '');
                this.filteredOptions.push(key);
              }
            }else{
                this.noResult=true;
            }
            this.loadingSearch=false;
          },
          error => {
            this.alertService.common(error);
          }
      ); 
  }
  
  onFocused(e){
    // do something when input is focused
  }

  seachProperty(data: any){
    this.loading=true;
    let result = this.commonActivityService.getFullFormDataWithDateFormatted(data);
    this.propertyResult.emit(result);
  }

  changeLimit(limit){
    this.propertyResult.emit({'limit':limit.target.value});
  }

  changeMortgage(mortgage){
    this.propertyResult.emit({'mortgage':mortgage.target.value});

  }
  
  rest_search(){
    this.propertyResult.emit('');
    this.countries=[];
    this.intilize();
  }
}
