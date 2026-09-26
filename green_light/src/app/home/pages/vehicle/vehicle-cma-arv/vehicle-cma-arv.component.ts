import { Component, Input, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { AlertService, apiUrl, CommonActivityService, CommonApplicationService } from '@shared-service/_services';
import { StorageService } from '@shared-service/_services/storage.service';

@Component({
  selector: 'app-vehicle-cma-arv',
  templateUrl: './vehicle-cma-arv.component.html',
  styleUrls: ['./vehicle-cma-arv.component.css']
})
export class VehicleCmaArvComponent implements OnInit {

  @Input() vehicleCmaArvInfo;
  @Input() vehicleId;
  vehicleCmaArvForm:FormGroup;
  property_config: any;
  info_added_by: any;
  submitted: boolean;
  loading: boolean;

  constructor(private storageService:StorageService,
             private formBuilder:FormBuilder,
             private commonActivityService:CommonActivityService,
             private commonApplicationService:CommonApplicationService,
             private alertService:AlertService) { }

  ngOnInit(): void {
    this.property_config =  this.storageService.get("property_config");
    this.info_added_by = this.property_config.info_added_by;

    this.vehicleCmaArvForm = this.formBuilder.group({
        id:[''],
        cma_arv_type: ['', Validators.required],
        in_cash_offer: ['', Validators.required],
        trade_in_value: ['', Validators.required],
        private_party_value: [''],
        vehicle_type: [''],  
        comps_url: [''],
        vehicleId:[this.vehicleId]
    });

  }

  panelExpand($event){

  }

  validateForm(data){
    let result = this.commonActivityService.getFullFormDataWithDateFormatted(data);
    this.submitted = true;
    console.log('niform');
    if(this.vehicleCmaArvForm.invalid) { 
      return;
    }    
    // FORM SUBMITTED
    console.log('form submitted');
    this.saveUpdateVehicleCmaArvInfo(result);  
  }

  saveUpdateVehicleCmaArvInfo(data){
    this.loading = true;
    let url = apiUrl.vehicleCmaArv;
    this.commonApplicationService.post(url, data).subscribe(
        response => {
          if(response.status=='success'){
            let itemIndex = this.vehicleCmaArvInfo.findIndex(item => item.id == response.data.id);
            if(itemIndex >= 0){
              this.vehicleCmaArvInfo[itemIndex] = response.data;
            }else{
              this.vehicleCmaArvInfo.push(response.data);
            }
            this.vehicleCmaArvForm.reset();
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

  editVehicleCmaArv(vehicle){
    this.vehicleCmaArvForm.get('id').setValue(vehicle?.id);
    this.vehicleCmaArvForm.get('cma_arv_type').setValue(vehicle?.cma_arv_type);
    this.vehicleCmaArvForm.get('in_cash_offer').setValue(vehicle?.in_cash_offer);
    this.vehicleCmaArvForm.get('trade_in_value').setValue(vehicle?.trade_in_value);
    this.vehicleCmaArvForm.get('private_party_value').setValue(vehicle?.private_party_value);
    this.vehicleCmaArvForm.get('vehicle_type').setValue(vehicle?.vehicle_type);
    this.vehicleCmaArvForm.get('vehicleId').setValue(vehicle?.vehicleId);
    this.vehicleCmaArvForm.get('comps_url').setValue(vehicle?.comps_url);

  }

}
