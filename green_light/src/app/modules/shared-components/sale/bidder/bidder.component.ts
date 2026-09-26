import { Component,  OnInit,Input } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ActivatedRoute } from '@angular/router';
import {IMyDpOptions, IMyDateModel} from 'mydatepicker';
import { CommonHelper } from '@shared-service/_utils/CommonHelper';
import { CommonApplicationService,AlertService,CommonActivityService, CommunicationService } from '@shared-service/_services';
import { apiUrl } from '@config/api-url';
import { StorageService } from '@shared-service/_services/storage.service';
import { DatePipe } from '@angular/common';
import { Subscription } from 'rxjs';


@Component({
  selector: 'app-bidder',
  templateUrl: './bidder.component.html',
  styleUrls: ['./bidder.component.css']
})
export class BidderComponent implements OnInit {
  _bidder_ref:any;
  _checker_ref:any;
  bidder_count:number;
  @Input() bidderData: any;
  @Input() saleDateId: number;
  bidderForm: FormGroup;  
  property_id: string;
  propErr: boolean = false;
  propErrMsg: any = "";
  saleResult: any;    
  loading = false;
  loadingMessage: any;

  uploadUrl: string;
  uploadedFiles: any[] = [];
  showBidderDocList: boolean = false;
  property_document_type_list: string[];
  document_type: string[];
  
  public documentDataBidder:any =  [];
  uploadLoading:boolean=false;
  property_config: any= {};
  bidder_info:any;
  bidder_id:number;
  autoLoader:boolean=false;
  saleIdSub:Subscription;
  propertyInfo:object;
  im_by_id:number;
  nos_by_id:number;
  im_check_by_id:number;
  cmaArv:number;
  user_info: any;
  imCheckedDate:boolean=false;
  nosDate:boolean=false;
  imDate:boolean=false;

  constructor(private route: ActivatedRoute,
              private commonApplicationService: CommonApplicationService,
              private commonActivityService: CommonActivityService,
              private alertService: AlertService,
              private formBuilder: FormBuilder,
              private storageService:StorageService,
              private datePipe:DatePipe,
              private communicationService:CommunicationService
              ) { 
              }

    submitted = false;
    ngOnInit() {
     this.property_id = this.route.snapshot.paramMap.get('property_id');
     this.property_config =  this.storageService.get("property_config");
     this.propertyInfo= this.storageService.getHard("property_info");
     this.cmaArv=parseInt(this.propertyInfo['recommended_cma_arv']); 
     this.user_info=this.storageService.get('user_info');
     if(this.property_config !== null){
         this.property_document_type_list = this.property_config.property_document_type;
         this.document_type=this.property_config.doc_type;
     }
      // Load info details
      if(this.property_id !== undefined){
        this.propErr = true;
        this.setDefaultInfoBidder();
        this.getBidderDetails();    
      }

      if(this.saleIdSub) this.saleIdSub.unsubscribe();
      this.saleIdSub=this.communicationService.getSaleId().subscribe(saleId=>{
          if(!this.saleDateId)
              this.saleDateId=saleId;
      });

    }
    onDateChanged(event: IMyDateModel,dateInput) {
      let name=dateInput.elem.nativeElement.getAttribute('formcontrolname');
      let data={'name':name,'value':this.datePipe.transform(event.formatted,'yyyy-MM-dd'),'el':dateInput.elem.nativeElement};
      this.autoSave(data);
      return event.formatted;
    }
    public myDatePickerOptions: IMyDpOptions = { dateFormat: 'mm/dd/yyyy',firstDayOfWeek : 'su'};

