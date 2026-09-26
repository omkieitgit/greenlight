import { Component, Injectable,ViewChild, OnInit, Renderer2 , ViewContainerRef, ComponentFactoryResolver} from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router,ActivatedRoute }    from '@angular/router';
import { MortgageModel } from './mortgage.model';
import { CommonApplicationService,AlertService,CommonActivityService,CommunicationService } from '@shared-service/_services';
import { apiUrl } from '@config/api-url';
import { StorageService } from '@shared-service/_services/storage.service';

// for ngb datepicker adapter
@Injectable()


@Component({
 selector: 'mortgage',
 templateUrl: './mortgage.html',
 //providers: [{provide: NgbDateAdapter, useClass: NgbDateNativeAdapter}]
 })


/*@Component({
  selector: 'owner',
  templateUrl: './owner.html',
  providers: [{provide: NgbDateAdapter, useClass: NgbDateNativeAdapter}]
})*/

export class MortgageComponent  implements OnInit{

  mortgageLoading:boolean=false;
  mortgageDataOther:any;

  property_config: any= {};
  loan_type_list: string[];
  public mortgageData : MortgageModel;
  public mortgageDataBlankModel : MortgageModel;
  property_id: string;
  result: any;

  mortgageForm: FormGroup;
  submitted = false;
  loading:boolean=false;
  firstlien:number=1;
  secondlien:number=2;
  thirdlien:number=3;
  fourthlien:number=4;
  fifthlien:number=5;

  openPanel:boolean = false;
  //Final
  firstLienInfo:any;
  secondLienInfo:any;
  thirdLienInfo:any;
  fourthLineInfo:any='';
  fifthLineInfo:any='';
  lienHoaData:any;
  lienTaxData:any;
  lienOtherData:any;
  amortization_info:any;
  sale_info:any;
  no_active_mortgage_lien:boolean=false;
  manualSearch:boolean=false;
  showFourthLien:boolean=false;
  showFifththLien:boolean=false;
  lienInfo:any;
  excessFunds:number;

  @ViewChild('mortgageSections', { read: ViewContainerRef }) container: ViewContainerRef;
  constructor(private formBuilder: FormBuilder,
              private router: Router,
              private route: ActivatedRoute,
              private commonActivityService :CommonActivityService,
              private commonApplicationService:CommonApplicationService,
              private alertService:AlertService,
              private storageService:StorageService,
              private _cfr: ComponentFactoryResolver,
              private communicationService:CommunicationService
              ) { 
                this.mortgageDataBlankModel = new MortgageModel();
                this.firstLienInfo=this.mortgageDataBlankModel.mortgage_liens;
                this.secondLienInfo=this.mortgageDataBlankModel.mortgage_liens;
                this.thirdLienInfo=this.mortgageDataBlankModel.mortgage_liens;
                this.fourthLineInfo=this.mortgageDataBlankModel.mortgage_liens;
                this.fifthLineInfo=this.mortgageDataBlankModel.mortgage_liens;
                this.property_config =  this.storageService.get("property_config");
                if(this.property_config !== null){
                  this.loan_type_list          = this.property_config.loan_type;
                }
  }


  ngOnInit() {
    this.property_id = this.route.snapshot.paramMap.get('property_id');
    if(this.property_id !== undefined){
      if(this.openPanel){
          this.getMortgageInfo();
         
      }
    }
    
  }

  panelExpand(flag){
    if(!this.openPanel){
      this.getMortgageInfo();
      this.openPanel = true;
    }
  }

