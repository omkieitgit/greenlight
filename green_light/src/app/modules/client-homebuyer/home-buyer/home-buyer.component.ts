import { ChangeDetectionStrategy, ChangeDetectorRef, Component, Input, OnInit, SimpleChanges } from '@angular/core';
import { apiUrl } from '@config/api-url';
import { SumPipe } from '@shared-modules/directives/sum.pipe';
import { CommonApplicationService, CommunicationService } from '@shared-service/_services';
import { StorageService } from '@shared-service/_services/storage.service';
import { FilterPipe } from '../filter.pipe';

@Component({
  selector: 'app-home-buyer',
  templateUrl: './home-buyer.component.html',
  styleUrls: ['./home-buyer.component.css'],
  //changeDetection: ChangeDetectionStrategy.OnPush

})
export class HomeBuyerComponent implements OnInit {

  @Input() property_id:string;
  @Input() payoutDetail:any;
  @Input() renoHomeBuyerCategory:any;
  category_list:any;
  payoutInfo:any;
  totalPayoutAmount:any=[];

  payoutSummery:PayoutAmount;
  amountType:AmountType;

  borderClass:string='text-box-border';
  isBorderFont:boolean=true;
  //font-weight-normal


  costType:any=['est_amount','amount','buyer_amount'];

  payoutCategory:any=[
    {'key':'a_to_b','title':'Costs paid out of closing Hud A to B:','total_title':'Total costs to Buy A to B'},
    {'key':'settlement','title':'Credits received on settlement:','total_title':'Total credits received on settlement'},
    {'key':'adjustment','title':'Cash Adjustments:','total_title':'Total Cash Adjustments on Settlement'},
    {'key':'b_to_c','title':'Less:Costs paid out of closing Hud B to C:','total_title':'Total Cost to Sell B to C'},
    {'key':'btoc_settlement','title':'Credits received on settlement:','total_title':'Total Credits received on Settlement'},
    {'key':'btoc_adjustment','title':'Income Statement Adjustments:','total_title':'Total Income Statement Adjustment'},
  ];

  payoutOtherDetail:any;
  totalFundsPriorClosing:number;

  additionDetail:any=[
        'Other Income',
        'Insurance Claim',
        'Long Term Rental'
    ];
  estAmountEditAccess:boolean=false;

  constructor(private commonApplicationService:CommonApplicationService,
             public sumPipe:SumPipe,
             private filterPipe:FilterPipe,
             private ref: ChangeDetectorRef,
             private storageService:StorageService,
             private communicationService:CommunicationService) { 
               this.amountType=new AmountType();
               this.payoutSummery=new PayoutAmount();
             }

  ngOnInit(): void {
    this.estAmountEditAccess=this.storageService.getHard('ac_view_access');
    this.payoutInfo=this.payoutDetail.payout;
    if(this.payoutInfo?.addition_field){
      //this.payoutInfo.addition_field=this.payoutInfo?.addition_field;
      this.payoutInfo.addition_field_info=this.payoutInfo?.addition_field.filter(items => items.field_type ==='otherincome');
    }
    if(this.payoutInfo && this.payoutInfo?.total_air_bnb?.length>0){
      this.payoutInfo.airbnb_amount=this.payoutInfo?this.payoutInfo?.total_air_bnb[0]?.totalAmountReceived:0;
      for(let i=0; i<this.additionDetail.length; i++){
        let index=this.payoutInfo?this.payoutInfo.addition_field_info.findIndex(item=>item.field_name==this.additionDetail[i]):-1;
        if(index==-1){
          let detail={buyer_amount:0,est_amount:0,field_name: this.additionDetail[i],field_type: "otherincome",field_value: 0,house_id: this.property_id};
          this.payoutInfo.addition_field_info.push(detail);
        }
      }
    }else{
      this.payoutInfo.airbnb_amount=0;
      this.defaultOtherDetail();
    }
    

    this.category_list=this.payoutDetail.category;
    this.payoutOtherDetail=this.getPayoutDetail();
    this.payoutOtherDetail.payout_top.push(this.dayStartToFinish());
    this.communicationService.getFundsPriorClosing().subscribe(res=>{
      this.totalFundsPriorClosing=res;
    });

   
  }

  

