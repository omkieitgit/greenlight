import { Component, OnInit, Input, Inject } from '@angular/core';
import { FormControl,FormArray, NgForm, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { CommonApplicationService,CommonActivityService,AlertService } from '../../../../shared/_services';
import { apiUrl } from '../../../../config/api-url';
import { Router,ActivatedRoute } from '@angular/router';
import {IMyDpOptions, IMyDateModel} from 'mydatepicker';
import { MatDialog, MatDialogRef, MAT_DIALOG_DATA } from '@angular/material/dialog';

@Component({
  selector: 'app-client-w9',
  templateUrl: './client-w9.component.html',
  styleUrls: ['./client-w9.component.css']
})
export class ClientW9Component implements OnInit {

  clientW9Form: FormGroup;
  loading = false;
  loadingMessage: any;
  propErr: boolean = false;
  propErrMsg: any = "";
  invalidFields: any;
  submitted = false;
  property_id:number;
  user_id:number;
  w9Result: any;
  openPanel:boolean = false;
  user:any;
  federal:any=[{key:'individual',value:1},{key:'corporation',value:2},{key:'trust_estate',value:3},
  {key:'liability',value:4},{key:'c_corporation',value:5},{key:'partinership',value:6},{key:'other',value:7}];
  ssNumber:any;
  dialogHeight:boolean=true;
  showSSN:boolean = false;

  constructor(private formBuilder: FormBuilder,
              private commonActivityService:CommonActivityService,
              private commonApplicationService:CommonApplicationService,
              private alertService:AlertService,
              private router: Router,
              private route: ActivatedRoute,
              private dialog:MatDialog,
              private dialogRef:MatDialogRef<ClientW9Component>,
              @Inject(MAT_DIALOG_DATA) public data) {

                this.property_id=this.data.property_id;
                this.user_id=this.data.user.id;
                this.user=this.data.user;
                // Load info details
                if(this.property_id !== undefined ){
                  this.clientW9Detail();
                }

               }
  
  public myDatePickerOptions: IMyDpOptions = { dateFormat: 'yyyy-mm-dd',firstDayOfWeek : 'su'};

  onDateChanged(event: IMyDateModel) {
      return event.formatted;
  }
  ngOnInit() {

   
    
  }
  panelExpand(flag){
    if(!this.openPanel){
      this.clientW9Detail();
      this.openPanel = true;
    }
  }
  initialize(){
    
   // =JSON.stringify(this.w9Result.federal_tax);
   let federal='';
   if(this.w9Result?.federal_tax){
      federal=JSON.parse(this.w9Result.federal_tax);
   }
    this.clientW9Form = this.formBuilder.group({
      name: [this.w9Result.name,[Validators.required]],
      business_name: [this.w9Result.business_name,[Validators.required]],
      address: [this.w9Result.address,[Validators.required]],
      city: [this.w9Result.city,[Validators.required]],
      account_number:[this.w9Result.account_number],
      requesters_name_address:[this.w9Result.requesters_name_address],
      exempt_payee_code:[this.w9Result.exempt_payee_code],
      exemption_FATCA_reporting_code:[this.w9Result.exemption_FATCA_reporting_code],
      taxpayer_identification_number:[this.w9Result.taxpayer_identification_number],
      social_security_number: this.formBuilder.array([]),
      social_security_number_2: this.formBuilder.array([]),
      social_security_number_3: this.formBuilder.array([]),
      employer_identification_number: this.formBuilder.array([]),
      employer_identification_number_2: this.formBuilder.array([]),
      other_instructions:[this.w9Result.other_instructions],
      tax_classification:[this.w9Result.tax_classification],
      signature:[this.w9Result.signature],
      user_id:[this.user_id],
      w9_date:[(this.w9Result.w9_date != null)? {jsdate: new Date(this.w9Result.w9_date)}: null],
      federal_tax_data:this.formBuilder.group({
        individual:[federal['individual']],
        corporation:[federal['corporation']],
        trust_estate:[federal['trust_estate']],
        liability:[federal['liability']],
        c_corporation:[federal['c_corporation']],
        partinership:[federal['partinership']],
        other:[federal['other']]
      }),
    });

    if(!this.w9Result || this.w9Result.length==0){
      this.showSSN=true;
      this.ssn1(3,this.w9Result?.social_security_number);
      this.ssn2(2,this.w9Result?.social_security_number_2);
      this.ssn3(4,this.w9Result?.social_security_number_3);
    }
    this.einNumber(2,this.w9Result?.employer_identification_number);
    this.einNumber2(7,this.w9Result?.employer_identification_number_2);
  }

     
  newEmployee(): FormGroup {
    return this.formBuilder.group({
      social_security_number: '',
    })
  }
 
 
  ssn1(count,info) {
    
    for(let i=0; i<count; i++){
      const control = <FormArray>this.clientW9Form.controls.social_security_number;
      control.push(this.formBuilder.group({ social_security_number: info?info.charAt(i):''}));
    }
  }
  ssn2(count,info) {
    for(let i=0; i<count; i++){
      const control = <FormArray>this.clientW9Form.controls.social_security_number_2;
      control.push(this.formBuilder.group({ social_security_number_2:info?info.charAt(i):''}));
    }
  }
  ssn3(count,info) {
    for(let i=0; i<count; i++){
      const control = <FormArray>this.clientW9Form.controls.social_security_number_3;
      control.push(this.formBuilder.group({ social_security_number_3: info?info.charAt(i):''}));
    }
  }

  einNumber(count,info) {
    for(let i=0; i<count; i++){
      const control = <FormArray>this.clientW9Form.controls.employer_identification_number;
      control.push(this.formBuilder.group({ employer_identification_number: info?info.charAt(i):''}));
    }
  }
  einNumber2(count,info) {
    for(let i=0; i<count; i++){
      const control = <FormArray>this.clientW9Form.controls.employer_identification_number_2;
      control.push(this.formBuilder.group({ employer_identification_number_2:info?info.charAt(i):''}));
    }
  }

  clientW9Detail(){
    //this.loading=true;
    let url = apiUrl.client_w9+"/"+this.user_id;
    //let url = apiUrl.tradesmanW9+"/"+this.property_id;

    this.commonApplicationService.get(url).subscribe(response => {
      if(response !== undefined){
        this.w9Result = response.data;
      }
      this.initialize();
      this.dialogHeight=false;
      this.propErr=true;
      //this.loading=false;
    },
    (err: any) => {
      this.alertService.common(err);
      this.loading = true;
      //this.loading=false; 
      this.dialogHeight=false;
    })
    }

   /*----------------------------- Property validation --------------------------------*/
   validateForm(data: any) {     
    let result = this.commonActivityService.getFullFormDataWithDateFormatted(data);
    this.invalidFields = this.commonActivityService.findInvalidControls(data);
    console.log("Form Invalid Filleds = ",this.invalidFields);
    this.submitted = true;
    console.log('niform');
    if(this.clientW9Form.invalid) { 
      console.log('Form is invalid, Fill all fields.');
      //this.propertyForm.get('prop_address').markAsTouched();      
      return;
    }    
    // FORM SUBMITTED
    console.log('form submitted');
    let social_security_number='';
    if(result['social_security_number']){
      result['social_security_number'].forEach(element => {
        social_security_number+=element['social_security_number'];
      });
      result['social_security_number']=social_security_number;
    }
   

    let social_security_number_2='';
    result['social_security_number_2'].forEach(element => {
      social_security_number_2+=element['social_security_number_2'];
    });
    result['social_security_number_2']=social_security_number_2;

    let social_security_number_3='';
    result['social_security_number_3'].forEach(element => {
      social_security_number_3+=element['social_security_number_3'];
    });
    result['social_security_number_3']=social_security_number_3;


    let employer_identification_number='';
    result['employer_identification_number'].forEach(element => {
      employer_identification_number+=element['employer_identification_number'];
    });
    result['employer_identification_number']=employer_identification_number;

    let employer_identification_number_2='';
    result['employer_identification_number_2'].forEach(element => {
      employer_identification_number_2+=element['employer_identification_number_2'];
    });
    result['employer_identification_number_2']=employer_identification_number_2;

    this.federal.forEach(element => {
        if(result['federal_tax_data'][element.key]){
          result['federal_tax_data'][element.key]=element.value;
        }
    });
      
    this.saveClientW9Details(result); 
    
  }
/*----------------------------- Property validation --------------------------------*/

/*----------------------------- Save stratgegy --------------------------------*/
  saveClientW9Details(data: any){
    this.loading = true;
    let url = apiUrl.client_w9+'/'+this.property_id;
    this.commonApplicationService.put(url, data)
        .subscribe(
            response => {
              this.user=response.data;
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

 get f() { return this.clientW9Form.controls; }


 setFederalVal($event){
   let formControlName=$event.target.getAttribute('formControlName');
   let checkbox=this.clientW9Form.controls.federal_tax_data['controls'][formControlName];
   if($event.target.checked){
    checkbox.setValue($event.target.value);
   }else{
    checkbox.setValue('');
   }
  }
  closeDialog(): void {
    this.dialogRef.close(this.user);
  }

  viewSsn(){
    this.loading=true;
    let url = apiUrl.viewSsn+"/"+this.user_id;
    this.commonApplicationService.get(url).subscribe(response => {
      if(response !== undefined){
        this.ssNumber = response.data;
        this.ssn1(3,this.ssNumber?.social_security_number);
        this.ssn2(2,this.ssNumber?.social_security_number_2);
        this.ssn3(4,this.ssNumber?.social_security_number_3);
        this.showSSN=true;
        // let ssn1=this.clientW9Form.controls.social_security_number['controls'];
        // if(this.ssNumber?.social_security_number){
        //   for(let i=0; i<ssn1.length; i++){
        //     ssn1[i].controls.social_security_number.setValue(this.ssNumber.social_security_number.charAt(i));
        //   }
        // }

        // if(this.ssNumber?.social_security_number_2){
        //   let ssn2=this.clientW9Form.controls.social_security_number_2['controls'];
        //   for(let i=0; i<ssn2.length; i++){
        //     ssn2[i].controls.social_security_number_2.setValue(this.ssNumber.social_security_number_2.charAt(i));
        //   }
        // }

        // if(this.ssNumber?.social_security_number_3){
        //   let ssn3=this.clientW9Form.controls.social_security_number_3['controls'];
        //   for(let i=0; i<ssn3.length; i++){
        //     ssn3[i].controls.social_security_number_3.setValue(this.ssNumber.social_security_number_3.charAt(i));
        //   }
        // }
      }
      this.loading=false;
    },
    (err: any) => {
      this.alertService.common(err);
      this.loading = true;
      this.loading=false;
    })
  }
}
