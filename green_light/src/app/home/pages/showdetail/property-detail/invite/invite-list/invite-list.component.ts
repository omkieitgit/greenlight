import { Component,Inject, OnInit} from '@angular/core';
import { FormGroup } from '@angular/forms';
import { apiUrl } from '@config/api-url';
import { CommonApplicationService,CommonActivityService,AlertService } from '@shared-service/_services';
import {MatDialogRef, MAT_DIALOG_DATA} from '@angular/material/dialog';

@Component({ 
  selector: 'invite-list',
  templateUrl: './invite-list.html',
})

export class InviteListComponent  implements OnInit{
  sendInviteForm: FormGroup;
  invalidFields: any;  
  submitted = false;
  loading = false;
  loadingMessage: any;
  propErr: boolean = false;
  propErrMsg: any = "";
  modifyBtn : boolean = false;
  property_id: string;

  records: any=[];
  loader:boolean=false;
  limit=10;
  offset=0;
  moreBtn:boolean=true;
  inviteLoader:boolean=false;

  constructor(private commonActivityService :CommonActivityService,
              private commonApplicationService:CommonApplicationService,
              private alertService: AlertService,
              private dialogRef: MatDialogRef<InviteListComponent>,
              @Inject(MAT_DIALOG_DATA) public data: any
       ) { 
        if(this.data.property_id){
          this.property_id = this.data.property_id;
          this.getInviteList();    
        }
   }
  

  ngOnInit() {
  }

  getInviteList(){
    this.loader=true; 
    let url = apiUrl.invite_list+this.property_id+'?limit='+this.limit+'&offset='+this.offset;    
      this.commonApplicationService.get(url).subscribe(response => {
        if(response !== undefined){             
          this.loader=false;  
          for(let i=0; response.data.length>i; i++){
            this.records.push(response.data[i]);
          }    
          if(response.data.length!=this.limit){
            this.moreBtn=false;
          }  
          this.offset=(this.offset+response.data.length);
        }
      },
        (err: any) => {
          this.loader=false; 
          this.propErrMsg = "Error occured, Please try again later!";
          this.alertService.common(err);
          this.propErr = true;
          this.loadingMessage = false;
        })  
  }
  


  get f() { return this.sendInviteForm.controls; }

  // Property Validation
  validateForm(data: any) {     
    let result = this.commonActivityService.getFullFormData(data);
    this.invalidFields = this.commonActivityService.findInvalidControls(data);
    console.log("Form Invalid Filleds = ",this.invalidFields);
    this.submitted = true;
    console.log('niform');
    if(this.sendInviteForm.invalid) { 
      console.log('Form is invalid, Fill all fields.');
      //this.propertyForm.get('prop_address').markAsTouched();      
      return;
    }    
    // FORM SUBMITTED
    console.log('form submitted');
    this.saveInfoDetails(result);  
  }

  // Save info detail
  saveInfoDetails(data: any){
    this.loading = true;
    //console.log("savingdata>"+this.propertyForm);
    console.log("savingdata>"+data);
    let url = apiUrl.send_invite+"/"+this.property_id;
    this.commonApplicationService.post(url, data)
        .subscribe(
            data => {
              this.modifyBtn = false;
              this.alertService.success(data.message);  
              this.loading = false;
              this.dialogRef.close();
            },
            error => {
              this.loading = false;
              this.alertService.common(error);
              this.dialogRef.close();
            }
        ); 
  }

  getloadmorepages(){
    this.getInviteList();
  }

  deleteInvite(inviteId){

    if(this.inviteLoader){
        return;
    }

    this.inviteLoader=true;
    let url = apiUrl.delete_invite+'/'+inviteId;
    this.commonApplicationService.delete(url).subscribe(response => {
      if(response){ 
        this.inviteLoader=false;
        const index:number=this.records.findIndex(item=>item.invitations_info_id==inviteId);
        if (index !== -1) {
          this.records.splice(index, 1);
        }
        this.alertService.success(response.message); 
      }
    },
    (err: any) => {
      this.inviteLoader=false;
      this.alertService.error("Error occured, Please try again later!");
    })
  }

}

