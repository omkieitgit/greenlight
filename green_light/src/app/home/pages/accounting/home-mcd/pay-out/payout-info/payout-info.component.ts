import { Component, EventEmitter, Input, OnInit, Output } from '@angular/core';
import { FormBuilder, FormGroup } from '@angular/forms';
import { AlertService, CommonActivityService, CommonApplicationService, CommunicationService } from '@shared-service/_services';
import { IMyDateModel, IMyDpOptions, IMyInputFieldChanged } from 'mydatepicker';
import {apiUrl} from '@config/api-url';
import { DatePipe } from '@angular/common';
import { CommonHelper } from '@shared-service/_utils/CommonHelper';

@Component({
  selector: 'app-payout-info',
  templateUrl: './payout-info.component.html',
  styleUrls: ['./payout-info.component.css']
})
export class PayoutInfoComponent implements OnInit {

  @Input() property_id:string;
  @Input() payoutInfo;
  @Input() openPanel:boolean = false;
  @Input() viewAccessOnly;
  property_info: any;
  
  payoutInfoForm:FormGroup;
  numberdays:number=0;
  numberOfMonth:any=0;
  loading:boolean;
  @Output() totalPurchaseAmount=new EventEmitter<number>();

  constructor(private formBuilder: FormBuilder,
              private commonActivityService:CommonActivityService,
              private alertService:AlertService,
              private commonApplicationService:CommonApplicationService,
              private communicationService:CommunicationService,
              private datePipe:DatePipe) { }

	ngOnInit() {
    
    if(this.openPanel){
      this.payoutInformation();
    }
   
    this.payoutInfoForm.get('purchase_price').valueChanges.subscribe(value=>{
        this.totalPurchaseAmount.emit(value);
    });
    
  }  

  payoutInformation(){

    let closeDateAtoB=this.payoutInfo.close_date_a_to_b?CommonHelper.getConvertDate(this.payoutInfo.close_date_a_to_b):null;
    let closeDateBtoC=this.payoutInfo.close_date_b_to_c?CommonHelper.getConvertDate(this.payoutInfo.close_date_b_to_c):null;

    this.payoutInfoForm = this.formBuilder.group({
      close_date_a_to_b: [(this.payoutInfo.close_date_a_to_b != null)? {jsdate: closeDateAtoB,formatted:this.datePipe.transform(closeDateAtoB,'MM/dd/yyyy')}: null],
      close_date_b_to_c: [(this.payoutInfo.close_date_b_to_c != null)? {jsdate: closeDateBtoC,formatted:this.datePipe.transform(closeDateBtoC,'MM/dd/yyyy')}: null],
      hud_a_to_b:[this.payoutInfo?.hud_a_to_b],
      purchase_price:[this.payoutInfo?.purchase_price]
    });
  }
  
  panelExpand(flag){
    if(!this.openPanel){
      this.openPanel=true;
    }
  }

  public myDatePickerOptions: IMyDpOptions = { dateFormat: 'mm/dd/yyyy',firstDayOfWeek : 'su'};

  onDateChanged(event: IMyDateModel,dateInput) {
    let name=dateInput.elem.nativeElement.getAttribute('formcontrolname');
    let data={'name':name,'value':this.datePipe.transform(event.formatted,'yyyy-MM-dd'),'el':dateInput.elem.nativeElement};
    this.autoSave(data);
    return event.formatted;
  }

  onInputFieldChanged(event: IMyInputFieldChanged) {
    var close_date_b_to_c= this.payoutInfoForm.controls.close_date_b_to_c?.value?.formatted;
    var close_date_a_to_b=this.payoutInfoForm.controls.close_date_a_to_b?.value?.formatted;
    var days = this.datediff(this.parseDate(close_date_a_to_b),this.parseDate(close_date_b_to_c));
    if(days){
      this.numberdays=days;
      this.numberOfMonth=(days/30.5).toFixed(2);
    }
  }
  
  datediff(first, second) {
    // Take the difference between the dates and divide by milliseconds per day.
    // Round to nearest whole number to deal with DST.
    return Math.round((second-first)/(1000*60*60*24));
  }

  parseDate(str) {
    if(str){
      var mdy = str.split('/');
      return new Date(mdy[2], mdy[0]-1, mdy[1]);
    }
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
    this.updateMortgageInfo(saveInfo,data);
  }

  updateMortgageInfo(saveInfo,data){

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

                if(data['name']=='close_date_a_to_b' || data['name']=='close_date_b_to_c'){
                  let dateInfo={close_date_a_to_b:this.payoutInfoForm.get('close_date_a_to_b').value?.formatted,
                                close_date_b_to_c:this.payoutInfoForm.get('close_date_b_to_c').value?.formatted,}
                  this.communicationService.setPayoutInfo(dateInfo);
                }
                //this.mortgageLienOneForm.controls.mortgage_id.setValue(response.data.mortgage_id);
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
