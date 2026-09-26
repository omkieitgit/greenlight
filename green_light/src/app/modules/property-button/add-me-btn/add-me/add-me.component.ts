import { Component, OnInit,AfterViewInit,Inject} from '@angular/core';
import { CommonApplicationService,CommonActivityService,AlertService } from '../../../../shared/_services';
import { apiUrl } from '../../../../config/api-url';
import { FormControl, NgForm,FormArray, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router,ActivatedRoute }    from '@angular/router';
import {MatDialog, MatDialogRef, MAT_DIALOG_DATA} from '@angular/material/dialog';

@Component({
  selector: 'app-add-me',
  templateUrl: './add-me.component.html',
  styleUrls: ['./add-me.component.css']
})
export class AddMeComponent implements OnInit,AfterViewInit {
  list_data:any;
  addMeForm:FormGroup;
  property_id:string;
  invalidFields: any;
  submitted = false;
  loading = false;

  constructor(private formBuilder: FormBuilder, 
              private route: ActivatedRoute,
              private dialogRef: MatDialogRef<AddMeComponent>,
              private commonActivityService :CommonActivityService,
              private commonApplicationService:CommonApplicationService,
              private alertService:AlertService,
              @Inject(MAT_DIALOG_DATA) public data: any) { }

  ngOnInit() {
    this.property_id=this.data.property_id;
    this.addMeForm = this.formBuilder.group({
      list_id: [ "", Validators.required],
    });
  }

  ngAfterViewInit(){
   
    if(this.property_id !== undefined){
      setTimeout(() => {
        this.get_list();
      });
    }
  }

  get_list(){
  
    let url = apiUrl.property_queue;
    this.commonApplicationService.get(url)
      .subscribe(
          data => {
            this.list_data=data.data;
          }
      ); 
  }

  // Property Validation
  validateForm(data: any) {     
    let result = this.commonActivityService.getFullFormData(data);
    this.invalidFields = this.commonActivityService.findInvalidControls(data);
    this.submitted = true;
    if(this.addMeForm.invalid) { 
      //this.propertyForm.get('prop_address').markAsTouched();      
      return;
    }    
    // FORM SUBMITTED
    this.saveAddMe(result);  
  }

  // Save info detail
  saveAddMe(data: any){
    this.loading = true;
   
    data.house_id=this.property_id;
    console.log(data);
    let url = apiUrl.add_property_queue+data.list_id;
    this.commonApplicationService.post(url, data)
        .subscribe(
            data => {
              this.dialogRef.close();
              this.alertService.success(data.message);  
              this.loading = false;
            },
            error => {
              this.loading = false;
              this.alertService.common(error); 
            }
        ); 
  }


}
