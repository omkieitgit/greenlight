import { Component,Input, OnInit,ViewChild, ComponentFactoryResolver, ViewContainerRef } from '@angular/core';
import {  FormBuilder, FormGroup, Validators } from '@angular/forms';
import {BidderComponent} from '../bidder/bidder.component';
import { Router,ActivatedRoute } from '@angular/router';
import { SaleBidderModel } from './sale.model';
import { CommonApplicationService,AlertService,CommonActivityService,CommunicationService } from '@shared-service/_services';
import { apiUrl } from '@config/api-url';
import { StorageService } from '@shared-service/_services/storage.service';
import { formConstants } from '@config/forms-constants';
import {IMyDpOptions, IMyDateModel} from 'mydatepicker';
import {DatePipe} from '@angular/common';
import { CommonHelper } from '@shared-service/_utils/CommonHelper';

@Component({
  selector: 'app-sale-date',
  templateUrl: './sale-date.component.html',
  styleUrls: ['./sale-date.component.css'],
})
export class SaleDateComponent implements OnInit {
  //private saleData : SaleModel;
  saleDateForm: FormGroup;
  property_config: any= {};
  sale_type_option: string [];
  sale_status_option: string [];
  property_id: string;
  propErr: boolean = false;
  propErrMsg: string = "";
  saleResult: any;    
  loading = false;
  loadingMessage: boolean;
  bidderResult: any;
  _ref:any; 
  _bidder:any; 
  sale_date_count: number;
  @Input() saleData: any;
  bidderDataLength: number;
  bidder_count:number=2;
  saleDateId: number;
  counter_10: number [];
  bidderOneDeta:any;
  loadBidderOne:boolean=false;
  nos_by_id:number;
  im_by_id:number;
  trustee_caller_id:number;
  auction_by_id:number;

  property_document_type_list: string[];
  saleDateTitle:any={'sale_date':'','sale_time':'','opening_bid':'','case_number':''};
  user_info:any;

  trusteeDate:boolean=false;
  nosDate:boolean=false;
  imDate:boolean=false;
  auctionDate:boolean=false;

  @Input() nosImTrusteeList;
  is_admin:boolean;
  autoLoader:boolean=false;
  beforeTrusteeNotes:string;
  afterTrusteeNotes:string;
// ----------------------------------- Document upload config ---------------------------------------//

  @ViewChild('bidderParent', { read: ViewContainerRef }) container: ViewContainerRef;

  constructor(private formBuilder: FormBuilder,private _cfr: ComponentFactoryResolver,
    private storageService:StorageService,
    private router: Router,
    private datePipe:DatePipe,
    private route: ActivatedRoute,
    private commonApplicationService: CommonApplicationService,
    private commonActivityService: CommonActivityService,
    private alertService: AlertService,
    private helper:CommonHelper,
    private communicationService:CommunicationService) {

   // this.saleData       = new SaleModel();
    this.counter_10     = formConstants.counter_10;
    this.property_config =  this.storageService.get("property_config");
    if(this.property_config !== null){
        this.sale_type_option = this.property_config.sale_type;
        this.sale_status_option= this.property_config.sale_status;
        this.property_document_type_list = this.property_config.sale_doc_type;
    }
   }

   public myDatePickerOptions: IMyDpOptions = { dateFormat: 'mm/dd/yyyy',firstDayOfWeek : 'su'};

  onDateChanged(event: IMyDateModel,dateInput) {
      let name=dateInput.elem.nativeElement.getAttribute('formcontrolname');
      let data={'name':name,'value':this.datePipe.transform(event.formatted,'yyyy-MM-dd'),'el':dateInput.elem.nativeElement};
      this.autoSave(data);
      return event.formatted;
    }


  onDateSelect(event){
    console.log(event);
  }

  submitted = false;
  ngOnInit() {
    this.property_id = this.route.snapshot.paramMap.get('property_id');
    this.user_info=this.storageService.get('user_info');
    this.is_admin=this.user_info.current_role=='admin'?true:false;
    // Load info details
    if(this.property_id !== undefined){
      console.log(this.nosImTrusteeList);
      
      this.propErr = true;
      this.getSaleDetails(); 
      this.isTrusteeNotesExist();
    }

  }


