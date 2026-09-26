import { Component, OnInit,Input, ChangeDetectorRef, SimpleChanges, EventEmitter, Output, ChangeDetectionStrategy } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { MatDialog } from '@angular/material/dialog';
import { apiUrl } from '@config/api-url';
import { AlertService, CommonActivityService, CommonApplicationService, CommunicationService } from '@shared-service/_services';
import { StorageService } from '@shared-service/_services/storage.service';
import { Subscription } from 'rxjs';
import { NetprofitPipe } from './net-profit.pipe';
import { PayoutCategoryListComponent } from './payout-category-list/payout-category-list.component';
import { PayoutCategory } from './payout-category.model';


@Component({
  selector: 'pay-out',
  templateUrl: './pay-out.html',
  changeDetection: ChangeDetectionStrategy.OnPush

})
export class PayOutComponent implements OnInit {
  
  @Input() property_id:string;
  @Input() payoutDetail;
  @Input() nonHubResult;

  @Input() totalAirBnbAmount:number;
  @Output() totalNetProfitAmount = new EventEmitter<number>();

  @Input() openPanel:boolean;
  @Input() viewAccessOnly;
  property_info: object;
  payoutInfo:any;
  loaded:boolean;

  totalPurchaseAmountAtoB:number=0;
  totalAmountAtoB:number=0;
  totalAmountCreditReceive:number=0;
  totalAdjustmentAmount:number=0;
  totalPurchaseAdjuestment:number=0;
  totalFeeAmount:number=0;

  totalNonHudAmount:number=0;
  totalPriceBtoC:number=0;
  totalCreditReceivedBtoC:number=0;
  totalIncomeAdjustmentBtoC:number=0;
  totalOtherSaleIncome:number=0;
  totalOtherIncome:number=0;
  bonusSpread:number=0;
  totalclosingPrior:number=0;

  category_list:PayoutCategory[];
  payoutForm:FormGroup;
  loading:boolean=false;
  mcdLenderResult:any;

  netProfitAmount:any=0;
  totalAirBnb:number=0;
  additionalField:any;
  moreFieldDetail:any;
  netProfitBeforeFcost:number;
  totalFundsPriorClosing:any=0;

  netProfitAmountArray=new Array;

  atobTotal:any=['a_to_b','settlement','adjustment'];
  btocTotal:any=['b_to_c','btoc_settlement','btoc_adjustment','othersale'];

  payoutSubTotalKey:payoutSubTotalKey;
  payoutSubTotal:any;
  subscription: Subscription;

  mcdSubTotal:any=[]; 


  constructor(private storageService:StorageService,
              private commonApplicationService:CommonApplicationService,
              private ref: ChangeDetectorRef,
              private formBuilder: FormBuilder,
              private commonActivityService:CommonActivityService,
              private alertService:AlertService,
              private communicationService:CommunicationService,
              private netProfitPipe:NetprofitPipe,
              private dailog:MatDialog,   
              ) {
                this.payoutSubTotalKey=new payoutSubTotalKey();
               }

	ngOnInit() {
    this.property_info=this.storageService.getHard('property_info');
    if(!this.openPanel){
      this.openPanel=true;
      this.getPayout();
      this.getMcdLender();

      if(this.subscription) this.subscription.unsubscribe();
      this.subscription=this.communicationService.getPayoutTotal().subscribe(res=>{
        this.payoutSubTotal=res;
      })
    }
  } 

  ngOnChanges(changes: SimpleChanges){
    if(changes.totalAirBnbAmount && this.openPanel){
      this.totalAirBnb=this.totalAirBnbAmount;
    }
  }

  payout_form(){
    this.payoutForm = this.formBuilder.group({
      selling_price_btoc:[this.payoutInfo['selling_price_btoc']],
      hud_btoc:[this.payoutInfo['hud_btoc']],  
      financing_cost:[this.payoutInfo['financing_cost']],
    });
  }
  
  panelExpand(flag){
    if(!this.openPanel){
      this.openPanel=true;
      this.getPayout();
      this.getMcdLender();
    }
  }