  defaultOtherDetail(){
    let otherInfo:any=[];
      for(let i=0; i<this.additionDetail.length; i++){
          let detail={buyer_amount:0,est_amount:0,field_name: this.additionDetail[i],field_type: "otherincome",field_value: 0,house_id: this.property_id};
          otherInfo.push(detail);
      }
      this.payoutInfo.addition_field_info=otherInfo;
  }

  panelExpand(flag){
   
  }

  dayStartToFinish(){
    let payOutTop= {
                    'name':'Days Start to Finish',
                    'amount':this.onInputFieldChanged(this.payoutInfo.close_date_a_to_b,this.payoutInfo.close_date_b_to_c),
                    'est_amount':this.onInputFieldChanged(this.payoutInfo.est_close_date_a_to_b,this.payoutInfo?.est_close_date_b_to_c),
                    'buyer_amount':this.onInputFieldChanged(this.payoutInfo.buyer_close_date_a_to_b,this.payoutInfo?.buyer_close_date_b_to_c),
                    'diff_amount':this.getDiffamount(this.onInputFieldChanged(this.payoutInfo.close_date_a_to_b,this.payoutInfo.close_date_b_to_c),this.onInputFieldChanged(this.payoutInfo.est_close_date_a_to_b,this.payoutInfo?.est_close_date_b_to_c)),
                    'disableAll':true,
                    'isCurrencyFormat':false,
                    'day_to_finish':true 
                    };

              return payOutTop;
    
  }
  updateDate($event){
    this.payoutInfo[$event.key_name[$event.name]]=$event.value;
    let index=this.payoutOtherDetail.payout_top.findIndex(res=>res.day_to_finish);
    this.payoutOtherDetail.payout_top[index]=this.dayStartToFinish();
  }

