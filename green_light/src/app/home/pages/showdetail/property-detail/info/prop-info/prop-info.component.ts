import { Component, Injectable, EventEmitter,ViewChild, OnInit, HostListener, ComponentFactoryResolver, Input, } from '@angular/core';
import { FormControl, NgForm, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { NgbDateAdapter, NgbDateStruct } from '@ng-bootstrap/ng-bootstrap';
import { Router,ActivatedRoute } from '@angular/router';
import { InfoModel } from '../info.model';
import {DatePipe} from '@angular/common';

//import { base_url, property_info } from '../../../../config/api-url';
import { formConstants } from '../forms-constants';
import { InjectDirective } from '../inject.directive';
import { HttpClient } from '@angular/common/http';

import jsonData from './state_county';

import {IMyDpOptions, IMyDateModel} from 'mydatepicker';
import { CommonHelper } from '@shared-service/_utils/CommonHelper';
import { CommonApplicationService,AlertService,CommonActivityService,MessageService,CommunicationService } from '@shared-service/_services';
import { apiUrl,map_api } from '@config/api-url';
import { StorageService } from '@shared-service/_services/storage.service';


interface LooseObject {
      [key: string]: any
}


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
  selector: 'prop-info',
  templateUrl: './prop-info.component.html',
  providers: [{provide: NgbDateAdapter, useClass: NgbDateNativeAdapter}]
})
export class PropInfoComponent implements OnInit {
   
    @Input() infoData: any;
    @Input() isFavourite: boolean;
    
    private infoDataOrg : any;
    propertyForm: FormGroup;
    submitted = false;
    record: LooseObject = {};
    result: any;  
    invalidFields: any;  
    loading = false;
    loadingMessage: any;
    propErr: boolean = false;
    propErrMsg: any = "";
    modifyBtn : boolean = false;
    property_id: string;
    address: any;
    countries: any [];
    states: any;
    counter_10: number [];
    ext_walls: any;
    roofing: string [];
    ac_option: string [];
    heating_option: string [];
    poll_option: string [];
    fire_place_option: any;
    garage_option: string [];
    property_type: string [];
    specific_property_type: string [];
    building_style: string [];
    price_desc: string[];
    property_config: any= {};
    property_document_type_list: string[];
    doc_other_name: string;
    prop_document_date: string = null;
    case_no: string = "";
    property_document_type: number;
    document_property: any[];
    property_file: string;
    uploadUrl: string;
    uploadedFiles: any[] = [];
    showPropertyList: boolean = false;
    stateCountyList: any;
    counter_5:number[];;
    counter_float_5:any[];
    uploadLoading: boolean = false;
    acrsValue:string='';
    fieldHide: boolean=true; // based on property type hide some of field. thoes not needed
    
    
    /* Notes info */
    notes:string='notes';

    searchProAddress:any=[];
    showSearchAddress:boolean=false;

    @HostListener('document:click', ['$event']) onDocumentClick(event) {
      this.showSearchAddress = false;
    }
    
    field_option:any={'other_name':false,'case_no':false};
   
    @ViewChild(InjectDirective) injectComp: InjectDirective;
    constructor(private formBuilder: FormBuilder,
          private router: Router,
          private route: ActivatedRoute,
          private commonApplicationService: CommonApplicationService,
          private commonActivityService: CommonActivityService,
          private storageService:StorageService,
          private _componentFactoryResolver: ComponentFactoryResolver,
          private httpClient: HttpClient,
          private alertService: AlertService,
          private datePipe:DatePipe,
          private messageService: MessageService,
          private communicationService:CommunicationService
          ) {
        this.property_config =  this.storageService.get("property_config");
       
        //this.countries         = formConstants.countries;
        this.states             = formConstants.states;
        this.counter_10     = formConstants.counter_10;
        this.counter_5     =      formConstants.counter_5;
        this.counter_float_5     = formConstants.counter_float_5;
        this.fire_place_option = formConstants.fire_place_option;
        if(this.property_config !== null && this.property_config !== false){
          this.ext_walls          = this.property_config.ext_wall_type;
          this.roofing              = this.property_config.roofing;
          this.ac_option          = this.property_config.ac_heating;
          this.heating_option   = this.property_config.ac_heating;
          this.poll_option         = this.property_config.pool_spa;
          this.garage_option    = this.property_config.garage_types;
          this.property_type    = this.property_config.property_types;
          this.specific_property_type     = this.property_config.specific_property_types[1].Residential;
          this.building_style     = this.property_config.building_style;
          this.price_desc          = this.property_config.ph_description;
          this.property_document_type_list = this.property_config.property_info_document_type;
        }

        
    }

    
    public myDatePickerOptions: IMyDpOptions = { dateFormat: 'mm/dd/yyyy',firstDayOfWeek : 'su'};

