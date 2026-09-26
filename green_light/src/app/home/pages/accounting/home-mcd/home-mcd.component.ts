import { Component, OnInit, Input, ChangeDetectionStrategy } from '@angular/core';
import { FormGroup, FormBuilder, Validators } from '@angular/forms';
import {IMyDpOptions, IMyDateModel} from 'mydatepicker';
import { CommonActivityService, CommonApplicationService, apiUrl, AlertService, CommunicationService } from '@shared-service/_services';
import { StorageService } from '@shared-service/_services/storage.service';

@Component({
  selector: 'app-home-mcd',
  templateUrl: './home-mcd.component.html',
  styleUrls: ['./home-mcd.component.css'],
  //changeDetection: ChangeDetectionStrategy.OnPush

})
export class HomeMcdComponent implements OnInit {
  
  
  @Input() property_id:string;
  @Input() payoutDetail;
  @Input() nonHubResult;

  loading = false;
  openPanel:boolean = false;
  homeMcdForm:FormGroup;

  submitted:boolean=false;
  invalidFields:any;
  mcdResult:any=[];
  totalByLender:any=[];
  loadingMcd:boolean;

  totalAmount:number=0;
  mcd_lender:any;
  defaultMcdType:string='contribution';
  property_info:any;
  totalAirBnbAmount:number;
  //netProfitInfo:any={'netProfit':0};
  netProfitInfo:number=0;
  invoiceAmount:number=0;
  clientInoviceExpense:any;
  interestType:number=35;
  lenderReferralPre:number=2;
  client_list:any;
  payers_list:any;
  other_mcd_info:any;  
  interest_type=[{'name':'with 35 days','value':35},{'name':'without 35 days','value':0}];
  lenderReferal=[{'name':'New Lender 2%','value':2},{'name':'Old Lender 1.45%','value':1.45}];
  investmentType=[{'name':'Contribution','value':'contribution'},{'name':'Distribution','value':'distribution'}];

  pts_data:any=[{'from_day':1,'to_day':7,'pts':'1.75'},
                {'from_day':8,'to_day':14,'pts':'2.75'},
                {'from_day':15,'to_day':21,'pts':'3.75'},
                {'from_day':22,'to_day':28,'pts':'4.75'},
                {'from_day':29,'to_day':35,'pts':'5.75'},];

  viewAccessOnly:boolean=false;

  constructor(private formBuilder:FormBuilder,
              private commonActivityService:CommonActivityService,
              private commonApplicationService:CommonApplicationService,
              private alertService:AlertService,
              private storageService:StorageService,
              private communicationService:CommunicationService) { }

  ngOnInit(): void {
    this.viewAccessOnly=this.storageService.getHard('ac_view_access');
    this.property_info=this.storageService.getHard('property_info');
    this.homeMcdForm = this.formBuilder.group({ 
      id:[],
      lender_name:['',[Validators.required]],
      purpose_of_funds:['',[Validators.required]],
      in_date:[''],
      out_date:[''],
      amount:['',[Validators.required]],
      returned_amount:[''],
      funded_days:[''],
      pts:[''],
      interest:[''],
      lender_referral:[''],
      interest_type:[this.interestType],
      mcd_type:[this.defaultMcdType,[Validators.required]],
      lender_referral_pre:[this.lenderReferralPre],
      document_url:['']
    });

    this.communicationService.getUpdateRenovation().subscribe(response=>{
      if(response && this.openPanel){
        this.getMcdInfo();
      }
    });
  }

  public myDatePickerOptions: IMyDpOptions = { dateFormat: 'mm/dd/yyyy',firstDayOfWeek : 'su'};
  onDateChanged(event: IMyDateModel,date,amountEvent) {
      this.numberOfDays(event.formatted,date.selectionDayTxt);
      this.inveterAmount(amountEvent.value);
      return event.formatted;
  }

  panelExpand(flag){
    if(!this.openPanel && this.property_id !== undefined){
      this.openPanel = true;
      this.getMcdLender();
      this.getMcdInfo();
    }
  }


  getMcdInfo(){
    this.loading=true;
    let url = apiUrl.mcd+'/'+this.property_id;
    this.commonApplicationService.get(url).subscribe(response => {
      if(response['row']['list']){
        this.mcdResult = response['row']['list'];
      }
      if(response['row']['total_by_lender']){
        this.totalByLender = response['row']['total_by_lender'];
      }
      // if(response['row']['netProfitAmount']){
      //   this.netProfitInfo = response['row']['netProfitAmount'];
      // }
      if(response['row']['invoiceAmount']){
        this.invoiceAmount = response['row']['invoiceAmount'];
      }
      if(response['row']['clientInoviceExpense']){
        this.clientInoviceExpense = response['row']['clientInoviceExpense'];
      }
      if(response['row']['client_list']){
        this.client_list = response['row']['client_list'];
      }
      if(response['row']['payers_list']){
        this.payers_list = response['row']['payers_list'];
      }
      if(response['row']['other_mcd_info']){
        this.other_mcd_info = response['row']['other_mcd_info'];
      }
      
      this.loading = false;
      this.loadingMcd=true;
    },
    (err: any) => {
      this.loading = false;
      this.loadingMcd=true;
    })
  }