  sale_date_title(){
    let result = this.commonActivityService.getFullFormDataWithDateFormatted(this.saleDateForm);
    this.saleDateTitle.sale_date=result['sale_date'];
    this.saleDateTitle.sale_time=result['sale_time'];
    this.saleDateTitle.opening_bid=result['opening_bid'];
    this.saleDateTitle.case_number=result['case_number'];
    this.saleDateTitle.sale_type=result['sale_type']?this.sale_type_option[result['sale_type']]:'';

  }

// ----------------------------------- Form ---------------------------------------//
  initialize(){
    
    this.saleDateForm = this.formBuilder.group({
      
      sale_date: [(this.saleData.sale_date != null)? {jsdate: this.changeTimezone(this.saleData.sale_date)}: null,Validators.required],
      case_number: [this.saleData.case_number,[Validators.required,Validators.pattern("^[A-Za-z0-9? ]+$")]],
      opening_bid: [this.saleData.opening_bid],
      sale_type: [this.saleData.sale_type],
      sale_status: [this.saleData.sale_status],
      sale_place: [this.saleData.sale_place,Validators.required],
      sale_time: [this.saleData.sale_time,Validators.required],
      trustee:[this.saleData.trustee,Validators.required],
      trustee_file_no: [this.saleData.trustee_file_no,Validators.required],
      trustee_scraped: [{value: this.saleData.trustee_scraped, disabled: true}],
      trustee_url:[this.saleData.trustee_url,Validators.required],
      trustee_address:[this.saleData.trustee_address,Validators.required],
      trustee_phone:[this.saleData.trustee_phone,Validators.required],
      trustee_hours:[{value: this.saleData.trustee_hours, disabled: true}],
      notice_of_foreclosure:[this.saleData.notice_of_foreclosure,Validators.required],
      //legal_notice_url:[this.saleData.legal_notice_url,Validators.required],
      //legal_date_pulled: [(this.saleData.legal_date_pulled != null)? {jsdate:this.changeTimezone(this.saleData.legal_date_pulled)}: null,Validators.required],
      nos_by:[this.is_admin?this.saleData.nos_by:this.saleData.nos_by?this.saleData.nos.first_name+' '+this.saleData.nos.last_name:''],
      nos_date:[(this.saleData.nos_date != null)? {jsdate: this.changeTimezone(this.saleData.nos_date)}: null],
      auction_com_url:[this.saleData.auction_com_url],
      auction_date_pulled: [(this.saleData.auction_date_pulled != null)? {jsdate: this.changeTimezone(this.saleData.auction_date_pulled)}: null],
      newspapaer_url:[this.saleData.newspapaer_url,Validators.required],
      newspapaer_date_pulled: [(this.saleData.newspapaer_date_pulled != null)? {jsdate:this.changeTimezone(this.saleData.newspapaer_date_pulled)}: null,Validators.required],
      news_paper_nos:[this.saleData.news_paper_nos],
      before_sale_trustee_notes: [this.saleData.before_sale_trustee_notes],
      after_sale_trustee_notes: [this.saleData.after_sale_trustee_notes],
      priceint:[this.saleData.priceint],
      book:[this.saleData.book,Validators.required],
      page_number:[this.saleData.page_number,Validators.required],
     // redemption_expires: [(this.saleData.redemption_expires != null)? {jsdate: this.changeTimezone(this.saleData.redemption_expires)}: null],
      house_id:[this.property_id],
      sale_id:[this.saleData.sale_id],
      im_by:[this.is_admin?this.saleData.im_by:this.saleData.im?this.saleData.im.first_name+' '+this.saleData.im.last_name:''],
      im_date:[(this.saleData.im_date != null)? {jsdate:this.changeTimezone(this.saleData.im_date)}: null],
      trustee_caller:[this.is_admin?this.saleData.trustee_caller:this.saleData.trustee_callers?this.saleData.trustee_callers.first_name+' '+this.saleData.trustee_callers.last_name:''],
      trustee_caller_date:[(this.saleData.trustee_caller_date != null)? {jsdate:this.changeTimezone(this.saleData.trustee_caller_date)}: null],
      auction_by:[this.is_admin?this.saleData.auction_by:this.saleData.auction?this.saleData.auction.first_name+' '+this.saleData.auction.last_name:''],
      auction_date:[(this.saleData.auction_date != null)? {jsdate:this.changeTimezone(this.saleData.auction_date)}: null],
      
      
    });

    this.disableUserField();

    this.propErr = false;
    setTimeout(()=>{ 
      this.loadFirstBidder();
      this.loadOtherBidder();
     }, 1000);    
     this.sale_date_title();
    //this.commonActivityService.isDisabled("SALE_DETAILS", this.saleDateForm);
  }

