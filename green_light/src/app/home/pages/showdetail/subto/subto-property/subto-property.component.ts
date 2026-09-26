import { Component, ElementRef, OnInit, Renderer2, ViewChild } from '@angular/core';
import { FormArray, FormBuilder, FormControl, FormGroup } from '@angular/forms';
import { ActivatedRoute } from '@angular/router';
import { apiUrl } from '@config/api-url';
import { AlertService, CommonActivityService, CommonApplicationService } from '@shared-service/_services';
import { StorageService } from '@shared-service/_services/storage.service';
import { CommonHelper } from '@shared-service/_utils/CommonHelper';
import { map } from 'rxjs/operators';

@Component({
  selector: 'app-subto-property',
  templateUrl: './subto-property.component.html',
  styleUrls: ['./subto-property.component.css']
})
export class SubtoPropertyComponent implements OnInit {
  buyer_notes:string='buyer';
  sale_type: any;
  propertyDetail: any;
  property_id:number;
  loader:boolean=false;
  property_config :any;
  subToForm:FormGroup;
  loading: boolean;
  subToInfo:any;
  subToDocumentInfo:any;
  secondLien:boolean=false;
  firstLien:boolean=false;
  excessFunds:number=0;
  contentLoad:boolean=false;

  constructor(private commonApplicationService:CommonApplicationService,
              private storageService:StorageService,
              private route: ActivatedRoute,
              private formBuilder:FormBuilder,
              private commonActivityService:CommonActivityService,
              private alertService:AlertService,
              private renderer: Renderer2) { }

  ngOnInit(): void {
    this.property_config=this.storageService.get("property_config");
    this.sale_type =  this.property_config['sale_type'];
    this.property_id = this.route.parent.snapshot.parent.params.property_id;
    this.getPropertyInfo();
    this.getSubtoProperty();

    this.subToForm = this.formBuilder.group({
      house_id: [],
      strategy_option: this.formBuilder.array([]),
      after_foreclosure: this.formBuilder.array([]),
      checklist: this.formBuilder.array([]),
      buy_back_hoa:this.formBuilder.array([]),
      flip_excess:this.formBuilder.array([]),
      
    });

  }

  onChangeBuyBackHoa(e) {

    const buyBack: FormArray = this.subToForm.get('buy_back_hoa') as FormArray;
    if (e.target.checked) {
      buyBack.push(new FormControl(e.target.value));
    } else {
       const index = buyBack.controls.findIndex(x => x.value === e.target.value);
       buyBack.removeAt(index);
    }
  }

  onChangeFlipExcess(e) {

    const flipExcess: FormArray = this.subToForm.get('flip_excess') as FormArray;
    if (e.target.checked) {
      flipExcess.push(new FormControl(e.target.value));
    } else {
       const index = flipExcess.controls.findIndex(x => x.value === e.target.value);
       flipExcess.removeAt(index);
    }
  }

  onCheckboxChange(e) {

    const strategy: FormArray = this.subToForm.get('strategy_option') as FormArray;
    if (e.target.checked) {
      strategy.push(new FormControl(e.target.value));
    } else {
       const index = strategy.controls.findIndex(x => x.value === e.target.value);
       strategy.removeAt(index);
    }
  }

  onForeclosureCheckboxChange(e) {

    const foreclosure: FormArray = this.subToForm.get('after_foreclosure') as FormArray;
    if (e.target.checked) {
      foreclosure.push(new FormControl(e.target.value));
    } else {
       const index = foreclosure.controls.findIndex(x => x.value === e.target.value);
       foreclosure.removeAt(index);
    }
  }
  onChecklistCheckboxChange(e) {

    const checklist: FormArray = this.subToForm.get('checklist') as FormArray;
    if (e.target.checked) {
      checklist.push(new FormControl(e.target.value));
    } else {
       const index = checklist.controls.findIndex(x => x.value === e.target.value);
       checklist.removeAt(index);
    }
  }

  validateForm(data: any) {
    let result = this.commonActivityService.getFullFormDataWithDateFormatted(data);
    this.updateSubToInfo(result);
  }

  updateSubToInfo(result){
    this.loading = true;
    if(this.property_id){
      let url = apiUrl.update_subto+'/'+this.property_id;
      console.log(url);
      this.commonApplicationService.put(url, result)
        .subscribe(
            data => {
              this.loading = false;
              this.alertService.success(data.message);                
            },
            error => {
              this.loading = false;
              this.alertService.common(error); 
            }
        ); 
      }else{
        this.alertService.error("Please save property detail.");  
    }
  }

