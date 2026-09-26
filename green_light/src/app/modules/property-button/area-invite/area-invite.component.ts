import { Component, Inject, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { apiUrl } from '../../../config/api-url';
import { CommonApplicationService } from '../../../shared/_services';
import { CommonActivityService,AlertService} from '../../../shared/_services';
import {MatDialogRef, MAT_DIALOG_DATA} from '@angular/material/dialog';


@Component({ 
  selector: 'area-invite',
  templateUrl: './area-invite.html',
})

export class AreaInviteComponent  implements OnInit{
  inviteForm: FormGroup;
  invalidFields: any;  
  submitted = false;
  loading = false;
  property_id: string;
  area_list:any=[];

  constructor(private formBuilder: FormBuilder,
        private dialogRef: MatDialogRef<AreaInviteComponent>,
        private commonActivityService :CommonActivityService,
        private commonApplicationService:CommonApplicationService,
        private alertService: AlertService,
        @Inject(MAT_DIALOG_DATA) public data: any

       ) { 
         
   }

  ngOnInit() {
    this.inviteForm = this.formBuilder.group({
      invite_type: [ "", Validators.required], 
      area_id: [ "", Validators.required],
      notes: ["", Validators.required],  
    });
    this.property_id = this.data.property_id;
  }

  get f() { return this.inviteForm.controls; }

  // Property Validation
  validateForm(data: any) {     
    let result = this.commonActivityService.getFullFormData(data);
    this.invalidFields = this.commonActivityService.findInvalidControls(data);
    this.submitted = true;
    if(this.inviteForm.invalid) { 
      return;
    }    
    // FORM SUBMITTED
    this.saveInfoDetails(result);  
  }

  // Save info detail
  saveInfoDetails(data: any){
    this.loading = true;
    let url = apiUrl.save_area_invite+"/"+this.property_id;
    this.commonApplicationService.post(url, data)
        .subscribe(
            data => {
              if(data.status=='success'){
                this.dialogRef.close();
                this.alertService.success(data.message);  
              }else{
                this.alertService.error(data.message);  
              }
              this.loading = false;
            },
            error => {
              this.loading = false;
              this.alertService.common(error); 
            }
        ); 
  }

  getAreaInvite($event){
    
    let url = apiUrl.get_area_invite;
    this.commonApplicationService.get(url)
        .subscribe(
            response => {
              this.area_list=response.data;
            },
            error => {
              this.alertService.common(error);
            }
        ); 
  }

}

