import { Component, EventEmitter, Input, OnInit, Output } from '@angular/core';
import { FormBuilder, FormGroup } from '@angular/forms';
import { CurrencyFormatPipe } from '@shared-modules/directives/currency-format.pipe';
import { AlertService, apiUrl, CommonActivityService, CommonApplicationService } from '@shared-service/_services';

@Component({
  selector: 'app-sub-total',
  templateUrl: './sub-total.component.html',
  styleUrls: ['./sub-total.component.css']
})
export class SubTotalComponent implements OnInit {

  @Input() subTotalTitle;
  @Input() actual_amount;
  @Input() estimate_amount:number=0;
  @Input() buyer_amount;
  @Input() isNormalFont;
  @Input() marginTop;
  @Input() editableField;
  @Input() property_id;
  @Output() fundPriorClosing = new EventEmitter<any>();

  constructor(  private commonApplicationService:CommonApplicationService,
                private commonActivityService:CommonActivityService,  
                private cp:CurrencyFormatPipe, 
                private alertService:AlertService) { }

  ngOnInit(): void {
  }
  
  autoSave(data){
    
    if(!data['value']){
      this.alertService.error("Please enter amount");
      return;
    }
    if(!data['value']){
      this.alertService.error("Please enter amount");
      return;
    }
    this.commonActivityService.addLoader(data['el']);
    this.commonActivityService.removeElement(data['el'],'saved-icon');
    let saveInfo:any={'house_id':this.property_id,'name':data['name'],'value':this.cp.detransformVal(data['value']??0)};

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
          this.fundPriorClosing.emit(saveInfo);
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