  getPayout(){

    this.payoutInfo=this.payoutDetail['payout'];
    this.category_list=this.payoutDetail['category'];
    if(this.payoutInfo?.total_air_bnb){
      this.totalAirBnb=this.payoutInfo?.total_air_bnb[0]?.totalAmountReceived;
     
    }
    this.loaded=true;
    if(this.payoutInfo['purchase_price']){
      this.totalPurchaseAmountAtoB=this.payoutInfo['purchase_price'];
      this.addUpdateMcdSubTotal('purchase_price',this.totalPurchaseAmountAtoB);
      //this.payoutSubTotalKey.totalPurchaseAmountAtoB=this.payoutInfo['purchase_price'];
      //this.communicationService.setPayoutTotal(this.payoutSubTotalKey);
    }

    if(this.payoutInfo['addition_field']){
      this.moreFieldDetail=this.payoutInfo['addition_field'];
      this.additionalField=this.payoutInfo['addition_field'].filter(res=>res.field_type=='closingprior');
    }
    if(this.payoutInfo['selling_price_btoc']){
      this.addUpdateMcdSubTotal('selling_price',this.payoutInfo['selling_price_btoc']);
      //this.payoutSubTotalKey.totalSellingPriceBtoc=this.payoutInfo['selling_price_btoc'];
      //this.communicationService.setPayoutTotal(this.payoutSubTotalKey);
    }
    if(this.payoutInfo['financing_cost']){
      this.addUpdateMcdSubTotal('financing_cost',this.payoutInfo['financing_cost']);
      //this.payoutSubTotalKey.totalFinancingCost=this.payoutInfo['financing_cost'];
      //this.communicationService.setPayoutTotal(this.payoutSubTotalKey);
    }
    this.payout_form();
    this.calculateNetProfit();
      
  }

  addUpdateMcdSubTotal(catType,amount){
    let itemIndex = this.mcdSubTotal?this.mcdSubTotal.findIndex(item => item.cat_type == catType):'-1';
    if(itemIndex >= 0){
      this.mcdSubTotal[itemIndex].actual_amount = amount;
    }else{
      this.mcdSubTotal.push({cat_type:catType,'actual_amount':amount});
    }
    this.netProfit();
  }


  netProfit(){
    let totalATobCost=this.netProfitPipe.transform(this.mcdSubTotal,['purchase_price','a_to_b','settlement','purchase_adjustment','total_fees','nunhud']);
    let totalBTobSale=((this.netProfitPipe.transform(this.mcdSubTotal,['selling_price'])-this.netProfitPipe.transform(this.mcdSubTotal,['b_to_c']))
    +this.netProfitPipe.transform(this.mcdSubTotal,['btoc_settlement','btoc_adjustment','other_income']));
    
    
    this.netProfitBeforeFcost=totalBTobSale-totalATobCost;
    this.netProfitAmount=this.netProfitBeforeFcost-this.netProfitPipe.transform(this.mcdSubTotal,['financing_cost']);
    this.communicationService.setNetProfit(this.netProfitAmount);
  } 

  autoSave(data){
    if(this.viewAccessOnly){
      this.alertService.error("You haven't access to update this record");
      return;
    }
    this.commonActivityService.addLoader(data['el']);
    this.commonActivityService.removeElement(data['el'],'saved-icon');
    
    let index=data.el.parentElement.parentElement.getAttribute('id');
    
    let saveInfo:any={'house_id':this.property_id,'name':data['name'],'value':data['value']};
    this.updatePayoutInfo(saveInfo,data);

  }

