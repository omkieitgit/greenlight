import { Component, Input, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { formConstants } from '@config/forms-constants';
import { AlertService, CommonActivityService, CommonApplicationService } from '@shared-service/_services';
import { apiUrl } from '@config/api-url';
import { StorageService } from '@shared-service/_services/storage.service';

@Component({
  selector: 'app-vehicle-info',
  templateUrl: './vehicle-info.component.html',
  styleUrls: ['./vehicle-info.component.css']
})
export class VehicleInfoComponent implements OnInit {
  states:any;
  vehicleInfoForm:FormGroup;
  submitted: boolean;
  loading: boolean;
  @Input() vehicleInfo;
  @Input() vehicleId;
  vehicle_make_info: any;

  constructor(private formBuilder:FormBuilder,
              private commonActivityService:CommonActivityService,
              private commonApplicationService:CommonApplicationService,
              private alertService:AlertService,
              private storageService:StorageService) { }

  ngOnInit(): void {
    console.log(this.vehicleId);
    this.vehicle_make_info =  this.storageService.get("property_config")['vehicle_make'];
    this.vehicleInfoForm = this.formBuilder.group({
      id:[''],
      vehicle_year: ['', Validators.required],
      vehicle_make: ['', Validators.required],
      vehicle_id: ['', Validators.required],
      registered_owner_1: [''],
      registered_owner_2: [''],
      county_tax: [''],
      year:[''],
      vehicle_condition:[''],
      vehicleId:[this.vehicleId]
     // price_info_data: this.formBuilder.array([this.priceFields()]), 
    });
  }

  validateForm(data,fileUploading){
    let result = this.commonActivityService.getFullFormDataWithDateFormatted(data);
    this.submitted = true;
    console.log('niform');
    if(this.vehicleInfoForm.invalid) { 
      return;
    }    
    // FORM SUBMITTED
    console.log('form submitted');
    this.saveUpdateVehicleInfo(result,fileUploading);  
  }

  uploadHandler(event){
    let elem = event.target; 
    if(elem.files.length > 0){
    }
  }

  saveUpdateVehicleInfo(data,document){
      this.loading = true;
      
    let input = new FormData();
    input.append("vehicle_year", data['vehicle_year']?data['vehicle_year']:'');
    input.append("vehicle_make", data['vehicle_make']?data['vehicle_make']:''); 
    input.append("vehicle_id", data['vehicle_id']?data['vehicle_id']:'');
    input.append("registered_owner_1", data['registered_owner_1']?data['registered_owner_1']:''); 
    input.append("registered_owner_2", data['registered_owner_2']?data['registered_owner_2']:'');
    input.append("county_tax", data['county_tax']?data['county_tax']:''); 
    input.append("year", data['year']?data['year']:'');
    input.append("vehicleId", data['vehicleId']);
    input.append("vehicle_condition", data['vehicle_condition']?data['vehicle_condition']:'');
    input.append("id", data['id']);
    if(document != "" && document != undefined){
      input.append('vehicle_document',document.files[0]);
    }
    
    
    let url = apiUrl.saveUpdateVehicleInfo;
    this.commonApplicationService.post(url, input).subscribe(
        response => {
          if(response.status=='success'){
            let itemIndex = this.vehicleInfo.findIndex(item => item.id == response.data.id);
            if(itemIndex >= 0){
              this.vehicleInfo[itemIndex] = response.data;
            }else{
              this.vehicleInfo.push(response.data);
            }
            this.vehicleInfoForm.reset();
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

  getCountyList(state){
    //this.countries = jsonData[state];
  }
  panelExpand($event){

  }
  
  editVehicleInfo(vehicle){
    this.vehicleInfoForm.get("id").setValue(vehicle?.id);
    this.vehicleInfoForm.get("vehicle_year").setValue(vehicle?.vehicle_year);
    this.vehicleInfoForm.get("vehicle_make").setValue(vehicle?.vehicle_make);
    this.vehicleInfoForm.get("vehicle_id").setValue(vehicle?.vehicle_id);
    this.vehicleInfoForm.get("registered_owner_1").setValue(vehicle?.registered_owner_1);
    this.vehicleInfoForm.get("registered_owner_2").setValue(vehicle?.registered_owner_2);
    this.vehicleInfoForm.get("county_tax").setValue(vehicle?.county_tax);
    this.vehicleInfoForm.get("year").setValue(vehicle?.year);
    this.vehicleInfoForm.get("vehicle_condition").setValue(vehicle?.vehicle_condition);
    this.vehicleInfoForm.get('vehicleId').setValue(vehicle?.vehicleId);
  }
}
