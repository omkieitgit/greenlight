import { Component, OnInit, Input } from '@angular/core';
import { FormControl, NgForm, FormBuilder, FormGroup, Validators, FormArray } from '@angular/forms';
import { InfoModel } from '../info.model';
import { Router,ActivatedRoute } from '@angular/router';
import { MoreAssessmentComponent} from './more-assessment/more-assessment.component';
import {MatDialog, MatDialogRef, MAT_DIALOG_DATA} from '@angular/material/dialog';

import { CommonApplicationService,AlertService,CommonActivityService } from '@shared-service/_services';
import { apiUrl } from '@config/api-url';
import { StorageService } from '@shared-service/_services/storage.service';
import { Subscription } from 'rxjs';
import { debounceTime, distinctUntilChanged } from 'rxjs/operators';


@Component({
  selector: 'assessment',
  templateUrl: './assessment.component.html'
})
export class AssessmentComponent implements OnInit {
  //assessmentForm: FormGroup;
  assessmentForm:FormGroup;

  private infoData : InfoModel;
  result: any;  
  @Input() assessmentData: any;
  invalidFields: any; 
  submitted = false;
  loading = false;
  loadingMessage: any;
  propErr: boolean = false;
  propErrMsg: any = "";
  property_id: string;
  assessment_tax_data=[1]; 
  owed_tax_data=[5]; 
  view_more: boolean = false;
  validateRow:number;
  autoLoader:boolean=false;
  assessmentSub:Subscription;
  currentIndex:number;

  constructor(private formBuilder: FormBuilder,          
          private commonApplicationService: CommonApplicationService,
          private commonActivityService: CommonActivityService,
          private storageService:StorageService,private alertService: AlertService,
          private router: Router,
          private route: ActivatedRoute,
          private dialog:MatDialog) { }

  ngOnInit() {
    this.property_id = this.route.snapshot.paramMap.get('property_id');
    // Load info details
    if(this.property_id !== undefined){
      this.initialize();
      this.populateDataInFormPrice();
      //this.propErr = true;
      //this.getSchoolDetails();    
    }
  }


  initialize(){

     this.assessmentForm=this.formBuilder.group({
        owed_tax_data: this.formBuilder.array([this.owedTaxFields(), this.owedTaxFields(), this.owedTaxFields(), this.owedTaxFields(), this.owedTaxFields()]),  
        house_id:[this.property_id],
        total_owed_tax: [''],
     });

 
  }

  viewMoreData(){
    if(!this.view_more){
      this.view_more = true;
      this.populateDataInFormPriceViewMore();
    }
  }
 
   owedTaxFields() : FormGroup
  {    
    return this.formBuilder.group({
      property_taxes_owed: ['', Validators.required],
      property_taxes_owed_year:['', Validators.required],
      taxes_assessed: ['', Validators.required],
      taxes_year: ['', Validators.required],
      house_id:[this.property_id],
      id:[],
    });
  }

  addTaxOwedField() : void
  {
   const control = <FormArray>this.assessmentForm.get('owed_tax_data')['controls'];
   control.push(this.owedTaxFields());
  }
  
 

// ----------------------------------- Fill Form Data ---------------------------------------// 
  populateDataInFormPrice(){
    if(this.assessmentData.length){
        if(this.assessmentData.length == 1){
            // If data is for fist row then fill
             this.fillPriceForm(0);
        }else{
            //If data is for fist row then fill
            //this.fillPriceForm(0);

            for(var i=0; i<this.assessmentData.length; i++ ){
                if(i <5 ){ 
                      this.fillPriceForm(i);
                  } 
            }
        }
    }     
  }

  populateDataInFormPriceViewMore(){
    if(this.assessmentData.length){
        if(this.assessmentData.length == 1){
             // If data is for fist row then fill
             this.fillPriceForm(0);
        }else{
            if(this.assessmentData.length>5){
              for(var i=5; i<this.assessmentData.length; i++ ){
                 this.addTaxOwedField(); this.fillPriceForm(i); 
              }
            }
        }
    }     
  }

  fillPriceForm(index:number){
    let assessmentInfo=this.assessmentForm.get('owed_tax_data')['controls'];
    assessmentInfo[index].controls.id.setValue(this.assessmentData[index].id);
    assessmentInfo[index].controls.property_taxes_owed.setValue(this.assessmentData[index].property_taxes_owed);
    assessmentInfo[index].controls.taxes_assessed.setValue(this.assessmentData[index].taxes_assessed);
    assessmentInfo[index].controls.taxes_year.setValue(this.assessmentData[index].taxes_year);
    assessmentInfo[index].controls.property_taxes_owed_year.setValue(this.assessmentData[index].property_taxes_owed_year);
}


