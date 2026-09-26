import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup,Validators} from '@angular/forms';
import {MatDialog, MatDialogRef, MAT_DIALOG_DATA} from '@angular/material/dialog';
import { CommonActivityService} from '../../../shared/_services';
import { StorageService } from '../../../shared/_services/storage.service';

@Component({
  selector: 'app-export-dialog',
  templateUrl: './export-dialog.component.html',
  styleUrls: ['./export-dialog.component.css']
})
export class ExportDialogComponent implements OnInit {

  exportForm:FormGroup;
  result:any;
  invalidFields: any; 
  submitted = false;
  user_role:any;

  constructor(private formBuilder:FormBuilder,
              private commonActivityService: CommonActivityService,
              public dialogRef: MatDialogRef<ExportDialogComponent>,
              private storageService:StorageService) { }

  ngOnInit() {
    this.user_role=this.storageService.get('user_info')['current_role'];
    this.initialize();
  }

  initialize(){
    this.exportForm = this.formBuilder.group({
        export_name: ['',Validators.required],     
        export_type:['',Validators.required],
   });
  }
  get f() { return this.exportForm.controls; }

  export_data(data: any) {
    this.submitted=true;
    this.result = this.commonActivityService.getFullFormDataWithDateFormatted(data);
      this.invalidFields = this.commonActivityService.findInvalidControls(data);
      console.log("Form Invalid Filleds = ",this.invalidFields);
      this.submitted = true;
      if(this.exportForm.invalid) {
        console.log('Form is invalid, Fill all fields.');    
      // this.alertService.error('Form is invalid, Fill all fields.');  
        return;
      }   
     this.dialogRef.close(this.result);
  }

  get exportAccess(){
    if(['home_buyer','wholesale_buyer'].indexOf(this.user_role) === -1){
      return true;
    }else{
      return false;
    }
  }
}
