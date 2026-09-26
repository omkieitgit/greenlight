import { Component, OnInit, Input } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { MortgateOtherPropTaxModel } from '../mortgage.model';
import { Router,ActivatedRoute } from '@angular/router';
import {IMyDpOptions, IMyDateModel} from 'mydatepicker';
import {MatDialog} from '@angular/material/dialog';
import {DefectiveNotesComponent} from '../defective-notes/defective-notes.component';
import {AmortizationService} from '../amortization.service';
import { Subject } from "rxjs";
import { SaleInfoComponent } from '../sale-info/sale-info.component';

import { CommonApplicationService,AlertService,CommonActivityService,CommunicationService } from '@shared-service/_services';
import { apiUrl } from '@config/api-url';
import { StorageService } from '@shared-service/_services/storage.service';
import { CommonHelper } from '@shared-service/_utils/CommonHelper';
import { DatePipe } from '@angular/common';

@Component({
  selector: 'lien-hoa',
  templateUrl: './lien-hoa.component.html'
})
export class LienHoaComponent implements OnInit {
  mortgageHoaForm: FormGroup;
  private mortgateData : MortgateOtherPropTaxModel;
  result: any;  
  @Input() lienHoaData: any;
  @Input() manualSearch: boolean;
  @Input() sale_info: any;

  invalidFields: any; 
  submitted = false;
  loading = false;
  loadingMessage: any;
  propErr: boolean = false;
  propErrMsg: any = "";
  property_id: string;
  parsedData: any = {};
  property_config: any= {};
  tax_code:any;
  date:Date;

  // Document 
  doc_other_name: string;
  case_no: string;
  property_document_type: number;  
  property_file: string;
  uploadUrl: string;
  uploadedFiles: any[] = [];
  showLienHoaDocList: boolean = false;
  property_document_type_list: string[];
  public documentDataLienHoa:any =  [];
  collapse:boolean=false;
  noStrChecked:boolean=false;
  manualSearchFlag:boolean=false;
  hoaLienRed:boolean=false;
  hoaLienFor:boolean=false;
  lien:string='hoa';
  defectiveNotesLine:number=5;

  hoa_lien_priorty_btn:any={'note_name':'hoa_lien_priorty','noteBtn':true,'noteDetail':false,'lien_type':'hoalien_priorty'};
  hoa_lien_priorty_detail:any={'note_name':'hoa_lien_priorty','noteBtn':false,'noteDetail':true,'lien_type':'hoalien_priorty'};
  hoaLienSubject: Subject<boolean> = new Subject<boolean>();

  hoa_rental_policy_btn:any={'note_name':'hoa_rental_policy','noteBtn':true,'noteDetail':false,'lien_type':'hoalien_policy'};
  hoa_rental_policy_detail:any={'note_name':'hoa_rental_policy','noteBtn':false,'noteDetail':true,'lien_type':'hoalien_policy'};

  notes_btn:any={'note_name':'mortgage_notes','noteBtn':true,'noteDetail':false,'lien_type':'hoalien'};
  notes_detail:any={'note_name':'mortgage_notes','noteBtn':false,'noteDetail':true,'lien_type':'hoalien'};
  resetFormSubject: Subject<boolean> = new Subject<boolean>();

 hoaSaleType:any=[5,21,22,23,24];
 excessFunds:number=0;

  constructor(private formBuilder: FormBuilder,          
          private commonApplicationService: CommonApplicationService,
          private commonActivityService: CommonActivityService,
          private storageService:StorageService,private alertService: AlertService,
          private router: Router,
          private route: ActivatedRoute,
          private dialog:MatDialog,            
          private amortization:AmortizationService,
          private datePipe:DatePipe,
          private communicationService:CommunicationService) {
          this.property_config =  this.storageService.get("property_config");
                if(this.property_config !== null){
                  this.property_document_type_list = this.property_config.mortgage_document_type;
                  this.tax_code=this.property_config.tax_code;
                }
          }

  ngOnInit() {
    this.property_id = this.route.snapshot.paramMap.get('property_id');
    // Load info details
    if(this.property_id !== undefined){
      this.updateEsBalance('');
      this.initialize();
      //this.propErr = true;
      //this.getSchoolDetails();    
    }
    if(this.manualSearch){
      this.collapse=true;
    }
    this.communicationService.getManualSearch().subscribe(collapse => {
      this.collapse=collapse;
    });
    if(this.lienHoaData.no_str_no_appt){
      this.noStrChecked=true;
    }
    if(this.lienHoaData.manual_search_hoa)
      this.manualSearchFlag=true;
    
  }

  updatedNotes(event){
    if(event)
      this.resetFormSubject.next(true);
  }

 

  public myDatePickerOptions: IMyDpOptions = { dateFormat: 'mm/dd/yyyy',firstDayOfWeek : 'su'};

