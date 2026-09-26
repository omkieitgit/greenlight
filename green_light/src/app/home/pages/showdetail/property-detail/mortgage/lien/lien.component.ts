import { Component, OnInit, Input, Output,EventEmitter, SimpleChanges } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { MortgateOtherPropTaxModel } from '../mortgage.model';
import { ActivatedRoute } from '@angular/router';
import {IMyDpOptions, IMyDateModel, IMyInputFieldChanged} from 'mydatepicker';
import {MatDialog} from '@angular/material/dialog';
import {EstimatedLatePaymentsComponent} from '../estimated-late-payments/estimated-late-payments.component';
import {AmortizationService} from '../amortization.service';
import {AmortizationComponent} from '../amortization/amortization.component';
import {DefectiveNotesComponent} from '../defective-notes/defective-notes.component';
import { Subject } from "rxjs";
import { SaleInfoComponent } from '../sale-info/sale-info.component';
import { CommonApplicationService,AlertService,CommonActivityService,CommunicationService } from '@shared-service/_services';
import { apiUrl } from '@config/api-url';
import { StorageService } from '@shared-service/_services/storage.service';
import { CommonHelper } from '@shared-service/_utils/CommonHelper';
import { DatePipe } from '@angular/common';



@Component({
  selector: 'app-lien',
  templateUrl: './lien.component.html',
  styleUrls: ['./lien.component.css']
})
export class LienComponent implements OnInit {
  mortgageLienOneForm: FormGroup;
  private mortgateData : MortgateOtherPropTaxModel;
  result: any;  
  @Input() lienData: any; 
  @Input() sale_info: any;
  @Input() manualSearch: boolean;
  @Input() amortization_info: any;
  @Input() lien:number;
  @Input() no_active_mortgage_lien: boolean;

  lienType={'1':'firstlien','2':'secondlien','3':'thirdlien', '4':'fourthlien','5':'fifthlien'};
  lienTitle={'1':'First Lien','2':'Second Lien','3':'Third Lien','4':'Fourth Lien','5':'Fifth Lien'};

  firstLienAmortization:boolean=false;
  firstLienMod:boolean=false;
  firstLienSub:boolean=false;
  noStrChecked:boolean=false;
  firstLienRed:boolean=false;
  firstLienFor:boolean=false;


  invalidFields: any; 
  submitted = false;
  loading = false;
  loadingMessage: any;
  propErr: boolean = false;
  propErrMsg: any = "";
  property_id: string;
  parsedData: any = {};
  loan_type_list: string[];
  property_config: any= {};

  //Documents
  property_document_type_list: string[];
  notes_btn:any;
  notes_detail:any;
  resetFormSubject: Subject<boolean> = new Subject<boolean>();
  // ----------- Document upload config --------------------//
  collapse:boolean=false;
  @Output() fourthLien=new EventEmitter<boolean>();
  @Output() updateLoanEsBalance=new EventEmitter<number>();

  @Input() excessFunds:any;
  // ----------- Document upload config --------------------//
  constructor(private formBuilder: FormBuilder,          
              private commonApplicationService: CommonApplicationService,
              private commonActivityService: CommonActivityService,
              private storageService:StorageService,
              private alertService: AlertService,
              private route: ActivatedRoute,
              private amortization:AmortizationService,
              private communicationService:CommunicationService,
              private dialog:MatDialog,
              private datePipe:DatePipe) { }

  ngOnInit() {
    this.property_id = this.route.snapshot.paramMap.get('property_id');

    this.property_config =  this.storageService.get("property_config");
    if(this.property_config !== null){
      this.loan_type_list          = this.property_config.loan_type;
      this.property_document_type_list = this.property_config.mortgage_document_type;
    }
    // Load info details
    if(this.property_id !== undefined){
      this.martgageNotes();
      this.updateEsBalance('');
      this.initialize();   
    }
    if(this.no_active_mortgage_lien || this.manualSearch){
      this.collapse=true;
    }
    if(this.lienData.no_str_no_appt){
      this.noStrChecked=true;
    }
   

    this.communicationService.getManualSearch().subscribe(collapse => {
      this.collapse=collapse;
    });
    this.communicationService.getNoActiveLien().subscribe(collapse => {
      this.collapse=collapse;
    });

    
  }

  ngOnChanges(changes: SimpleChanges): void{
    if(changes?.excessFunds?.previousValue && changes?.excessFunds?.previousValue !=changes?.excessFunds?.currentValue){
      this.excessFunds=changes.excessFunds.currentValue;
      this.mortgageLienOneForm.get('es_excess_funds').setValue(this.excessFunds);
    }
  }