    onDateChanged(event: IMyDateModel) {
      return event.formatted;
    }

    getCountyList(state){
      this.countries = jsonData[state];
    }

   
    changeSpecificProp(propValue){

        switch (parseInt(propValue)) {
          case 1:
            this.specific_property_type = this.property_config.specific_property_types[1].Residential;
            break;

         case 2:
          this.specific_property_type = this.property_config.specific_property_types[2].Land;
          break;

         case 3:
          this.specific_property_type = this.property_config.specific_property_types[3].Commercial;
          break;

         case 4:
          this.specific_property_type = this.property_config.specific_property_types[4].Industrial;
          break;
          case 5:
            this.specific_property_type = this.property_config.specific_property_types[5].Water;
          break;
         default:
            this.specific_property_type = this.property_config.specific_property_types[1].Residential;
            break;
        }

        this.propertyForm['controls'].specific_property_type.setValue(this.infoData.specific_property_type );
            
    }

    selectInput(event){
      let selected = event.target.value;
        if (selected == 'Vacant') {
          this.fieldHide = false;
          console.log(selected);
        } else {
          this.fieldHide = true;
        }
    }


    onProChange(ele,event){
    //$(event.target).insertAfter("<h2>Hi</h2>");
    if(this.infoDataOrg[ele] != this.infoData[ele]){
      console.log("Not same"+ele);

    }else{
      console.log("Same");
    }
  }

  updateRecords(ele){
      this.record[ele] = this.infoData[ele];
    this.saveInfoDetails(this.record);
  }

  ngOnChanges() {
    if (!!this.propertyForm && this.propertyForm.dirty) {
        console.log("The form is dirty!");
    }
    else {
        console.log("No changes yet!");
    }      
  }

  ngOnInit() {   
      this.property_id = this.route.snapshot.paramMap.get('property_id');


      
      
      // Load info details
      if(this.property_id !== undefined){
        this.propErr = true;
        this.initialize();
        
        //this.formatDocuments();
        if(this.infoData.property_type != null && this.infoData.property_type > 0){
            // If prop is set then populate specifi prop 
            this.changeSpecificProp(this.infoData.property_type);
        }    
      }


      if(this.result !== undefined){
        this.address = this.result.address;
      }

      this.communicationService.getScrapperData().subscribe(scrapper=>{
          this.update_scrapper_info(scrapper);
      });

      if(this.infoData.state){
        this.getCountyList(this.infoData.state);
      }
      
      // this.propertyForm.get('country_assessor_url').valueChanges.subscribe(value =>
      // {
         
      //    let el=(<any>this.propertyForm.get('country_assessor_url')).nativeElement;
      //    let data={'name':'country_assessor_url','value':value,'el':el};
      //    this.autoSave(data);
      // });
  }

  update_scrapper_info(scrapper){
    this.infoData.address=scrapper.address;
    this.infoData.state=scrapper.state;
    this.infoData.zip=scrapper.zip;
    this.infoData.county=scrapper.county;
    this.infoData.year_built=scrapper.year_built;
    this.infoData.ac=scrapper.ac;
    this.infoData.heating=scrapper.heating;
    this.infoData.specific_property_type=scrapper.specific_property_type;
    this.infoData.bath=scrapper.bath;
    this.infoData.bed=scrapper.bed;
    this.infoData.total_living_sqft=scrapper.total_living_sqft;
    this.infoData.lot_acreage_sf=scrapper.lot_acreage_sf;
    this.infoData.stories=scrapper.stories;
    this.infoData.bonus_room=scrapper.bonus_room;
    this.infoData.pool=scrapper.pool;
    this.infoData.city=scrapper.city;
    this.infoData.property_type=scrapper.property_type;
    this.infoData.ext_wall_type=scrapper.ext_wall_type;
    this.infoData.garage_types=scrapper.garage_types;
    this.infoData.property_descriptions.legal_description=scrapper.legal_description?scrapper.legal_description:'';
    this.infoData.property_descriptions.property_description=scrapper.property_description;
    this.infoData.half_baths=scrapper.half_baths;
    this.infoData.full_bath=scrapper.full_bath;
    this.getCountyList(this.infoData.state);

    this.initialize();
  }

