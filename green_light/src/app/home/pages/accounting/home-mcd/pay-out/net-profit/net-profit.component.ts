import { Component, EventEmitter, Input, OnInit, Output, SimpleChanges } from '@angular/core';
import { CommunicationService } from '@shared-service/_services';

@Component({
  selector: 'app-net-profit',
  templateUrl: './net-profit.component.html',
  styleUrls: ['./net-profit.component.css']
})
export class NetProfitComponent implements OnInit {

  @Input() title:string;
  @Input() amount;
  @Input() border_bottom:number;
  @Input() moreField:number=0;
  @Input() property_id;
  @Input() additionalField;
  @Input() fieldType;
  @Input() totalAirBnb;
  @Input() totalFundsPriorClosing;
  @Input() isNetProfit;
  @Input() payoutTotalAmount;
  
  @Output() totalAmount = new EventEmitter<number>();

  constructor() { }

  ngOnInit(): void {
    
    if(isNaN(this.amount)){
      this.amount=0;
    }

    // if(this.isNetProfit){
    //   this.amount=this.calculateNetProfit(this.payoutTotalAmount);
    // }

    if(this.additionalField){
      this.additionalField=this.additionalField.filter(res=>res.field_type==this.fieldType);
    }
  }

  totalOtherAmount($event){
    this.totalAmount.emit($event);
  }
  ngOnChanges(changes: SimpleChanges){
  }

}
