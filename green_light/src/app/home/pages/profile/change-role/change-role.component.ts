import { Component, OnInit } from '@angular/core';
import {StorageService} from '../../../../shared/_services/storage.service';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { AlertService, CommonApplicationService,CommonActivityService,CommunicationService } from '../../../../shared/_services';
import { apiUrl } from '../../../../config/api-url';

@Component({
  selector: 'app-change-role',
  templateUrl: './change-role.component.html',
  styleUrls: ['./change-role.component.css']
})
export class ChangeRoleComponent implements OnInit {
  user:any;
  user_roles:any;
  changeRoleForm: FormGroup;
  submitted = false;
  invalidFields:any;
  loader:boolean=false;
  current_role:any;

  constructor(private commonActivityService :CommonActivityService,
      private commonApplicationService:CommonApplicationService,
      private alertService:AlertService,private storageService:StorageService,
      private formBuilder:FormBuilder,
      private communicationService:CommunicationService) { }

  ngOnInit() {
    this.user=this.storageService.get("user_info");
    this.user_roles=this.user.user_roles;
    console.log(this.user);

    for(var i=0; i<this.user_roles.length; i++){
      if(this.user_roles[i].role_key==this.user.current_role)
      {
        this.current_role=this.user_roles[i].id;
      }
    }
    console.log(this.current_role);

    this.changeRoleForm = this.formBuilder.group({
      role_id: [this.current_role,[Validators.required]]            
    });

  }

   /*----------------------------- Form validation ---------------------------------------*/
   validateForm(data: any) { 
    this.submitted=true; 
    console.log(data);
    console.log(this.changeRoleForm.hasError('notSame'));  
    let result = this.commonActivityService.getFullFormData(data);
    this.invalidFields = this.commonActivityService.findInvalidControls(data);
    if(this.changeRoleForm.invalid) { 
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
    let url = apiUrl.change_role;
    this.commonApplicationService.post(url, data)
        .subscribe(
            data => {
              this.storageService.set("user_info",data.data);
              this.alertService.success(data.message);  
              this.loader = false;
              window.location.reload();
              // this.router.navigateByUrl(this.router.url, {skipLocationChange: true}).then(() =>{
              //   this.router.navigate(['/home/dashboard']);
              // });
            },
            error => {
              this.alertService.common(error); 
            }
        ); 
  }


}
