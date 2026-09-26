import { Component, EventEmitter, Input, OnInit, Output } from '@angular/core';
import { FormBuilder, FormGroup } from '@angular/forms';
import { apiUrl } from '@config/api-url';
import { AlertService, CommonActivityService, CommonApplicationService, CommunicationService } from '@shared-service/_services';
import { StorageService } from '@shared-service/_services/storage.service';

@Component({
  selector: 'app-fees',
  templateUrl: './fees.component.html',
  styleUrls: ['./fees.component.css']
})
export class FeesComponent implements OnInit {

  @Input() property_id:string;
  @Input() payoutInfo;
  
  purchaseAdjForm:FormGroup;
  loading: boolean;
  llc_fees:Array<string>=['travel_office_fee','labor_charges','interest_expenses'];
  estates_fee:Array<string>=['office_fee','bookkeeping_fee','web_fee','assignment_fee']
  totalLLCAmount=0;
  totalESAmount=0;
  netProfitAmount:number=0;
  webFee:number=0;
  @Output() totalEstateFee = new EventEmitter<number>();
  @Input() viewAccessOnly;

  constructor(private formBuilder:FormBuilder,
              private commonActivityService:CommonActivityService,
              private commonApplicationService:CommonApplicationService,
              private alertService:AlertService,
              private storageService:StorageService,
              private communicationService:CommunicationService
              ) { }

  ngOnInit(): void {
    this.purchaseAdjForm = this.formBuilder.group({
      house_id:[this.property_id],
      travel_office_fee:[this.payoutInfo.travel_office_fee],
      labor_charges:[this.payoutInfo.labor_charges],
      interest_expenses:[this.payoutInfo.interest_expenses],
      office_fee:[this.payoutInfo.office_fee],
      bookkeeping_fee:[this.payoutInfo.bookkeeping_fee],
      web_fee:[this.payoutInfo.web_fee],
      assignment_fee:[this.payoutInfo.assignment_fee],
      llc_name:[this.payoutInfo.llc_name],
    });
    
    let totalPurchaseAdj:number=0;
    this.purchaseAdjForm.valueChanges.subscribe(response=>{
        if(this.purchaseAdjForm.touched){
          this.totalESAmount =0;
          this.totalLLCAmount =0;
          Object.keys(response).forEach((key)=>{ 
          if((this.llc_fees.includes(key))&& response[key]){
              if (this.purchaseAdjForm.controls[key]) {
                  totalPurchaseAdj = parseFloat(response[key]);
                  this.totalLLCAmount += parseFloat(response[key]);;
              }
          }
          if((this.estates_fee.includes(key))&& response[key]){
            if (this.purchaseAdjForm.controls[key]) {
                totalPurchaseAdj = parseFloat(response[key]);
                this.totalESAmount += parseFloat(response[key]);;
            }
          }
          this.totalEstateFee.emit(this.totalESAmount+this.totalLLCAmount);
          });
        }
    });


    for (let field in this.purchaseAdjForm.controls) { 
      let fieldValue=parseFloat(this.purchaseAdjForm.controls[field].value);
      if(this.llc_fees.includes(field) && fieldValue){
        totalPurchaseAdj += fieldValue;
        this.totalLLCAmount += fieldValue;
      }
      if(this.estates_fee.includes(field) && fieldValue){
        totalPurchaseAdj += fieldValue;
        this.totalESAmount += fieldValue;
      }
    }
    this.totalEstateFee.emit(totalPurchaseAdj);
    //console.log(totalPurchaseAdj);
  }

  ngAfterViewInit(){
    this.communicationService.getNetProfit().subscribe(response=>
      {
          this.webFee=response>21000?1000:0;
          //this.purchaseAdjForm.get('web_fee').setValue(webFee);
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
}
