import { Component, Inject,OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { apiUrl } from '../../../config/api-url';
import { CommonApplicationService } from '../../../shared/_services';
import { CommonActivityService,AlertService} from '../../../shared/_services';
import {MatDialogRef, MAT_DIALOG_DATA} from '@angular/material/dialog';


@Component({ 
  selector: 'pass-on-it',
  templateUrl: './pass-on-it.html',
})

export class PassOnItComponent  implements OnInit{

  passForm: FormGroup;
  invalidFields: any;  
  submitted = false;
  loading = false;
  property_id: string;

  constructor(private formBuilder: FormBuilder,
              private dialogRef: MatDialogRef<PassOnItComponent>,
              private commonActivityService :CommonActivityService,
              private commonApplicationService:CommonApplicationService,
              private alertService: AlertService,
              @Inject(MAT_DIALOG_DATA) public data: any
       ) { 
         
   }
   
  ngOnInit() {
    this.passForm = this.formBuilder.group({
      notes: ["", Validators.required],
    });
    this.property_id = this.data.property_id;
  }

  get f() { return this.passForm.controls; }

  // Property Validation
  validateForm(data: any) {     
    let result = this.commonActivityService.getFullFormData(data);
    this.invalidFields = this.commonActivityService.findInvalidControls(data);
    this.submitted = true;
    if(this.passForm.invalid) { 
      return;
    }    
    this.saveInfoDetails(result);  
  }

  // Save info detail
  saveInfoDetails(data: any){
    this.loading = true;
    let url = apiUrl.save_pass_on+"/"+this.property_id;
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

}