  updateEsBalance($event){
    if($event?.target?.value){
      let lienName=this.lienType[this.lien]+'_amount';
      this.sale_info.assessment[lienName] = $event.target.value;
      this.updateLoanEsBalance.emit(this.sale_info);
    }
  }


  martgageNotes(){
    this.notes_btn={'note_name':'mortgage_notes','noteBtn':true,'noteDetail':false,'lien_type':this.lienType[this.lien]};
    this.notes_detail={'note_name':'mortgage_notes','noteBtn':false,'noteDetail':true,'lien_type':this.lienType[this.lien]};
    
  }

  updatedNotes(event){
    if(event)
      this.resetFormSubject.next(true);
  }

  public myDatePickerOptions: IMyDpOptions = { dateFormat: 'mm/dd/yyyy',
  firstDayOfWeek : 'su'};

  onDateChanged(event: IMyDateModel,dateInput) {
      let name=dateInput.elem.nativeElement.getAttribute('formcontrolname');
      let data={'name':name,'value':this.datePipe.transform(event.formatted,'yyyy-MM-dd'),'el':dateInput.elem.nativeElement};
      this.autoSave(data);
      return event.formatted;
    }
 
  initialize(){
     this.mortgageLienOneForm = this.formBuilder.group({
      mortgage_id: [this.lienData.mortgage_id],
      lien_foreclosing: [this.lienData.lien_foreclosing?this.lienData.lien_foreclosing:0],      
      no_str_no_appt: [this.lienData.no_str_no_appt?this.lienData.no_str_no_appt:0],
      lien_type: [''],
      defective_lien: [this.lienData.defective_lien?this.lienData.defective_lien:0],
      lender: [this.lienData.lender], 
      instrument: [this.lienData.instrument],
      lien_amount: [this.lienData.lien_amount],
      date_recorded: [(this.lienData.date_recorded != null)? {jsdate: CommonHelper.getConvertDate(this.lienData.date_recorded)}: null],
      dt_book_page: [this.lienData.dt_book_page],
      assignment_bp: [this.lienData.assignment_bp],
      loan_type: [this.lienData.loan_type],
      loan_term: [this.lienData.loan_term],
      maturity_date: [(this.lienData.maturity_date != null)? {jsdate: CommonHelper.getConvertDate(this.lienData.maturity_date)}: null],
      right_to_cure: [this.lienData.right_to_cure],
      trustee_fees: [this.lienData.trustee_fees],
      str_book_page: [this.lienData.str_book_page],
      str_date: [(this.lienData.str_date != null)? {jsdate: CommonHelper.getConvertDate(this.lienData.str_date)}: null],
      trustee: [this.lienData.trustee],
      reasonable_attorney_fees: [this.lienData.reasonable_attorney_fees?this.lienData.reasonable_attorney_fees:0],
      estimated_a_match: [this.lienData.estimated_a_match?this.lienData.estimated_a_match:0],
      dt_nos: [this.lienData.dt_nos?this.lienData.dt_nos:0],
      est_late_payment_and_fees: [this.lienData.est_late_payment_and_fees],
      est_equity: [this.lienData.est_equity],
      total_est_debt: [this.lienData.total_est_debt],
      amortization_calculation: [this.lienData.amortization_calculation?this.lienData.amortization_calculation:0],
      amortization_annual_interest: [this.lienData.amortization_annual_interest],
      amortization_monthly_payment: [this.lienData.amortization_monthly_payment],
      amortization_monthly_principal_payment: [this.lienData.amortization_monthly_principal_payment],
      amortization_monthly_interest_payment: [this.lienData.amortization_monthly_interest_payment],
      amortization_loan_estimate_balance: [this.lienData.amortization_loan_estimate_balance],
      modification_agreement: [this.lienData.modification_agreement?this.lienData.modification_agreement:0],
      modification_book_page: [this.lienData.modification_book_page],
      modification_date: [(this.lienData.modification_date)? {jsdate: CommonHelper.getConvertDate(this.lienData.modification_date)}: null],
      modification_lien_amount: [this.lienData.modification_lien_amount],
      modification_loan_term: [this.lienData.modification_loan_term],
      modification_maturity_date: [(this.lienData.modification_maturity_date)?   {jsdate: CommonHelper.getConvertDate(this.lienData.modification_maturity_date)}: null],
      modification_annual_interest: [this.lienData.modification_annual_interest],
      modification_monthly_payment: [this.lienData.modification_monthly_payment],
      modification_loan_estimate_balance: [this.lienData.modification_loan_estimate_balance],
      modification_est_late_payment_and_fees: [this.lienData.modification_est_late_payment_and_fees],
      subordination_agreement: [this.lienData.subordination_agreement?this.lienData.subordination_agreement:0],
      sub_a_book_page: [this.lienData.sub_a_book_page],
      sub_a_date: [(this.lienData.sub_a_date)?  {jsdate: CommonHelper.getConvertDate(this.lienData.sub_a_date)}: null],
      sub_lien_position: [this.lienData.sub_lien_position],
      property_owner_1: [this.lienData.property_owner_1?this.lienData.property_owner_1:0],
      property_owner_2: [this.lienData.property_owner_2?this.lienData.property_owner_2:0],
      property_owner_3: [this.lienData.property_owner_3?this.lienData.property_owner_3:0],
      property_owner_4: [this.lienData.property_owner_4?this.lienData.property_owner_4:0],
      company_not_current: [this.lienData.company_not_current?this.lienData.company_not_current:0],
      dtc_first_check: [this.lienData.dtc_first_check?this.lienData.dtc_first_check:0],
      dca_second_check: [this.lienData.dca_second_check?this.lienData.dca_second_check:0],
      dca_final_check: [this.lienData.dca_final_check?this.lienData.dca_final_check:0],
      
      // redemption_info:[this.lienData.redemption_info?this.lienData.redemption_info:0],
      // redemption_notice:[this.lienData.redemption_notice?this.lienData.redemption_notice:''],
      // redemption_date:[(this.lienData.redemption_date)? {jsdate: new Date(this.lienData.redemption_date)}: null],
      // red_by_owner:[this.lienData.red_by_owner?this.lienData.red_by_owner:0],
      // redemption_expires:[(this.lienData.redemption_expires)?{jsdate: new Date(this.lienData.redemption_expires)}: null],
     
      foreclosure_result:[this.lienData.foreclosure_result?this.lienData.foreclosure_result:0],
      trdeep_instrument:[this.lienData.trdeep_instrument?this.lienData.trdeep_instrument:''],
      trdeed_date:[(this.lienData.trdeed_date)? {jsdate: CommonHelper.getConvertDate(this.lienData.trdeed_date)}: null],
      winning_bidder:[this.lienData.winning_bidder?this.lienData.winning_bidder:''],
      winning_bid:[this.lienData.winning_bid?this.lienData.winning_bid:''],
      cma_arv_value:[this.amortization_info?this.amortization_info.cma_arv:''],
      rental_rate:[this.amortization_info?this.amortization_info.rental_rate:''],
      total_debt:[this.lienData.total_debt],
      es_excess_funds:[this.lienData.es_excess_funds!='0.00'?this.lienData.es_excess_funds:this.excessFunds],
      //sale_type:[this.sale_info?.sale_type]
    });
  
    this.firstLienAmortization=this.lienData.amortization_calculation?this.lienData.amortization_calculation:0;
    this.firstLienMod=this.lienData.modification_agreement?this.lienData.modification_agreement:0;
    this.firstLienSub=this.lienData.subordination_agreement?this.lienData.subordination_agreement:0;
    this.firstLienRed=this.lienData.redemption_info?this.lienData.redemption_info:0;
    this.firstLienFor=this.lienData.foreclosure_result?this.lienData.foreclosure_result:0;
    
  }

/*----------------------------- Form validation ---------------------------------------*/
  validateForm(data: any) {     
    this.parsedData = this.commonActivityService.getFullFormDataWithDateFormatted(data);
    this.invalidFields = this.commonActivityService.findInvalidControls(data);
    console.log("Form Invalid Filleds = ",this.invalidFields);
    this.submitted = true;
    console.log('niform');
    if(this.mortgageLienOneForm.invalid) { 
      console.log('Form is invalid, Fill all fields.');
      //this.propertyForm.get('prop_address').markAsTouched();      
      return;
    }    
    // FORM SUBMITTED
    console.log('form submitted');
    if(this.parsedData.reasonable_attorney_fees)
      this.parsedData.reasonable_attorney_fees=1
      
    if(this.parsedData.mortgage_id !== undefined && this.parsedData.mortgage_id > 0){
      this.updateMortgageLienOneForm(this.parsedData);  
    }else{
      this.saveMortgageLienOneForm(this.parsedData);  
    }
  }

/*----------------------------- Form validation ---------------------------------------*/

/*----------------------------- UPdate Details ---------------------------------------*/
  updateMortgageLienOneForm(data: any){
    this.loading = true;
    data.house_id = this.property_id;
    data.lien_type=this.lien;
    let url = apiUrl.mortgage_lien+'/'+data.mortgage_id;
    this.commonApplicationService.put(url, data)
        .subscribe(
            data => {
              this.alertService.success(data.message);  
              this.loading = false;
            },
            error => {
                this.loading = false;
                this.alertService.common(error);  
            }
        ); 
  }

/*----------------------------- UPdate Details ---------------------------------------*/

/*----------------------------- Save Details ---------------------------------------*/
  saveMortgageLienOneForm(data: any){
    this.loading = true;
    data.house_id = this.property_id;
    data.lien_type=this.lien;
    let url = apiUrl.mortgage_lien;
    this.commonApplicationService.post(url, data)
        .subscribe(
            data => {
              this.lienData.mortgage_id = data.data.mortgage_id;
              this.mortgageLienOneForm.controls.mortgage_id.setValue(data.data.mortgage_id);
              this.alertService.success(data.message);  
              this.loading = false;
            },
            error => {
                this.loading = false;
                this.alertService.common(error); 
            }
        ); 
  }

/*----------------------------- Save Details ---------------------------------------*/

