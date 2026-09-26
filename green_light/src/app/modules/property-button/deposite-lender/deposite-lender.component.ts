import { Component, OnInit,Inject } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { apiUrl } from '../../../config/api-url';
import { CommonApplicationService,CommonActivityService,AlertService } from '../../../shared/_services';
import {MatDialogRef, MAT_DIALOG_DATA} from '@angular/material/dialog';

@Component({
  selector: 'app-deposite-lender',
  templateUrl: './deposite-lender.component.html',
  styleUrls: ['./deposite-lender.component.css']
})
export class DepositeLenderComponent implements OnInit {

  depositLenderForm: FormGroup;
  invalidFields: any;  
  submitted = false;
  loading = false;
  loadingMessage: any;
  propErr: boolean = false;
  propErrMsg: any = "";
  modifyBtn : boolean = false;
  property_id: string;
  constructor(private formBuilder: FormBuilder,
              private commonActivityService :CommonActivityService,
              private commonApplicationService:CommonApplicationService,
              private alertService: AlertService,
              private dialogRef: MatDialogRef<DepositeLenderComponent>,
              @Inject(MAT_DIALOG_DATA) public data: any) { }

  ngOnInit() {
    this.property_id = this.data.property_id;

    this.depositLenderForm = this.formBuilder.group({
          turn_around_time: [ "", Validators.required],
          rate_of_return: ["", Validators.required], 
          renovation_risk: ["", Validators.required],  
          funding_deposit: ["", Validators.required],  
          loan_current: ["", Validators.required], 
          needed_for_deposit: ["", Validators.required],  
          needed_for_renovation: ["", Validators.required],   
          miscelainous_fees: ["", Validators.required],   
          estimated_values_ab: ["", Validators.required],   
          estimated_values_bc: ["", Validators.required], 
          full_material_list: ["", Validators.required],
          full_scope_work: ["", Validators.required],
          your_timeline: ["", Validators.required],   
          purchased_over: ["", Validators.required], 
          flip_transactions: ["", Validators.required],
          rehab_currently: ["", Validators.required],   
          p1_value: ["", Validators.required], 
          p1_adom: ["", Validators.required],
          p2_value: ["", Validators.required], 
          p2_adom: ["", Validators.required],
          p3_value: ["", Validators.required], 
          p3_adom: ["", Validators.required],
          wholetail_value: ["", Validators.required],
          rental_rate: ["", Validators.required], 
          loan_type: ["", Validators.required],
          deposit_notes:["", Validators.required]
    });
  }

  get f() { return this.depositLenderForm.controls; }

  // Property Validation
  validateForm(data: any) {  
    
    let result = this.commonActivityService.getFullFormData(data);
    this.invalidFields = this.commonActivityService.findInvalidControls(data);
    this.submitted = true;
    if(this.depositLenderForm.invalid) { 
      return;
    }    
    console.log(result);   
    this.saveInfoDetails(result);  
  }

  // Save info detail
  saveInfoDetails(data: any){
    this.loading = true;
    let url = apiUrl.deposit_executive_lender+"/"+this.property_id;
    this.commonApplicationService.post(url, data)
        .subscribe(
            data => {
              if(data.status=='success'){
                this.dialogRef.close();
                this.alertService.success(data.message);  
              }
              if(data.status=='failed'){
                this.alertService.success(data.message);  
              }
              this.loading=false;
            },
            error => {
                this.loading=false;
                this.alertService.common(error);
            }
        ); 
  }

}
