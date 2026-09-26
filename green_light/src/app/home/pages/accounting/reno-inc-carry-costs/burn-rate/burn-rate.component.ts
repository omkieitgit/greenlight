import { DatePipe } from '@angular/common';
import { Component, EventEmitter, Input, OnInit, Output } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { apiUrl } from '@config/api-url';
import { AlertService, CommonActivityService, CommonApplicationService, CommunicationService } from '@shared-service/_services';
import { StorageService } from '@shared-service/_services/storage.service';
import { CommonHelper } from '@shared-service/_utils/CommonHelper';
import { IMyDateModel, IMyDpOptions, IMyInputFieldChanged } from 'mydatepicker';
import { Observable } from 'rxjs';
import { map, startWith } from 'rxjs/operators';

@Component({
  selector: 'app-burn-rate',
  templateUrl: './burn-rate.component.html',
  styleUrls: ['./burn-rate.component.css']
})
export class BurnRateComponent implements OnInit {

  burnRateForm:FormGroup;
  @Input() property_id;
  @Input() burnRateList;
  @Input() actualBurnRateData;
  @Input() burn_rate_detail;
  @Input() payout;
  @Input() viewAccessOnly;
  
  @Output() updateBurnRate = new EventEmitter<boolean>();

  categoryList: any;
  loading:boolean=false;
  totalAmount:number=0;
  actualBurnRateInfo:any=[];
  burnRateInfo:any;
  totalAmountActual:number=0;
  totalActual:number=0;
  actualBurnRate:string='actual';
  estimateBurnRate:string='estimated';
  numberdays:number=0;
  filteredOptions: Observable<Category[]>;


  constructor(private fb: FormBuilder,
              private commonApplicationService:CommonApplicationService,
              private commonActivityService:CommonActivityService,
              private alertService:AlertService,
              private communicationService:CommunicationService,
              private storageService:StorageService) { }

  ngOnInit(): void {
    if(this.property_id !== undefined){
      this.actualBurnRateInfo=this.burnRateList.filter(res=>res.burn_rate_type==this.actualBurnRate);
      this.burnRateInfo=this.burnRateList.filter(res=>res.burn_rate_type==this.estimateBurnRate);

      this.getTotal();
      this.getTotalActual();
      this.getCategory();
      this.getNumberOfDays();
    }
    this.burnRateForm = this.fb.group({
      id:[''],
      category: ['', Validators.required],
      description: [''], 
      amount: [''],
      burn_rate_type:[this.estimateBurnRate]
    });
    this.communicationService.getPayoutInfo().subscribe(info=>{
      if(info){
        this.payout=info;
        this.getNumberOfDays();
      }
    })

    this.filteredOptions = this.burnRateForm.get('category').valueChanges
    .pipe(startWith(''),map(value => this._filter(value)) ); 

  }
  
  
  getNumberOfDays() {
    console.log(this.burn_rate_detail);
    let estimate_a_to_b_date=this.payout?.close_date_a_to_b	;
    let estimate_b_to_c_date=this.payout?.close_date_b_to_c;
    if(estimate_a_to_b_date && estimate_b_to_c_date){
      var days = this.datediff(this.parseDate(estimate_a_to_b_date),this.parseDate(estimate_b_to_c_date));
      if(days){
        this.numberdays=days;
      }
    }
  }

  parseDate(str) {
    if(str){
      var mdy = str.split('/');
      return new Date(mdy[2], mdy[0]-1, mdy[1]);
    }
  }
  
  datediff(first, second) {
    return Math.round((second-first)/(1000*60*60*24));
  }

  getCategory(){
    this.categoryList=this.storageService.getHard('inovice_category_list');
    if(!this.categoryList){
      let url = apiUrl.renovationCategory;
      this.commonApplicationService.get(url).subscribe(response => {
        this.categoryList=response.data;
      },
      (err: any) => {})    }
    
  }

  // Property Validation
  validateForm(data: any) {     
    let result = this.commonActivityService.getFullFormData(data);
    if(this.burnRateForm.invalid) { 
      return;
    }    
    // FORM SUBMITTED
    this.saveInfoDetails(result);  
  }

  // Save info detail
  saveInfoDetails(data: any){ // +"/"+this.property_id
    this.loading = true;
    data['category']=data['category'].id;
    let url = apiUrl.burnRate+'/'+this.property_id;
    this.commonApplicationService.post(url, data)
        .subscribe(
            response => {
              this.updateBurnRate.emit(true);
              this.burnRateForm.reset();
              this.alertService.success(response.message);  
              this.loading = false;
            
            },
            error => {
              this.loading = false;
              this.alertService.common(error);
            }
        ); 
  }