  estimated_late_payment(){

    var mortgageInfo = this.commonActivityService.getFullFormDataWithDateFormatted(this.mortgageLienOneForm);
   var data={'lien_amount':mortgageInfo['lien_amount'],
            'date_recorded':mortgageInfo['date_recorded'],
            'maturity_date':mortgageInfo['maturity_date'],
            'str_date':mortgageInfo['str_date'],
            'cma_arv':this.amortization_info?this.amortization_info.cma_arv:0,
            'sale_date':this.amortization_info?this.amortization_info.sale_date:'',
            'nos_date': !mortgageInfo['no_str_no_appt']?this.amortization_info?this.amortization_info.nos_date:'':'',
            'annual_interest':mortgageInfo['amortization_annual_interest']}
    let dialogRef=this.dialog.open(EstimatedLatePaymentsComponent,{ width: '800px',data:data,disableClose:true} );
    dialogRef.afterClosed().subscribe(result => {
      if(result){
        this.mortgageLienOneForm.get('est_late_payment_and_fees').setValue(result.estimated_payment);
        this.mortgageLienOneForm.get('total_est_debt').setValue(result.lien_late_attorney_fee);
        this.mortgageLienOneForm.get('est_equity').setValue(result.est_equity);

      }
    })
  }

  get f() { return this.mortgageLienOneForm.controls; }