 // ----------------------------------- Fill Form Data ---------------------------------------//


// ----------------------------------- Form Validation Owner ---------------------------------------//
validateTaxOwedForm(data: any,i: number) {
  this.validateRow=i;
  data.controls.house_id.setValue(this.property_id);
  let result = this.commonActivityService.getFullFormDataWithDateFormatted(data);
  this.invalidFields = this.commonActivityService.findInvalidControls(data);

  this.submitted = true;
  if(this.assessmentForm.invalid) { 
    return;
  }   
  this.loading=true;
  for(var i=0; i<result['owed_tax_data'].length; i++){
    if(result['owed_tax_data'][i].id !== undefined && result['owed_tax_data'][i].id != null && result['owed_tax_data'][i].id > 0){
      this.updateTaxOwedAssessmentDetails(result['owed_tax_data'][i],i);
    }else{
      this.createTaxOwedAssessmentDetails(result['owed_tax_data'][i],i);
    }
  }     
}

// ----------------------------------- Form Validation Owner---------------------------------------//  


 // ----------------------------------- Update Owner ---------------------------------------//  
 updateTaxOwedAssessmentDetails(data: any,index: number){

  let url = apiUrl.property_assessment+'/'+data.id;
  console.log(url);
  this.commonApplicationService.put(url, data)
    .subscribe(
        data => {
          //this.modifyBtn = false;
          this.loading=false;
          this.alertService.success(data.message);                
        },
        error => {
          this.loading=false;
          this.alertService.common(error); 
        }
    ); 
  }
  // ----------------------------------- Update Owner ---------------------------------------//  

  // ----------------------------------- Create Borrower ---------------------------------------//  
  createTaxOwedAssessmentDetails(data: any,index: number){
   
    let url = apiUrl.property_assessment;
    this.commonApplicationService.post(url, data)
        .subscribe(
            data => {
              this.loading=false;
              this.alertService.success(data.message); 
              this.assessmentForm.get('owed_tax_data')['controls'][index].controls.id.setValue(data.row.id);
            },
            error => {
              this.loading=false;
              this.alertService.common(error);  
            }
        ); 
  }

// ----------------------------------- Form Validation Owner---------------------------------------//  



calculateTotal(){
  var totalOwed:number=0;

   for(var i=0; i<this.assessmentData.length; i++){
    var owedValue=this.assessmentData[i].property_taxes_owed;
    if(owedValue>0){
      totalOwed=(totalOwed+parseFloat(owedValue));
      
    }
  }
  // const control = this.assessmentForm.get('owed_tax_data')['controls'];
  // for(var i=0; i<control.length; i++){
  //   var owedValue=control[i].controls.property_taxes_owed.value;
  //   if(owedValue>0)
  //     totalOwed=totalOwed+parseFloat(owedValue);
  // }
  return totalOwed.toFixed(2);
}
 // ----------------------------------- Create Owner ---------------------------------------//  

 get f() { return this.assessmentForm.controls; }
 
 getControls(frmGrp: FormGroup, key: string) {
  return (<FormArray>frmGrp.controls[key]).controls;
}

assessmentInfo(){
  this.dialog.open(MoreAssessmentComponent,
    { width: '800px',
      data:{'assessmentData':this.assessmentData,'property_id':this.property_id}
    });
}
 log(item){
   console.log(item);
 }

 autoSave(data){

  this.commonActivityService.addLoader(data['el']);
  this.commonActivityService.removeElement(data['el'],'saved-icon');
  
  let index=data.el.parentElement.parentElement.getAttribute('id');
  let id = this.assessmentForm.get('owed_tax_data')['controls'][index].controls.id.value;
  let saveInfo:any={'id':id,'name':data['name'],'value':data['value'].replace(/,/g, '')};
  
  if(!id && !this.loading && this.currentIndex!=index){
    this.loading=true;
    this.currentIndex=index;
    this.updateAssessmentInfo(saveInfo,data);
  }else if(!id){
    data.el.value='';
    this.commonActivityService.removeElement(data['el'],'loader-icon');
  }
  if(id){
    this.updateAssessmentInfo(saveInfo,data);
  }
  
}

updateAssessmentInfo(saveInfo,data){


    let url = apiUrl.auto_save_assessment+'/'+this.property_id;
  //  if(this.assessmentSub) this.assessmentSub.unsubscribe();

    this.assessmentSub=this.commonApplicationService.put(url, saveInfo)
    .pipe(debounceTime(5000),distinctUntilChanged())
    .subscribe(
        response => {
          if(response['status']=='failed'){
            this.commonActivityService.removeElement(data['el'],'loader-icon');
            this.alertService.error(response['message']);
            this.loading=false;
          }else{
              this.commonActivityService.addElement(data['el']);
              this.commonActivityService.removeElement(data['el'],'loader-icon');
              let index=data.el.parentElement.parentElement.getAttribute('id');
              this.assessmentForm.get('owed_tax_data')['controls'][index].controls.id.setValue(response.data);
              this.loading=false;
          }
          
        },
        error => {
          this.commonActivityService.removeElement(data['el'],'loader-icon');
          this.loading = false;
          this.alertService.common(error);
        }
    ); 
}
 
} 