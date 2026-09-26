import { Component, OnInit, Input } from '@angular/core';
import {formConstants} from '@config/forms-constants';
import { FormControl, NgForm,FormArray, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router,ActivatedRoute }    from '@angular/router';
import { AlertService, apiUrl, CommonActivityService, CommonApplicationService } from '@shared-service/_services';

@Component({
  selector: 'app-mailing',
  templateUrl: './mailing.component.html',
  styleUrls: ['./mailing.component.css']
})
export class MailingComponent implements OnInit {

  mailing_list:any;
  mailingForm: FormGroup;
  @Input() property_id:string;
  propErr: boolean = false;
  propErrMsg: any = "";
  loading = false;
  mailingInfo:any;
  openPanel:boolean = false;
  invalidFields: any;
  submitted = false;
  constructor(private formBuilder:FormBuilder,
              private commonApplicationService:CommonApplicationService,
              private commonActivityService: CommonActivityService,
              private alertService:AlertService) { }

  ngOnInit() {
    this.mailing_list=formConstants.mailing;
    this.initilize();
  }

  panelExpand(flag){
    if(!this.openPanel && this.property_id !== undefined){
      this.getTotalSthb();
      this.openPanel = true;
    }
  }
  
  /*----------------------------- Get property Info Details --------------------------------*/
  getTotalSthb(){
      this.loading=true;
      let url = apiUrl.mailing+"/"+this.property_id;
      this.commonApplicationService.get(url).subscribe(response => {
        if(response.data !== undefined){
          this.mailingInfo = response.data;
          this.populateDataInForm();
        }
        this.loading=false;
        this.propErr = true;
      },
      (err: any) => {
        this.propErrMsg = "Error occured, Please try again later!";
        this.propErr = true;
        this.loading=false;
      })
  }

  populateDataInForm(){
    if(this.mailingInfo.length){
      for(var i=0; i<this.mailingInfo.length; i++ ){
        this.fillMailingForm(i);
        this.addMailingInputField();
      }
    }     
  }
  fillMailingForm(index){
    let mailing=this.mailingForm.get('mailing_info_data')['controls'][index].controls;
    mailing.id.setValue(this.mailingInfo[index].id);
    mailing.user_type.setValue(this.mailingInfo[index].user_type);
    mailing.email_id.setValue(this.mailingInfo[index].email_id);
  }

  initilize() {
  	this.mailingForm = this.formBuilder.group({
      mailing_info_data: this.formBuilder.array([this.mailingFields()]),
      //email_notice: this.formBuilder.array([this.emailNoticeFields()]),
      house_id:[this.property_id]
	  });

  }

  mailingFields() : FormGroup
  {    
    return this.formBuilder.group({
      id:[''],
      user_type: ['', Validators.required],
      email_id: ['', [Validators.required,Validators.email]]
    });
  }

  emailNoticeFields() : FormGroup
  {    
    return this.formBuilder.group({
      id:[''],
      notice_chk: ['', Validators.required],
      notice_name: ['', Validators.required]
    });
  }

  addMailingInputField() : void
  {
    const control = <FormArray>this.mailingForm.controls.mailing_info_data;
    control.push(this.mailingFields());
  }

  addNoticeInputField() : void
  {
    const control = <FormArray>this.mailingForm.controls.email_notice;
    control.push(this.emailNoticeFields());
  }

  removeMailingField(i,id){
    if(confirm("Are you sure want to delete record ?")){
      const control = <FormArray>this.mailingForm.controls.mailing_info_data;
      control.removeAt(i);
    }else{
      return;
    }
    if(id){
      let url = apiUrl.mailing+'/'+id;
      this.commonApplicationService.delete(url).subscribe(response => {
      if(response !== undefined){             
            this.alertService.success(response.message); 
        }
      },
      (err: any) => {
        this.alertService.error("Error occured, Please try again later!");
      })
    }
  }

  removeNoticeField(i,id){
    if(confirm("Are you sure want to delete record ?")){
      const control = <FormArray>this.mailingForm.controls.email_notice;
      control.removeAt(i);
    }else{
      return;
    }
  }

  getUserInfo($event){
    console.log($event.target.value);
  }


  validateForm(data: any) {     
    let result = this.commonActivityService.getFullFormDataWithDateFormatted(data);
    this.invalidFields = this.commonActivityService.findInvalidControls(data);
    console.log("Form Invalid Filleds = ",this.invalidFields);
    this.submitted = true;
    console.log('niform');
    if(this.mailingForm.invalid) { 
      console.log('Form is invalid, Fill all fields.');
      return;
    }    
   
    if(!this.loading){
      this.loading = true;
      this.saveClientW9Details(result); 
    }
    
  }
/*----------------------------- Property validation --------------------------------*/

/*----------------------------- Save stratgegy --------------------------------*/
  saveClientW9Details(data: any){
   
    let url = apiUrl.mailing;
    this.commonApplicationService.post(url, data)
        .subscribe(
            response => {
              this.mailingInfo=response.data;
              for(var i=0; i<this.mailingInfo.length; i++ ){
                let mailing=this.mailingForm.get('mailing_info_data')['controls'][i].controls;
                mailing.id.setValue(this.mailingInfo[i].id);
              }
              this.alertService.success(response.message);  
              this.loading = false;
            },
            error => {
              this.loading = false;
              this.alertService.common(error); 
            }
        ); 
  }

  sendEmailToUser(){
    this.loading=true;
    let url = apiUrl.sendEmail+"/"+this.property_id;
    this.commonApplicationService.get(url).subscribe(response => {
      this.loading=false;
      this.propErr = true; 
      this.alertService.success(response.message);  
    },
    (err: any) => {
      this.propErrMsg = "Error occured, Please try again later!";
      this.propErr = true;
      this.loading=false;
      this.alertService.common(err); 
    })
}

}
