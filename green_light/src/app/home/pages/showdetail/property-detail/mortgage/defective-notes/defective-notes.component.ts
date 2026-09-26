import { Component, OnInit,Inject } from '@angular/core';
import {MatDialog, MatDialogRef, MAT_DIALOG_DATA} from '@angular/material/dialog';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';

import { CommonApplicationService,AlertService,CommonActivityService,CommunicationService } from '@shared-service/_services';
import { apiUrl } from '@config/api-url';


@Component({
  selector: 'app-defective-notes',
  templateUrl: './defective-notes.component.html',
  styleUrls: ['./defective-notes.component.css']
})
export class DefectiveNotesComponent implements OnInit {

  property_id:string;
  notesForm:FormGroup;
  invalidFields:any;
  result:any;
  submitted:boolean=false;

  constructor(private formBuilder: FormBuilder,
              private commonActivityService:CommonActivityService,
              private commonApplicationService:CommonApplicationService,
              private alertService:AlertService,
              private communicationService:CommunicationService,
              public dialogRef: MatDialogRef<DefectiveNotesComponent>,
    @Inject(MAT_DIALOG_DATA) public data: any) { }

    ngOnInit() {
      this.property_id=this.data.property_id;
      if(this.property_id !== undefined){
  
      //if(this.property_id !== undefined){
        
        this.notesForm = this.formBuilder.group({
          notes: ['', Validators.required],
        });    
      }
      
    }
  
    // Property Validation
    validateForm(data: any) {   
      this.submitted=true;  
      let result = this.commonActivityService.getFullFormData(data);
      this.invalidFields = this.commonActivityService.findInvalidControls(data);
      if(this.notesForm.invalid) { 
        return;
      }    
      this.save_wholesale_note(result);  
    }
  
  
    save_wholesale_note(data){
      let url = apiUrl.mortgage_notes+this.property_id;
      data.lien_type=this.data.lien_type;
      this.commonApplicationService.post(url,data).subscribe(response => {
        if(response.status == 'success'){        
          this.alertService.success(response.message); 
          this.communicationService.sendMortgageNotes(response.data);
          this.dialogRef.close();
  
        }else{
          this.alertService.common(response); 
        }
      },
        (err: any) => {
          this.alertService.error("Error occured, Please try again later!");     
        })
    }
    
    get f() { return this.notesForm.controls; }

}

