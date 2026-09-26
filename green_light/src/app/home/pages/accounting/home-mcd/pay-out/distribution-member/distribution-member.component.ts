import { Component, Input, OnInit} from '@angular/core';
import { FormBuilder, FormGroup } from '@angular/forms';
import { AlertService, apiUrl, CommonActivityService, CommonApplicationService, CommunicationService } from '@shared-service/_services';

@Component({
  selector: 'app-distribution-member',
  templateUrl: './distribution-member.component.html',
  styleUrls: ['./distribution-member.component.css'],

})
export class DistributionMemberComponent implements OnInit {
  
  @Input() property_id;  
  @Input() mcdLenderResult;
  @Input() additionalField;
  @Input() fieldType;
  @Input() aaFee;
  @Input() bonusSpread;
  @Input() avilableAmount;
  @Input() distribution_llc;

  distributionForm:FormGroup;
  payoutForm:FormGroup;
  loading:boolean=false;
  grandTotalDistibutionAmount:number=0;
  totalDistibutionMember:any=[];
  netProfitAmount:number=0;
  disFieldType:string='distribution_llc';
  moreDistField:any;
  moreLlcAmount:number;
  distAmountTotal:number;

  constructor(private commonActivityService:CommonActivityService,
              private formBuilder:FormBuilder,
              private commonApplicationService:CommonApplicationService,
              private alertService:AlertService,
              private communicationService:CommunicationService) { }

  ngOnInit(): void {
    this.payoutForm = this.formBuilder.group({
      lender_name:[this.distribution_llc?.lender_name],
      deposit_return_no:[this.distribution_llc?.deposit_return_no],
      wiriing_routing_no:[this.distribution_llc?.wiriing_routing_no],
      bank_ac_no:[this.distribution_llc?.bank_ac_no],
      bank_name:[this.distribution_llc?.bank_name]
    });

    this.distributionForm = this.formBuilder.group({
      house_id:[this.property_id],
      lender_id:[],
      lender_info_data: this.formBuilder.group({
        deposit_return_no:[],
        wiriing_routing_no:[],
        bank_ac_no:[],
        bank_name:[]
      })
    });

    if(this.additionalField){
      this.moreDistField=this.additionalField.filter(res=>(res.field_type==this.disFieldType));
      this.additionalField=this.additionalField.filter(res=>(res.field_type==this.fieldType));
    }
    

    this.communicationService.getNetProfit().subscribe(response=>
    {
       //setTimeout(() => {
        this.netProfitAmount=response;
      // }, 0); 
    });
  }
  

  priceFields() : FormGroup
  {    
    return this.formBuilder.group({
      deposit_return_no:[],
      wiriing_routing_no:[],
      bank_ac_no:[],
      bank_name:[]
    });
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

  totalDistibution($event){
    let itemIndex = this.totalDistibutionMember.findIndex(item => item.lender_id == $event.lender_id);
    if(itemIndex >= 0){
      this.totalDistibutionMember[itemIndex].total =$event.total;
    }else{
      this.totalDistibutionMember.push($event);
    }

    this.grandTotalDistibutionAmount=0;
    this.totalDistibutionMember.forEach(element => {
      this.grandTotalDistibutionAmount=this.grandTotalDistibutionAmount+parseFloat(element.total);
    });

    this.distAmountTotal= this.grandTotalDistibutionAmount;
    this.grandTotalDistibutionAmount=this.grandTotalDistibutionAmount+this.moreLlcAmount;
  }
  
  updateMoreInfo($event){
    this.moreLlcAmount=$event;
    this.grandTotalDistibutionAmount=this.distAmountTotal+this.moreLlcAmount;
  }

  autoSave(data){

    this.commonActivityService.addLoader(data['el']);
    this.commonActivityService.removeElement(data['el'],'saved-icon');
    
    let index=data.el.parentElement.parentElement.getAttribute('id');
    
    let saveInfo:any={'house_id':this.property_id,'name':data['name'],'value':data['value']};
    this.updatePayoutInfo(saveInfo,data);

  }

  updatePayoutInfo(saveInfo,data){

      let url = apiUrl.payoutLender+'/'+this.property_id;
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

}