  amortizationCalculation(mortgageLienOneForm){

    var mortgageInfo = this.commonActivityService.getFullFormDataWithDateFormatted(mortgageLienOneForm);
    console.log(mortgageInfo);

    var data={'lien_amount':mortgageInfo['lien_amount'],
            'date_recorded':mortgageInfo['date_recorded'],
            'maturity_date':mortgageInfo['maturity_date'],
            'str_date':!mortgageInfo['no_str_no_appt']?mortgageInfo['str_date']:'',
            'annual_interest':mortgageInfo['amortization_annual_interest'],
            'no_str_no_appt':mortgageInfo['no_str_no_appt'],
            'cma_arv':this.amortization_info?this.amortization_info.cma_arv:0,
            'sale_date':this.amortization_info?this.amortization_info.sale_date:'',
            'nos_date':this.amortization_info?this.amortization_info.nos_date:''
          }
      
      var info=this.amortization.amortization_calculate(data);
      if(info){
        this.mortgageLienOneForm.controls.amortization_monthly_interest_payment.setValue(info.monthly_interest);
        this.mortgageLienOneForm.controls.amortization_monthly_principal_payment.setValue(info.monthly_principle);
        this.mortgageLienOneForm.controls.total_est_debt.setValue(info.late_attorney_fee);
        this.mortgageLienOneForm.controls.est_equity.setValue(info.est_equity);
        this.mortgageLienOneForm.controls.est_late_payment_and_fees.setValue(info.estimated_payment);
        this.mortgageLienOneForm.controls.amortization_loan_estimate_balance.setValue(info.est_loan_balance);
        this.mortgageLienOneForm.controls.loan_term.setValue(info.loan_term);
        this.mortgageLienOneForm.controls.amortization_monthly_payment.setValue(info.monthly_payment);
      }
      
  }

