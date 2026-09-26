import { Component, OnInit, Inject } from '@angular/core';
import {FormBuilder, FormGroup, Validators, FormArray } from '@angular/forms';
import { MAT_DIALOG_DATA} from '@angular/material/dialog';
import { CommonApplicationService,AlertService,CommonActivityService,MessageService,CommunicationService } from '@shared-service/_services';
import { apiUrl } from '@config/api-url';


@Component({
  selector: 'app-more-assessment',
  templateUrl: './more-assessment.component.html',
  styleUrls: ['./more-assessment.component.css']
})
export class MoreAssessmentComponent implements OnInit {
  //moreAssessmentForm: FormGroup;
  assessmentForm:FormGroup;
  result: any;  
  assessmentData: any;
  invalidFields: any; 
  submitted = false;
  loading = false;
  loadingMessage: any;
  propErr: boolean = false;
  propErrMsg: any = "";
  property_id: string;
  assessment_tax_data=[1]; 
  owed_tax_data=[1]; 
  view_more: boolean = false;
  validateRow:number;
  autoLoader:boolean=false;
  currentIndex:number;
  
  constructor(@Inject(MAT_DIALOG_DATA) public data,
              private formBuilder: FormBuilder,
              private commonApplicationService: CommonApplicationService,
              private alertService:AlertService,
              private commonActivityService: CommonActivityService,) { }

  ngOnInit() {

    this.assessmentData=this.data.assessmentData;
    this.property_id=this.data.property_id;
    this.initialize();
    this.populateDataInFormPrice();
    
  }

  initialize(){

    this.assessmentForm=this.formBuilder.group({
       house_id:[this.property_id],
       owed_tax_data: this.formBuilder.array([]), 
    });
  }

  // viewMoreData(){
  //   if(!this.view_more){
  //     this.view_more = true;
  //     this.populateDataInFormPriceViewMore();
  //   }
  // }
 
   owedTaxFields() : FormGroup
  {    
    return this.formBuilder.group({
      property_taxes_owed: ['', [Validators.required]],
      property_taxes_owed_year:['', [Validators.required]],
      taxes_assessed: ['', [Validators.required]],
      taxes_year: ['', [Validators.required]],
      house_id:[this.property_id],
      id:[],
    })
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
             this.fillPriceForm(0);
        }else{
            for(var i=0; i<this.assessmentData.length; i++ ){
              this.addTaxOwedField(); 
              this.fillPriceForm(i);
            }
        }
    }     
  }

  // populateDataInFormPriceViewMore(){
  //   if(this.assessmentData.length){
  //       if(this.assessmentData.length == 1){
  //            this.fillPriceForm(0);
  //       }else{
  //           if(this.assessmentData.length>5){
  //             for(var i=5; i<this.assessmentData.length; i++ ){
  //                this.addTaxOwedField(); 
  //                this.fillPriceForm(i); 
  //             }
  //           }
  //       }
  //   }     
  // }

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
    this.submitted = true;

    let assessmentFormControl=this.assessmentForm.get('owed_tax_data')['controls'];

    for(let j=0; j<assessmentFormControl.length; j++ ){
       if(assessmentFormControl[j].invalid)
          return;
          
    }
    this.loading=true;
    for(var i=0; i<assessmentFormControl.length; i++){
      let result = this.commonActivityService.getFullFormDataWithDateFormatted(assessmentFormControl[i]);
      if(result['id'] !== undefined && result['id'] != null && result['id'] > 0){
        this.updateTaxOwedAssessmentDetails(result,i);
      }else{
        this.createTaxOwedAssessmentDetails(result,i);
      }
    }  
    this.loading=false;   
  }


   // ----------------------------------- Update Owner ---------------------------------------//  
   updateTaxOwedAssessmentDetails(data: any,index: number){
    //console.log("savingdata>"+this.assessmentForm);
    console.log("savingdata>"+data);
    console.log(this.property_id);
    data['house_id']=this.property_id;
    let url = apiUrl.property_assessment+'/'+data.id;
    console.log(url);
    this.commonApplicationService.put(url, data)
      .subscribe(
          data => {
            //this.modifyBtn = false;
            this.alertService.success(data.message);                
          },
          error => {
            this.alertService.common(error); 
          }
      ); 
    }
    // ----------------------------------- Update Owner ---------------------------------------//  
  
    // ----------------------------------- Create Borrower ---------------------------------------//  
    createTaxOwedAssessmentDetails(data: any,index: number){
      //console.log("savingdata>"+this.assessmentForm);
      console.log("savingdata>"+data);
      console.log(this.property_id);
      data['house_id']=this.property_id;
      let url = apiUrl.property_assessment;
      console.log(url);
      this.commonApplicationService.post(url, data)
          .subscribe(
              data => {
                //this.modifyBtn = false;
                this.alertService.success(data.message); 
                this.assessmentForm.get('owed_tax_data')['controls'][index].id.setValue(data.row.id);
                //this.getOwnerInfo();               
              },
              error => {
                this.alertService.common(error);  
              }
          ); 
    }
  
// ----------------------------------- Form Validation Owner---------------------------------------//  



  calculateTotal(){
    var totalOwed:number=0;
    const control = this.assessmentForm.get('owed_tax_data')['controls'];
    for(var i=0; i<control.length; i++){
      var owedValue=control[i].controls.property_taxes_owed.value;
      if(owedValue>0)
        totalOwed=totalOwed+parseFloat(owedValue);
    }
    return totalOwed;
  }
 // ----------------------------------- Create Owner ---------------------------------------//  

  get f() { return this.assessmentForm.controls; }
 
  getControls(frmGrp: FormGroup, key: string) {
    return (<FormArray>frmGrp.controls[key]).controls;
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
      this.commonApplicationService.put(url, saveInfo)
      .subscribe(
          response => {
            if(response['status']=='failed'){
              this.commonActivityService.removeElement(data['el'],'loader-icon');
              this.alertService.error(response['message']);
            }else{
                this.commonActivityService.addElement(data['el']);
                this.commonActivityService.removeElement(data['el'],'loader-icon');
                let index=data.el.parentElement.parentElement.getAttribute('id');
                this.assessmentForm.get('owed_tax_data')['controls'][index].controls.id.setValue(response.data);
            }
            this.loading=false;
          },
          error => {
            this.commonActivityService.removeElement(data['el'],'loader-icon');
            this.loading = false;
            this.alertService.common(error);
          }
      ); 
  }
}
