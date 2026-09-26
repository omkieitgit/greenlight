import { Component, OnInit, Input } from '@angular/core';
import { FormControl, NgForm, FormBuilder, FormGroup, Validators, FormArray } from '@angular/forms';
import { InfoModel } from '../info.model';

import { Router,ActivatedRoute } from '@angular/router';
import {IMyDpOptions, IMyDateModel} from 'mydatepicker';

import { CommonApplicationService,AlertService,CommonActivityService, CommunicationService } from '@shared-service/_services';
import { apiUrl } from '@config/api-url';
import { StorageService } from '@shared-service/_services/storage.service';
import { DatePipe } from '@angular/common';

@Component({
  selector: 'price-history',
  templateUrl: './price-history.component.html'
})
export class PriceHistoryComponent implements OnInit {
  priceHistoryForm: FormGroup;
  private infoData : InfoModel;
  result: any;  
  @Input() priceHistoryData: any;
  invalidFields: any; 
  submitted = false;
  loading = false;
  loadingMessage: any;
  propErr: boolean = false;
  propErrMsg: any = "";
  property_id: string;
  property_config: any= {};
  price_desc: string[];
  price_info_data=[1];
  priceResult: any;
  validRow:number;

  totalSqft:number=1;
  autoLoader:boolean=false;
  currentIndex:number;
  loadingRow = false;

 constructor(private formBuilder: FormBuilder,          
          private commonApplicationService: CommonApplicationService,
          private commonActivityService: CommonActivityService,
          private storageService:StorageService,private alertService: AlertService,
          private communicationService: CommunicationService,
          private datePipe:DatePipe,
          private route: ActivatedRoute,) { 

           this.property_config =  this.storageService.get("property_config");
            if(this.property_config !== null){
              this.price_desc          = this.property_config.ph_description;
            }
 }

  public myDatePickerOptions: IMyDpOptions = { dateFormat: 'mm/dd/yyyy',firstDayOfWeek : 'su'};

  onDateChanged(event: IMyDateModel,dateInput) {
      let name=dateInput.elem.nativeElement.getAttribute('formcontrolname');
      let data={'name':name,'value':this.datePipe.transform(event.formatted,'yyyy-MM-dd'),'el':dateInput.elem.nativeElement};
      this.autoSave(data);
        return event.formatted;
    }

  ngOnInit() {
    this.property_id = this.route.snapshot.paramMap.get('property_id');
    // Load info details
    let proInfo= this.storageService.getHard('property_info');
    if(proInfo.total_living_sqft && proInfo.total_living_sqft!='0.00'){
      this.totalSqft=proInfo.total_living_sqft;
    }
    
    if(this.property_id !== undefined){
      this.initialize();
      this.populateDataInFormPrice();
      //this.propErr = true;
      //this.getSchoolDetails();    
    }
    this.communicationService.getScrapperData().subscribe(scrapper=>{
      if(scrapper?.price_history){
        this.priceHistoryData.push(scrapper?.price_history);
        this.populateDataInFormPrice();
      }
    });
  }
  
  initialize(){
     this.priceHistoryForm = this.formBuilder.group({
      price_info_data: this.formBuilder.array([this.priceFields()]),     
      house_id:[this.property_id],
      add_more:['Add More']
    });
    //this.commonActivityService.isDisabled("PROPERTY_INFO", this.priceHistoryForm);
  }

  priceFields() : FormGroup
  {    
    return this.formBuilder.group({
      price_date: ['',Validators.required],
      price: ['',Validators.required],
      cost_per_sqft: [''],
      source: [''],
      description: [''],
      id:[''],
      house_id:[this.property_id],
      save:['Save'],
      remove:['Remove'],
    });
  }

// ----------------------------------- Form Validation Owner ---------------------------------------//
  validatePriceForm(data: any,i: number) {
    this.validRow=i;
    data.controls.house_id.setValue(this.property_id);
    this.result = this.commonActivityService.getFullFormDataWithDateFormatted(data);
    this.invalidFields = this.commonActivityService.findInvalidControls(data);
    console.log("Form Invalid Filleds = ",this.invalidFields);
    this.submitted = true;
    if(this.priceHistoryForm.get('price_info_data')['controls'][i].invalid) {
      console.log('Form is invalid, Fill all fields.');    
     // this.alertService.error('Form is invalid, Fill all fields.');  
      return;
    }    
    // FORM SUBMITTED
    console.log("Form Data = ",this.result);
    console.log('form submitted');

    if(this.result.id !== undefined && this.result.id != null && this.result.id > 0){
        this.updatePriceInfoDetails(this.result,i);
      }else{
        this.createPriceInfoDetails(this.result,i);
      }
  }

// ----------------------------------- Form Validation Owner---------------------------------------//  

// ----------------------------------- Fill Form Data ---------------------------------------// 
  populateDataInFormPrice(){
    if(this.priceHistoryData.length){
        if(this.priceHistoryData.length == 1){
            // If data is for fist row then fill
             this.fillPriceForm(0);
        }else{
            //If data is for fist row then fill
            this.fillPriceForm(0);

            for(var i=1; i<this.priceHistoryData.length; i++ ){
                this.addPriceInputField();
                this.fillPriceForm(i);
            }
        }
    }     
  }

