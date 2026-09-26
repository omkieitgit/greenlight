import { Component, EventEmitter, Input, OnInit, Output } from '@angular/core';
import { FormBuilder, FormGroup } from '@angular/forms';
import { apiUrl } from '@config/api-url';
import { CurrencyFormatPipe } from '@shared-modules/directives/currency-format.pipe';
import { AlertService, CommonActivityService, CommonApplicationService } from '@shared-service/_services';
import { StorageService } from '@shared-service/_services/storage.service';
import { HomeBuyerService } from '../home-buyer.service';

@Component({
  selector: 'app-addition-field',
  templateUrl: './addition-field.component.html',
  styleUrls: ['./addition-field.component.css']
})
export class AdditionFieldComponent implements OnInit {
  
  @Input() otherDetail;
  @Input() property_id;
  additionalFieldForm:FormGroup;
  total_est:number=0;
  total_act:number=0;
  total_calc:number=0;
  @Output() estimatedAmount = new EventEmitter<any>();
  estAmountEditAccess:boolean=false;

  constructor(private homeBuyerService:HomeBuyerService,
              private fb: FormBuilder,
              private cp:CurrencyFormatPipe,
              private commonApplicationService:CommonApplicationService,
              private commonActivityService:CommonActivityService,
              private storageService:StorageService,
              private alertService:AlertService) { }

  ngOnInit(): void {
    this.estAmountEditAccess=this.storageService.getHard('ac_view_access');
    this.additionalFieldForm=this.fb.group({
      est_amount:[this.cp.transformVal(this.otherDetail.est_amount) ],
      actual_amount: [{value:this.cp.transformVal(this.otherDetail.field_value), disabled: true}],
      diff_amount:[{value:(this.cp.transformVal(this.otherDetail.est_amount-this.otherDetail.field_value)), disabled: true}],
      buyer_amount:[this.cp.transformVal(this.otherDetail.buyer_amount)],
 
    });
  }

  diffCalculation(est_amount,act_amount,diff_amount){
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
     let name=est_amount.getAttribute('formcontrolname');
    this.otherDetail[name]=est_amount.value;
    //let keyName=this.additionalFieldForm.get('key_name').value;  
     this.estimatedAmount.emit(this.otherDetail);
  }

  calculateTotal(){
    this.total_est=this.homeBuyerService.calculateTotalCost('_est_amount');
    this.total_act=this.homeBuyerService.calculateTotalCost('_act_amount');
    this.total_calc=this.homeBuyerService.calculateTotalCost('_calc_amount');
    this.homeBuyerService.setTotalAtoB({"total_est_atob":this.total_est,
    "total_act_atob":this.total_act,"total_calc_atob":this.total_calc});
  }


  autoSave(data){
    if(this.estAmountEditAccess && data['name']=='est_amount'){
      this.alertService.error("You haven't access to update this record");
      return;
    }
    if(!data['value']){
      this.alertService.error("Please enter amount");
      return;
    }
    this.commonActivityService.addLoader(data['el']);
    this.commonActivityService.removeElement(data['el'],'saved-icon');
    let index=data.el.parentElement.parentElement.getAttribute('id');
    let id = this.otherDetail.id;
    let saveInfo:any={'id':id,'name':data['name'],'value':data['value']??0,field_name:this.otherDetail.field_name,lender_id:0,'field_type':'otherincome'};
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
              this.commonActivityService.addElement(data['el']);
              this.commonActivityService.removeElement(data['el'],'loader-icon');
            }
          },
          error => {
            this.commonActivityService.removeElement(data['el'],'loader-icon');
            this.alertService.common(error);
          }
      ); 
  }
  

}