  disableUserField(){
    if(this.saleDateForm.get('nos_by').value && this.user_info.current_role!='admin'){
      this.nosDate=this.f.nos_date?true:false;
      this.saleDateForm.controls['nos_by'].disable();
    }
    if(this.saleDateForm.get('im_by').value && this.user_info.current_role!='admin'){
      this.imDate=this.f.im_date?true:false;
      this.saleDateForm.controls['im_by'].disable();
    }
    if(this.saleDateForm.get('trustee_caller').value && this.user_info.current_role!='admin'){
      this.trusteeDate=this.f.trustee_caller_date?true:false;
      this.saleDateForm.controls['trustee_caller'].disable();
    }
    if(this.saleDateForm.get('auction_by').value && this.user_info.current_role!='admin'){
      this.auctionDate=this.f.auction_date?true:false;
      this.saleDateForm.controls['auction_by'].disable();
    }
  }
// ----------------------------------- Form ---------------------------------------//

loadFirstBidder(){
  if(this.bidderDataLength){  
    this.bidderOneDeta=this.bidderResult[0];
  }else{
    this.bidderOneDeta= new SaleBidderModel();
  }
  this.loadBidderOne=true;
}

// ----------------------------------- Loading Bidder Forms-----------------------//
  loadOtherBidder(){
    
    if(this.bidderDataLength>1){            
            for(var i=1; i<this.bidderDataLength; i++){
              var comp = this._cfr.resolveComponentFactory(BidderComponent);
              var bidderComponent = this.container.createComponent(comp);
              bidderComponent.instance._bidder_ref = bidderComponent;
              bidderComponent.instance.bidder_count=this.bidder_count;
              bidderComponent.instance.bidderData  =  this.bidderResult[i];
              bidderComponent.instance.saleDateId  =  this.saleData.sale_id;
              this.bidder_count++;
            }
    }else{
          var comp = this._cfr.resolveComponentFactory(BidderComponent);
          var bidderComponent = this.container.createComponent(comp);
          bidderComponent.instance._bidder_ref = bidderComponent;
          bidderComponent.instance.bidder_count=this.bidder_count;
          bidderComponent.instance.bidderData = new SaleBidderModel();
          bidderComponent.instance.saleDateId  =  this.saleData.sale_id;
          this.bidder_count++;
    }
    
  }
// ----------------------------------- Loading Bidder Forms-----------------------//

// ----------------------------------- Form Validation ---------------------------------------//
  validateSaleDateForm(data: any) {     
    let result = this.commonActivityService.getFullFormDataWithDateFormatted(data);
    let invalidFields = this.commonActivityService.findInvalidControls(data);
    this.submitted = true;
    console.log('niform');
    if(this.saleDateForm.invalid) { 
      console.log('Form is invalid, Fill all fields.');
      //this.propertyForm.get('prop_address').markAsTouched();      
      return;
    }    
    this.sale_date_title();
    // FORM SUBMITTED
    console.log('form submitted');

    if(result['sale_id'] !== undefined && result['sale_id'] != null && result['sale_id'] > 0){
      this.updateSaleDetails(result);  // Update sale
    }else{
      this.saveSaleDetails(result);  // Save fresh sale
    }
    
  }

// ----------------------------------- Form Validation ---------------------------------------//

// ----------------------------------- Form Save  ---------------------------------------//
  saveSaleDetails(data: any){
    this.loading = true;
    if(!this.is_admin){
      data['nos_by']=this.nos_by_id?this.user_info['id']:this.saleData.nos_by;
      data['im_by']=this.im_by_id?this.user_info['id']:this.saleData.im_by;
      data['trustee_caller']=this.trustee_caller_id?this.user_info['id']:this.saleData.trustee_caller;
      data['auction_by']=this.auction_by_id?this.user_info['id']:this.saleData.auction_by;
    }
      

    let url = apiUrl.sale_detail;
    this.commonApplicationService.post(url, data)
        .subscribe(
          response => {
              this.saleData.sale_id = response.data.sale_id;
              this.saleDateForm.controls.sale_id.setValue(response.data.sale_id); // legal_nos_name
              if(response.status=='success'){
                this.alertService.success(response.message); 
                this.disableUserField(); 
              }else{
                this.alertService.error(response.message);  
              }
              this.loading = false;
            },
            error => {
              this.alertService.common(error);
                this.loading = false;
            }
        ); 
  }
// ----------------------------------- Form Save  ---------------------------------------//

// ----------------------------------- Update Sale Form  ------------------------------//
 updateSaleDetails(data: any){
    this.loading = true;
    
    if(!this.is_admin){
    
      data['nos_by']=this.nos_by_id?this.user_info['id']:this.saleData.nos_by;
      data['im_by']=this.im_by_id?this.user_info['id']:this.saleData.im_by;
      data['trustee_caller']=this.trustee_caller_id?this.user_info['id']:this.saleData.trustee_caller;
      data['auction_by']=this.auction_by_id?this.user_info['id']:this.saleData.auction_by;

    }

    let url = apiUrl.sale_detail+"/"+data.sale_id;
    this.commonApplicationService.put(url, data)
        .subscribe(
          response => {
              if(response.status=='success'){
                this.disableUserField();
                this.alertService.success(response.message);  
              }else{
                this.alertService.error(response.message);  
              }            
              this.loading = false;
            },
            error => {
              this.alertService.common(error);
                this.loading = false;
            }
        ); 
  }

// ----------------------------------- Update Sale Form  ------------------------------//

// ----------------------------------- Get Sale Form  ------------------------------//
  getSaleDetails(){
      this.loadingMessage = true;
      this.initialize();
      this.loadingMessage = false;
      if(this.saleData.sale_id !== undefined && this.saleData.sale_id != null){
       // this.formatDocumentsSale();
        this.bidderDataLength = this.saleData.bidders.length;
        this.saleDateId=this.saleData.sale_id;
        if(this.bidderDataLength){
          this.bidderResult = this.saleData.bidders;
        }        
      }
      //this.saleData = this.sale_date_data;
  }
// ----------------------------------- Get Sale Form  ------------------------------//