  onSubmit() { this.submitted = true; alert("Fomsu");}

  /*ngAfterViewInit(){
    this.modifyBtn = false;
  }*/
  
  initialize(){

      let cmaArv=this.infoData.last_cma_arv_recommendations?this.infoData.last_cma_arv_recommendations.recommended_cma_arv:0;
      let cost_per_sqft:any=0;
      if(cmaArv && Math.floor(this.infoData.total_living_sqft)){
        cost_per_sqft=(cmaArv/this.infoData.total_living_sqft).toFixed(2);
      }
      // From validation
      console.log("INITLITING THE FORM................................");
      let realState = this.infoData.local_real_estate_details;
      let propertyDesc = this.infoData.property_descriptions;
      this.propertyForm = this.formBuilder.group({
      address: [ this.infoData.address, Validators.required],
      city: [this.infoData.city, Validators.required],
      county: [this.infoData.county, Validators.required],
      zip: [this.infoData.zip, Validators.required],
      state: [this.infoData.state, Validators.required],
      total_living_sqft: [this.infoData.total_living_sqft?CommonHelper.convertInt(this.infoData.total_living_sqft):0, Validators.required],
      cost_per_sqft: [cost_per_sqft],
      total_sqft: [this.infoData.total_sqft?CommonHelper.convertInt(this.infoData.total_sqft):0, Validators.required],
      main_floor_area: [this.infoData.main_floor_area?CommonHelper.convertInt(this.infoData.main_floor_area):0, Validators.required],
      second_floor_area: [this.infoData.second_floor_area?CommonHelper.convertInt(this.infoData.second_floor_area):0, Validators.required],
      third_floor_area: [this.infoData.third_floor_area?CommonHelper.convertInt(this.infoData.third_floor_area):0, Validators.required],
      basement_area: [this.infoData.basement_area?CommonHelper.convertInt(this.infoData.basement_area):0, Validators.required],
      finished_basement_area: [this.infoData.finished_basement_area?CommonHelper.convertInt(this.infoData.finished_basement_area):0, Validators.required],
      finished_attic: [this.infoData.finished_attic?CommonHelper.convertInt(this.infoData.finished_attic):0, Validators.required],
      enclosed_porch: [this.infoData.enclosed_porch?CommonHelper.convertInt(this.infoData.enclosed_porch):''],
      bonus_room: [this.infoData.bonus_room?CommonHelper.convertInt(this.infoData.bonus_room):0, Validators.required],
      year_built: [this.infoData.year_built?this.infoData.year_built:0, Validators.required],
      bed: [(this.infoData.bed != null)? this.infoData.bed : 0, Validators.required],
      bath: [(this.infoData.bath != null)? this.infoData.bath : 0, Validators.required],
      full_bath: [this.infoData.full_bath, Validators.required],
      half_bath: [this.infoData.half_bath, Validators.required],
      three_quarter_bath: [this.infoData.three_quarter_bath, Validators.required],
      of_families: [this.infoData.of_families, Validators.required],
      of_kitchen: [(this.infoData.of_kitchen != null)? this.infoData.of_kitchen : 0, Validators.required],
      fireplaces: [(this.infoData.fireplaces != null)? this.infoData.fireplaces : 0, Validators.required],
      subdivision: [this.infoData.subdivision?this.infoData.subdivision:''],
      property_description: [(propertyDesc != null) ?propertyDesc.property_description : '', Validators.required],
      legal_description: [(propertyDesc!= null) ? propertyDesc.legal_description : '', Validators.required],
      ext_wall_type: [(this.infoData.ext_wall_type != null)? this.infoData.ext_wall_type : 0, Validators.required],
      roofing: [(this.infoData.roofing != null)? this.infoData.roofing : 0, Validators.required],
      ac: [(this.infoData.ac != null)? this.infoData.ac : 0, Validators.required],
      heating: [(this.infoData.heating != null)? this.infoData.heating : 0, Validators.required],
      pool: [(this.infoData.pool != null)? this.infoData.pool : 0, Validators.required],
      garages: [(this.infoData.garages != null)? this.infoData.garages : 0, Validators.required],
      garage_types: [(this.infoData.garage_types != null)? this.infoData.garage_types : 0, Validators.required],
      garage_sf: [this.infoData.garage_sf?CommonHelper.convertInt(this.infoData.garage_sf):0, Validators.required],
      lot_acreage_sf: [this.infoData.lot_acreage_sf, Validators.required],
      stories: [(this.infoData.stories != null)? this.infoData.stories : 0, Validators.required],
      property_type: [(this.infoData.property_type != null)? this.infoData.property_type : 0, Validators.required],
      specific_property_type: [(this.infoData.specific_property_type != null)? this.infoData.specific_property_type : 0, Validators.required],
      building_style: [(this.infoData.building_style != null)? this.infoData.building_style : 0, Validators.required],
      parcel_id1: [this.infoData.parcel_id1, Validators.required],
      county_value:  [this.infoData.county_value],
      //parcel_id2: [this.infoData.parcel_id2, Validators.required],
      prc_url: [this.infoData.prc_url, Validators.required],
      country_assessor_url: [this.infoData.country_assessor_url, Validators.required],
      gis_url: [this.infoData.gis_url, Validators.required],
      treasurer_url: [this.infoData.treasurer_url, Validators.required],
      tax_bill_url: [this.infoData.tax_bill_url, Validators.required],
      // county_url:[this.infoData.county_url, Validators.required],
      choose_prc: [''],
      taxes_assessed: [''],    
      year: [''],
      date: [''],
      price: [''],
      cost_per_sqft_pr: [''],
      source: [''],
      description: [''],
      save_form:['Save'],
    });

    this.propErr = false;

  //this.propertyForm.valueChanges.subscribe(data => console.log('form changes', data));
    this.propertyForm.valueChanges.subscribe(data => {
      if(this.propertyForm.dirty) {
        this.modifyBtn = true;
        let result = this.commonActivityService.getModifiedOnly(this.propertyForm);
        
        this.messageService.sendArrData(result);
        // If property has change show save button 
      }    
    });
  
    this.sendMessage();
    this.checkPropertyType(this.infoData.specific_property_type);
    if(this.isLandProperty()){
      this.acrsValue=(this.propertyForm.get('lot_acreage_sf').value/43560).toFixed(2);
    }
    //this.commonActivityService.isDisabled("PROPERTY_INFO", this.propertyForm);
}

