import { ChangeDetectorRef, Component, EventEmitter, Input, OnInit, Output, SimpleChanges } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { AlertService, apiUrl, CommonActivityService, CommonApplicationService, CommunicationService } from '@shared-service/_services';
import { StorageService } from '@shared-service/_services/storage.service';
import { take } from 'rxjs/operators';

@Component({
  selector: 'app-purchase-adjustments',
  templateUrl: './purchase-adjustments.component.html',
  styleUrls: ['./purchase-adjustments.component.css']
})
export class PurchaseAdjustmentsComponent implements OnInit {
  
  @Input() property_id:string;
  @Input() payoutInfo;
  @Input() viewAccessOnly;
  
  purchaseAdjForm:FormGroup;
  loading: boolean;
  purchaseAdj:Array<string>=['aa_fee','buyer_ref_fee','bonus_on_spread'];
  totalPurchAdj:number;
  netProfitAmount:number=0;
  bonusSpreadAmount:number=0;
  aaFee:number=0;

  @Output() totalPurchaseAdjustment = new EventEmitter<number>();
  @Output() bonusSpread = new EventEmitter<number>();

  constructor(private formBuilder:FormBuilder,
              private commonActivityService:CommonActivityService,
              private commonApplicationService:CommonApplicationService,
              private alertService:AlertService,
              private storageService:StorageService,
              private communicationService:CommunicationService
              ) { }

  ngOnInit(): void {

    let bonusSpread=0;//this.calculateBonusSpread();
    
    this.purchaseAdjForm = this.formBuilder.group({
      house_id:[this.property_id],
      aa_fee:[this.payoutInfo.aa_fee],
      aa_fee_paid:[this.payoutInfo.aa_fee_paid?this.payoutInfo.aa_fee_paid:false],
      buyer_ref_fee:[this.payoutInfo.buyer_ref_fee],
      buyer_ref_fee_paid:[this.payoutInfo.buyer_ref_fee_paid],
      bonus_on_spread:[this.payoutInfo.bonus_on_spread?this.payoutInfo.bonus_on_spread:bonusSpread],
    });
    
    this.purchaseAdjForm.valueChanges.subscribe(response=>{
          this.totalPurchAdj =0;
          Object.keys(response).forEach((key)=>{ 

            if((this.purchaseAdj.includes(key))&& response[key]){
              if (this.purchaseAdjForm.controls[key]) {
                  totalPurchaseAdj = parseFloat(response[key]);
                  this.totalPurchAdj += parseFloat(response[key]);;
              }
          }
          this.totalPurchaseAdjustment.emit(this.totalPurchAdj);
        })
    });

    let totalPurchaseAdj:number=0;
    for (let field in this.purchaseAdjForm.controls) { 
      if(this.purchaseAdj.includes(field) && this.purchaseAdjForm.controls[field].value){
        totalPurchaseAdj += parseFloat(this.purchaseAdjForm.controls[field].value);
        this.totalPurchAdj=totalPurchaseAdj;
      }
    }
    this.totalPurchaseAdjustment.emit(totalPurchaseAdj);
    this.bonusSpread.emit(this.payoutInfo?.bonus_on_spread);
   
    //console.log(totalPurchaseAdj);

    
  }
  ngAfterViewInit(){
    this.purchaseAdjForm.get('bonus_on_spread').valueChanges.subscribe(val=>{
      this.bonusSpread.emit(val);
    })
    this.aaFee=this.getAaFee();

    this.communicationService.getNetProfit().subscribe(response=>
      {
        let bonusSpread=0;
        if(response>5000){
          bonusSpread=((Math.trunc((response/10000))+1)*500)-250;
        }
        if(this.bonusSpreadAmount!=bonusSpread){
          //this.purchaseAdjForm.get('bonus_on_spread').setValue(bonusSpread);
          this.bonusSpreadAmount=bonusSpread;
        }
      });
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

  alreadyPaid($event,field,fieldEvent){

    let value=(field=='aa_fee')?750:500;
    $event.target.value=0;
    if($event.target.checked){
      value=0;
      $event.target.value=1;
    }
    this.purchaseAdjForm.get(field).setValue(value);
    let data={'name':field,'value':value,'el':fieldEvent};
    this.autoSave(data);
  }

  getAaFee(){
    let cmaArv=this.storageService.getHard('property_info').recommended_cma_arv;
    let aaFee=0;
    if(cmaArv < 100000){
      aaFee=750;
    }else if(cmaArv > 100000 && 149000 >cmaArv){
      aaFee=1250;
    }
    else if(cmaArv > 150000 && 249000 >cmaArv){
      aaFee=1500;
    }
    else if(cmaArv > 250000 && 349000 >cmaArv){
      aaFee=2000;
    }
    else if(cmaArv >350000 && 499000 >cmaArv){
      aaFee=2500;
    }
    else if(cmaArv >500000){
      aaFee= Math.trunc(cmaArv/100000)*500;
    }

    return aaFee;
  }
  
}