  getPropertyInfo(){
    let url = apiUrl.sub_to+'/'+this.property_id;
    this.commonApplicationService.get(url).subscribe(response =>{
        this.propertyDetail=response.row;
        this.propertyDetail.recommended_cma_arv=CommonHelper.convertInt(this.propertyDetail.recommended_cma_arv);
        this.propertyDetail.total_living_sqft=CommonHelper.convertInt(this.propertyDetail.total_living_sqft);
        this.propertyDetail.county_value=CommonHelper.convertInt(this.propertyDetail.county_value);
        this.propertyDetail.last_sale_details.opening_bid=CommonHelper.convertInt(this.propertyDetail.last_sale_details.opening_bid);
        if(this.propertyDetail.last_sale_details.last_bidder){
          this.propertyDetail.last_sale_details.last_bidder.winning_bid=CommonHelper.convertInt(this.propertyDetail.last_sale_details.last_bidder.winning_bid);
        }
        this.calculateExcessFunds();
        let secondLienInfo=this.propertyDetail?.second_liens;
        
        if(secondLienInfo){
          if(!secondLienInfo.es_excess_funds || secondLienInfo.es_excess_funds=='0.00'){
            secondLienInfo.es_excess_funds=this.excessFunds;
          }
          if(secondLienInfo?.amortization_monthly_payment || secondLienInfo?.amortization_loan_estimate_balance || secondLienInfo?.total_est_debt || secondLienInfo?.es_excess_funds!='0.00'){
            this.secondLien=true;
          }
        }
        
        let firstLienInfo=this.propertyDetail?.first_liens;
        if(firstLienInfo){
          if(!firstLienInfo.es_excess_funds || firstLienInfo.es_excess_funds=='0.00'){
            firstLienInfo.es_excess_funds=this.excessFunds;
          }
          if(firstLienInfo?.amortization_monthly_payment || firstLienInfo?.amortization_loan_estimate_balance || firstLienInfo?.total_est_debt || firstLienInfo?.es_excess_funds!='0.00'){
            this.firstLien=true;
          }
        }
        this.loader=true;
    },error=>{
      this.loader=true;
    })
    
  }

  calculateExcessFunds(){
    if(this.propertyDetail){
      this.excessFunds=parseFloat(this.propertyDetail.last_sale_details?.last_bidder?.winning_bid || 0)-
      ( parseFloat(this.propertyDetail.last_sale_details?.opening_bid || 0)+
        parseFloat(this.propertyDetail.first_liens?.amortization_loan_estimate_balance || 0)+
        parseFloat(this.propertyDetail.second_liens?.amortization_loan_estimate_balance || 0)+
        parseFloat(this.propertyDetail.third_liens?.amortization_loan_estimate_balance || 0)+
        parseFloat(this.propertyDetail.assessment[0]?.total_property_taxes_owed || 0)
        );

        
    }
   
  }



  getSubtoProperty(){
    let url = apiUrl.get_sub_to+'/'+this.property_id;
    this.commonApplicationService.get(url).subscribe(response =>{
       
        this.loader=true;
       
        this.subToInfo=response['data'];
        if(response['data']?.subto_document){
          this.subToDocumentInfo=response['data']?.subto_document.filter(items => items.subto_doc_type ==='allSubToDoc');
        }

        if(response['data'].strategy_option){
          let strategy_option= response['data'].strategy_option.split("|||"); 
          const strategy: FormArray = this.subToForm.get('strategy_option') as FormArray;
          strategy_option.forEach(element => {
            strategy.push(new FormControl(element));
          });
          for(var key in this.property_config.strategy_option) {
            if(strategy_option.indexOf(key) !==-1){
              this.property_config.strategy_option[key][1]=true;
            }
          }
        }
        
        if(response['data'].after_foreclosure){
          let after_foreclosure= response['data'].after_foreclosure.split("|||"); 
          const foreclosure: FormArray = this.subToForm.get('after_foreclosure') as FormArray;
          after_foreclosure.forEach(element => {
            foreclosure.push(new FormControl(element));
          });

          for(var key1 in this.property_config.subto_after_foreclosure) {
            if(after_foreclosure.indexOf(key1) !==-1){
              this.property_config.subto_after_foreclosure[key1][1]=true;
            }
          }
        }

        if(response['data'].checklist){
          let checklist_option= response['data'].checklist.split("|||"); 
          const checklist: FormArray = this.subToForm.get('checklist') as FormArray;
          checklist_option.forEach(element => {
            checklist.push(new FormControl(element));
          });
          for(var key in this.property_config.checklist_option) {
            if(checklist_option.indexOf(key) !==-1){
              this.property_config.checklist_option[key][1]=true;
            }
          }
        }
        this.contentLoad=true;

    },error=>{
      this.loader=true;
    })
  }


}