    onDateChanged(event: IMyDateModel,dateInput) {
      let name=dateInput.elem.nativeElement.getAttribute('formcontrolname');
      let data={'name':name,'value':this.datePipe.transform(event.formatted,'yyyy-MM-dd'),'el':dateInput.elem.nativeElement};
      this.autoSave(data);
          return event.formatted;
    }
 
  initialize(){
     this.mortgageHoaForm = this.formBuilder.group({
      mortgage_id: [this.lienHoaData.mortgage_id],
      defective_notice_hoa: [this.lienHoaData.defective_notice_hoa?this.lienHoaData.defective_notice_hoa:0],
      hoa_lien_foreclosing: [this.lienHoaData.hoa_lien_foreclosing?this.lienHoaData.hoa_lien_foreclosing:0],
      manual_search_hoa: [this.lienHoaData.manual_search_hoa?this.lienHoaData.manual_search_hoa:0],
      no_str: [this.lienHoaData.no_str?this.lienHoaData.no_str:0],
      hoa_name: [this.lienHoaData.hoa_name],
      hoa_lien_amount: [this.lienHoaData.hoa_lien_amount],
      date_of_hoa_lien: [(this.lienHoaData.date_of_hoa_lien != null)? 
        {jsdate: CommonHelper.getConvertDate(this.lienHoaData.date_of_hoa_lien)}: null],
      hoa_lien_book_page: [this.lienHoaData.hoa_lien_book_page],
      str_date: [(this.lienHoaData.str_date != null)? 
        {jsdate: CommonHelper.getConvertDate(this.lienHoaData.str_date)}: null],
      str_book_page: [this.lienHoaData.str_book_page],
      trustee_hoa: [this.lienHoaData.trustee_hoa],
      prop_sign_owner_1: [this.lienHoaData.prop_sign_owner_1?this.lienHoaData.prop_sign_owner_1:0],
      prop_sign_owner_2: [this.lienHoaData.prop_sign_owner_2?this.lienHoaData.prop_sign_owner_2:0],
      prop_sign_owner_3: [this.lienHoaData.prop_sign_owner_3?this.lienHoaData.prop_sign_owner_3:0],
      prop_sign_owner_4: [this.lienHoaData.prop_sign_owner_4?this.lienHoaData.prop_sign_owner_4:0],
      company_not_ct_rcd: [this.lienHoaData.company_not_ct_rcd?this.lienHoaData.company_not_ct_rcd:0],
      dtc_first_check: [this.lienHoaData.dtc_first_check?this.lienHoaData.dtc_first_check:0],
      dca_second_check: [this.lienHoaData.dca_second_check?this.lienHoaData.dca_second_check:0],
      dca_final_check: [this.lienHoaData.dca_final_check?this.lienHoaData.dca_final_check:0],
      dcc_rs_instrument:[this.lienHoaData.dcc_rs_instrument?this.lienHoaData.dcc_rs_instrument:''],
      dcc_rs_date:[(this.lienHoaData.dcc_rs_date != null)?{jsdate: CommonHelper.getConvertDate(this.lienHoaData.dcc_rs_date)}: null],
      
      redemption_info:[this.lienHoaData.redemption_info?this.lienHoaData.redemption_info:0],
      redemption_notice:[this.lienHoaData.redemption_notice?this.lienHoaData.redemption_notice:''],
      redemption_date:[(this.lienHoaData.redemption_date)? {jsdate:CommonHelper.getConvertDate(this.lienHoaData.redemption_date)}: null],
      red_by_owner:[this.lienHoaData.red_by_owner?this.lienHoaData.red_by_owner:0],
      redemption_expires:[(this.lienHoaData.redemption_expires)?{jsdate: CommonHelper.getConvertDate(this.lienHoaData.redemption_expires)}: null],
      tax_code: [this.lienHoaData.tax_code],

      foreclosure_result:[this.lienHoaData.foreclosure_result?this.lienHoaData.foreclosure_result:0],
      trdeep_instrument:[this.lienHoaData.trdeep_instrument?this.lienHoaData.trdeep_instrument:''],
      trdeed_date:[(this.lienHoaData.trdeed_date)? {jsdate: CommonHelper.getConvertDate(this.lienHoaData.trdeed_date)}: null],
      winning_bidder:[this.lienHoaData.winning_bidder?this.lienHoaData.winning_bidder:''],
      winning_bid:[this.lienHoaData.winning_bid?this.lienHoaData.winning_bid:''],

      affidavit_date:[(this.lienHoaData.affidavit_date != null)?{jsdate: CommonHelper.getConvertDate(this.lienHoaData.affidavit_date)}: null],
      total_debt:[this.lienHoaData.total_debt],
      es_excess_funds:[this.lienHoaData.es_excess_funds!='0.00'?this.lienHoaData.es_excess_funds:this.excessFunds],

      // Documents
      property_document_type:[''],
      doc_other_name: [''],
      prop_document_date: [null],
      case_no: [''],
      property_file: ['']
    }); 
    
    this.hoaLienRed=this.lienHoaData.redemption_info?this.lienHoaData.redemption_info:0;
    this.hoaLienFor=this.lienHoaData.foreclosure_result?this.lienHoaData.foreclosure_result:0;

  }