  validateForm(data){
    let result = this.commonActivityService.getFullFormDataWithDateFormatted(data);
    this.invalidFields = this.commonActivityService.findInvalidControls(data);
    this.submitted = true;
    console.log('niform');
    if(this.homeMcdForm.invalid) { 
      console.log('Form is invalid, Fill all fields.');
      //this.propertyForm.get('prop_address').markAsTouched();      
      return;
    }    
    // FORM SUBMITTED
    console.log('form submitted');
    this.saveUpdateMcd(result);  
  }

  saveUpdateMcd(data: any){
    this.loading = true;
    let input = new FormData();
    input.append("amount", data['amount']);
    input.append("funded_days", data['funded_days']?data['funded_days']:0);
    input.append("in_date", data['in_date']);
    input.append("interest", data['interest']?data['interest']:0);
    input.append("lender_name", data['lender_name']?.id);
    input.append("lender_referral", data['lender_referral']?data['lender_referral']:0);
    input.append("out_date", data['out_date']);
    input.append("pts", data['pts']?data['pts']:0);
    input.append("purpose_of_funds", data['purpose_of_funds']?data['purpose_of_funds']:0);
    input.append("returned_amount", data['returned_amount']?data['returned_amount']:0);
    input.append("id", data['id']);
    input.append("interest_type", data['interest_type']?.value);
    input.append("mcd_type", data['mcd_type']?.value);
    input.append("document_url", data['document_url']);
    input.append("lender_referral_pre", data['lender_referral_pre']?.value);

    // if(mcdDocument != "" && mcdDocument != undefined){
    //   input.append('invenstor_document',mcdDocument.files[0]);
    // }
    
    let url = apiUrl.mcd+'/'+this.property_id;
    this.commonApplicationService.post(url, input)
        .subscribe(
            data => {
              if(data.status=='success'){

                let itemIndex = this.mcdResult.findIndex(item => item.id == data.data['mcd_detail'].id);
                if(itemIndex >= 0){
                  this.mcdResult[itemIndex] = data.data['mcd_detail'];
                }else{
                  this.mcdResult.push(data.data['mcd_detail']);
                }

                let lenderIndex = this.totalByLender.findIndex(item => item.id == data.data['total_by_lender'].id);
                if(lenderIndex >= 0){
                  this.totalByLender[lenderIndex] = data.data['total_by_lender'];
                }else{
                  this.totalByLender.push(data.data['total_by_lender']);
                }

                this.alertService.success(data.message);  
                this.homeMcdForm.reset();
                this.homeMcdForm.get('interest_type').setValue(this.interestType);
                this.homeMcdForm.get('mcd_type').setValue(this.defaultMcdType);
                this.homeMcdForm.get('lender_referral_pre').setValue(this.lenderReferralPre);

                this.submitted=false;
              }else{
                this.alertService.error(data.message); 
              }
              
              this.loading = false;
            },
            error => {
              this.loading = false;
              this.alertService.common(error); 
            }
        ); 
  }

  get f() { return this.homeMcdForm.controls; }

  numberOfDays(inDate,OutDate){
    const oneDay = 24 * 60 * 60 * 1000; // hours*minutes*seconds*milliseconds
    let firstDate:any = new Date(inDate);
    let secondDate:any = new Date(OutDate);
    const diffDays = Math.round(Math.abs((firstDate - secondDate) / oneDay));
    if(diffDays){
      this.homeMcdForm.get('funded_days').setValue(diffDays);
    }
  }

  removeMcd(id){

    if(confirm("Are you sure want to delete record ?")){
      let url = apiUrl.mcd+'/'+id;
      this.commonApplicationService.delete(url).subscribe(response => {
        if(response.status=='success'){
          this.mcdResult = this.mcdResult.filter(item => item.id !== id);
          this.alertService.success(response.message);
        }else{
          this.alertService.error(response.message); 
        }
      },
      (err: any) => {
        this.loading = false;
        this.alertService.common(err); 
      })
    }
  }

  inveterAmount(amount){
    let inDate=this.homeMcdForm.get('in_date').value;
    let outDate=this.homeMcdForm.get('out_date').value;
    let funded_days=this.homeMcdForm.get('funded_days').value;
    let pstPrice=this.getPstFormula(funded_days);
    this.homeMcdForm.get('pts').setValue((amount*pstPrice).toFixed(2));
    this.calInterest(amount);
    this.calReferal(amount);
  }

  getPstFormula(funded_days){

    let pts=5.75;
    if(funded_days > 1 && funded_days < 7 ){
      pts=1.75;
    }
    else if(funded_days > 8 && funded_days < 14 ){
      pts=2.75;
    }
    else if(funded_days > 15 && funded_days < 21 ){
      pts=3.75;
    }
    else if(funded_days > 22 && funded_days < 28 ){
      pts=4.75;
    }
    return pts/100;
    
  }