  /*----------------------------- Get property Info Details --------------------------------*/
  getMortgageInfo(){
    //this.commonApplicationService.get(url,data,sucess_message,error_message);
      this.loading = true;
      let url = apiUrl.mortgage+'/'+this.property_id;
      this.commonApplicationService.get(url).subscribe(response =>{
        this.result = response['data'];
        if(this.result !== undefined){
            this.loading=false;
            this.mortgageData = this.result;
            this.mortgageLoading=true;

            if(this.mortgageData['last_cma_arv_recommendations']){
              this.mortgageDataBlankModel.amortizationInfo.cma_arv=this.mortgageData['last_cma_arv_recommendations']['recommended_cma_arv'];
            }
            if(this.mortgageData['last_rental_rate']){
              this.mortgageDataBlankModel.amortizationInfo.rental_rate=this.mortgageData['last_rental_rate']['rental_rate'];
            }

            if(this.mortgageData['last_sale_details']){
              this.mortgageDataBlankModel.amortizationInfo.sale_date=this.mortgageData['last_sale_details']['sale_date'];
              this.mortgageDataBlankModel.amortizationInfo.nos_data=this.mortgageData['last_sale_details']['nos_data'];
              this.sale_info=this.mortgageData['last_sale_details'];
            }

            // Property Form
            if(this.mortgageData.other_4 !== undefined && this.mortgageData.other_4[0] !== undefined ){
              this.mortgageDataOther =  this.mortgageData.other_4[0];
            }else{
              this.mortgageDataOther = this.mortgageDataBlankModel.other_4;
            }

            if(this.mortgageData.mortgage_liens !== undefined){

              for (let index in this.mortgageData.mortgage_liens) {
                if(this.mortgageData.mortgage_liens[index].lien_type==this.firstlien){
                  this.firstLienInfo=this.mortgageData.mortgage_liens[index];
                  if(this.firstLienInfo.amortization_loan_estimate_balance){
                    this.sale_info.assessment['firstlien_amount']=this.firstLienInfo.amortization_loan_estimate_balance;
                  }
                }
                if(this.mortgageData.mortgage_liens[index].lien_type==this.secondlien){
                  this.secondLienInfo=this.mortgageData.mortgage_liens[index];
                  if(this.secondLienInfo.amortization_loan_estimate_balance){
                    this.sale_info.assessment.secondlien_amount=this.secondLienInfo.amortization_loan_estimate_balance?this.secondLienInfo.amortization_loan_estimate_balance:0;
                  }
                }
                if(this.mortgageData.mortgage_liens[index].lien_type==this.thirdlien){
                  this.thirdLienInfo=this.mortgageData.mortgage_liens[index];
                  if(this.thirdLienInfo.amortization_loan_estimate_balance){
                    this.sale_info.assessment.thirdlien_amount=this.thirdLienInfo.amortization_loan_estimate_balance?this.thirdLienInfo.amortization_loan_estimate_balance:0;
                  }
                }
                if(this.mortgageData.mortgage_liens[index].lien_type==this.fourthlien){
                  this.fourthLineInfo=this.mortgageData.mortgage_liens[index];
                }
                if(this.mortgageData.mortgage_liens[index].lien_type==this.fifthlien){
                  this.fifthLineInfo=this.mortgageData.mortgage_liens[index];
                }
              }
            }

            if(this.mortgageData.hoa_liens !== undefined && this.mortgageData.hoa_liens[0] !== undefined ){
              this.lienHoaData  =  this.mortgageData.hoa_liens[0];
            }else{
              this.lienHoaData  = this.mortgageDataBlankModel.hoa_liens;
            }
            if(this.mortgageData.tax_liens !== undefined && this.mortgageData.tax_liens[0] !== undefined ){
              this.lienTaxData  =  this.mortgageData.tax_liens[0];
            }else{
              this.lienTaxData  = this.mortgageDataBlankModel.tax_liens;
            }
            if(this.mortgageData.other_liens !== undefined && this.mortgageData.other_liens[0] !== undefined ){
              this.lienOtherData =  this.mortgageData.other_liens[0];
            }else{
              this.lienOtherData  = this.mortgageDataBlankModel.other_liens;
            }
            
            this.no_active_mortgage_lien=this.mortgageData.other_4[0]?this.mortgageData.other_4[0].no_active_mortgage_lien==1?true:false:false;
            this.manualSearch=this.mortgageData.other_4[0]?this.mortgageData.other_4[0].manual_search==1?true:false:false;
            this.updateEsBalance('');
        }
      },
        (err: any) => {
          this.alertService.error("Error occured, Please try again later!");
          this.loading = true;
        })
  }

  viewFourthLien($event){
    this.showFourthLien=$event;
  }
  viewFifthLien($event){
    this.showFifththLien=$event;
  }

  updateEsBalance($event){
    if($event){
      this.sale_info=$event;
    }
    this.excessFunds=parseFloat(this.sale_info?.last_bidder?.amount_of_bid || 0)-
      ( parseFloat(this.sale_info?.opening_bid || 0)+
        parseFloat(this.sale_info?.assessment?.firstlien_amount || 0)+
        parseFloat(this.sale_info?.assessment?.secondlien_amount || 0)+
        parseFloat(this.sale_info?.assessment?.thirdlien_amount || 0)+
        parseFloat(this.sale_info?.assessment?.total_taxes_owed || 0)
        );
  }
 
  /*----------------------------- Get property Info Details --------------------------------*/
  

}