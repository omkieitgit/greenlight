import { DatePipe } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FormArray, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { apiUrl } from '@config/api-url';
import { formConstants } from '@config/forms-constants';
import { AlertService, CommonActivityService, CommonApplicationService } from '@shared-service/_services';
import { StorageService } from '@shared-service/_services/storage.service';
import { IMyDateModel, IMyDpOptions } from 'mydatepicker';

@Component({
  selector: 'app-create-vehicle',
  templateUrl: './create-vehicle.component.html',
  styleUrls: ['./create-vehicle.component.css']
})
export class CreateVehicleComponent implements OnInit {

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
               private router: Router,
               private datePipe:DatePipe,) { }

  ngOnInit(): void {
    this.states= formConstants.states;
    this.property_config =  this.storageService.get("property_config");
    this.sale_type_option = this.property_config.vehicle_sale_type;

    this.vehicleSaleInfoForm = this.formBuilder.group({
      id:[''],
      state: ['', Validators.required],
      county: ['', Validators.required],
      case_number: ['', Validators.required],
      sale_type: ['', Validators.required],
      sale_date: [ null, Validators.required],
      sale_time: ['', Validators.required],
      sale_location:[''],
      trustee:[''],
      petitioner:[''],
      respondent_info_data: this.formBuilder.array([this.priceFields()]), 
    });
   
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
              this.router.navigate(['home/vehicle/detail/'+response.data]);  
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
  
  


}
