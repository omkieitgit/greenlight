import { Component, Inject, Input, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material/dialog';
import { AlertService, apiUrl, CommonActivityService, CommonApplicationService } from '@shared-service/_services';

@Component({
  selector: 'app-client-msc-form',
  templateUrl: './client-msc-form.component.html',
  styleUrls: ['./client-msc-form.component.css']
})
export class ClientMscFormComponent implements OnInit {

  miscFormData: FormGroup;
  loading = false;
  loadingMessage: any;
  propErr: boolean = false;
  propErrMsg: any = "";
  invalidFields: any;
  submitted = false;
  @Input() property_id:string;
  misc_result: any;
  openPanel:boolean = false;
  amount:number=0;
  @Input() client_master:any;
  recipients_info:any;
  showRecipients:boolean=false;

  constructor(private formBuilder: FormBuilder,
              private commonActivityService:CommonActivityService,
              private commonApplicationService:CommonApplicationService,
              private alertService:AlertService,
              @Inject(MAT_DIALOG_DATA) public data,
              private dialogRef:MatDialogRef<ClientMscFormComponent>,
              ) {
                
              }

  ngOnInit() {
    this.property_id=this.data.property_id;
    this.amount=this.data.amount;
    this.recipients_info=this.data.recipients_info;

    if(this.property_id !== undefined){
      this.initialize();
     // this.form1099MiscDetail();
    }
    
  }

  panelExpand(flag){
    if(!this.openPanel){
      this.openPanel=true;
      
    }
  }

  initialize(){
    
     this.miscFormData = this.formBuilder.group({
        recipients_tin: [this.misc_result?.recipients_tin,[Validators.required]],
        recipients_name:[this.misc_result?.recipients_name,[Validators.required]],
        recipients_address:[this.misc_result?.recipients_address,[Validators.required]],
        recipients_city_state:[this.misc_result?.recipients_city_state,[Validators.required]],
        amount:[{value:this.amount, disabled: true}],
        id:['',[Validators.required]]
     });
   }
 
 
    /*----------------------------- Property validation --------------------------------*/
    validateForm(data: any) {     
     let result = this.commonActivityService.getFullFormDataWithDateFormatted(data);
     this.invalidFields = this.commonActivityService.findInvalidControls(data);
     console.log("Form Invalid Filleds = ",this.invalidFields);
     this.submitted = true;
     console.log('niform');
     if(this.miscFormData.invalid) { 
       console.log('Form is invalid, Fill all fields.');
       //this.propertyForm.get('prop_address').markAsTouched();      
       return;
     }    
     // FORM SUBMITTED
     console.log('form submitted');
     this.saveform1099MiscDetails(result);  
   }
 /*----------------------------- Property validation --------------------------------*/
 
 /*----------------------------- Save stratgegy --------------------------------*/
   saveform1099MiscDetails(data: any){
     this.loading = true;
     //console.log("savingdata>"+this.propertyForm);
     console.log("savingdata>"+data);
 
     let url = apiUrl.misc_1099+'/'+this.property_id;
     this.commonApplicationService.put(url, data)
         .subscribe(
              response => {
                let itemIndex = this.recipients_info?this.recipients_info.findIndex(item => item.id == response.data?.id):'-1';
                if(itemIndex < 0){
                  this.recipients_info.push(response.data);
                }else{
                  this.recipients_info[itemIndex]=response.data;
                }
                this.miscFormData.reset();this.submitted = false;
                this.miscFormData.get('amount').setValue(this.amount);
                this.alertService.success(response.message);  
                this.loading = false;
             },
             error => {
              this.loading = false;
              this.alertService.common(error); 
             }
         ); 
   }
 
 /*----------------------------- Save stratgegy --------------------------------*/
 
  get f() { return this.miscFormData.controls; }

  onChangeRecipients($event){
    if($event.target.value=='new'){
      this.miscFormData.reset();
      this.miscFormData.get('amount').setValue(this.amount);
      this.miscFormData.get('id').setValue('new');
      this.showRecipients=true;
    }else{
      let selectedRecipient=this.recipients_info.find(x=>x.id==$event.target.value);
      this.miscFormData.get('recipients_name').setValue(selectedRecipient.recipients_name);
      this.miscFormData.get('recipients_tin').setValue(selectedRecipient.recipients_tin);
      this.miscFormData.get('recipients_address').setValue(selectedRecipient.recipients_address);
      this.miscFormData.get('recipients_city_state').setValue(selectedRecipient.recipients_city_state);
      this.miscFormData.get('id').setValue(selectedRecipient.id);
      this.showRecipients=true;
    }
  }

  closeDialog(): void {
    this.dialogRef.close(this.recipients_info);
  }
}
