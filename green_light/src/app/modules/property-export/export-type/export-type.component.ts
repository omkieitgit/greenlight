import { Component, OnInit,Inject } from '@angular/core';
import {MatDialog, MatDialogRef, MAT_DIALOG_DATA} from '@angular/material/dialog';
import { FormBuilder, FormGroup,Validator, Validators} from '@angular/forms';
import { CommonActivityService} from '../../../shared/_services';
@Component({
  selector: 'app-export-type',
  templateUrl: './export-type.component.html',
  styleUrls: ['./export-type.component.css']
})
export class ExportTypeComponent implements OnInit {

  exportTypeForm:FormGroup;
  export_type:string;
  emailType:boolean=false;
  msg:string;
  result:any;
  invalidFields: any; 
  submitted = false;
  constructor(private formBuilder:FormBuilder,
              private commonActivityService: CommonActivityService,
              public dialogRef: MatDialogRef<ExportTypeComponent>,
              @Inject(MAT_DIALOG_DATA) public data: any) { }

    ngOnInit() {
      this.export_type=this.data.export_type;
      this.defaultMsg();
      this.initialize();
      console.log(this.msg);
    }
  
    initialize(){
      this.exportTypeForm = this.formBuilder.group({
          type: ['',Validators.required],     
          to:['',[Validators.required,Validators.email]],
          subject:['',Validators.required],
          message:[this.msg,Validators.required]
     });
    }

    exportType(){
      this.submitted=true;
      if(this.exportTypeForm.controls.type.value=="") { 
        return;
      }    
      if(this.export_type=='Email'){
        this.submitted=false;
        this.emailType=true;
      }
      if(this.export_type=='Print'){
        let data=this.exportTypeForm.controls;
        let datainfo={'type':data.type.value};
        this.dialogRef.close(datainfo);
    
      }
    }

    get f() { return this.exportTypeForm.controls; }


    send(data: any) {

      this.result = this.commonActivityService.getFullFormDataWithDateFormatted(data);
      this.invalidFields = this.commonActivityService.findInvalidControls(data);
      console.log("Form Invalid Filleds = ",this.invalidFields);
      this.submitted = true;
      if(this.exportTypeForm.invalid) {
        console.log('Form is invalid, Fill all fields.');    
      // this.alertService.error('Form is invalid, Fill all fields.');  
        return;
      }   

      //let data=this.exportTypeForm.controls;
     // let datainfo={'type':data.type.value,'to':data.to.value,'subject':data.subject.value,'message':data.message.value};
      this.dialogRef.close(this.result);
    }

    defaultMsg(){

      this.msg="I've attached information about a number of foreclosure properties."  
                +"Please contact me for further information."

                +"Thanks,"
               // +"craig@theestates.com"
                +"To view the attachment, download the free Adobe Reader:"
                +"http://www.adobe.com/products/acrobat/readstep2.html";
    }

}
