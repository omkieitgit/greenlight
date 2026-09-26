import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { apiUrl } from '@config/api-url';
import { formConstants } from '@config/forms-constants';
import { AlertService, CommonActivityService, CommonApplicationService } from '@shared-service/_services';
import { StorageService } from '@shared-service/_services/storage.service';
import jsonData from '@config/state_county';

@Component({
  selector: 'app-subto-invite-setting',
  templateUrl: './subto-invite-setting.component.html',
  styleUrls: ['./subto-invite-setting.component.css']
})
export class SubtoInviteSettingComponent implements OnInit {

  countries: any [];
  states: any;
  sale_type_option: string [];
  inviteSettingFormOne: FormGroup;
  inviteSettingFormSecond: FormGroup;
  

  invalidFields: any; 
  
  loading = false;
  inviteSettingResult:any;
  selectable = true;
  removable = true;

  submitted = false;  
  submitted1 = false;
  wholesale_buyer_list:any;
  user_info:any;
  selected_user:any;


  constructor(private formBuilder: FormBuilder,
    private storageService:StorageService,
    private commonApplicationService: CommonApplicationService,
    private commonActivityService: CommonActivityService,
    private alertService: AlertService,
    private route: ActivatedRoute,
    private router:Router
    ) { }

  ngOnInit() {

    this.user_info= this.storageService.get('user_info');
    let inviteAccess=['admin','sub_to'];
		if(inviteAccess.indexOf(this.user_info.current_role) === -1){
      this.router.navigate(['home/dashboad']);
		}
    

    this.countries= formConstants.countries;
    this.states= formConstants.states;
    let property_config =  this.storageService.get("property_config");
    if(property_config!== null){
      this.sale_type_option = property_config.sale_type;
      this.sale_type_option[0]='All';
    }

    this.intilize();
    this.getInviteSetting();
    this.getWholeSalebuyer();
    
  }

  intilize(){

    this.inviteSettingFormSecond= this.formBuilder.group({
      sale_type:['0',[Validators.required]], 
      state: ['',[Validators.required]],
      county:['',[Validators.required]],
      request_type:['3',[Validators.required]],
      user_id:[]
    });
  }

  getCountyList(state){
    this.countries = jsonData[state];
    this.inviteSettingFormSecond.get('county').setValue('All');
  }


  validateForm(data: any) {     
    let result = this.commonActivityService.getFullFormDataWithDateFormatted(data);
    this.invalidFields = this.commonActivityService.findInvalidControls(data);
    
    if(result['request_type']==3 ) { 
      this.submitted1 = true;
      if(this.inviteSettingFormSecond.invalid)
        return;

    }  
    
    // this.inviteSettingFormOne.reset();
    // this.inviteSettingFormSecond.reset(); 
    // this.submitted = false; 
    // this.submitted1 = false;
    // FORM SUBMITTED
    this.saveInviteSetting(result);  
  }


  saveInviteSetting(data: any){
    this.loading = true;
    data.sale_type=[data.sale_type];
    data.state=[data.state];
    data.county=[data.county];
    let url = apiUrl.invite_setting;
    this.commonApplicationService.post(url, data)
        .subscribe(
            response => {
              if(this.user_info.current_role!='admin'){
                this.inviteSettingResult=response['data'];
              }
              this.inviteSettingFormSecond.get("county").setValue('');
              this.alertService.success(response.message);  
              this.loading = false;
            },
            error => {
              this.loading = false;
              this.alertService.common(error);  
            }
        ); 
  }

  get f() { return this.inviteSettingFormOne.controls; }
  get g() { return this.inviteSettingFormSecond.controls; }


 getInviteSetting(id=''){
    
    //this.loading = true;
    let url = apiUrl.invite_setting+'?request_type=3';
    if(id){
       url = apiUrl.invite_setting+'?request_type=3&user_id='+id;
    }
    this.commonApplicationService.get(url).subscribe(response =>{
        this.inviteSettingResult=response['data'];
    })

  }
  getResultList(id){
    if(id){
      this.selected_user=id;
      this.getInviteSetting(id);
    }
    
  }

  removeInviteSetting(id : number) : void
  {
      if(id){
        let url = apiUrl.invite_setting+'/'+id;
        this.commonApplicationService.delete(url).subscribe(response => {
        if(response !== undefined){             
          this.inviteSettingResult = this.inviteSettingResult.filter( invite=> invite.id !== id); 
          this.alertService.success(response.message); 
        }
        },
        (err: any) => {
          this.alertService.common(err); 
        })
      }
  }

  getWholeSalebuyer(){
    
    let url = apiUrl.user_list+'?role=sub_to';
    this.commonApplicationService.get(url).subscribe(response =>{
        this.wholesale_buyer_list=response;
    })
  }

}
