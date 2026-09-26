import { ChangeDetectionStrategy, Component, EventEmitter, Input, OnInit, Output } from '@angular/core';
import { FormBuilder, FormGroup } from '@angular/forms';
import { apiUrl } from '@config/api-url';
import { AlertService, CommonActivityService, CommonApplicationService } from '@shared-service/_services';

@Component({
  selector: 'app-member',
  templateUrl: './member.component.html',
  styleUrls: ['./member.component.css'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class MemberComponent implements OnInit {
  @Input() property_id;  
  @Input() mcdlender;
  @Input() netProfitAmount;
  @Input() additionalField;
  @Input() fieldType;
  @Output() updateTotalDistibution = new EventEmitter<any>();

  totalCalculateAmount:number=0;
  distributionForm:FormGroup;
  loading:boolean=false;
  totalMoreAmount:number=0;
  estateLLCName:any=['estates llc','mantica llc'];
  noCalProfit:any=['estates llc','mantica llc','timbra llc'];

  memberProfit:number=0;
  memberPointAndInt:number=0;


  constructor(private commonActivityService:CommonActivityService,
              private formBuilder:FormBuilder,
              private commonApplicationService:CommonApplicationService,
              private alertService:AlertService) { }

  ngOnInit(): void {
    this.distributionForm = this.formBuilder.group({
      house_id:[this.property_id],
      lender_id:[this.mcdlender.id],
      deposit_return_no:[this.mcdlender.deposit_return_no],
      wiriing_routing_no:[this.mcdlender.wiriing_routing_no],
      bank_ac_no:[this.mcdlender.bank_ac_no],
      bank_name:[this.mcdlender.bank_name]
    });
    if(this.additionalField){
      this.additionalField=this.additionalField.filter(res=>{
        return (res.field_type==this.fieldType && res.lender_id==this.mcdlender.id)
      });
    }
    
    this.calProfitAndInt();

  }

  ngOnChanges(){
    this.calProfitAndInt();
  }

  calProfitAndInt(){
    this.memberProfit=(this.netProfitAmount*this.mcdlender.percentage)/100;
    this.memberPointAndInt=parseFloat(this.mcdlender?.pts)+parseFloat(this.mcdlender?.interest);
    if(this.memberPointAndInt > this.memberProfit && this.memberPointAndInt>0) {
      this.memberProfit=0;
    }else{
      this.memberPointAndInt=0;
    }
  }

  calculateTotalAmount(){
    // console.log('NetProfit==>'+this.netProfitAmount);
    // console.log('amount==>'+this.mcdlender?.amount);
    // console.log('returned_amount==>'+this.mcdlender?.returned_amount);
    // console.log('pts==>'+this.mcdlender?.pts);
    // console.log('interest==>'+this.mcdlender?.interest);
    //this.totalCalculateAmount=
    let totalAdditionalAmount:any=0;
    if(this.additionalField && this.totalMoreAmount==0){
      this.additionalField.forEach(element => {
        if(element.field_value){
          totalAdditionalAmount =parseFloat(totalAdditionalAmount)+parseFloat(element.field_value);
        }
      });
    }else{
      totalAdditionalAmount =this.totalMoreAmount;
    }
    
    let total:any= parseFloat(totalAdditionalAmount).toFixed(2);
    if(!this.estateLLCName.includes(this.mcdlender.lender_name.toLowerCase())){
      
      if(!this.noCalProfit.includes(this.mcdlender.lender_name.toLowerCase()))
        total=parseFloat(total)+this.memberProfit;
      
      total=parseFloat(total)+parseFloat(this.mcdlender?.amount?this.mcdlender?.amount:0) 
        -parseFloat(this.mcdlender?.returned_amount?this.mcdlender?.returned_amount:0)
        +this.memberPointAndInt
    }

    if(isNaN(total)){ this.updateTotalDistibution.emit({'lender_id':this.mcdlender.id,'total':0});return 0; }
    else{
      this.updateTotalDistibution.emit({'lender_id':this.mcdlender.id,'total':total});
      return total;  
    }             
  }
  autoSave(data,lender_id){

    this.commonActivityService.addLoader(data['el']);
    this.commonActivityService.removeElement(data['el'],'saved-icon');
    
    let index=data.el.parentElement.parentElement.getAttribute('id');
   // let id = this.distributionForm.get('more_info_data')['controls'][index].controls.id.value;
    let saveInfo:any={'id':lender_id,'name':data['name'],'value':data['value']};
    this.updatePayoutInfo(saveInfo,data);

  }

  updatePayoutInfo(saveInfo,data){

    let url = apiUrl.mcd_single_lender+'/'+this.property_id;
    this.commonApplicationService.post(url, saveInfo)
    .subscribe(
        response => {
          if(response['status']=='failed'){
            this.commonActivityService.removeElement(data['el'],'loader-icon');
            this.alertService.error(response['message']);
          }else{
              response.data;
              let index=data.el.parentElement.parentElement.getAttribute('id');
             // let id = this.moreFieldForm.get('more_info_data')['controls'][index].controls.id.setValue(response.data);
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

  mcdLenderinfo(lender){
    console.log(this.distributionForm);
    let index=0;
    let lenderdetail=this.distributionForm.get('lender_info_data')['controls'];
    if(lender){
      lenderdetail.deposit_return_no.patchValue(lender.deposit_return_no);
      // lenderdetail.wiriing_routing_no.setValue(lender.wiriing_routing_no);
      // lenderdetail.bank_ac_no.setValue(lender.bank_ac_no);
      // lenderdetail.bank_name.setValue(lender.bank_name);
    }
    
  }

  updateMoreInfo($event){
   this.totalMoreAmount=$event;
  }
 
}