  updateEsBalance($event){
    
    let hoaAmount=parseFloat(this.lienHoaData?.hoa_lien_amount || 0);
    if($event){
      hoaAmount=$event.target.value;
    }
    this.excessFunds=parseFloat(this.sale_info?.last_bidder?.amount_of_bid || 0)-
      (hoaAmount);
  }
    
// ----------------------------------- Form Documents ----------------------------//
/*----------------------------- Form validation ---------------------------------------*/
  validateForm(data: any) {     
    this.parsedData = this.commonActivityService.getFullFormDataWithDateFormatted(data);
    this.invalidFields = this.commonActivityService.findInvalidControls(data);
    console.log("Form Invalid Filleds = ",this.invalidFields);
    this.submitted = true;
    console.log('niform');
    if(this.mortgageHoaForm.invalid) { 
      console.log('Form is invalid, Fill all fields.');
      //this.propertyForm.get('prop_address').markAsTouched();      
      return;
    }    
    // FORM SUBMITTED
    console.log('form submitted');
    this.saveHoaForm(this.parsedData);  
  }

/*----------------------------- Form validation ---------------------------------------*/

/*----------------------------- Save Details ---------------------------------------*/
  saveHoaForm(data: any){
    this.loading = true;
    data.house_id = this.property_id;
    //console.log("savingdata>"+this.propertyForm);
    console.log("savingdata>"+data);
    let url = apiUrl.mortgage+'/hoa/'+this.property_id;
    this.commonApplicationService.put(url, data)
        .subscribe(
            data => {
              //this.lienHoaData.mortgage_id.setValue(data.mortgage_id);
              //this.mortgageHoaForm.controls.house_id.setValue(data.house_id);
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

 get f() { return this.mortgageHoaForm.controls; }
 
 noStr(e){
    if(e.target.checked){
      this.noStrChecked=true;
    }
    else{
      this.noStrChecked=false;
    }   
  }

  defectiveLien(e){
    if(e.target.checked){
      var data={'lien_type':this.defectiveNotesLine,'property_id':this.property_id}
      this.dialog.open(DefectiveNotesComponent,{ width: '800px',data:data,disableClose:true} );
    }
  }

  manualSearchHoa(e){
    if(e.target.checked){
      this.manualSearchFlag=true;
    }
    else{
      this.manualSearchFlag=false;
    }   
  }
  
  viewSaleInfo(){
    this.dialog.open(SaleInfoComponent,{ width: '800px',data:this.sale_info,disableClose:true});
  }
  showHidePanel($event){
    this.collapse=$event;
  }

  selectTaxCode(event,element){
    
    event.target.value;
    let affidaviteDate=this.mortgageHoaForm.get('affidavit_date').value;
    
    if(affidaviteDate && event.target.value==3){
      let days=180;
      this.date = new Date(affidaviteDate.jsdate);
      this.date.setDate( this.date.getDate() +days);
      this.mortgageHoaForm.get('redemption_expires').setValue({jsdate:this.date});
      this.autoSave({'name':'redemption_expires','value':this.datePipe.transform(this.date,'yyyy-MM-dd'),'el':element.elem.nativeElement});
    }
    else if(this.sale_info.sale_date && event.target.value==4){
      let days=90;
      this.date = new Date(this.sale_info.sale_date);
      this.date.setDate( this.date.getDate() +days);
      this.mortgageHoaForm.get('redemption_expires').setValue({jsdate:this.date});
      this.autoSave({'name':'redemption_expires','value':this.datePipe.transform(this.date,'yyyy-MM-dd'),'el':element.elem.nativeElement});
    }

    
  }

  autoSave(data){

    this.commonActivityService.addLoader(data['el']);
    this.commonActivityService.removeElement(data['el'],'saved-icon');
    
    let index=data.el.parentElement.parentElement.getAttribute('id');    
    let saveInfo:any={'name':data['name'],'value':data['value']};
    
    this.updateMortgageInfo(saveInfo,data);
  }

  updateMortgageInfo(saveInfo,data){
    let url = apiUrl.auto_save_mortgage_record+'/hoa/'+this.property_id;
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
        },
        error => {
          this.loading = false;
          this.commonActivityService.removeElement(data['el'],'loader-icon');
          this.alertService.common(error);
        }
    ); 
  }
}
