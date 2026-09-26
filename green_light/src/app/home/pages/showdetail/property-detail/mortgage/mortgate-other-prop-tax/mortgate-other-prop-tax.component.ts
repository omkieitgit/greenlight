import { Component, OnInit, Input,AfterViewInit } from '@angular/core';
import { FormControl, NgForm, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { MortgateOtherPropTaxModel } from '../mortgage.model';
import { Router,ActivatedRoute } from '@angular/router';
import {MatDialog, MatDialogRef, MAT_DIALOG_DATA} from '@angular/material/dialog';
import { SaleInfoComponent } from '../sale-info/sale-info.component';

import { CommonApplicationService,AlertService,CommonActivityService,CommunicationService } from '@shared-service/_services';
import { apiUrl } from '@config/api-url';
import { StorageService } from '@shared-service/_services/storage.service';


@Component({
  selector: 'mortgate-other-prop-tax',
  templateUrl: './mortgate-other-prop-tax.component.html'
})
export class MortgateOtherPropTaxComponent implements OnInit{
  mortgagePropTaxForm: FormGroup;
  private mortgateData : MortgateOtherPropTaxModel;
  result: any;  
  @Input() mortgageDataOther: any;
  @Input() sale_info: any;
  
  invalidFields: any; 
  submitted = false;
  loading = false;
  loadingMessage: any;
  propErr: boolean = false;
  propErrMsg: any = "";
  property_id: string;
  hideLien:boolean=false;
  county_rod_url:string='';//this.storageService.get('rod_url');


  constructor(private formBuilder: FormBuilder,          
          private commonApplicationService: CommonApplicationService,
          private commonActivityService: CommonActivityService,
          private storageService:StorageService,
          private alertService: AlertService,
          private router: Router,
          private communicationService:CommunicationService,
          private route: ActivatedRoute,
          private dialog:MatDialog) { }

  ngOnInit() {
    this.property_id = this.route.snapshot.paramMap.get('property_id');
    // Load info details
    if(this.property_id !== undefined){
      this.initialize();
      //this.propErr = true;
      //this.getSchoolDetails();    
    }
    
    this.communicationService.getRodUrl().subscribe(response=>{
        if(response){
          this.mortgagePropTaxForm.get('county_rod_ur').setValue(response);
        }
    });
  }

  ngAfterViewInit(){
    
    if(this.mortgageDataOther.no_active_mortgage_lien){
      this.communicationService.sendData(true);
    }
  }

 
  initialize(){
    if(this.mortgageDataOther.county_rod_ur && this.mortgageDataOther.county_rod_ur!== undefined){
      this.county_rod_url=this.mortgageDataOther.county_rod_ur;
    }
     this.mortgagePropTaxForm = this.formBuilder.group({
      county_rod_ur: [this.county_rod_url, Validators.required],
      manual_search: [this.mortgageDataOther.manual_search==1?true:false],
      no_active_mortgage_lien: [this.mortgageDataOther.no_active_mortgage_lien==1?true:false],
    });
    // this.commonActivityService.isDisabled("MORTGAGE", this.mortgagePropTaxForm);
  }

/*----------------------------- Form validation ---------------------------------------*/
  validateForm(data: any) {     
   
    let result = this.commonActivityService.getFullFormData(data);
    this.invalidFields = this.commonActivityService.findInvalidControls(data);
    console.log("Form Invalid Filleds = ",this.invalidFields);
    this.submitted = true;
    console.log('niform');
    if(this.mortgagePropTaxForm.invalid) { 
      console.log('Form is invalid, Fill all fields.');
      //this.propertyForm.get('prop_address').markAsTouched();      
      return;
    }    
    // FORM SUBMITTED
    console.log('form submitted');
    result['manual_search']=result['manual_search']?result['manual_search']:0;
    result['no_active_mortgage_lien']=result['no_active_mortgage_lien']?result['no_active_mortgage_lien']:0;
    this.saveMortgagePropTaxForm(result);  
  }

/*----------------------------- Form validation ---------------------------------------*/

/*----------------------------- Save Details ---------------------------------------*/
  saveMortgagePropTaxForm(data: any){
    this.loading = true;
    data.house_id = this.property_id;
    //console.log("savingdata>"+this.propertyForm);
    let url = apiUrl.mortgage+'/'+this.property_id;
    this.commonApplicationService.put(url, data)
        .subscribe(
            data => {
              this.alertService.success(data.message);  
              this.loading = false;
            },
            error => {
              this.loading = false;
              this.alertService.common(error);  
            }
        ); 
  }

/*----------------------------- Save Details ---------------------------------------*/

 get f() { return this.mortgagePropTaxForm.controls; }
 
 noActiveLien(e){
    if(e.target.checked){
      this.communicationService.setNoActiveLien(true);
    }
    else{
      this.communicationService.setNoActiveLien(false);
    }   
  }

  manualSearch(e){
    if(e.target.checked){
      this.communicationService.setManualSearch(true);
    }
    else{
      this.communicationService.setManualSearch(false);
    }   
  }

  viewSaleInfo(){
    this.dialog.open(SaleInfoComponent,{ width: '800px',data:this.sale_info,disableClose:true});
  }

}