    setDefaultInfoBidder(){
      if(this.bidder_count==2){
        let bidder_info={'attorney_name':this.bidderData.attorney_name,
                         'attorney_address':this.bidderData.attorney_address,
                         'attorney_city':this.bidderData.attorney_city,
                         'attorney_zipcode':this.bidderData.attorney_zipcode,
                         'attorney_phone':this.bidderData.attorney_phone,
                        }
        this.storageService.set('bidder_info',bidder_info);
      }else{
        this.bidder_info=this.storageService.get('bidder_info');
      }
    }


// ----------------------------------- Form ---------------------------------------//
    initialize(){

    this.bidderForm = this.formBuilder.group({
      bidder_id:[this.bidderData.bidder_id],
      name_upset_bidder: [this.bidderData.name_upset_bidder,Validators.required],
      address: [this.bidderData.address],
      city: [this.bidderData.city],
      zipcode:[this.bidderData.zipcode],
      phone: [this.bidderData.phone],
      email: [this.bidderData.email],
      attorney_name: [this.bidderData.attorney_name?this.bidderData.attorney_name:this.bidder_info?this.bidder_info.attorney_name:''],
      attorney_address: [this.bidderData.attorney_address?this.bidderData.attorney_address:this.bidder_info?this.bidder_info.attorney_address:''],
      attorney_city: [this.bidderData.attorney_city?this.bidderData.attorney_city:this.bidder_info?this.bidder_info.attorney_city:''],
      attorney_zipcode: [this.bidderData.attorney_zipcode?this.bidderData.attorney_zipcode:this.bidder_info?this.bidder_info.attorney_zipcode:''],
      attorney_phone: [this.bidderData.attorney_phone?this.bidderData.attorney_phone:this.bidder_info?this.bidder_info.attorney_phone:''],
      amount_of_bid: [this.bidderData.amount_of_bid],
      deposit_clerk: [this.bidderData.deposit_clerk],
      filling_date: [(this.bidderData.filling_date != null)? {jsdate: CommonHelper.getConvertDate(this.bidderData.filling_date)}: null],
      last_date_to_upset_bid: [(this.bidderData.last_date_to_upset_bid != null)? {jsdate:CommonHelper.getConvertDate(this.bidderData.last_date_to_upset_bid)}: null],
      last_date_to_next_upset_bid: [(this.bidderData.last_date_to_next_upset_bid != null)? {jsdate:CommonHelper.getConvertDate(this.bidderData.last_date_to_next_upset_bid)}: null],
      min_amt_nxt_ub: [this.bidderData.min_amt_nxt_ub],
      deposit_amt_nxt_ub: [this.bidderData.deposit_amt_nxt_ub],
      deputy_csc: [this.bidderData.deputy_csc?this.bidderData.deputy_csc:0],
      assistant_csc: [this.bidderData.assistant_csc?this.bidderData.assistant_csc:0],
      clerk_superior_court: [this.bidderData.clerk_superior_court?this.bidderData.clerk_superior_court:0],
      bidder_notes: [this.bidderData.bidder_notes],
      sale_id:[this.saleDateId],
      house_id:[this.property_id],
      im_by:[this.bidderData.im?this.bidderData.im.first_name+' '+this.bidderData.im.last_name:''],
      im_date:[(this.bidderData.im_date != null) ? {jsdate:CommonHelper.getConvertDate(this.bidderData.im_date)} : null],
      im_checked_by:[this.bidderData.im_checked?this.bidderData.im_checked.first_name+' '+this.bidderData.im_checked.last_name:''],
      im_checker_date:[(this.bidderData.im_checker_date != null) ? {jsdate:CommonHelper.getConvertDate(this.bidderData.im_checker_date)} : null],
      
      property_document_type_sale:[''],
      doc_other_name_sale: [''],
      prop_document_date_sale: [null],
      case_no_sale: [''],
      property_file_sale: [''],
      auction:[this.bidderData.auction?this.bidderData.auction:''],
      nos_by:[this.bidderData.nos?this.bidderData.nos.first_name+' '+this.bidderData.nos.last_name:''],
      nos_date:[(this.bidderData.nos_date != null) ? {jsdate:CommonHelper.getConvertDate(this.bidderData.nos_date)} : null],

    });
    this.disableUserField();
    this.propErr = false;
    this.bidder_id=this.bidderData.bidder_id;
    //this.commonActivityService.isDisabled("SALE_DETAILS", this.bidderForm);
  }