  modificationCalculation(mortgageLienOneForm){

    var mortgageInfo = this.commonActivityService.getFullFormDataWithDateFormatted(mortgageLienOneForm);

    var data={'modification_lien_amount':mortgageInfo['modification_lien_amount'],
            'modification_date':mortgageInfo['modification_date'],
            'modification_maturity_date':mortgageInfo['modification_maturity_date'],
            'str_date':!mortgageInfo['no_str_no_appt']?mortgageInfo['str_date']:'',
            'modification_annual_interest':mortgageInfo['modification_annual_interest'],
            'cma_arv':this.amortization_info?this.amortization_info.cma_arv:0,
            'sale_date':this.amortization_info?this.amortization_info.sale_date:'',
            'nos_date':this.amortization_info?this.amortization_info.nos_date:'',
            'no_str_no_appt':mortgageInfo['no_str_no_appt'],
          }
      
      var info=this.amortization.modification_calculate(data);
      if(info){
        this.mortgageLienOneForm.controls.modification_est_late_payment_and_fees.setValue(info.est_late_payment_fee?info.est_late_payment_fee:0);
        this.mortgageLienOneForm.controls.modification_monthly_payment.setValue(info.mod_month_payment?info.mod_month_payment:0);
        this.mortgageLienOneForm.controls.modification_loan_term.setValue(info.mod_loan_term?info.mod_loan_term:0);
        this.mortgageLienOneForm.controls.modification_loan_estimate_balance.setValue(info.loan_est_balance?info.loan_est_balance:0);
      }
      
  }



  viewAmortizationCal(){
    var mortgageInfo = this.commonActivityService.getFullFormDataWithDateFormatted(this.mortgageLienOneForm);

    var data={'lien_amount':mortgageInfo['lien_amount'],
              'date_recorded':mortgageInfo['date_recorded'],
              'maturity_date':mortgageInfo['maturity_date'],
              'str_date':mortgageInfo['str_date'],
              'annual_interest':mortgageInfo['amortization_annual_interest']}
      this.dialog.open(AmortizationComponent,{ width: '800px',data:data,disableClose:true} );

  }

    noStr(e){
      if(e.target.checked){
        this.noStrChecked=true;
        this.mortgageLienOneForm.get('est_late_payment_and_fees').setValue(0);
        this.mortgageLienOneForm.get('total_est_debt').setValue(0);
        this.mortgageLienOneForm.get('modification_est_late_payment_and_fees').setValue(0);
        //this.mortgageLienOneForm.get('amortization_loan_estimate_balance').setValue(0);

      }
      else{
        this.noStrChecked=false;
      }   
    }
  
  defectiveLien(e){
    if(e.target.checked){
      var data={'lien_type':this.lienData.lien_type?this.lienData.lien_type:this.lien,'property_id':this.property_id}
      this.dialog.open(DefectiveNotesComponent,{ width: '520px',data:data,disableClose:true} );
    }
  }

  
  showHidePanel($event){
      this.collapse=$event;
  }


  addFourthLien(){
   this.fourthLien.emit(true);
  }
  viewSaleInfo(){
    this.dialog.open(SaleInfoComponent,{ width: '800px',data:this.sale_info,disableClose:true});
  }

  get viewSaleInfoLink(){
      if(this.lien==1 && this.sale_info && (this.sale_info.sale_type==2 || this.sale_info.sale_type==21))
      {
        return true;
      }
      else if(this.lien==2  && this.sale_info &&  (this.sale_info.sale_type==3 || this.sale_info.sale_type==22)){
        return true;
      }
      else if(this.lien==3  && this.sale_info &&  (this.sale_info.sale_type==4 || this.sale_info.sale_type==23)){
        return true;
      }else{
        return false;
      }
  }

  autoSave(data){

    this.commonActivityService.addLoader(data['el']);
    this.commonActivityService.removeElement(data['el'],'saved-icon');
    
    let index=data.el.parentElement.parentElement.getAttribute('id');
    let id = this.mortgageLienOneForm.get('mortgage_id').value;
    
    let saveInfo:any={'mortgage_id':id,'lien_type':this.lien,
                      'name':data['name'],'value':data['value']};
    if(!id){
      this.loading=true;
    }
    this.updateMortgageInfo(saveInfo,data);
  }

  updateMortgageInfo(saveInfo,data){

      let url = apiUrl.auto_save_mortgage_lien_record+'/'+this.property_id;
      this.commonApplicationService.put(url, saveInfo)
      .subscribe(
          response => {
            if(response['status']=='failed'){
              this.commonActivityService.removeElement(data['el'],'loader-icon');
              this.alertService.error(response['message']);
            }else{
                this.commonActivityService.addElement(data['el']);
                this.commonActivityService.removeElement(data['el'],'loader-icon');
                this.mortgageLienOneForm.controls.mortgage_id.setValue(response.data.mortgage_id);
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
  onInputFieldChanged(event,input) {
    console.log(input);
    console.log('onInputFieldChanged(): Value: ', event.value, ' - dateFormat: ', event.dateFormat, ' - valid: ', event.valid);
  }

}

