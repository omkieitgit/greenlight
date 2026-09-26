import { Component, OnInit, Input } from '@angular/core';
import { FormControl, NgForm, FormBuilder, FormGroup, Validators, FormArray} from '@angular/forms';
import { MortgateOtherPropTaxModel } from '../mortgage.model';
import { Router,ActivatedRoute } from '@angular/router';
import {IMyDpOptions, IMyDateModel} from 'mydatepicker';

import { CommonApplicationService,AlertService,CommonActivityService } from '@shared-service/_services';
import { apiUrl } from '@config/api-url';
import { StorageService } from '@shared-service/_services/storage.service';

@Component({
  selector: 'tax-property',
  templateUrl: './tax-property.component.html'
})
export class TaxPropertyComponent implements OnInit {
  mortgageTaxPropertyForm: FormGroup;
  private mortgateData : MortgateOtherPropTaxModel;
  result: any;  
  @Input() taxData: any;
  collapse:boolean=false;

  invalidFields: any; 
  submitted = false;
  loading = false;
  loadingMessage: any;
  propErr: boolean = false;
  propErrMsg: any = "";
  property_id: string;
  parsedData: any = {};

  constructor(private formBuilder: FormBuilder,          
          private commonApplicationService: CommonApplicationService,
          private commonActivityService: CommonActivityService,
          private storageService:StorageService,private alertService: AlertService,
          private router: Router,
          private route: ActivatedRoute,) { }

  ngOnInit() {
    this.property_id = this.route.snapshot.paramMap.get('property_id');
    // Load info details
    if(this.property_id !== undefined){
      this.initialize();
      //this.propErr = true;
      //this.getSchoolDetails();    
    }
  }

  public myDatePickerOptions: IMyDpOptions = { dateFormat: 'yyyy-mm-dd',firstDayOfWeek : 'su'};

    onDateChanged(event: IMyDateModel) {
          return event.formatted;
    }
 
  initialize(){
     this.mortgageTaxPropertyForm = this.formBuilder.group({
      treasure_url: [this.taxData.treasure_url, Validators.required],
      tax_bill_url: [this.taxData.tax_bill_url],
      total_property_taxes_owed: [this.taxData.total_property_taxes_owed],
      owed_tax_data: this.formBuilder.array([this.owedTaxFields(), this.owedTaxFields(), this.owedTaxFields(), this.owedTaxFields(), this.owedTaxFields()]),  

    });
  }

/*----------------------------- Form validation ---------------------------------------*/
  validateForm(data: any) {     
    let result = this.commonActivityService.getFullFormDataWithDateFormatted(data);
    this.invalidFields = this.commonActivityService.findInvalidControls(data);
    console.log("Form Invalid Filleds = ",this.invalidFields);
    this.submitted = true;
    console.log('niform');
    if(this.mortgageTaxPropertyForm.invalid) { 
      console.log('Form is invalid, Fill all fields.');
      //this.propertyForm.get('prop_address').markAsTouched();      
      return;
    }    
    // FORM SUBMITTED
    console.log('form submitted');
    this.saveMortgageTaxForm(result);  
  }

/*----------------------------- Form validation ---------------------------------------*/

/*----------------------------- Save Details ---------------------------------------*/
  saveMortgageTaxForm(data: any){
    this.loading = true;
    data.house_id = this.property_id;
    //console.log("savingdata>"+this.propertyForm);
    console.log("savingdata>"+data);
    let url = apiUrl.mortgate_tax+'/'+this.property_id;
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

 get f() { return this.mortgageTaxPropertyForm.controls; }
 
 owedTaxFields() : FormGroup
 {    
   return this.formBuilder.group({
     property_taxes_owed: ['', Validators.required],
     property_taxes_owed_year:['', Validators.required],
     taxes_assessed: ['', Validators.required],
     taxes_year: ['', Validators.required],
     house_id:[this.property_id],
     id:[],
     save:['Save'],
     remove:['Remove'],
   });
 }

 addTaxOwedField() : void
 {
  const control = <FormArray>this.mortgageTaxPropertyForm.controls.owed_tax_data;
  control.push(this.owedTaxFields());
 }
 
 removeTaxOwedField(i : number, id: number) : void
 {
     if(confirm("Are you sure want to delete record ?")){
          const control = <FormArray>this.mortgageTaxPropertyForm.controls.owed_tax_data;
          control.removeAt(i);
     }else{
       return;
     }

     let url = apiUrl.property_assessment+'/'+id;
     this.commonApplicationService.delete(url).subscribe(response => {
       if(response !== undefined){             
            this.alertService.success(response.message); 
       }
     },
       (err: any) => {
        this.alertService.common(err); 
       })
 }
 calculateTotal(){
    var totalOwed:number=0;
    const control = this.mortgageTaxPropertyForm.get('owed_tax_data')['controls'];
    for(var i=0; i<control.length; i++){
      var owedValue=control[i].controls.property_taxes_owed.value;
      if(owedValue>0)
        totalOwed=totalOwed+parseFloat(owedValue);
    }
    return totalOwed;
  }
}