  fillPriceForm(index:number){
      //this.priceHistoryForm.get('price_info_data')['controls'][index].controls.price_date.setValue(this.priceHistoryData[index].price_date);
      let priceHistory=this.priceHistoryForm.get('price_info_data')['controls'][index].controls;

      priceHistory.id.setValue(this.priceHistoryData[index].id);
      
      priceHistory.price.setValue(this.priceHistoryData[index].price);

      if(this.priceHistoryData[index].cost_per_sqft)
        priceHistory.cost_per_sqft.setValue(this.priceHistoryData[index].cost_per_sqft);
      else{
        let perCost=this.calculateCostPerSqft(this.priceHistoryData[index].price);
        priceHistory.cost_per_sqft.setValue(perCost);

      }
      priceHistory.source.setValue(this.priceHistoryData[index].source);
      priceHistory.description.setValue(this.priceHistoryData[index].description);
      
      let deedData = this.priceHistoryData[index].price_date;
      let dObj = null;
      if(deedData !== undefined && deedData != null){
            let dDate = new Date(deedData);
            dObj = {
              date: {
                  year: dDate.getFullYear(),
                  month: dDate.getMonth() + 1,
                  day: dDate.getDate()}
              }
      }else{
          dObj = null;
      }
      priceHistory.price_date.setValue(dObj);
  }


 // ----------------------------------- Fill Form Data ---------------------------------------// 
 // ----------------------------------- Update Owner ---------------------------------------//  
  updatePriceInfoDetails(data: any,index: number){
    this.loadingRow = true;

    console.log("savingdata>"+data);
    console.log(this.property_id);
    //data['house_id']=this.property_id;
    if(this.property_id){
      let url = apiUrl.price_history+'/'+data.id;
      console.log(url);
      this.commonApplicationService.put(url, data)
        .subscribe(
            data => {
              this.loadingRow = false;
              this.alertService.success(data.message);                
            },
            error => {
              this.loadingRow = false;
              this.alertService.common(error); 
            }
        ); 
      }else{
        this.alertService.error("Please save property detail.");  
    }
  }
// ----------------------------------- Update Owner ---------------------------------------//  

// ----------------------------------- Create Borrower ---------------------------------------//  
createPriceInfoDetails(data: any,index: number){
    //console.log("savingdata>"+this.priceHistoryForm);
    console.log("savingdata>"+data);
    console.log(this.property_id);
    //data['house_id']=this.property_id;
    if(this.property_id){
        let url = apiUrl.price_history;
        console.log(url);
        this.commonApplicationService.post(url, data)
        .subscribe(
            data => {
              this.loadingRow = false;
              this.alertService.success(data.message); 
              this.priceHistoryForm.get('price_info_data')['controls'][index].controls.id.setValue(data.row.id);
              //this.getOwnerInfo();               
            },
            error => {
              this.loadingRow = false;
              this.alertService.common(error); 
            }
        ); 
      }else{
        this.alertService.error("Please save property detail.");  
    }
  }


 // ----------------------------------- Create Owner ---------------------------------------//  
  
  addPriceInputField() : void
  {
   const control = <FormArray>this.priceHistoryForm.controls.price_info_data;
   control.push(this.priceFields());
  }
  
  removePriceInputField(i : number,id) : void
  {
      if(confirm("Are you sure want to delete record ?")){
           const control = <FormArray>this.priceHistoryForm.controls.price_info_data;
           control.removeAt(i);
           if(id){
         
              let url = apiUrl.price_history+'/'+id;
              this.commonApplicationService.delete(url).subscribe(response => {
                if(response !== undefined){             
                    this.alertService.success(response.message); 
                }
              },
              (err: any) => {
                this.alertService.error("Error occured, Please try again later!");
              })
          }
      }
  }

   get f() { return this.priceHistoryForm.controls; }

   onDescChange(descValue, index){
     //console.log(descValue, index);
         if(descValue == 0){
            this.priceHistoryForm.get('price_info_data')['controls'][index].controls.id.setValue(this.priceHistoryData[index].id);
            this.priceHistoryForm.get('price_info_data')['controls'][index].controls.price.setValue(0);
            this.priceHistoryForm.get('price_info_data')['controls'][index].controls.cost_per_sqft.setValue(0);
            this.priceHistoryForm.get('price_info_data')['controls'][index].controls.source.setValue("N/A");
            this.priceHistoryForm.get('price_info_data')['controls'][index].controls.price_date.setValue("");
         }

       if(descValue == 7){
         this.priceHistoryForm.get('price_info_data')['controls'][index].controls.source.setValue("Public Record");
       }
  }

  calculateCostPerSqft(price){
   return (price/this.totalSqft).toFixed(2);
  }

  costPerSqft(price,index){
    let costPer= (price.target.value/this.totalSqft).toFixed(2);
    this.priceHistoryForm.get('price_info_data')['controls'][index].controls.cost_per_sqft.setValue(costPer);
  }


  autoSave(data){

    let index=data.el.parentElement.parentElement.getAttribute('id');
    let id = this.priceHistoryForm.get('price_info_data')['controls'][index].controls.id.value;
    let saveInfo:any={'id':id,'name':data['name'],'value':data['value']};
    if(data['name']=='price'){
      saveInfo['cost_per_sqft'] = (data['value']/this.totalSqft).toFixed(2);
    }

    if(!id && !this.loading && this.currentIndex!=index){
      this.loading=true;
      this.currentIndex=index;
      this.updatePriceHistoryInfo(saveInfo,data);
    }else if(!id){
      data.el.value='';
      this.commonActivityService.removeElement(data['el'],'loader-icon');
    }
    if(id){
      this.updatePriceHistoryInfo(saveInfo,data);
    }
  }

  updatePriceHistoryInfo(saveInfo,data){
      this.commonActivityService.addLoader(data['el']);
      this.commonActivityService.removeElement(data['el'],'saved-icon');
      let url = apiUrl.auto_price_history+'/'+this.property_id;
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
                let id = this.priceHistoryForm.get('price_info_data')['controls'][index].controls.id.setValue(response.data);
            }
            this.loading = false;
          },
          error => {
            this.commonActivityService.removeElement(data['el'],'loader-icon');
            this.loading = false;
            this.alertService.common(error);
          }
      ); 
  }

}