  getPayoutDetail(){

    
    let payoutDetail={
      'payout_top':[
        {
          'name':'Close Date A to B',
          'amount':this.payoutInfo.close_date_a_to_b,
          'est_amount':this.payoutInfo.est_close_date_a_to_b?{jsdate: new Date(this.payoutInfo.est_close_date_a_to_b)}:null,
          'buyer_amount':this.payoutInfo.buyer_close_date_a_to_b?{jsdate: new Date(this.payoutInfo.buyer_close_date_a_to_b)}:null,
          'diff_amount':this.onInputFieldChanged(this.payoutInfo.close_date_a_to_b,this.payoutInfo?.est_close_date_a_to_b),
          'key_name':{'est_amount':'est_close_date_a_to_b','buyer_amount':'buyer_close_date_a_to_b'},
          'category_type':'payout_top',
          'input':'date',
          'isCurrencyFormat':false
        },
        {
          'name':'Close Date B to C',
          'amount':this.payoutInfo.close_date_b_to_c,
          'est_amount':this.payoutInfo.est_close_date_b_to_c?{jsdate: new Date(this.payoutInfo.est_close_date_b_to_c)}:null,
          'buyer_amount':this.payoutInfo.buyer_close_date_b_to_c?{jsdate: new Date(this.payoutInfo.buyer_close_date_b_to_c)}:null,
          'diff_amount':this.onInputFieldChanged(this.payoutInfo.close_date_b_to_c,this.payoutInfo?.est_close_date_b_to_c),
          'key_name':{'est_amount':'est_close_date_b_to_c','buyer_amount':'buyer_close_date_b_to_c'},
          'category_type':'payout_top',
          'input':'date',
          'isCurrencyFormat':false  
        },
        // {'name':'Days Start to Finish',
        //  'amount':this.onInputFieldChanged(this.payoutInfo.close_date_a_to_b,this.payoutInfo.close_date_b_to_c),
        //  'est_amount':this.onInputFieldChanged(this.payoutInfo.est_close_date_a_to_b,this.payoutInfo?.est_close_date_b_to_c),
        //  'buyer_amount':this.onInputFieldChanged(this.payoutInfo.buyer_close_date_a_to_b,this.payoutInfo?.buyer_close_date_b_to_c),
        //  'diff_amount':this.getDiffamount(this.onInputFieldChanged(this.payoutInfo.close_date_a_to_b,this.payoutInfo.close_date_b_to_c),this.onInputFieldChanged(this.payoutInfo.est_close_date_a_to_b,this.payoutInfo?.est_close_date_b_to_c)),
        //   'disableAll':true,
        //   'isCurrencyFormat':false  
        // },
      ],
      'purchase_price':[
          {'name':'Purchase Price',
          'amount':this.getPayoutVal(this.payoutInfo.purchase_price),
          'est_amount':this.getPayoutVal(this.payoutInfo.est_purchase_price),
          'buyer_amount':this.getPayoutVal(this.payoutInfo.buyer_purchase_price),
          'diff_amount':this.getDiffamount(this.payoutInfo.purchase_price,this.payoutInfo?.est_purchase_price),
          'key_name':{'est_amount':'est_purchase_price','buyer_amount':'buyer_purchase_price'},
          'category_type':'purchase_price',
          'isCurrencyFormat':true  
          }
      ],
      'payout_selling':[
        {'name':'Selling Price B to C',
         'amount':this.getPayoutVal(this.payoutInfo.selling_price_btoc),
         'est_amount':this.getPayoutVal(this.payoutInfo.est_selling_price_btoc),
         'buyer_amount':this.getPayoutVal(this.payoutInfo.buyer_selling_price_btoc),
         'diff_amount':this.getDiffamount(this.payoutInfo.selling_price_btoc,this.payoutInfo?.est_selling_price_btoc),
         'key_name':{'est_amount':'est_selling_price_btoc','buyer_amount':'buyer_selling_price_btoc'},
         'category_type':'payout_selling',
         'isCurrencyFormat':true
        },
      ],
      'llc_fee':[
          {
            'name':'Travel/Office Fee',
            'amount':this.getPayoutVal(this.payoutInfo.travel_office_fee),
            'est_amount':this.getPayoutVal(this.payoutInfo?.est_travel_office_fee),
            'buyer_amount':this.getPayoutVal(this.payoutInfo?.buyer_travel_office_fee),
            'diff_amount':this.getDiffamount(this.payoutInfo.travel_office_fee,this.payoutInfo?.est_travel_office_fee),
            'key_name':{'est_amount':'est_travel_office_fee','buyer_amount':'buyer_travel_office_fee'},
            'category_type':'llc_fee',
            'isCurrencyFormat':true
          },
          {
            'name':'Labor Charges',
            'amount':this.getPayoutVal(this.payoutInfo.labor_charges),
            'est_amount':this.getPayoutVal(this.payoutInfo?.est_labor_charges),
            'buyer_amount':this.getPayoutVal(this.payoutInfo?.buyer_labor_charges),
            'diff_amount':this.getDiffamount(this.payoutInfo.labor_charges,this.payoutInfo?.est_labor_charges),
            'key_name':{'est_amount':'est_labor_charges','buyer_amount':'est_labor_charges'},
            'category_type':'llc_fee',
            'isCurrencyFormat':true
          },
       ],
       'estates_fee':[
          {
            'name':'Office Fee',
            'amount':this.getPayoutVal(this.payoutInfo.office_fee),
            'est_amount':this.getPayoutVal(this.payoutInfo?.est_office_fee),
            'buyer_amount':this.getPayoutVal(this.payoutInfo?.buyer_office_fee),
            'diff_amount':this.getDiffamount(this.payoutInfo.office_fee,this.payoutInfo?.est_office_fee),
            'key_name':{'est_amount':'est_office_fee','buyer_amount':'buyer_office_fee'},
            'category_type':'estates_fee',
            'isCurrencyFormat':true
          },
          {
            'name':'Bookkeeping Fee',
            'amount':this.getPayoutVal(this.payoutInfo.bookkeeping_fee),
            'est_amount':this.getPayoutVal(this.payoutInfo?.est_bookkeeping_fee),
            'buyer_amount':this.getPayoutVal(this.payoutInfo?.buyer_bookkeeping_fee),
            'diff_amount':this.getDiffamount(this.payoutInfo.bookkeeping_fee,this.payoutInfo?.est_bookkeeping_fee),
            'key_name':{'est_amount':'est_bookkeeping_fee','buyer_amount':'buyer_bookkeeping_fee'},
            'category_type':'estates_fee',
            'isCurrencyFormat':true
          },
          {
            'name':'Web & Data Fees',
            'amount':this.getPayoutVal(this.payoutInfo.web_fee),
            'est_amount':this.getPayoutVal(this.payoutInfo?.est_web_fee),
            'buyer_amount':this.getPayoutVal(this.payoutInfo?.buyer_web_fee),
            'diff_amount':this.getDiffamount(this.payoutInfo.web_fee,this.payoutInfo?.est_web_fee),
            'key_name':{'est_amount':'est_web_fee','buyer_amount':'buyer_web_fee'},
            'category_type':'estates_fee',
            'isCurrencyFormat':true
          },
       ],
       'purchase_cost':[
           {
             'name':'AA Fee',
             'amount':this.getPayoutVal(this.payoutInfo.aa_fee),
             'est_amount':this.getPayoutVal(this.payoutInfo?.est_aa_fee),
             'buyer_amount':this.getPayoutVal(this.payoutInfo?.buyer_aa_fee),
             'diff_amount':this.getDiffamount(this.payoutInfo.aa_fee,this.payoutInfo?.est_aa_fee),
             'key_name':{'est_amount':'est_aa_fee','buyer_amount':'buyer_aa_fee'},
             'category_type':'purchase_cost',
             'isCurrencyFormat':true
            },
           {
              'name':'Buyer Referral Fee',
              'amount':this.getPayoutVal(this.payoutInfo.buyer_ref_fee),
              'est_amount':this.getPayoutVal(this.payoutInfo?.est_buyer_ref_fee),
              'buyer_amount':this.getPayoutVal(this.payoutInfo?.homebuyer_ref_fee),
              'diff_amount':this.getDiffamount(this.payoutInfo.buyer_ref_fee,this.payoutInfo?.est_buyer_ref_fee),
              'key_name':{'est_amount':'est_buyer_ref_fee','buyer_amount':'homebuyer_ref_fee'},
              'category_type':'purchase_cost',
              'isCurrencyFormat':true
            },
           {
             'name':'Bonus on Spread',
             'amount':this.getPayoutVal(this.payoutInfo.bonus_on_spread),
             'est_amount':this.getPayoutVal(this.payoutInfo?.est_bonus_on_spread),
             'buyer_amount':this.getPayoutVal(this.payoutInfo?.buyer_bonus_on_spread),
             'diff_amount':this.getDiffamount(this.payoutInfo.bonus_on_spread,this.payoutInfo?.est_bonus_on_spread),
             'key_name':{'est_amount':'est_bonus_on_spread','buyer_amount':'buyer_bonus_on_spread'},
             'category_type':'purchase_cost',
             'isCurrencyFormat':true
            },
       ]
     }
     return payoutDetail;
  }

