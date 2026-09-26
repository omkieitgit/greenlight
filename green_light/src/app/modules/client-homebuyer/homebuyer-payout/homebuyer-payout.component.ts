import { Component, ElementRef, EventEmitter, Input, OnInit, Output, ViewChild } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { apiUrl } from '@config/api-url';
import { CurrencyFormatPipe } from '@shared-modules/directives/currency-format.pipe';
import { AlertService, CommonActivityService, CommonApplicationService } from '@shared-service/_services';
import {HomeBuyerService} from '../home-buyer.service';
import {IMyDpOptions, IMyDateModel} from 'mydatepicker';
import { CurrencyPipe, DatePipe } from '@angular/common';
import { StorageService } from '@shared-service/_services/storage.service';

@Component({
  selector: 'app-homebuyer-payout',
  templateUrl: './homebuyer-payout.component.html',
  styleUrls: ['./homebuyer-payout.component.css']
})
export class HomebuyerPayoutComponent implements OnInit {
  
  @Input() payoutdetail:any;
  @Input() borderClass:string;
  @Input() property_id;

  total_est:number=0;
  total_act:number=0;
  total_calc:number=0;
  homebuyerPayoutForm:FormGroup;
  @Output() estimatedAmount = new EventEmitter<any>();
  @ViewChild('diff_amount') diffVal:ElementRef;
  estAmountEditAccess:boolean=false;
  @Output() emittedUpdateDate = new EventEmitter<any>();

  constructor(private homeBuyerService:HomeBuyerService,
              private fb: FormBuilder,
              private cp:CurrencyFormatPipe,
              private commonApplicationService:CommonApplicationService,
              private commonActivityService:CommonActivityService,
              private alertService:AlertService,
              private storageService:StorageService,
              private datePipe:DatePipe) { }

  ngOnInit(): void {
    //this.estAmountEditAccess=this.storageService.getHard('ac_view_access');
    this.homebuyerPayoutForm=this.fb.group({
      est_amount:[{value:this.formatedValue(this.payoutdetail.est_amount),disabled:this.payoutdetail?.disableAll} ],
      actual_amount: [{value:this.formatedValue(this.payoutdetail.amount), disabled: true}],
      diff_amount:[{value:this.formatedValue(this.payoutdetail.diff_amount), disabled: true}],
      buyer_amount:[{value:this.formatedValue(this.payoutdetail.buyer_amount),disabled:this.payoutdetail?.disableAll}],
      key_name:[this.payoutdetail.key_name]
    });

    

  }

  formatedValue(amount){
    if(this.payoutdetail.isCurrencyFormat){
      return this.cp.transformVal(amount);
    }else
    {
      return amount;
    }
  }

  onDateChanged(event: IMyDateModel,dateInput) {
    let name=dateInput.elem.nativeElement.getAttribute('formcontrolname');
    let formatedDate=this.datePipe.transform(event.formatted,'yyyy-MM-dd');
    let data={'name':name,'value':formatedDate,'el':dateInput.elem.nativeElement};
    if(name=='est_amount'){
      this.homebuyerPayoutForm.get('diff_amount').setValue(this.onInputFieldChanged(this.payoutdetail.amount,formatedDate));

    }
    data['key_name']=this.payoutdetail.key_name;
    this.emittedUpdateDate.emit(data);
    this.autoSave(data);
    return event.formatted;
  }

  diffCalculation(est_amount,act_amount,diff_amount){
    if(this.payoutdetail.isCurrencyFormat){
      est_amount.value=this.cp.detransformVal(est_amount.value);
    }
    this.emitAmountCom(est_amount);
    this.homeBuyerService.calculationDiff(est_amount,act_amount,diff_amount);
    this.calculateTotal();
  }

  calcamount($event,buyer_amount){
    if(isNaN($event.target.amount)===false){
      this.emitAmountCom(buyer_amount);
      $event.target.amount=this.cp.transformVal($event.target.amount);
    }else{
      $event.target.amount='';
    }
    this.calculateTotal();
  }

  emitAmountCom(est_amount){
    let keyName=this.homebuyerPayoutForm.get('key_name').value;  
    let name=est_amount.getAttribute('formcontrolname');
    this.estimatedAmount.emit({'name':name,amount:est_amount.value,keyname:keyName[name],category_type:this.payoutdetail.category_type});
  }

  calculateTotal(){
    this.total_est=this.homeBuyerService.calculateTotalCost('_est_amount');
    this.total_act=this.homeBuyerService.calculateTotalCost('_act_amount');
    this.total_calc=this.homeBuyerService.calculateTotalCost('_calc_amount');
    this.homeBuyerService.setTotalAtoB({"total_est_atob":this.total_est,
    "total_act_atob":this.total_act,"total_calc_atob":this.total_calc});
  }

  autoSave(data){
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
    let keyName=this.homebuyerPayoutForm.get('key_name').value;  
    let saveInfo:any={'house_id':this.property_id,'name':keyName[data['name']],'value':data['value']??0};
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
         // this.loading=false;
        },
        error => {
        //  this.loading = false;
          this.commonActivityService.removeElement(data['el'],'loader-icon');
          this.alertService.common(error);
        }
    ); 
  }

  onInputFieldChanged(fromdate,todate) {
    var days = this.datediff(this.parseDate(fromdate),this.parseDate(todate));
    if(days){
      return days;
    }else{return 0;}
  }

  datediff(first, second) {
    return Math.round((second-first)/(1000*60*60*24));
  }
  parseDate(str) {
    if(str){
      //var mdy = str.split('/');
      return new Date(str);
    }
  }
}
