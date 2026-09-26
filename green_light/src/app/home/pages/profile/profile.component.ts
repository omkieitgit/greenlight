import { Component, OnInit } from '@angular/core';
import { CommonApplicationService,CommonActivityService ,AlertService,CommunicationService} from '../../../shared/_services';
import { apiUrl } from '../../../config/api-url';
import { FormControl, NgForm, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { formConstants } from '@config/forms-constants';

@Component({
  selector: 'app-profile',
  templateUrl: './profile.component.html',
  styleUrls: ['./profile.component.css']
})
export class ProfileComponent implements OnInit {

  profileForm:FormGroup;
  result:any;
  loading:boolean=true;
  invalidFields:any;
  loader:boolean=false;
  states:any;

  constructor(private commonActivityService :CommonActivityService,
    private commonApplicationService:CommonApplicationService,
    private alertService:AlertService,
    private formBuilder: FormBuilder,
    private communicationService:CommunicationService) { }

  ngOnInit() {
    this.states = formConstants.states;

    this.getUserInfo();
  }

  getUserInfo(){
    let url = apiUrl.profile;
    this.commonApplicationService.get(url)
      .subscribe(
          response => {
            this.result=response.row
            this.intilize();
            this.loading=false;
          },
          error => {
          }
      ); 
  }
  intilize(){
    this.profileForm = this.formBuilder.group({
      address: [this.result.address, ''],
      city: [this.result.city, ''],
      state: [this.result.state, ''],
      first_name: [this.result.first_name, ''],
      last_name: [this.result.last_name, ''],
      email:[this.result.email, ''],
      mobile:[this.result.mobile, ''],
      username:[this.result.username]
            
    });
  }

  /*----------------------------- Form validation ---------------------------------------*/
  validateForm(data: any) {     
    let result = this.commonActivityService.getFullFormData(data);
    this.invalidFields = this.commonActivityService.findInvalidControls(data);
    if(this.profileForm.invalid) { 
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
    let url = apiUrl.profile;
    this.commonApplicationService.put(url, data)
        .subscribe(
            data => {
              if(data['status']=='success'){
                this.communicationService.user_info(data.data.user);
              }

              this.alertService.common(data);  
              this.loader = false;
            },
            error => {
                this.alertService.common(error);  
            }
        ); 
  }
}