  disableUserField(){
    if(this.bidderForm.get('nos_by').value && this.user_info.current_role!='admin'){
      this.nosDate=this.f.nos_date?true:false;
      this.bidderForm.controls['nos_by'].disable();
    }
    if(this.bidderForm.get('im_by').value && this.user_info.current_role!='admin'){
      this.imDate=this.f.im_date?true:false;
      this.bidderForm.controls['im_by'].disable();
    }
    if(this.bidderForm.get('im_checked_by').value && this.user_info.current_role!='admin'){
      this.imCheckedDate=this.f.im_checker_date?true:false;
      this.bidderForm.controls['im_checked_by'].disable();
    }
  }

// ----------------------------------- Form ---------------------------------------//

// ----------------------------------- Form Validation ----------------------------//
  validateBidderForm(data: any) {     
    let result = this.commonActivityService.getFullFormDataWithDateFormatted(data);
    let invalidFields = this.commonActivityService.findInvalidControls(data);
    this.submitted = true;
    if(this.bidderForm.invalid) { 
      console.log('Form is invalid, Fill all fields.');
      return;
    }    
    // FORM SUBMITTED
    if(result['bidder_id'] !== undefined && result['bidder_id'] != null && result['bidder_id'] > 0){
      this.updateBidderDetails(result);  // Update sale
    }else{
      this.saveBidderDetails(result);  // Save fresh sale
    }
    
  }

// ----------------------------------- Form Validation ----------------------------//

// ----------------------------------- Get Bidder Details  ------------------------------//
  getBidderDetails(){
    this.loadingMessage = true;
    this.initialize();
    this.loadingMessage = false;
    //this.formatDocumentsSale();
  }
// ----------------------------------- Get Bidder Details  ------------------------------//


// ----------------------------------- Form Save  ---------------------------------------//
  saveBidderDetails(data: any){
    data=this.updateData(data);
    this.loading = true;
    let url = apiUrl.bidder_detail;
    this.commonApplicationService.post(url, data)
        .subscribe(
          response => {
              this.bidderForm.controls.bidder_id.setValue(response.data.bidder_id);
              this.bidder_id=response.data.bidder_id;
              if(this.bidder_count==2){
                let bidder_info={'attorney_name':data.attorney_name,
                                 'attorney_address':data.attorney_address,
                                 'attorney_city':data.attorney_city,
                                 'attorney_zipcode':data.attorney_zipcode,
                                 'attorney_phone':data.attorney_phone,
                                }
                this.storageService.set('bidder_info',bidder_info);
              }
              if(response.status=='success'){
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
// ----------------------------------- Form Save  ---------------------------------------//
getUserId(){
  return this.storageService.get('user_info')['id'];
}
updateData(data){
  if(data['im_by']){
    data['im_by']=this.bidderData.im_by;
  }
  if(data['nos_by']){
    data['nos_by']=this.bidderData.im_by;
  }
  if(data['im_checked_by']){
    data['im_checked_by']=this.bidderData.im_checked_by;
  }
   
  data['nos_by']=this.nos_by_id?this.getUserId():'';
  data['im_by']=this.im_by_id?this.getUserId():'';
  data['im_checked_by']=this.im_check_by_id?this.getUserId():'';
  return data;
}
// ----------------------------------- Update Sale Form  ------------------------------//
 updateBidderDetails(data: any){
    this.loading = true;
    data=this.updateData(data);
    let url = apiUrl.bidder_detail+"/"+data.bidder_id;
    this.commonApplicationService.put(url, data)
        .subscribe(
            response => {
              if(response.status=='success'){
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

  get f() { return this.bidderForm.controls; }

  im_by_user(element){
    if(this.checkEditAccess('im_by') && !this.f.im_by.value){
      this.im_by_id=this.storageService.get('user_info')['id'];
      let userName=this.storageService.get('user_info')['first_name']+' '+this.storageService.get('user_info')['last_name'];
      this.bidderForm.controls.im_by.setValue(userName); // legal_nos_name
      this.autoSave({'name':'im_by','value':this.storageService.get('user_info')['id'],'el':element});
    }
  }

  im_checked_by_user(element){
    if(this.checkEditAccess('im_checked_by') && !this.f.im_checked_by.value){
      this.im_check_by_id=this.storageService.get('user_info')['id'];
      let userName=this.storageService.get('user_info')['first_name']+' '+this.storageService.get('user_info')['last_name'];
      this.bidderForm.controls.im_checked_by.setValue(userName); // legal_nos_name
      this.autoSave({'name':'im_checked_by','value':this.storageService.get('user_info')['id'],'el':element});
    }
  }

  nos_user(element){
    if(this.checkEditAccess('nos_by') && !this.f.nos_by.value){
      this.nos_by_id=this.storageService.get('user_info')['id'];
      let userName=this.storageService.get('user_info')['first_name']+' '+this.storageService.get('user_info')['last_name'];
      this.bidderForm.controls.nos_by.setValue(userName); // legal_nos_name
      this.autoSave({'name':'nos_by','value':this.storageService.get('user_info')['id'],'el':element});
  
    }
  }
  // ----------------------------------- Form Documents ----------------------------//

  uploadHandlerSale(event){
    let elem = event.target;  //line 2 
    if(elem.files.length > 0){
      this.bidderForm['controls']['property_file_sale'].setErrors({'required': false});
    }
  }


  // ----------------------------------- Form Documents ----------------------------//

  calculateBidderAmount(){
    let amount_of_bid=this.bidderForm.controls.amount_of_bid.value;

      if (parseFloat(amount_of_bid) < 15001)
      {
          //return true;
      }
      // //minimum_amount_next_upset_bid1
      var result = parseFloat(amount_of_bid) * 1.05;
      if (isNaN(result))
          result = 0;

      this.bidderForm.controls.min_amt_nxt_ub.setValue(result.toFixed(2));

       var _upset_result = result * 0.05;
       if (isNaN(_upset_result))
           _upset_result = 0;
      
       this.bidderForm.controls.deposit_amt_nxt_ub.setValue(_upset_result.toFixed(2));

       var deposit_result = amount_of_bid * 0.05;
       if (isNaN(deposit_result))
           deposit_result = 0;
       
        this.bidderForm.controls.deposit_clerk.setValue(deposit_result.toFixed(2));

  }

  calculateMinBidderAmount(){
    let amount_of_bid=this.bidderForm.controls.min_amt_nxt_ub.value;
    var _upset_result = parseFloat(amount_of_bid) * 0.05;

    if (isNaN(_upset_result))
        _upset_result = 0;

    this.bidderForm.controls.deposit_amt_nxt_ub.setValue(_upset_result.toFixed(2));

  }

  autoSave(data){

    this.commonActivityService.addLoader(data['el']);
    this.commonActivityService.removeElement(data['el'],'saved-icon');
    //let index=data.el.parentElement.parentElement.getAttribute('id');
    let id = this.bidderForm.get('bidder_id').value;
    let saveInfo:any={'id':id,'sale_id':this.saleDateId,'name':data['name'],'value':data['value']};
    if(!id){
      this.bidderForm.disable();
      this.loading=true;
    }
    this.updatePriceHistoryInfo(saveInfo,data);
  }

  updatePriceHistoryInfo(saveInfo,data){

      let url = apiUrl.save_single_bidder_record+'/'+this.property_id;
      this.commonApplicationService.put(url, saveInfo)
      .subscribe(
          response => {
            if(response['status']=='failed'){
              this.commonActivityService.removeElement(data['el'],'loader-icon');
              this.alertService.error(response['message']);
            }else{
                this.commonActivityService.addElement(data['el']);
                this.commonActivityService.removeElement(data['el'],'loader-icon');
                this.bidderForm.controls.bidder_id.setValue(response.data);
                this.bidder_id=response.data;
            }
            this.bidderForm.enable();
            this.loading=false;
          },
          error => {
            this.commonActivityService.removeElement(data['el'],'loader-icon');
            this.loading = false;
            this.alertService.common(error);
          }
      ); 
  }

  checkEditAccess(userRole){
    if(this.user_info.current_role=='admin' || this.user_info.current_role==userRole){
      return true;
    }
  }
  removeBidder($event){
    if($event==true && this.bidderData.bidder_id){
        let url = apiUrl.remove_bidder+'/'+this.bidderData.bidder_id;
        this.commonApplicationService.delete(url).subscribe(response => {
          if(response !== undefined){             
              this.alertService.success(response.message); 
          }
        },
        (err: any) => {
          this.alertService.error("Error occured, Please try again later!");
        })
    }
  }
}