  get f() { return this.saleDateForm.controls; }

  removeObjectTEST(){
    this._ref.destroy();
  }   


  more_bidder_date(){    
   
    var comp = this._cfr.resolveComponentFactory(BidderComponent);
    var bidderComponent = this.container.createComponent(comp);
    bidderComponent.instance._bidder_ref = bidderComponent;
    bidderComponent.instance.bidder_count=this.bidder_count;
    bidderComponent.instance.bidderData = new SaleBidderModel();
    bidderComponent.instance.saleDateId  =  this.saleData.sale_id;
    this.bidder_count++;
  }

  nos_user(element){
    if(this.checkEditAccess('nos_by') && !this.f.nos_by.value){
      let userName=this.user_info['first_name']+' '+this.user_info['last_name'];
      this.f.nos_by.setValue(userName); // legal_nos_name
      this.nos_by_id=this.user_info['id'];
      this.autoSave({'name':'nos_by','value':this.user_info['id'],'el':element});

    }
  }
  
  im_by_user(element){
    if(this.checkEditAccess('im_by') && !this.f.im_by.value){
      let userName=this.user_info['first_name']+' '+this.user_info['last_name'];
      this.f.im_by.setValue(userName); // legal_nos_name
      this.im_by_id=this.user_info['id'];
      this.autoSave({'name':'im_by','value':this.user_info['id'],'el':element});
    }
  }
  trustee_caller_user(element){
    if(this.checkEditAccess('trustee_caller') && !this.f.trustee_caller.value){
      let userName=this.user_info['first_name']+' '+this.user_info['last_name'];
      this.f.trustee_caller.setValue(userName); // legal_nos_name
      this.trustee_caller_id=this.user_info['id'];
      this.autoSave({'name':'trustee_caller','value':this.user_info['id'],'el':element});
    }
  }