  sendMessage(): void {
        let data = {
        "total_living_sqft" : this.infoData.total_living_sqft, 
        "bed": this.infoData.bed,
        "bath": this.infoData.bath, 
        "year_built": this.infoData.year_built, 
        "lot_acreage_sf": this.infoData.lot_acreage_sf, 
        "stories": this.infoData.stories, 
        "county_value": this.infoData.county_value,
        "slug_address":this.infoData.address,
        "cost_per_sqft":this.infoData.cost_per_sqft,
        "address":this.infoData.address+', '+this.infoData.city+','+this.infoData.state+', '+this.infoData.zip,
        "state":this.infoData.state,
        "county":this.infoData.county,
        "zestimate":this.infoData.local_real_estate_details?this.infoData.local_real_estate_details.zestimate:'',
        "recommended_cma_arv":this.infoData.last_cma_arv_recommendations?this.infoData.last_cma_arv_recommendations.recommended_cma_arv:'',
      };
      console.log(data);
        this.storageService.setHard('property_info',data);
        // send message to subscribers via observable subject
        this.messageService.sendArrData(data);
    }
    
  // Save Mulit Fields in Once
  saveModifiedValues(data: any) {
      let result = this.commonActivityService.getModifiedOnly(data);
      this.saveInfoDetails(result);
      //return result;
  }

  // Property Validation
  validateForm(data: any) {     
    let result = this.commonActivityService.getFullFormData(data);
    this.invalidFields = this.commonActivityService.findInvalidControls(data);
    console.log("Form Invalid Filleds = ",this.invalidFields);
    this.submitted = true;
    console.log('niform');
    if(this.propertyForm.invalid) { 
      this.alertService.error('Form is invalid, Fill all fields.');  
      //this.propertyForm.get('prop_address').markAsTouched();      
      return;
    }    
    // FORM SUBMITTED
    console.log('form submitted');
    this.saveInfoDetails(result);  
  }

  // Save info detail
  saveInfoDetails(data: any){
    this.loading = true;
    //console.log("savingdata>"+this.propertyForm);
    console.log("savingdata>"+data);
    if(this.property_id){
      this.updateInfoDetail(data);
    }else{
      let url = apiUrl.property_info;
      this.commonApplicationService.post(url, data)
          .subscribe(
              data => {
                this.modifyBtn = false;
                this.alertService.common(data);  
                this.loading = false;
                this.router.navigate(['home/showdetail/'+data.row.house_id]);  
              },
              error => {
                this.loading = false;
                this.alertService.common(error);  
              }
          ); 
    }
     
  }

