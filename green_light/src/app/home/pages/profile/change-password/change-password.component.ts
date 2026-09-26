import { Component, OnInit } from '@angular/core';
import { AbstractControl, FormBuilder, FormGroup, ValidationErrors, Validators } from '@angular/forms';
import { AlertService, CommonApplicationService,CommonActivityService } from '../../../../shared/_services';
import { apiUrl } from '../../../../config/api-url';

@Component({
  selector: 'app-change-password',
  templateUrl: './change-password.component.html',
  styleUrls: ['./change-password.component.css']
})


export class ChangePasswordComponent implements OnInit {

  changePasswordForm: FormGroup;
  submitted = false;
  token:string;
  error_message:boolean=false;
  message:string;
  success_message:boolean=false;
  loading = false;
  invalidFields:any;
  loader:boolean=false;

  constructor(private commonActivityService :CommonActivityService,
    private commonApplicationService:CommonApplicationService,
    private alertService:AlertService,
    private formBuilder:FormBuilder) { }

  ngOnInit() {
    this.intilize();
  }

  intilize(){
    this.changePasswordForm = this.formBuilder.group({
      password: ['',[Validators.required]],
      new_password: ['', [Validators.required,Validators.minLength(8),Validators.pattern(/^(?=\D*\d)(?=[^a-z]*[a-z])(?=[^A-Z]*[A-Z]).{8,30}$/)]],
      conf_password: ['', [Validators.required]],            
    },{validator: this.checkPasswords });
  }


  checkPasswords(group: FormGroup) { // here we have the 'passwords' group
    let pass = group.controls.new_password.value;
    let confirmPass = group.controls.conf_password.value;
    console.log(pass === confirmPass);
    return pass === confirmPass ? null : { notSame: true }     
  }
    /*----------------------------- Form validation ---------------------------------------*/
    validateForm(data: any) { 
      this.submitted=true; 
      console.log(data);
      console.log(this.changePasswordForm.hasError('notSame'));  
      let result = this.commonActivityService.getFullFormData(data);
      this.invalidFields = this.commonActivityService.findInvalidControls(data);
      if(this.changePasswordForm.invalid) { 
        console.log('Form is invalid, Fill all fields.');
        //this.propertyForm.get('prop_address').markAsTouched();      
        return;
      }    
      // FORM SUBMITTED
      console.log('form submitted');
      this.saveSchoolDetails(result);  
    }
  
  /*----------------------------- Form validation ---------------------------------------*/
  
  /*----------------------------- Save Details ---------------------------------------*/
    saveSchoolDetails(data: any){
      this.loader=true;
      //console.log("savingdata>"+this.propertyForm);
      let url = apiUrl.change_password;
      this.commonApplicationService.put(url, data)
          .subscribe(
              data => {
                this.alertService.common(data);  
                this.loader = false;
              },
              error => {
                  this.alertService.common(error);  
              }
          ); 
    }

    get f() { return this.changePasswordForm.controls; }

    
}
