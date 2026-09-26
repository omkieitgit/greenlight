import { Component,  OnInit,Input,ViewChild, ViewContainerRef, SimpleChanges } from '@angular/core';
import { FormBuilder, FormGroup, Validators,FormControl } from '@angular/forms';
import { ActivatedRoute } from '@angular/router';
import {IMyDpOptions, IMyDateModel} from 'mydatepicker';
import { CommonHelper } from '@shared-service/_utils/CommonHelper';
import { CommonApplicationService,AlertService,CommonActivityService, CommunicationService } from '@shared-service/_services';
import { apiUrl } from '@config/api-url';
import { StorageService } from '@shared-service/_services/storage.service';
import { DatePipe } from '@angular/common';
import { Subscription } from 'rxjs';

@Component({
  selector: 'app-bidder-one',
  templateUrl: './bidder-one.component.html',
  styleUrls: ['./bidder-one.component.css']
})
export class BidderOneComponent implements OnInit {
 
  bidder_count:number=1;
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
  public documentDataBidder:any =  [];
  uploadLoading:boolean=false;
  property_config: any= {};
  autoLoader:boolean=false;
  bidder_id:number;
  saleIdSub:Subscription;
  im_by_id:number;
  nos_by_id:number;
  im_checked_by_id:number;
  propertyInfo:object;
  cmaArv:number;
  user_info:any;
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
              ) { }


        submitted = false;
        ngOnInit() {
          this.property_id = this.route.snapshot.paramMap.get('property_id');
          this.property_config =  this.storageService.get("property_config");
          this.propertyInfo= this.storageService.getHard("property_info");
          this.cmaArv=parseInt(this.propertyInfo['recommended_cma_arv']); 
          this.user_info=this.storageService.get('user_info');
          if(this.property_config !== null){
              this.property_document_type_list = this.property_config.doc_type;
          }
          // Load info details
          if(this.property_id !== undefined){
            this.propErr = true;
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
    
    // ----------------------------------- Form ---------------------------------------//
        initialize(){
        this.bidderForm = this.formBuilder.group({
          bidder_id:[this.bidderData.bidder_id?this.bidderData.bidder_id:''],
          bidder_name: [this.bidderData.bidder_name],
          bid_amount: [this.bidderData.bid_amount],
          name_upset_bidder: [this.bidderData.name_upset_bidder,Validators.required],
          amount_of_bid: [this.bidderData.amount_of_bid],
          bid_date: [(this.bidderData.bid_date != null)? {jsdate:CommonHelper.getConvertDate(this.bidderData.bid_date)}: null],
          last_date_to_upset_bid: [(this.bidderData.last_date_to_upset_bid != null)? {jsdate:CommonHelper.getConvertDate(this.bidderData.last_date_to_upset_bid)}: null],
          min_amt_nxt_ub: [this.bidderData.min_amt_nxt_ub],
          email: [this.bidderData.email],
          address: [this.bidderData.address],
          phone: [this.bidderData.phone],
          fax: [this.bidderData.fax],
          date_of_sale: [(this.bidderData.date_of_sale != null) ? {jsdate:CommonHelper.getConvertDate(this.bidderData.date_of_sale)} : null],
          date_of_report: [(this.bidderData.date_of_report != null) ? {jsdate:CommonHelper.getConvertDate(this.bidderData.date_of_report)} : null],
          deposit_upset: [this.bidderData.deposit_upset],
          name_of_mortage: [this.bidderData.name_of_mortage],
          name_of_cryer: [this.bidderData.name_of_cryer],
          bid_confirmed: [this.bidderData.bid_confirmed?this.bidderData.bid_confirmed:0],
          bid_upset: [this.bidderData.bid_upset?this.bidderData.bid_upset:0],
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
          bidder_notes: [this.bidderData.bidder_notes],
          nos_by:[this.bidderData.nos?this.bidderData.nos.first_name+' '+this.bidderData.nos.last_name:''],
          nos_date:[(this.bidderData.nos_date != null) ? {jsdate:CommonHelper.getConvertDate(this.bidderData.nos_date)} : null],
        
        });
    
        this.propErr = false;
        this.bidder_id=this.bidderData.bidder_id;
        this.disableUserField();
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
      data['im_checked_by']=this.im_checked_by_id?this.getUserId():'';
      return data;
    }
    // ----------------------------------- Form Save  ---------------------------------------//
      saveBidderDetails(data: any){
        
        this.loading = true;
        data=this.updateData(data);
        let url = apiUrl.bidder_detail;
        this.commonApplicationService.post(url, data)
            .subscribe(
              response => {
                  this.bidder_id=response.data.bidder_id;
                  this.bidderForm.controls.bidder_id.setValue(response.data.bidder_id);
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
          let userName=this.storageService.get('user_info')['first_name']+' '+this.storageService.get('user_info')['last_name'];
          this.bidderForm.controls.im_by.setValue(userName); // legal_nos_name
          this.im_by_id=this.storageService.get('user_info')['id'];
          this.autoSave({'name':'im_by','value':this.storageService.get('user_info')['id'],'el':element});
        }
      }

      im_checked_by_user(element){
        if(this.checkEditAccess('im_checked_by') && !this.f.im_checked_by.value){
          this.im_checked_by_id=this.storageService.get('user_info')['id'];
          let userName=this.storageService.get('user_info')['first_name']+' '+this.storageService.get('user_info')['last_name'];
          this.bidderForm.controls.im_checked_by.setValue(userName); // legal_nos_name
          this.autoSave({'name':'im_checked_by','value':this.storageService.get('user_info')['id'],'el':element});
        }  
      }

      nos_user(element){
        if(this.checkEditAccess('nos_by') && !this.f.nos_by.value){
          let userName=this.storageService.get('user_info')['first_name']+' '+this.storageService.get('user_info')['last_name'];
          this.bidderForm.controls.nos_by.setValue(userName); // legal_nos_name
          this.nos_by_id=this.storageService.get('user_info')['id'];
          this.autoSave({'name':'nos_by','value':this.storageService.get('user_info')['id'],'el':element});
        }
      }
    
    
    
      
    
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
          
           this.bidderForm.controls.deposit_upset.setValue(_upset_result.toFixed(2));
        
      }
    
      calculateMinBidderAmount(){
        let amount_of_bid=this.bidderForm.controls.min_amt_nxt_ub.value;
        var _upset_result = parseFloat(amount_of_bid) * 0.05;
    
        if (isNaN(_upset_result))
            _upset_result = 0;
    
        this.bidderForm.controls.deposit_upset.setValue(_upset_result.toFixed(2));
    
      }


    autoSave(data){

      this.commonActivityService.addLoader(data['el']);
      this.commonActivityService.removeElement(data['el'],'saved-icon');
      //let index=data.el.parentElement.parentElement.getAttribute('id');
      let id = this.bidderForm.get('bidder_id').value;
      let saveInfo:any={'id':id,'sale_id':this.saleDateId,'name':data['name'],'value':data['value']};
      if(!id){
        this.loading=true;
        this.bidderForm.disable();
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
