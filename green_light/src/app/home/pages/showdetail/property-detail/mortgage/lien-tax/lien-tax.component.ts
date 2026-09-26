import { Component, OnInit, Input, ViewChild } from '@angular/core';
import { FormControl, NgForm, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { MortgateOtherPropTaxModel } from '../mortgage.model';
import { Router,ActivatedRoute } from '@angular/router';
import {IMyDpOptions, IMyDateModel} from 'mydatepicker';
import {MatDialog, MatDialogRef, MAT_DIALOG_DATA} from '@angular/material/dialog';
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
  selector: 'app-lien-tax',
  templateUrl: './lien-tax.component.html',
  styleUrls: ['./lien-tax.component.css']
})
export class LienTaxComponent implements OnInit {

  mortgageTaxForm: FormGroup;
  private mortgateData : MortgateOtherPropTaxModel;
  result: any;  
  @Input() lienTaxData: any;
  @Input() manualSearch: boolean;
  @Input() sale_info: any;
  @Input() excessFunds:any;

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
  
  // Document 
  doc_other_name: string;
  case_no: string;
  property_document_type: number;  
  property_file: string;
  uploadUrl: string;
  uploadedFiles: any[] = [];
  showLienTaxDocList: boolean = false;
  property_document_type_list: string[];
  public documentDataLienTax:any =  [];
  collapse:boolean=false;
  noStrChecked:boolean=false;
  manualSearchFlag:boolean=false;
  taxLienRed:boolean=false;
  taxLienFor:boolean=false;
  lien:string='tax';
  defectiveTaxLine:number=6;

  notes_btn:any={'note_name':'mortgage_notes','noteBtn':true,'noteDetail':false,'lien_type':'taxlien'};
  notes_detail:any={'note_name':'mortgage_notes','noteBtn':false,'noteDetail':true,'lien_type':'taxlien'};
  resetFormSubject: Subject<boolean> = new Subject<boolean>();
  
 
  constructor(private formBuilder: FormBuilder,          
          private commonApplicationService: CommonApplicationService,
          private commonActivityService: CommonActivityService,
          private storageService:StorageService,private alertService: AlertService,
          private router: Router,
          private route: ActivatedRoute,
          private dialog:MatDialog, 
          private datePipe:DatePipe,           
          private amortization:AmortizationService,
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
    if(this.lienTaxData.no_str_no_appt){
      this.noStrChecked=true;
    }
    if(this.lienTaxData.manual_search_tax)
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
     this.mortgageTaxForm = this.formBuilder.group({
      mortgage_id: [this.lienTaxData.mortgage_id],
      defective_notice_tax: [this.lienTaxData.defective_notice_tax?this.lienTaxData.defective_notice_tax:0],
      tax_lien_foreclosing: [this.lienTaxData.tax_lien_foreclosing?this.lienTaxData.tax_lien_foreclosing:0],
      tax_code: [this.lienTaxData.tax_code],

      tax_name: [this.lienTaxData.tax_name],
      tax_lien_amount: [this.lienTaxData.tax_lien_amount],
      date_of_tax_lien: [(this.lienTaxData.date_of_tax_lien != null)? 
        {jsdate: CommonHelper.getConvertDate(this.lienTaxData.date_of_tax_lien)}: null],
      tax_lien_instrument: [this.lienTaxData.tax_lien_instrument],
      tax_lien_cause: [this.lienTaxData.tax_lien_cause],
      sheriff_tax: [this.lienTaxData.sheriff_tax],
      prop_sign_owner_1: [this.lienTaxData.prop_sign_owner_1?this.lienTaxData.prop_sign_owner_1:0],
      prop_sign_owner_2: [this.lienTaxData.prop_sign_owner_2?this.lienTaxData.prop_sign_owner_2:0],
      prop_sign_owner_3: [this.lienTaxData.prop_sign_owner_3?this.lienTaxData.prop_sign_owner_3:0],
      prop_sign_owner_4: [this.lienTaxData.prop_sign_owner_4?this.lienTaxData.prop_sign_owner_4:0],
      company_not_ct_rcd: [this.lienTaxData.company_not_ct_rcd?this.lienTaxData.company_not_ct_rcd:0],
      dtc_first_check: [this.lienTaxData.dtc_first_check?this.lienTaxData.dtc_first_check:0],
      dca_second_check: [this.lienTaxData.dca_second_check?this.lienTaxData.dca_second_check:0],
      dca_final_check: [this.lienTaxData.dca_final_check?this.lienTaxData.dca_final_check:0],
      dcc_rs_instrument:[this.lienTaxData.dcc_rs_instrument?this.lienTaxData.dcc_rs_instrument:''],
      
      redemption_info:[this.lienTaxData.redemption_info?this.lienTaxData.redemption_info:0],
      redemption_notice:[this.lienTaxData.redemption_notice?this.lienTaxData.redemption_notice:''],
      redemption_date:[(this.lienTaxData.redemption_date)? {jsdate:CommonHelper.getConvertDate(this.lienTaxData.redemption_date)}: null],
      red_by_owner:[this.lienTaxData.red_by_owner?this.lienTaxData.red_by_owner:0],
      redemption_expires:[(this.lienTaxData.redemption_expires)?{jsdate: CommonHelper.getConvertDate(this.lienTaxData.redemption_expires)}: null],
     
      foreclosure_result:[this.lienTaxData.foreclosure_result?this.lienTaxData.foreclosure_result:0],
      trdeep_instrument:[this.lienTaxData.trdeep_instrument?this.lienTaxData.trdeep_instrument:''],
      trdeed_date:[(this.lienTaxData.trdeed_date)? {jsdate: CommonHelper.getConvertDate(this.lienTaxData.trdeed_date)}: null],
      winning_bidder:[this.lienTaxData.winning_bidder?this.lienTaxData.winning_bidder:''],
      winning_bid:[this.lienTaxData.winning_bid?this.lienTaxData.winning_bid:''],
      es_excess_funds:[this.excessFunds],

      // Documents
      property_document_type:[''],
      doc_other_name: [''],
      prop_document_date: [null],
      case_no: [''],
      property_file: ['']
    }); 
    
    this.taxLienRed=this.lienTaxData.redemption_info?this.lienTaxData.redemption_info:0;
    this.taxLienFor=this.lienTaxData.foreclosure_result?this.lienTaxData.foreclosure_result:0;

  }


    