  onInputFieldChanged(fromDate,toDate) {
    var days = this.datediff(this.parseDate(fromDate),this.parseDate(toDate));
    if(days){
      return days;
    }else{return 0;}
  }

  datediff(first, second) {
    // Take the difference between the dates and divide by milliseconds per day.
    // Round to nearest whole number to deal with DST.
    return Math.round((second-first)/(1000*60*60*24));
  }
  parseDate(str) {
    if(str){
      //var mdy = str.split('/');
      return new Date(str);
    }
  }

  getPayout(){

    let url = apiUrl.payout+'/'+this.property_id;
    this.commonApplicationService.get(url).subscribe(response => {

    },
    (err: any) => {})
  }

  totalEstimateAmount($event){
      console.log($event);
      let updateVal=$event;
      this.payoutOtherDetail[updateVal.category_type].find(a=>a.key_name[updateVal.name]==updateVal.keyname)[updateVal.name]=updateVal.amount;
      this.totalCostAtoB();
  }

  updateFundPriorClosing($event){
    this.payoutInfo[$event.name]=$event.value;
    //this.totalCostAtoB();
}


  getDiffamount(val1,val2){
    return (val2?val2:0)-(val1?val1:0);
  }

  getPayoutVal(amount){
    return (amount?amount:0) 
  }

