import { Component, OnInit ,Inject} from '@angular/core';
import {IMyDpOptions, IMyDateModel} from 'mydatepicker';
import { FormGroup,FormBuilder } from '@angular/forms';
import {MatDialog, MatDialogRef, MAT_DIALOG_DATA} from '@angular/material/dialog';
import {EstimateLatePaymentService} from './estimated-late-payments.service';
import { MessageService,AlertService,} from '@shared-service/_services';

import { Subscription } from 'rxjs';
@Component({
  selector: 'app-estimated-late-payments',
  templateUrl: './estimated-late-payments.component.html',
  styleUrls: ['./estimated-late-payments.component.css']
})
export class EstimatedLatePaymentsComponent implements OnInit {

  estimatedLatePaymentForm:FormGroup;
  final_result:any;
  subscribedData: any;
  subscription: Subscription;

  constructor(private formBuilder:FormBuilder,
              @Inject(MAT_DIALOG_DATA) public data,
              private messageService:MessageService,
              public dialogRef: MatDialogRef<EstimatedLatePaymentsComponent>,
              private estimateLatePaymentService:EstimateLatePaymentService) { }


  public myDatePickerOptions: IMyDpOptions = { dateFormat: 'yyyy-mm-dd',firstDayOfWeek : 'su'};

  onDateChanged(event: IMyDateModel) {
      return event.formatted;
  }

  ngOnInit() {
    this.subscription = this.messageService.getArrData().subscribe(message => { this.subscribedData = message; });
    this.calulate();
  }

  intialize(){
    //console.log(this.final_result);
    this.estimatedLatePaymentForm = this.formBuilder.group({
      _str_date: [this.final_result.str_date?{jsdate: new Date(this.final_result.str_date)}:null],
      _back_str_date: [this.final_result.back_date?{jsdate: new Date(this.final_result.back_date)}:null],
      _current_date: [this.final_result.current_date?{jsdate: new Date(this.final_result.current_date)}:null],
      _total_month_estimated: [this.final_result.calculatedDate?this.final_result.calculatedDate:0],
      _monthly_payment: [this.final_result.emi_str?this.final_result.emi_str:0],
      _attorney_fees: [this.final_result.attonyFee?this.final_result.attonyFee:0],
      _estimated_late_fee: [this.final_result.estimated_payment?this.final_result.estimated_payment:0],
      _estimated_loan_balance: [this.final_result.end_dd?this.final_result.end_dd:0],
      _total_estimated_debt: [this.final_result.lien_late_attorney_fee?this.final_result.lien_late_attorney_fee:0],
      _estimated_equity: [this.final_result.est_equity?this.final_result.est_equity:0],
      _cma_arv_val:[this.final_result.cma_cva?this.final_result.cma_cva:0]

    });
  }

  calulate(){
    this.final_result=this.estimateLatePaymentService.calculateEstimate(this.data);
    this.intialize();
  }
  saveInfo(){
    this.dialogRef.close(this.final_result);
  }
}