  updatePayoutInfo(saveInfo,data){

      let url = apiUrl.payout;
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
            this.loading=false;
          },
          error => {
            this.loading = false;
            this.commonActivityService.removeElement(data['el'],'loader-icon');
            this.alertService.common(error);
          }
      ); 
  }
  

  purchaseAmountAtoB($event){
    this.addUpdateMcdSubTotal('purchase_price',$event??0);
    this.totalPurchaseAmountAtoB=$event??0;
  }
  totalAmountPayout($event){
    this.addUpdateMcdSubTotal('a_to_b',$event);
    this.totalAmountAtoB=$event;
  }
  
  totalCreditReceiveAmount($event){
    this.addUpdateMcdSubTotal('settlement',$event);
    this.totalAmountCreditReceive=$event;
  }

  totalAdjustmentsSettlementAmount($event){
    this.addUpdateMcdSubTotal('adjustment',$event);
    this.totalAdjustmentAmount=$event;
  }

  totalPurchaseAdj($event){
    this.addUpdateMcdSubTotal('purchase_adjustment',$event);
    this.totalPurchaseAdjuestment=$event;
  }
  totalFee($event){
    this.addUpdateMcdSubTotal('total_fees',$event);
    this.totalFeeAmount=$event;
  }
  totalNonHud($event){
    this.addUpdateMcdSubTotal('nunhud',$event);
    this.totalNonHudAmount=$event;
  }

  totalSellingPriceBtoC($event){
    this.addUpdateMcdSubTotal('b_to_c',$event);
    this.totalPriceBtoC=$event;
  }
  totalCreditReceived($event){
    this.addUpdateMcdSubTotal('btoc_settlement',$event);
    this.totalCreditReceivedBtoC=$event;
  }
  totalIncomeAdjustment($event){
    this.addUpdateMcdSubTotal('btoc_adjustment',$event);
    this.totalIncomeAdjustmentBtoC=$event;
  }
  otherSaleIncome($event){
    this.totalOtherSaleIncome=$event;
  }
  otherIncome($event){
    this.addUpdateMcdSubTotal('other_income',$event);
    this.totalOtherIncome=$event;
  }

  getMcdLender(){
    let url = apiUrl.mcd_lender_detail+'/'+this.property_id;
    this.commonApplicationService.get(url).subscribe(response => {
      this.mcdLenderResult=response.row;

      let totalContributed=0; let totaldistributed=0;
      this.mcdLenderResult.forEach(element => {
        totalContributed+=+element?.amount;
        totaldistributed+=+element?.returned_amount;
      });
      this.totalFundsPriorClosing=(totalContributed-totaldistributed).toFixed(2);
      this.communicationService.setFundsPriorClosing( this.totalFundsPriorClosing);
    },
    (err: any) => {
    })
  }

  bonusSpreadDetail($event){
    this.bonusSpread=$event;
  }

  

    totalclosingPriorAmount($event){
        this.totalclosingPrior=$event;
    }

    updateMoreInfo($event){
      this.moreFieldDetail.push($event);
    }
    
    ngAfterContentChecked() {
      this.ref.detectChanges();
    }
    calculateNetProfit(){

      let totalAtoB=0;
      let totalBtoC=0;
      this.payoutInfo['payout_detail'].forEach(element => {
        if(this.atobTotal.includes(element.payout_type)){
          totalAtoB+=+element.amount;
        }
        if(this.btocTotal.includes(element.payout_type)){
          totalBtoC+=+element.amount;
        }
      });
      this.payoutForm.get('selling_price_btoc').valueChanges.subscribe(val=>{
        this.addUpdateMcdSubTotal('selling_price',val);
      })

      this.payoutForm.get('financing_cost').valueChanges.subscribe(val=>{
        this.addUpdateMcdSubTotal('financing_cost',val);
      })
      
    }

    get totalFundPrior(){
      let totalSetalMent=this.totalPurchaseAmountAtoB*1+this.totalAmountAtoB*1+ this.totalAmountCreditReceive*1; 
      let total= (this.totalNonHudAmount*1+totalSetalMent*1)-(this.totalFundsPriorClosing*1)
      return total;
    }

    payoutCategoryList(){
      const dialogRef =this.dailog.open(PayoutCategoryListComponent,{ width: '800px',data:{categoryList:this.category_list}});
      dialogRef.afterClosed().subscribe(result => {
        if(result)
          this.category_list=result;
      });
    }

   
}


export class payoutSubTotalKey{
  totalSellingPriceBtoc:number=0;
  totalPurchaseAmountAtoB:number=0;
  totalAmountAtoB:number=0;
  totalAmountCreditReceive:number=0;
  totalAdjustmentAmount:number=0;
  totalPurchaseAdjuestment:number=0;
  totalFeeAmount:number=0;
  totalNonHudAmount:number=0;
  totalPriceBtoC:number=0;
  totalCreditReceivedBtoC:number=0;
  totalIncomeAdjustmentBtoC:number=0;
  totalOtherSaleIncome:number=0;
  totalOtherIncome:number=0;
  bonusSpread:number=0;
  totalclosingPrior:number=0;
  totalFinancingCost:number=0;
}