  totalAmount(event){

    let index=this.totalPayoutAmount.findIndex(total=>total.category_type==event.category_type);
    if(index>=0){
      this.totalPayoutAmount[index]=event;
    }else{
      this.totalPayoutAmount.push(event);
    }
    this.totalCostAtoB();
   
  }

  additionAmount($event){
    let index=this.payoutInfo.addition_field.findIndex(item=>item.id===$event.id);
    if(index>=0){
      this.payoutInfo.addition_field[index]=$event;
    }
   // this.totalCostAtoB();
  }

  totalCostAtoB(){

     let amountType=new AmountType();
     let fundAmountType=new AmountType();
     this.costType.forEach(element => {
        let totalEstCostAtob=(parseFloat(this.payoutOtherDetail?.purchase_price?this.payoutOtherDetail.purchase_price[0][element]:0)+
                this.sumPipe.transform( (this.filterPipe.transform(this.totalPayoutAmount,['a_to_b','settlement'])), element) + 
                this.sumPipe.transform(this.payoutOtherDetail.purchase_cost,element) +
                this.sumPipe.transform(this.payoutOtherDetail.llc_fee,element) + 
                this.sumPipe.transform(this.payoutOtherDetail.estates_fee,element)+
                this.sumPipe.transform( (this.filterPipe.transform(this.totalPayoutAmount,['nonhud'])), element)
              );
              amountType[element]=totalEstCostAtob.toFixed(2);
              
        let totalAtoBFund=(parseFloat(this.payoutOtherDetail?.purchase_price?this.payoutOtherDetail.purchase_price[0][element]:0)+
              this.sumPipe.transform( (this.filterPipe.transform(this.totalPayoutAmount,['a_to_b','settlement'])), element) + 
              this.sumPipe.transform( (this.filterPipe.transform(this.totalPayoutAmount,['nonhud'])), element)
            );     
            fundAmountType[element]=totalAtoBFund.toFixed(2);
      });
      this.payoutSummery.totalAtoBCost=amountType;
      this.payoutSummery.totalFundCostAtob=fundAmountType;
      
      this.totalCostBtoC();
  }

  totalCostBtoC(){
    this.costType.forEach(elm => {
       let totalEstCostbtoc=(parseFloat(this.payoutOtherDetail?.payout_selling?this.payoutOtherDetail.payout_selling[0][elm]:0)-
               this.sumPipe.transform( (this.filterPipe.transform(this.totalPayoutAmount,['b_to_c'])), elm))+
               this.sumPipe.transform( (this.filterPipe.transform(this.totalPayoutAmount,['btoc_settlement','btoc_adjustment'])), elm);

       this.amountType[elm]=totalEstCostbtoc.toFixed(2);       
     });
     this.payoutSummery.totalBtoCCost=this.amountType;
     this.ref.detectChanges();
 }

 

}


export class AmountType {
  est_amount : number;
  amount:number;
  buyer_amount:number;
}

export class PayoutAmount {
  totalAtoBCost : AmountType;
  totalBtoCCost:AmountType;
  totalFundCostAtob:AmountType;
  
}

export class payoutDetail{ 
  payout_top:payoutMoreDetail[];
  llc_fee:payoutMoreDetail[];
  estates_fee:payoutMoreDetail[];
  purchase_cost:payoutMoreDetail[];
}

export class payoutMoreDetail{
  name:string;
  amount:number=0;
  category_type:string='';
}

