import { DatePipe } from '@angular/common';
import { Component, Input, OnInit } from '@angular/core';
import { FormArray, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { formConstants } from '@config/forms-constants';
import { AlertService, apiUrl, CommonActivityService, CommonApplicationService } from '@shared-service/_services';
import { StorageService } from '@shared-service/_services/storage.service';
import { CommonHelper } from '@shared-service/_utils/CommonHelper';
import { IMyDateModel, IMyDpOptions } from 'mydatepicker';

@Component({
  selector: 'app-vehicle-sale-info',
  templateUrl: './vehicle-sale-info.component.html',
  styleUrls: ['./vehicle-sale-info.component.css']
})
export class VehicleSaleInfoComponent implements OnInit {
  
  @Input() vehicleSaleInfo;
  vehicleInfo:any;

  states:any;
  vehicleSaleInfoForm:FormGroup;
  property_config: any;
  sale_type_option: any;
  loading:boolean= false;
  submitted: boolean;

  constructor( private formBuilder:FormBuilder,
               private storageService:StorageService,
               private commonActivityService:CommonActivityService,
               private commonApplicationService:CommonApplicationService,
               private  alertService:AlertService,
               private datePipe:DatePipe,) { }

  ngOnInit(): void {
    this.states= formConstants.states;
    this.property_config =  this.storageService.get("property_config");
    this.sale_type_option = this.property_config.vehicle_sale_type;

    this.vehicleSaleInfoForm = this.formBuilder.group({
      id:[this.vehicleSaleInfo?.id],
      state: [this.vehicleSaleInfo?.state, Validators.required],
      county: [this.vehicleSaleInfo?.county, Validators.required],
      case_number: [this.vehicleSaleInfo?.case_number, Validators.required],
      sale_type: [this.vehicleSaleInfo?.sale_type, Validators.required],
      sale_date: [(this.vehicleSaleInfo.sale_date != null)? {jsdate: this.changeTimezone(this.vehicleSaleInfo.sale_date)}: null, Validators.required],
      sale_time: [this.vehicleSaleInfo?.sale_time, Validators.required],
      sale_location:[this.vehicleSaleInfo?.sale_location],
      trustee:[this.vehicleSaleInfo?.trustee],
      petitioner:[this.vehicleSaleInfo?.petitioner],
      respondent_info_data: this.formBuilder.array([this.priceFields()]), 
    });
    this.respondentForm(this.vehicleSaleInfo);
  }

  changeTimezone(date) { 
    return CommonHelper.getConvertDate(date);
  }


  priceFields() : FormGroup
  {    
    return this.formBuilder.group({
      id: [''],
      respondent: [''],
    });
  }

  autoSave($event){

  }

  addMoreRespondent() : void
  {
   const control = <FormArray>this.vehicleSaleInfoForm.controls.respondent_info_data;
   control.push(this.priceFields());
  }

  getCountyList(state){
    //this.countries = jsonData[state];
  }
  panelExpand($event){

  }
  public myDatePickerOptions: IMyDpOptions = { dateFormat: 'mm/dd/yyyy',firstDayOfWeek : 'su'};

  onDateChanged(event: IMyDateModel,dateInput) {
    return event.formatted;
  }

  validateForm(data){
    let result = this.commonActivityService.getFullFormDataWithDateFormatted(data);
    this.submitted = true;
    if(this.vehicleSaleInfoForm.invalid) { 
      return;
    }    
    // FORM SUBMITTED
    console.log('form submitted');
    this.saveUpdateVehicleSaleInfo(result);  
  }

  saveUpdateVehicleSaleInfo(data){
    this.loading = true;
    let url = apiUrl.vehicleSale;
    this.commonApplicationService.post(url, data)
        .subscribe(
            response => {
            if(response.status=='success'){
              // let itemIndex = this.vehicleSaleInfo.findIndex(item => item.id == response.data.id);
              // if(itemIndex >= 0){
              //   this.vehicleSaleInfo[itemIndex] = response.data;
              // }else{
              //   this.vehicleSaleInfo.push(response.data);
              // }
              // this.vehicleSaleInfoForm.reset();
              this.alertService.success(response.message);  
              }else{
                this.alertService.error(response.message); 
              }
              this.submitted=false;
              this.loading = false;
            },
            error => {
              this.loading = false;
              this.alertService.common(error);
            }
        ); 
  }
  
  respondentForm(vehicleInfo){
    let respondent_info=this.vehicleSaleInfoForm.get('respondent_info_data')['controls'];
    if(vehicleInfo?.respondent.length>0){
      for(var i=0; i<vehicleInfo?.respondent.length; i++ ){
        if(i >= respondent_info.length){
          this.addMoreRespondent();
        }
        let respondent=this.vehicleSaleInfoForm.get('respondent_info_data')['controls'][i].controls;
        respondent.id.setValue(vehicleInfo?.respondent[i]?.id);
        respondent.respondent.setValue(vehicleInfo?.respondent[i]?.respondent);
        
      }
    }
    else{
      for(let j=1; j<respondent_info.length; j++){
          const control = <FormArray>this.vehicleSaleInfoForm.controls.respondent_info_data;
          control.removeAt(j);
      }
    }
  }


  editVehicleSaleInfo(vehicleInfo){
    this.vehicleSaleInfoForm.reset();
    let saleDate=null;
    if(vehicleInfo?.sale_date !== undefined && vehicleInfo?.sale_date != null){
      let dDate = new Date(vehicleInfo?.sale_date);
      saleDate = {date: {year: dDate.getFullYear(),month: dDate.getMonth() + 1,day: dDate.getDate()}}
    }
    this.vehicleSaleInfoForm.get("id").setValue(vehicleInfo?.id);
    this.vehicleSaleInfoForm.get("state").setValue(vehicleInfo?.state);
    this.vehicleSaleInfoForm.get("county").setValue(vehicleInfo?.county);
    this.vehicleSaleInfoForm.get("case_number").setValue(vehicleInfo?.case_number);
    this.vehicleSaleInfoForm.get("sale_type").setValue(vehicleInfo?.sale_type);
    this.vehicleSaleInfoForm.get("sale_date").setValue(saleDate);
    this.vehicleSaleInfoForm.get("sale_time").setValue(vehicleInfo?.sale_time);
    this.vehicleSaleInfoForm.get("sale_location").setValue(vehicleInfo?.sale_location);
    this.vehicleSaleInfoForm.get("trustee").setValue(vehicleInfo?.trustee);
    this.vehicleSaleInfoForm.get("petitioner").setValue(vehicleInfo?.sale_location);

    let respondent_info=this.vehicleSaleInfoForm.get('respondent_info_data')['controls'];
    if(vehicleInfo?.respondent.length>0){
      for(var i=0; i<vehicleInfo?.respondent.length; i++ ){
        if(i >= respondent_info.length){
          this.addMoreRespondent();
        }
        let respondent=this.vehicleSaleInfoForm.get('respondent_info_data')['controls'][i].controls;
        respondent.id.setValue(vehicleInfo?.respondent[i]?.id);
        respondent.respondent.setValue(vehicleInfo?.respondent[i]?.respondent);
        
      }
    }
    else{
      for(let j=1; j<respondent_info.length; j++){
          const control = <FormArray>this.vehicleSaleInfoForm.controls.respondent_info_data;
          control.removeAt(j);
      }
    }

  }

}