  calInterest(amount){
    let funded_days=this.homeMcdForm.get('funded_days').value;
    let interest_type=this.homeMcdForm.get('interest_type')?.value?.value;
    interest_type=interest_type?interest_type:0;
    
    let interest=0;
    if(funded_days && amount){
      interest = ((0.12*amount) * (funded_days-interest_type))/365;
    }
    this.homeMcdForm.get('interest').setValue(interest.toFixed(2));
  }

  calReferal(amount){
    // let funded_days=this.homeMcdForm.get('funded_days').value;
     let referal=0;
    // let pts=this.homeMcdForm.get('pts').value;
   // if(funded_days>36 && pts){
      referal=amount*(this.homeMcdForm.get('lender_referral_pre')?.value?.value/100);
    //}
    this.homeMcdForm.get('lender_referral').setValue(referal.toFixed(2));
  }

  calTotalAmount(key){
    let totalAmount:any=0;
    for(var i=0; i<this.mcdResult.length; i++){
      totalAmount=parseFloat(totalAmount)+parseFloat(this.mcdResult[i][key]?this.mcdResult[i][key]:0); 
    }
    return totalAmount.toFixed(2);
  }

  editMcd(mcd){
    this.homeMcdForm.get('id').setValue(mcd.id);
    this.homeMcdForm.get('lender_name').setValue(mcd.mcd_lender);
    this.homeMcdForm.get('purpose_of_funds').setValue(mcd.purpose_of_funds);
    this.homeMcdForm.get('in_date').setValue((mcd.in_date != null)? {jsdate:new Date(mcd.in_date)}: null);
    this.homeMcdForm.get('out_date').setValue((mcd.out_date != null)? {jsdate:new Date(mcd.out_date)}: null);
    this.homeMcdForm.get('amount').setValue(mcd.amount);
    this.homeMcdForm.get('returned_amount').setValue(mcd.returned_amount);
    this.homeMcdForm.get('funded_days').setValue(mcd.funded_days);
    this.homeMcdForm.get('pts').setValue(mcd.pts);
    this.homeMcdForm.get('interest').setValue(mcd.interest);
    this.homeMcdForm.get('lender_referral').setValue(mcd.lender_referral);
    this.homeMcdForm.get('interest_type').setValue(this.interest_type.filter(x=>x.value==mcd.interest_type)[0]);
    this.homeMcdForm.get('mcd_type').setValue(this.investmentType.filter(x=>x.value==mcd.mcd_type)[0]);
    this.homeMcdForm.get('document_url').setValue(mcd.document_url);
    this.homeMcdForm.get('lender_referral_pre').setValue(this.lenderReferal.filter(x=>x.value==mcd.lender_referral_pre)[0]);

  }
  
  uploadHandler(event){
    let elem = event.target; 
    if(elem.files.length > 0){
    }
  }

  getMcdLender(){
    let url = apiUrl.mcd_lender+'/'+this.property_id;
    this.commonApplicationService.get(url).subscribe(response => {
      this.mcd_lender=response.row;
      if(response.row.length==1){
        this.homeMcdForm.get('lender_name').setValue(response.row[0].id);
      }
    },
    (err: any) => {
    })
  }
  
  onUpdateLender(event){
      this.getMcdLender();
  }

  totalShortTermRental($event){
    this.totalAirBnbAmount=$event;
  }
  isValidURL(string) {
    var res = string.match(/(http(s)?:\/\/.)?(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)/g);
    return (res !== null)
  };

 

  calTotalLenderInvoiceAmount(key){
    let totalAmount:any=0;
    if(this.clientInoviceExpense){
      for(var i=0; i<this.clientInoviceExpense.length; i++){
        totalAmount=parseFloat(totalAmount)+parseFloat(this.clientInoviceExpense[i][key]?this.clientInoviceExpense[i][key]:0); 
      }
    }
    return totalAmount.toFixed(2);
  }

  calTotallenderAmount(key){
    let totalAmount:any=0;
    for(var i=0; i<this.totalByLender.length; i++){
      if(key=='percentage'){
        //console.log(totalAmount,(this.netProfitInfo.netProfit*this.totalByLender[i][key])/100);
        let amount:any=this.totalByLender[i][key]?((this.netProfitInfo*this.totalByLender[i][key])/100):0;
        totalAmount+=parseFloat(amount); 
      }else{
        totalAmount=parseFloat(totalAmount)+parseFloat(this.totalByLender[i][key]?this.totalByLender[i][key]:0); 
      }
    }
    return totalAmount.toFixed(2);
  }
  
  ngAfterViewInit(){
    this.communicationService.getNetProfit().subscribe(response=>
      {
       setTimeout(() => {
         if(response)
          this.netProfitInfo=response;
       }, 0); 
      });
  }

  displayFn(lender?: any): string | undefined {
    return lender ? lender.lender_name : undefined;
  }

  displayInterestFn(item?: any): string | undefined {
    return item ? item.name : undefined;

  }

  onChangeInterest($event){
    let amount=this.homeMcdForm.get('amount').value;
    this.calInterest(amount);
  }


  onChangeReferal($event){
    let amount=this.homeMcdForm.get('amount').value;
    this.calReferal(amount);
  }

}
