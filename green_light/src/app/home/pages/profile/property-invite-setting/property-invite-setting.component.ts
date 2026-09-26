import { Component, OnInit,ElementRef,ViewChild } from '@angular/core';
import { FormControl, NgForm, FormArray, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { formConstants } from '@config/forms-constants';
import jsonData from '@config/state_county';
import { StorageService } from '../../../../shared/_services/storage.service';
import {Observable} from 'rxjs';
import {map, startWith} from 'rxjs/operators';
import {COMMA, ENTER} from '@angular/cdk/keycodes';
import {MatAutocompleteSelectedEvent, MatAutocomplete} from '@angular/material/autocomplete';
import {MatChipInputEvent} from '@angular/material/chips';
import { AlertService,CommonApplicationService,CommonActivityService,CommunicationService} from '../../../../shared/_services';
//import { MortgateOtherPropTaxModel } from '../../mortgage.model';
import { apiUrl } from '../../../../config/api-url';
import { analyzeAndValidateNgModules } from '@angular/compiler';
import { Router,ActivatedRoute } from '@angular/router';


@Component({
  selector: 'app-property-invite-setting',
  templateUrl: './property-invite-setting.component.html',
  styleUrls: ['./property-invite-setting.component.css']
})
export class PropertyInviteSettingComponent implements OnInit {
  countries: any [];
  states: any;
  sale_type_option: string [];
  inviteSettingFormOne: FormGroup;
  inviteSettingFormSecond: FormGroup;
  
  ownerInfoForm: FormGroup;

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
    let inviteAccess=['admin','home_buyer','wholesale_buyer'];
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

    this.inviteSettingFormOne= this.formBuilder.group({
        sale_type:['',[Validators.required]], 
        state: ['',[Validators.required]],
        request_type:['1',[Validators.required]],
        user_id:[]
    });

    this.inviteSettingFormSecond= this.formBuilder.group({
      sale_type:['',[Validators.required]], 
      state: ['',[Validators.required]],
      county:['',[Validators.required]],
      request_type:['2',[Validators.required]],
      user_id:[]
    });
  }

  getCountyList(state){
    this.countries = jsonData[state];
  }


  validateForm(data: any) {     
    let result = this.commonActivityService.getFullFormDataWithDateFormatted(data);
    this.invalidFields = this.commonActivityService.findInvalidControls(data);
    
    if(result['request_type']==1 ) { 
      this.submitted = true;
      if(this.inviteSettingFormOne.invalid)
        return;
    }  
    if(result['request_type']==2 ) { 
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
    let url = apiUrl.invite_setting;
    if(id){
       url = apiUrl.invite_setting+'?user_id='+id;
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
    
    let url = apiUrl.wholesale_buyer;
    this.commonApplicationService.get(url).subscribe(response =>{
        this.wholesale_buyer_list=response['data'];
    })
  }

}

