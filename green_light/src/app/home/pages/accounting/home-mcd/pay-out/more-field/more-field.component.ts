import { Component, EventEmitter, Input, OnInit, Output, SimpleChanges } from '@angular/core';
import { FormArray, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { AlertService, apiUrl, CommonActivityService, CommonApplicationService } from '@shared-service/_services';
import { StorageService } from '@shared-service/_services/storage.service';

@Component({
  selector: 'app-more-field',
  templateUrl: './more-field.component.html',
  styleUrls: ['./more-field.component.css']
})
export class MoreFieldComponent implements OnInit {

  moreFieldForm:FormGroup;
  loading:boolean=false;
  totalOtherIncome:number=0;

  @Input() property_id;  
  @Input() additionalField;
  @Input() fieldType;
  @Input() lender_id:number=0;
  @Input() totalAirBnb;
  @Input() totalFundsPriorClosing;

  @Output() totalAmount = new EventEmitter<number>();
  @Output() updateMoreDetail = new EventEmitter<any>();
  viewAccessOnly:boolean=false;

  constructor(private formBuilder:FormBuilder,
              private commonActivityService:CommonActivityService,
              private commonApplicationService:CommonApplicationService,
              private alertService:AlertService,
              private storageService:StorageService,
              ) { }

  ngOnInit(): void {
    this.viewAccessOnly=this.storageService.getHard('ac_view_access');
    this.moreFieldForm = this.formBuilder.group({
      more_info_data: this.formBuilder.array([this.priceFields()]),     
      house_id:[],
    });
    this.populateDataInForm();
    this.calculateTotalShortTerm();
    this.calculateMoreDataOnload();
    
  }
  ngOnChanges(changes: SimpleChanges){
      if(changes?.totalAirBnb?.currentValue!=changes?.totalAirBnb?.previousValue){
        this.calculateMoreDataOnload();
        //this.totalAmount.emit(this.totalOtherIncome+parseFloat(this.totalAirBnb));
      }
      if(changes?.totalFundsPriorClosing?.currentValue!=changes?.totalFundsPriorClosing?.previousValue){
        this.calculateMoreDataOnload();
        //this.totalAmount.emit(this.totalOtherIncome+parseFloat(this.totalFundsPriorClosing));
      }
  }

  calculateMoreDataOnload(){
    if(this.additionalField){
      this.totalOtherIncome=0;
      this.additionalField.forEach(element => {
        if(element.field_value){
          this.totalOtherIncome+=+element.field_value;
        }
      });
      if(this.fieldType=='otherincome'){
        this.totalOtherIncome=this.totalOtherIncome+parseFloat(this.totalAirBnb?this.totalAirBnb:0)+parseFloat(this.totalFundsPriorClosing?this.totalFundsPriorClosing:0);
      }
      this.totalAmount.emit(this.totalOtherIncome);
    }
  }
  
  calculateTotalShortTerm(){
    this.moreFieldForm.get('more_info_data').valueChanges.subscribe(value=>{
      this.totalOtherIncome=0;
      value.forEach(element => {
        if(element.field_value){
          this.totalOtherIncome+=+element.field_value;
        }
      });

      if(this.fieldType=='otherincome'){
        this.totalOtherIncome=this.totalOtherIncome+parseFloat(this.totalAirBnb?this.totalAirBnb:0)+parseFloat(this.totalFundsPriorClosing?this.totalFundsPriorClosing:0);
      }

      this.totalAmount.emit(this.totalOtherIncome);
    });
  }

  priceFields() : FormGroup
  {    
    return this.formBuilder.group({
      id:[],
      field_name: ['',Validators.required],
      field_value: ['',Validators.required],
      readOnly:[true]
    });
  }
  addPriceInputField() : void
  {
   const control = <FormArray>this.moreFieldForm.controls.more_info_data;
   control.push(this.priceFields());
  }
  
  removePriceInputField(i : number,id) : void
  {
      if(confirm("Are you sure want to delete record ?")){
           const control = <FormArray>this.moreFieldForm.controls.more_info_data;
           control.removeAt(i);
           if(id){
              let url = apiUrl.additionalField+'/'+id;
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


  populateDataInForm(){
    if(this.additionalField){
      for(var i=0; i<this.additionalField.length; i++ ){
        if((this.fieldType=='distributions' && this.additionalField[i].lender_id==this.lender_id) || this.fieldType=='otherincome' || this.fieldType=='closingprior' || this.fieldType=='distribution_llc'){
          this.addPriceInputField();
          this.fillPriceForm(i);
          //j++;
        }
      }
        // if(this.additionalField.length>0){
        //     // If data is for fist row then fill
        //      this.fillPriceForm(0,0);
        // }else{
        //     //If data is for fist row then fill
        //     this.fillPriceForm(0,0);
        //     let j=1;
            
        // }
    }     
  }

  fillPriceForm(index:number){
      //this.priceHistoryForm.get('price_info_data')['controls'][index].controls.price_date.setValue(this.priceHistoryData[index].price_date);
    let additional=this.moreFieldForm.get('more_info_data')['controls'][index]?.controls;
    if(additional){
      additional.id.setValue(this.additionalField[index].id);
      additional.field_name.setValue(this.additionalField[index].field_name);
      additional.field_value.setValue(this.additionalField[index].field_value);
      additional.readOnly.setValue(false);
    }
  }


  autoSave(data){

    this.commonActivityService.addLoader(data['el']);
    this.commonActivityService.removeElement(data['el'],'saved-icon');
    
    let index=data.el.parentElement.parentElement.getAttribute('id');
    let id = this.moreFieldForm.get('more_info_data')['controls'][index].controls.id.value;
    let saveInfo:any={'id':id,'name':data['name'],'value':data['value'],'lender_id':this.lender_id,'field_type':this.fieldType};
    this.updatePayoutInfo(saveInfo,data);

  }

  updatePayoutInfo(saveInfo,data){

      let url = apiUrl.additionalField+'/'+this.property_id;
      this.commonApplicationService.post(url, saveInfo)
      .subscribe(
          response => {
            if(response['status']=='failed'){
              this.commonActivityService.removeElement(data['el'],'loader-icon');
              this.alertService.error(response['message']);
            }else{
                response.data;
                let index=data.el.parentElement.parentElement.getAttribute('id');
                let id = this.moreFieldForm.get('more_info_data')['controls'][index].controls.id.setValue(response.data.id);
                this.commonActivityService.addElement(data['el']);
                this.commonActivityService.removeElement(data['el'],'loader-icon');
                this.moreFieldForm.get('more_info_data')['controls'][index].controls.readOnly.setValue(false);
               // this.additionalField.push(response.data);
            }
            this.loading=false;
          },
          error => {
            this.loading = false;
            this.commonActivityService.removeElement(data['el'],'loader-icon');
            this.alertService.common(error);
          }
      ); 
  }



}