  auction_user(element){
    if(this.checkEditAccess('auction_by') && !this.f.auction_by.value){
      let userName=this.user_info['first_name']+' '+this.user_info['last_name'];
      this.f.auction_by.setValue(userName); // legal_nos_name
      this.auction_by_id=this.user_info['id'];
      this.autoSave({'name':'auction_by','value':this.user_info['id'],'el':element});
    }
  }

  checkEditAccess(userRole){
    if(this.user_info.current_role=='admin' || this.user_info.current_role==userRole){
      return true;
    }
  }

  changeTimezone(date) { 
    return CommonHelper.getConvertDate(date);
  }


  autoSave(data){

    this.commonActivityService.addLoader(data['el']);
    this.commonActivityService.removeElement(data['el'],'saved-icon');
    this.commonActivityService.removeElement(data['el'],'error-icon');
    //let index=data.el.parentElement.parentElement.getAttribute('id');
    let id = this.saleDateForm.get('sale_id').value;
    let saveInfo:any={'id':id,'name':data['name'],'value':data['value']};
    
    let validater=this.saleDateForm.get(data['name']).validator;
    if(validater && validater.length>0){
      this.saleDateForm.get(data['name']).setValue(data['value']);
      if(this.saleDateForm.get(data['name']).valid){
        this.autoSaveInfo(id,saveInfo,data);
      }else{
        this.commonActivityService.addErrorElement(data['el']);
        this.commonActivityService.removeElement(data['el'],'loader-icon');
      }
    }else{
      this.autoSaveInfo(id,saveInfo,data);
    }
   }

  autoSaveInfo(id,saveInfo,data){
    if(!id){
      this.saleDateForm.disable();
      this.loading=true;
    }
    this.updatePriceHistoryInfo(saveInfo,data);
  }

  updatePriceHistoryInfo(saveInfo,data){
      let url = apiUrl.save_single_sale_record+'/'+this.property_id;
      this.commonApplicationService.put(url, saveInfo)
      .subscribe(
          response => {
            if(response['status']=='failed'){
              this.commonActivityService.removeElement(data['el'],'loader-icon');
              this.alertService.error(response['message']);
            }else{
                this.commonActivityService.addElement(data['el']);
                this.commonActivityService.removeElement(data['el'],'loader-icon');
                this.saleDateForm.controls.sale_id.setValue(response.data);
                this.communicationService.setSaleId(response.data);
                this.saleData.sale_id=response.data;
                // if(response?.trustee_notes){
                //   let itemIndex = this.saleData.sale_trustee_notes.findIndex(item => item.id == response?.trustee_notes.id);
                //   if(itemIndex == -1){
                //     this.saleData.sale_trustee_notes.unshift(response?.trustee_notes);
                //   }
                //   this.isTrusteeNotesExist();
                // }
            }
            this.saleDateForm.enable();
            this.loading=false;
          },
          error => {
            this.commonActivityService.removeElement(data['el'],'loader-icon');
            this.loading = false;
            this.alertService.common(error);
          }
      ); 
  }

  isTrusteeNotesExist(){
    if(this.saleData.sale_trustee_notes){
      this.beforeTrusteeNotes=this.saleData.sale_trustee_notes.find(temp=>temp.before_sale_trustee_notes);
    }
    if(this.saleData.sale_trustee_notes){
      this.afterTrusteeNotes=this.saleData.sale_trustee_notes.find(temp=>temp.after_sale_trustee_notes);
    }
    
  }
  
}