  //  rowDeleteLienHoa(row:any){

  //   console.log("InfoCOmp>>",row);
  //     //this.commonApplicationService.get(url,data,sucess_message,error_message);
  //     let url = apiUrl.mortgage_hoa_document+'/'+row.document_mortgage_hoa_id;
  //     this.commonApplicationService.delete(url).subscribe(response => {
  //       if(response !== undefined){             
  //            this.alertService.success(response.message); 
  //             var index =this.documentDataLienHoa.indexOf(row);
  //             if (index !== -1) {
  //                this.documentDataLienHoa.splice(index, 1);
  //                this.docLength = this.documentDataLienHoa.length;
  //             }
  //       }
  //       this.documentChangeLienHoa();
  //     },
  //       (err: any) => {
  //         this.alertService.common(err); 
  //       })

  // }
// ----------------------------------- Form Documents ----------------------------//
/*----------------------------- Form validation ---------------------------------------*/
  validateForm(data: any) {     
    this.parsedData = this.commonActivityService.getFullFormDataWithDateFormatted(data);
    this.invalidFields = this.commonActivityService.findInvalidControls(data);
    console.log("Form Invalid Filleds = ",this.invalidFields);
    this.submitted = true;
    console.log('niform');
    if(this.mortgageTaxForm.invalid) { 
      console.log('Form is invalid, Fill all fields.');
      //this.propertyForm.get('prop_address').markAsTouched();      
      return;
    }    
    // FORM SUBMITTED
    console.log('form submitted');
    this.saveTaxForm(this.parsedData);  
  }

/*----------------------------- Form validation ---------------------------------------*/

/*----------------------------- Save Details ---------------------------------------*/
  saveTaxForm(data: any){
    this.loading = true;
    data.house_id = this.property_id;
    //console.log("savingdata>"+this.propertyForm);
    console.log("savingdata>"+data);
    let url = apiUrl.mortgage+'/tax/'+this.property_id;
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

 get f() { return this.mortgageTaxForm.controls; }
 
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
      var data={'lien_type':this.defectiveTaxLine,'property_id':this.property_id}
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

  autoSave(data){

    this.commonActivityService.addLoader(data['el']);
    this.commonActivityService.removeElement(data['el'],'saved-icon');
    
    let index=data.el.parentElement.parentElement.getAttribute('id');    
    let saveInfo:any={'name':data['name'],'value':data['value']};
    
    this.updateMortgageInfo(saveInfo,data);
  }

  updateMortgageInfo(saveInfo,data){
    let url = apiUrl.auto_save_mortgage_record+'/tax/'+this.property_id;
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