  ngOnChanges(){
    if(this.burnRateInfo){
        this.actualBurnRateInfo=this.burnRateList.filter(res=>res.burn_rate_type==this.actualBurnRate);
        this.burnRateInfo=this.burnRateList.filter(res=>res.burn_rate_type==this.estimateBurnRate);
        this.getTotal();
        this.getTotalActual();
    }
  }

  getTotal()
  { 
    this.totalAmount=0;
    this.burnRateInfo.forEach(info=>{
       this.totalAmount=this.totalAmount+parseFloat(info.amount?info.amount:0);
    })
   }

   getTotalActual()
  { 
    this.totalAmountActual=0;
    if(this.actualBurnRateInfo && this.actualBurnRateInfo.length>0){
      this.actualBurnRateInfo.forEach(info=>{
        this.totalAmountActual=this.totalAmountActual+parseFloat(info.amount?info.amount:0)/12;
        this.totalActual=this.totalActual+parseFloat(info.amount?info.amount:0);
      })
    }
    if(this.actualBurnRateData && this.actualBurnRateData.length>0){
      this.actualBurnRateData.forEach(element => {
        this.totalAmountActual=this.totalAmountActual+parseFloat(element.total_amount)/element.actual_months;
        this.totalActual=this.totalActual+parseFloat(element.total_amount);
      });
    }
   }


  editburnRate(burnRate){
    this.burnRateForm.get('id').setValue(burnRate.id);
    this.burnRateForm.get('category').setValue(burnRate.category);
    this.burnRateForm.get('amount').setValue(burnRate.amount);
    this.burnRateForm.get('description').setValue(burnRate.description);
    this.burnRateForm.get('burn_rate_type').setValue(burnRate.burn_rate_type);

    //this.showCatName=false;

  }

  removeBurnRate(id){

    if(confirm("Are you sure want to delete record ?")){
      let url = apiUrl.burnRate+'/'+id;
      this.commonApplicationService.delete(url).subscribe(response => {
        if(response.status=='success'){
          this.burnRateInfo = this.burnRateInfo.filter(item => item.id !== id);
          this.alertService.success(response.message);
        }else{
          this.alertService.error(response.message); 
        }
      },
      (err: any) => {
        this.loading = false;
        this.alertService.common(err); 
      })
    }
  }

  autoSave(data,actual=""){

    this.commonActivityService.addLoader(data['el']);
    this.commonActivityService.removeElement(data['el'],'saved-icon');
    
    let index=actual['id'];
    
    let saveInfo:any={'house_id':this.property_id,'id':index,'name':data['name'],'value':data['value']};
    this.updatePayoutInfo(saveInfo,data);

  }

  autoSaveActual(data,actual=""){

    this.commonActivityService.addLoader(data['el']);
    this.commonActivityService.removeElement(data['el'],'saved-icon');
    let index=actual['id'];
    let saveInfo:any={'house_id':this.property_id,'id':index,'category_id':actual['category'].id,'name':data['name'],'value':data['value']};
    this.updateCreateActualBurnRate(saveInfo,data);

  }

  updateCreateActualBurnRate(saveInfo,data){

    let url = apiUrl.updateActualBurnRate+'/'+this.property_id;
    this.commonApplicationService.put(url, saveInfo)
    .subscribe(
        response => {
          if(response['status']=='failed'){
            this.commonActivityService.removeElement(data['el'],'loader-icon');
            this.alertService.error(response['message']);
          }else{
              this.commonActivityService.addElement(data['el']);
              this.commonActivityService.removeElement(data['el'],'loader-icon');
              
              let itemIndex = this.actualBurnRateData?this.actualBurnRateData.findIndex(item => item.id == saveInfo.id):'-1';
              if(itemIndex >= 0){
                this.actualBurnRateData[itemIndex].total_amount = response.data.amount;
                this.getTotalActual();
              }
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

  updatePayoutInfo(saveInfo,data){

      let url = apiUrl.burnRateRecord+'/'+this.property_id;
      this.commonApplicationService.put(url, saveInfo)
      .subscribe(
          response => {
            if(response['status']=='failed'){
              this.commonActivityService.removeElement(data['el'],'loader-icon');
              this.alertService.error(response['message']);
            }else{
                this.commonActivityService.addElement(data['el']);
                this.commonActivityService.removeElement(data['el'],'loader-icon');
                
                let itemIndex = this.actualBurnRateData?this.actualBurnRateData.findIndex(item => item.id == response.data):'-1';
                if(itemIndex >= 0){
                  this.actualBurnRateData[itemIndex].actual_months = data.value;
                  this.getTotalActual();
                }
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

  displayFn(category?: any): string | undefined {
    return category ? category.category_name : undefined;
  }

  private _filter(name: string): Category[] {
    if(typeof name !== 'object'){
      const filterValue = name.toLowerCase();
      return this.categoryList.filter(option => option.category_name.toLowerCase().indexOf(filterValue) === 0);

    }
  }
}

export interface Category{
  id:number,
  category_name:string;   
}