  updateInfoDetail(data: any){
    let url = apiUrl.property_info+'/'+this.property_id;
    this.commonApplicationService.put(url, data)
        .subscribe(
            data => {
              this.modifyBtn = false;
              this.alertService.success(data.row);  
              this.loading = false;
            },
            error => {
                this.loading = false;
                this.alertService.common(error); 
            }
        );
  }
  

  get f() { return this.propertyForm.controls; }

  populateCounty(response){
    this.propertyForm.controls.gis_url.setValue(response.gis_url);
    this.propertyForm.controls.country_assessor_url.setValue(response.county_assessor_url);
    this.propertyForm.controls.treasurer_url.setValue(response.treasurer_url);
    this.storageService.set('rod_url',response.rod_url);
    this.communicationService.setRodUrl(response.rod_url);

    let data={'gis_url':response.gis_url,
              'country_assessor_url':response.county_assessor_url,
              'treasurer_url':response.treasurer_url,
              'rod_url':response.rod_url};
    this.updateInfoDetail(data);
  }



  searchPropertyAddress($event){
    
    if($event.target.value.length>=3 && this.property_id==null){
      this.showSearchAddress=true;
      let url=map_api.map_api_url+'?apiKey='+map_api.api_key+'&in=countryCode%3AUSA&limit=10&q='+$event.target.value;
      //let url=map_api.map_api_url+'?app_id='+map_api.api_id+'&app_code='+map_api.api_code+'&country=USA&maxresults=10&query='+$event.target.value;
      this.commonApplicationService.get(url)
        .subscribe(
            response => {
              this.searchProAddress=[];
              if(response.suggestions.length>0){
                for(var i=0; i<response.suggestions.length; i++){
                  var strArr = response.suggestions[i].label.split(',');
  
                  var key=strArr[strArr.length-1].replace(/^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g, '');
                  let value=(key+' '+strArr[strArr.length-2].replace(/^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g, ''));
                  const newDoc = {'full_address': value,
                                  'address':key};
                  this.searchProAddress.push(newDoc);
                }
              }else{
                const newDoc = {'full_address': 'No Result Found', 'address':''};
                  this.searchProAddress.push(newDoc);
              }
              
            },
            error => {
              this.alertService.common(error); 
            }
        ); 
    }
  }

  selectPropertAddress(propertAddress){
    this.propertyForm.controls.address.setValue(propertAddress);
    this.showSearchAddress=false;
  }

  checkPropertyType(value){

      let property_type=this.propertyForm.get('property_type').value;
      if(property_type && value){
        let special_pro_type=this.property_config.hide_proprty_detail[property_type];
        if (special_pro_type && special_pro_type.hasOwnProperty(value)) { // true
          this.fieldHide=false;
        }
        else{
          this.fieldHide=true;
        }
      }
  }
  
  autoSave(data){
    this.commonActivityService.addLoader(data['el']);
    this.commonActivityService.removeElement(data['el'],'saved-icon');
    
    if(data['name']=='lot_acreage_sf'){
      data['value']=this.calSqlft();
    }
    let saveInfo:any={'name':data['name'],'value':data['value']}
    let url = apiUrl.auto_save_property+'/'+this.property_id;
    this.commonApplicationService.put(url, saveInfo)
        .subscribe(
            response => {
              if(response['status']=='failed'){
                this.commonActivityService.removeElement(data['el'],'loader-icon');
                this.alertService.error(response['message']);
              }else{
                  this.commonActivityService.addElement(data['el']);
                  this.commonActivityService.removeElement(data['el'],'loader-icon');
              }
            },
            error => {
              this.loading = false;
              this.alertService.common(error);
            }
        ); 
  }

  calSqlft(){
    let acr=this.propertyForm.get('lot_acreage_sf').value;
    if(this.isLandProperty()){
        if(acr){
          let sqftVal=(acr*43560).toFixed(2);
          this.acrsValue=acr;
           this.propertyForm.get('lot_acreage_sf').setValue(sqftVal);
           return sqftVal;
        }else{
          return acr;
        }      
    }else{
      return acr;
    }
  }

  isLandProperty(){
    let isLandPro=false;
    if(this.propertyForm.get('property_type').value ==2 && this.propertyForm.get('specific_property_type').value==21){
      isLandPro=true;
    }  
    return isLandPro;
  }
}