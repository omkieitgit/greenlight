import { Component, OnInit, Input } from '@angular/core';
import { FormControl, NgForm, FormBuilder, FormGroup, Validators, FormArray } from '@angular/forms';
import { Router,ActivatedRoute } from '@angular/router';
import { CommonApplicationService } from '../../../../../shared/_services';
import { CommonActivityService } from '../../../../../shared/_services';
import { AlertService } from '../../../../../shared/_services';
import { apiUrl,scraperApiUrl } from '../../../../../config/api-url';
import {IMyDpOptions, IMyDateModel} from 'mydatepicker';
import { StorageService } from '../../../../../shared/_services/storage.service';
import {show_am_county,county_email} from '../../../../../config/forms-constants';
@Component({
  selector: 'am-email',
  templateUrl: './am-email.component.html'
})
export class AmEmailComponent implements OnInit {
  @Input() property_id;
  amEmailForm: FormGroup;
  result: any;
  openPanel:boolean=false;
  am_email_fields=[1];
  loading = false;
  loadingMessage: any;
  propErr: boolean = false;
  propErrMsg: any = "";
  invalidFields: any;
  submitted = false;
  rowCount:number;
  amEmailData: any;
  

  constructor(private formBuilder: FormBuilder,
    private router: Router,
    private route: ActivatedRoute,
    private commonApplicationService: CommonApplicationService,
    private commonActivityService: CommonActivityService,
    private alertService: AlertService,
    private storageService:StorageService,) { }

    public myDatePickerOptions: IMyDpOptions = { dateFormat: 'yyyy-mm-dd',firstDayOfWeek : 'su'};

    onDateChanged(event: IMyDateModel) {
        return event.formatted;
    }

    ngOnInit() {
      //this.property_id = this.route.snapshot.paramMap.get('property_id');
      // Load info details
      if(this.property_id !== undefined){
        console.log(this.storageService.getHard('property_info'));
      }
    }
    panelExpand(flag){
      if(!this.openPanel){
        this.propErr = true;
        this.getHomeBuyerDetails();
        this.initialize();  
        this.openPanel = true;
      }
    }


    getHomeBuyerDetails(){

      let url = apiUrl.emails_am+"/"+this.property_id+"/all";
      this.commonApplicationService.get(url).subscribe(response => {
        if(response.data !== undefined){
            this.amEmailData  =  response.data;
            this.populateDataInForm();
        }
      },
        (err: any) => {
          this.propErrMsg = "Error occured, Please try again later!";
          this.propErr = true;
        })
    }

  initialize(){
     
     this.amEmailForm = this.formBuilder.group({
      am_email_fields: this.formBuilder.array([this.amEmailFields()]),     
      house_id:[this.property_id],
      add_more:['Add More']
    });
    
    setTimeout(()=>{    
      //this.commonActivityService.isDisabled("STRATEGY", this.amEmailForm);
      // this.commonActivityService.isDisabled("STRATEGY", this.emailTimeLeft);
      // this.commonActivityService.isDisabled("STRATEGY", this.companyTeamForm);
      // this.commonActivityService.isDisabled("STRATEGY", this.funderLendForm);
   }, 2000);
    
  }

  amEmailFields() : FormGroup
  {    
    return this.formBuilder.group({
      email: ['',[Validators.required,Validators.email]],
      house_id:[this.property_id],
      user_id: [this.storageService.get("user_info")['id']],
      emails_am_id:[''],
     });
  }


// ************************ AM EMAIL ******************************//
addAmEmail() : void
  {
   const control = <FormArray>this.amEmailForm.controls.am_email_fields;
   control.push(this.amEmailFields());
  }
  
  removeAmEmail(i : number, id : number) : void
  {
      if(confirm("Are you sure want to delete record ?")){
        const control = <FormArray>this.amEmailForm.controls.am_email_fields;
        control.removeAt(i);
        if(id){
          let url = apiUrl.emails_am+'/'+id;
          this.commonApplicationService.delete(url).subscribe(response => {
            if(response !== undefined){             
                this.alertService.success(response.message); 
            }
          },
          (err: any) => {
            this.alertService.common(err);
          })
        }
      }
  }
/*----------------------------- Property validation --------------------------------*/
  validateAmEmail(data: any,i: number) {     
    this.rowCount=i;
    this.submitted = true;
    this.result = this.commonActivityService.getFullFormDataWithDateFormatted(data);
    this.invalidFields = this.commonActivityService.findInvalidControls(data);
    if(this.amEmailForm.get('am_email_fields')['controls'][i].invalid) { 
      return;
    }    
    // FORM SUBMITTED
    console.log('form submitted');
    if(this.result.emails_am_id !== undefined && this.result.emails_am_id != null && this.result.emails_am_id > 0){
        this.updateAmEmailDetails(this.result);  
      }else{
        this.saveAmEmailDetails(this.result);  
      }
  }
/*----------------------------- Property validation --------------------------------*/

/*----------------------------- Save stratgegy --------------------------------*/
  saveAmEmailDetails(data: any){
    this.loading = true;
    let url = apiUrl.emails_am;
    this.commonApplicationService.post(url, data)
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

/*----------------------------- Save stratgegy --------------------------------*/
 
 /*----------------------------- Save stratgegy --------------------------------*/
  updateAmEmailDetails(data: any){
    this.loading = true;
    //console.log("savingdata>"+this.propertyForm);
    console.log("savingdata>"+data);
    let url = apiUrl.emails_am+"/"+data.emails_am_id;
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
/*----------------------------- Save stratgegy --------------------------------*/

// ----------------------------------- Fill Form Data ---------------------------------------// 
  populateDataInForm(){

    if(this.amEmailData.length){
      for(var i=0; i<this.amEmailData.length; i++ ){
        this.addAmEmail();
        this.fillPriceForm(i);
      }
      this.fillInfoCountyBy(i);
    }else{
      this.fillInfoCountyBy(0);
    }     
  }

  fillInfoCountyBy(index){

    let propertyInfo=this.storageService.getHard('property_info');
    const amCounty =  show_am_county[propertyInfo['state']].find(x => x== propertyInfo['county']);
    let amEmail:any='';
    if(amCounty){
      amEmail=county_email[propertyInfo['state']][0];
    }else{
      amEmail=county_email['DEFAULT'][0][propertyInfo['state']];
    }

    let existEmail=this.amEmailData.find(data=>{console.log(data); return data.email.toLowerCase()==amEmail});
    if(!existEmail){
      this.amEmailForm.get('am_email_fields')['controls'][index].controls.email.setValue(amEmail);
    }
  }

  fillPriceForm(index:number){
      //this.amEmailForm.get('am_email_fields')['controls'][index].controls.price_date.setValue(this.amEmailData[index].price_date);
      this.amEmailForm.get('am_email_fields')['controls'][index].controls.emails_am_id.setValue(this.amEmailData[index].emails_am_id);
      this.amEmailForm.get('am_email_fields')['controls'][index].controls.email.setValue(this.amEmailData[index].email);
  }


 // ----------------------------------- Fill Form Data ---------------------------------------// 

// ************************ AM EMAIL ******************************//

}