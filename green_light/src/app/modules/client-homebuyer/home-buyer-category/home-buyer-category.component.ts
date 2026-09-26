import { Component, EventEmitter, Input, OnInit, Output, SimpleChanges } from '@angular/core';
import { CurrencyFormatPipe } from '@shared-modules/directives/currency-format.pipe';
import { SumPipe } from '@shared-modules/directives/sum.pipe';
import { AlertService, apiUrl, CommonActivityService, CommonApplicationService } from '@shared-service/_services';
import { StorageService } from '@shared-service/_services/storage.service';
import { HomeBuyerService } from '../home-buyer.service';

@Component({
  selector: 'app-home-buyer-category',
  templateUrl: './home-buyer-category.component.html',
  styleUrls: ['./home-buyer-category.component.css']
})
export class HomeBuyerCategoryComponent implements OnInit {

  @Input() payoutInfo;
  @Input() property_id;
  @Input() categoryType;
  @Input() title;
  @Input() renoHomeBuyerCategory;  
  @Input() totalTitle;  
  @Input() categoryList;  

  payoutCategoryList:any=[];
  defaultCategoryList:any=[];
  total_est:number=0;
  total_act:number=0;
  total_calc:number=0;
  estAmountEditAccess:boolean=false;

  @Output() estimatedTotalAmount = new EventEmitter<any>();

  
  constructor(private homeBuyerService:HomeBuyerService,
              private storageService:StorageService,
              public sumPipe:SumPipe,
              private cp:CurrencyFormatPipe,
              private commonApplicationService:CommonApplicationService,
              private commonActivityService:CommonActivityService,
              private alertService:AlertService) { }

  ngOnInit(): void {
    
    //this.estAmountEditAccess=this.storageService.getHard('ac_view_access');
    if(this.payoutInfo){
      let payoutCatList=this.payoutInfo.payout_detail.filter(items => items.payout_type ===this.categoryType);
      this.categoryList=this.categoryList.filter(items => items.category_type ===this.categoryType && items.homebuyer_category==1);
     
      this.categoryList.forEach(element => {
        const foundIndex = payoutCatList.findIndex(cat=>cat.category_id==element.id);
        if(foundIndex >=0 ){
          let isPayoutCategory=payoutCatList[foundIndex];
          isPayoutCategory.category_name=isPayoutCategory?.category?.category_name;
          this.payoutCategoryList.push(isPayoutCategory); 
        }
        else{
          element.amount=0;
          element.est_amount=0;
          element.buyer_amount=0;
          element.category_id=element.id;
          this.payoutCategoryList.push(element);
        }
      });

      payoutCatList.forEach(element => {
        if(!this.payoutCategoryList.find(cat=>cat.category_id==element.category_id)){
         element.category_name=element?.category?.category_name;
         this.payoutCategoryList.push(element); 
        }
     });
    }

    this.renoCategory();
    
  }

  renoCategory(){
    if(this.renoHomeBuyerCategory){
      let catList=this.storageService.getHard('inovice_category_list');
      let filterCategoryList=catList.filter(items => items.homebuyer_category==1);
     
      filterCategoryList.forEach(element => {
        const foundIndex = this.renoHomeBuyerCategory.findIndex(cat=>cat.sub_category==element.id);
        if(foundIndex >=0 ){
          let isRenoCategory=this.renoHomeBuyerCategory[foundIndex];
          isRenoCategory.category_id=isRenoCategory.sub_category;
          isRenoCategory.category_name=isRenoCategory?.category?.category_name;
          let homebuyerCat=isRenoCategory.homebuyer_reno_category;
          isRenoCategory.est_amount=homebuyerCat?homebuyerCat.est_amount:0;
          isRenoCategory.buyer_amount=homebuyerCat?homebuyerCat.buyer_amount:0;
          this.payoutCategoryList.push(isRenoCategory); 
        } 
        else{
          element.amount=0;
          element.est_amount=0;
          element.buyer_amount=0;
          element.category_id=element.id;
          this.payoutCategoryList.push(element); 
        }
      });

      this.renoHomeBuyerCategory.forEach(element => {
         if(!this.payoutCategoryList.find(cat=>cat.category_id==element.sub_category)){
          element.category_id=element.sub_category;
          element.category_name=element?.category?.category_name;
          let homebuyerCat=element.homebuyer_reno_category;
          element.est_amount=homebuyerCat?homebuyerCat.est_amount:0;
          element.buyer_amount=homebuyerCat?homebuyerCat.buyer_amount:0;
          this.payoutCategoryList.push(element); 
         }
      });
     
    }
  }

