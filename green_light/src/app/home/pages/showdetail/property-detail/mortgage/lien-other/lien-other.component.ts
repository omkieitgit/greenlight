import { Component, OnInit, Input, ViewChild } from '@angular/core';
import { FormControl, NgForm, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { MortgateOtherPropTaxModel } from '../mortgage.model';
import { Router,ActivatedRoute } from '@angular/router';
import {IMyDpOptions, IMyDateModel} from 'mydatepicker';
import { Subject } from "rxjs";
import { SaleInfoComponent } from '../sale-info/sale-info.component';
import { MatDialog } from '@angular/material/dialog';
import {AmortizationService} from '../amortization.service';

import { CommonApplicationService,AlertService,CommonActivityService,CommunicationService } from '@shared-service/_services';
import { apiUrl } from '@config/api-url';
import { StorageService } from '@shared-service/_services/storage.service';
import { CommonHelper } from '@shared-service/_utils/CommonHelper';
import { DatePipe } from '@angular/common';

@Component({
  selector: 'lien-other',
  templateUrl: './lien-other.component.html'
})
export class LienOtherComponent implements OnInit {
  mortgageLienOtherForm: FormGroup;
  private mortgateData : MortgateOtherPropTaxModel;
  result: any;  
  @Input() lienOtherData: any; 
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

  // Document 
  doc_other_name: string;
  case_no: string;
  property_document_type: number;  
  property_file: string;
  uploadUrl: string;
  uploadedFiles: any[] = [];
  showLienOtherDocList: boolean = false;
  property_document_type_list: string[];
  public documentDataLienOther:any =  [];
  collapse:boolean=false;
  otherLienRed:boolean=false;
  notes_btn:any={'note_name':'mortgage_notes','noteBtn':true,'noteDetail':false,'lien_type':'otherlien'};
  notes_detail:any={'note_name':'mortgage_notes','noteBtn':false,'noteDetail':true,'lien_type':'otherlien'};
  resetFormSubject: Subject<boolean> = new Subject<boolean>();
  lien:string='other';

  constructor(private formBuilder: FormBuilder,          
          private commonApplicationService: CommonApplicationService,
          private commonActivityService: CommonActivityService,
          private storageService:StorageService,
          private alertService: AlertService,
          private router: Router,
          private communicationService:CommunicationService,
          private amortization:AmortizationService,
          private route: ActivatedRoute,
          private datePipe:DatePipe,
          private dialog:MatDialog) { 
            this.property_config =  this.storageService.get("property_config");
                if(this.property_config !== null){
                  this.property_document_type_list = this.property_config.mortgage_document_type;
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
    this.mortgageLienOtherForm = this.formBuilder.group({
     mortgage_id: [this.lienOtherData.mortgage_id],
     lender: [this.lienOtherData.lender, Validators.required],
     lien_amount: [this.lienOtherData.lien_amount],
     date_recorded: [(this.lienOtherData.date_recorded != null)? 
       {jsdate: CommonHelper.getConvertDate(this.lienOtherData.date_recorded)}: null],
     book_page_assignment_bp: [this.lienOtherData.book_page_assignment_bp],
     assignment_bp: [this.lienOtherData.assignment_bp],
      prop_sign_owner_1: [this.lienOtherData.property_owner_1?this.lienOtherData.property_owner_1:0],
      prop_sign_owner_2: [this.lienOtherData.property_owner_2?this.lienOtherData.property_owner_2:0],
      prop_sign_owner_3: [this.lienOtherData.property_owner_3?this.lienOtherData.property_owner_3:0],
      prop_sign_owner_4: [this.lienOtherData.property_owner_4?this.lienOtherData.property_owner_4:0],
      company_not_current: [this.lienOtherData.company_not_current?this.lienOtherData.company_not_current:0],
      dtc_first_check: [this.lienOtherData.dtc_first_check?this.lienOtherData.dtc_first_check:0],
      dca_second_check: [this.lienOtherData.dca_second_check?this.lienOtherData.dca_second_check:0],
      dca_final_check: [this.lienOtherData.dca_final_check?this.lienOtherData.dca_final_check:0],
      

      redemption_info:[this.lienOtherData.redemption_info?this.lienOtherData.redemption_info:0],
      redemption_notice:[this.lienOtherData.redemption_notice?this.lienOtherData.redemption_notice:''],
      redemption_date:[(this.lienOtherData.redemption_date)? {jsdate: CommonHelper.getConvertDate(this.lienOtherData.redemption_date)}: null],
      red_by_owner:[this.lienOtherData.red_by_owner?this.lienOtherData.red_by_owner:0],
      redemption_expires:[(this.lienOtherData.redemption_expires)?{jsdate: CommonHelper.getConvertDate(this.lienOtherData.redemption_expires)}: null],
     
    });

    //this.commonActivityService.isDisabled("PROPERTY_INFO", this.mortgageLienOtherForm);
    this.otherLienRed=this.lienOtherData.redemption_info?this.lienOtherData.redemption_info:0;

  }


/*----------------------------- Form validation ---------------------------------------*/
  validateForm(data: any) {     
    let result = this.commonActivityService.getFullFormDataWithDateFormatted(data);
    this.invalidFields = this.commonActivityService.findInvalidControls(data);
    console.log("Form Invalid Filleds = ",this.invalidFields);
    this.submitted = true;
    console.log('niform');
    if(this.mortgageLienOtherForm.invalid) { 
      console.log('Form is invalid, Fill all fields.');
      //this.propertyForm.get('prop_address').markAsTouched();      
      return;
    }    
    // FORM SUBMITTED
    console.log('form submitted');
    this.savemortgageLienOtherForm(result);  
  }

/*----------------------------- Form validation ---------------------------------------*/

/*----------------------------- Save Details ---------------------------------------*/
  savemortgageLienOtherForm(data: any){
    this.loading = true;
    data.house_id = this.property_id;
    //console.log("savingdata>"+this.propertyForm);
    //console.log("savingdata>"+this.mortgageLienOtherForm);
    console.log("savingdata>"+data);
    let url = apiUrl.mortgage+'/other/'+this.property_id;
    this.commonApplicationService.put(url, data)
        .subscribe(
            data => {
              this.alertService.success(data.message);  
              this.loading = false;
            },
            error => {
              this.loading = false;
                this.alertService.error(data.message);  
            }
        ); 
  }

/*----------------------------- Save Details ---------------------------------------*/

 get f() { return this.mortgageLienOtherForm.controls; }
 
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
    let url = apiUrl.auto_save_mortgage_record+'/other/'+this.property_id;
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
