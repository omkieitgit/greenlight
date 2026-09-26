import { Component, OnInit,Inject } from '@angular/core';
import { CommonApplicationService,CommonActivityService,AlertService,CommunicationService } from '../../../shared/_services';
import { apiUrl } from '../../../config/api-url';
import {MatDialogRef, MAT_DIALOG_DATA} from '@angular/material/dialog';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import {StorageService} from '../../../shared/_services/storage.service';

@Component({
  selector: 'app-common-notes-dialog',
  templateUrl: './common-notes-dialog.component.html',
  styleUrls: ['./common-notes-dialog.component.css']
})
export class CommonNotesDialogComponent implements OnInit {

  property_id:string;
  notesForm:FormGroup;
  invalidFields:any;
  result:any;
  submitted:boolean=false;
  note_name:string;
  updateNote:any;
  
  constructor(private formBuilder: FormBuilder,
              private commonActivityService:CommonActivityService,
              private commonApplicationService:CommonApplicationService,
              private alertService:AlertService,
              private storageService:StorageService,
              public dialogRef: MatDialogRef<CommonNotesDialogComponent>,
              @Inject(MAT_DIALOG_DATA) public data: any) { }


 

  ngOnInit() {
    this.property_id=this.data.property_id;
    this.note_name=this.data.note_name;

    this.updateNote=this.data.info?this.data.info[0]:'';

    if(this.property_id !== undefined){
      this.notesForm = this.formBuilder.group({
        notes: [this.updateNote.notes?this.updateNote.notes:'', Validators.required],
        note_type:[this.data.note_type],
      });    
    }

  }

  get f() { return this.notesForm.controls; }


  // Property Validation
  validateForm(data: any) {     
    this.submitted=true;
    let result = this.commonActivityService.getFullFormData(data);
    this.invalidFields = this.commonActivityService.findInvalidControls(data);
    if(this.notesForm.invalid) { 
      return;
    } 
    if(this.updateNote.id){
      this.update_note(this.updateNote.id,result);
    }else{
      this.save_note(result); 
    }
    
  }


  save_note(data){
    let url = this.data.url+'/'+this.property_id;
    this.commonApplicationService.post(url,data).subscribe(response => {

      if(response !== undefined){   
        if(response.status=='failed'){
          this.alertService.error(response.message); 
        }  
        if(response.status=='success'){  
          this.alertService.success(response.message);
          var jsObj = { "user": {"first_name": this.storageService.get('user_info')['first_name']},
                        "created_at":response.data.created_at,
                        "notes":response.data.notes,
                        "user_id":response.data.user_id,
                        "id":response.data.id
                      }
          this.dialogRef.close(jsObj);
        }
      }
    },
    (err: any) => {
      this.alertService.common(err);    
    })
  }

  update_note(id,data){
    let url = this.data.url+'/'+id;
    data['house_id']=this.property_id;
    this.commonApplicationService.put(url,data).subscribe(response => {

      if(response !== undefined){   
        if(response.status=='failed'){
          this.alertService.error(response.message); 
        }  
        if(response.status=='success'){  
          this.alertService.success(response.message);
          var jsObj = { "user": {"first_name": this.storageService.get('user_info')['first_name']},
                        "created_at":response.data.created_at,
                        "notes":response.data.notes,
                        "user_id":response.data.user_id,
                        "id":response.data.id
                      }
          this.dialogRef.close(jsObj);
        }
      }
    },
    (err: any) => {
      this.alertService.common(err);    
    })
  }

}