  ngOnChanges(changes: SimpleChanges){
    if(changes?.renoHomeBuyerCategory?.currentValue && changes?.renoHomeBuyerCategory?.previousValue){
      this.renoHomeBuyerCategory=changes?.renoHomeBuyerCategory?.currentValue;
      this.payoutCategoryList=[];
      this.renoCategory();
      this.emitTotalAmount();
    }
 
  }

  ngAfterViewInit(){
    this.emitTotalAmount();
  }

  diffCalculation(est_value,act_value,diff_value,category){
    est_value.value=this.cp.detransformVal(est_value.value);
    this.calculateTotalAmount(est_value,category);
    this.homeBuyerService.calculationDiff(est_value,act_value,diff_value);
    this.calculateTotal();
  }

  calculateTotalAmount(est_value,category){
    let name=est_value.getAttribute('name');
    this.payoutCategoryList.find(cat=>cat.category_id==category.category_id)[name]=est_value.value;
    console.log(this.payoutCategoryList);
    this.emitTotalAmount();
  }

  
  calcValue($event,buyer_amount,category){
    $event.target.value=this.cp.detransformVal($event.target.value);
    if(isNaN($event.target.value)===false){
      this.calculateTotalAmount(buyer_amount,category);
      $event.target.value=this.cp.transformVal(this.cp.detransformVal($event.target.value));
      this.calculateTotal();
    }else{
      $event.target.value='';
    }
    
  }

  emitTotalAmount(){
    let catType=this.categoryType;
    let totalAmount={'category_type':catType,
                      'total_amount':[{
                        'est_amount':this.sumPipe.transform(this.payoutCategoryList,'est_amount'),
                        'amount':this.sumPipe.transform(this.payoutCategoryList,'amount'),
                        'buyer_amount':this.sumPipe.transform(this.payoutCategoryList,'buyer_amount')
                      }]
                     };
    this.estimatedTotalAmount.emit(totalAmount);
  }


  calculateTotal(){
    this.total_est=this.homeBuyerService.calculateTotalCost('_est_a_to_b');
    this.total_act=this.homeBuyerService.calculateTotalCost('_act_a_to_b');
    this.total_calc=this.homeBuyerService.calculateTotalCost('_calc_b_to_c');
    this.homeBuyerService.setTotalAtoB({"total_est_atob":this.total_est,
    "total_act_atob":this.total_act,"total_calc_atob":this.total_calc});
  }

  autoSave(data,category){

    // if(this.estAmountEditAccess && data['name']=='est_amount'){
    //   this.alertService.error("You haven't access to update this record");
    //   return;
    // }
    if(!data['value']){
      this.alertService.error("Please enter amount");
      return;
    }
    this.commonActivityService.addLoader(data['el']);
    this.commonActivityService.removeElement(data['el'],'saved-icon');
    let saveInfo:any={'house_id':this.property_id,'payout_type':this.categoryType,
                     'category_id':category.category_id,'name':data['name'],'value':this.cp.detransformVal(data['value']??0)};
    this.updatePayoutInfo(saveInfo,data);

  }
  updatePayoutInfo(saveInfo,data){

    let url = apiUrl.updatePayoutDetail;
    this.commonApplicationService.put(url, saveInfo)
    .subscribe(
        response => {
          if(response['status']=='failed'){
            this.commonActivityService.removeElement(data['el'],'loader-icon');
            this.alertService.error(response['message']);
          }else{
              this.commonActivityService.addElement(data['el']);
              this.commonActivityService.removeElement(data['el'],'loader-icon');
          }
         // this.loading=false;
        },
        error => {
        //  this.loading = false;
          this.commonActivityService.removeElement(data['el'],'loader-icon');
          this.alertService.common(error);
        }
    ); 
  }
}
