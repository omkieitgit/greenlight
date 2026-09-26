import { ChangeDetectionStrategy, Component, EventEmitter, Input, OnInit, Output } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { apiUrl } from '@config/api-url';
import { AlertService, CommonActivityService, CommonApplicationService } from '@shared-service/_services';
import { IMyDateModel, IMyDpOptions } from 'mydatepicker';
import { DepositSheetModel } from './deposit-sheet.model';

@Component({
  selector: 'app-deposit-sheet',
  templateUrl: './deposit-sheet.component.html',
  styleUrls: ['./deposit-sheet.component.css'],
  //changeDetection: ChangeDetectionStrategy.OnPush
})
export class DepositSheetComponent implements OnInit {

 
  depositSheetForm:FormGroup;
  submitted: boolean;
  openPanel: boolean;
  depositSheetInfo:DepositSheetModel[];
  @Input() property_id: any;
  @Output() updatedeposit= new EventEmitter<boolean>();
  prevBalance:number=0;
  loading: boolean;
  numberValidPattern:string="^[0-9]*\.*\-?[0-9]+$";
  deposit_lender_list:any;
  deposit_lender_category:any;
  deposit_lender:any;
  deposit_link:any;
  deposit_link_category:any;


  constructor(private formBuilder:FormBuilder,
              private commonActivityService:CommonActivityService,
              private commonApplicationService:CommonApplicationService,
              private alertService:AlertService) { }

  ngOnInit(): void {

   
    
    this.depositSheetForm = this.formBuilder.group({ 
      id:[],
      deposit_date:['',[Validators.required]],
      transaction:[''],
      in_amount_bidding:['',[Validators.pattern(this.numberValidPattern)]],
      out_amount_bidding:['',[Validators.pattern(this.numberValidPattern)]],
      llc_name:[''],
      sp_number:[''],
      county:[''],
    
    });


  }
  public myDatePickerOptions: IMyDpOptions = { dateFormat: 'mm/dd/yyyy',firstDayOfWeek : 'su'};
  onDateChanged(event: IMyDateModel) {
      return event.formatted;
  }
  validateForm(data){
    let result = this.commonActivityService.getFullFormDataWithDateFormatted(data);
    this.submitted = true;
    console.log('niform');
    if(this.depositSheetForm.invalid) { 
      return;
    }    
    // FORM SUBMITTED
    console.log('form submitted');
    this.saveUpdateDepositSheet(result);  
  }

  panelExpand(flag){
    if(!this.openPanel && this.property_id !== undefined){
      this.openPanel = true;
      this.getDepositSheet();
    }
  }

  getDepositSheet(){
    this.loading=true;
    let url = apiUrl.depositSpreadSheet+'/'+this.property_id;
    this.commonApplicationService.get(url).subscribe(response => {
      if(response['row']){
        this.depositSheetInfo = response['row']['deposit_sheet'];
        this.deposit_lender_list = response['row']['deposit_lender'];
        this.deposit_lender_category = response['row']['lender_category'];
        this.deposit_link_category = response['row']['link_category'];

        this.calculateBlance();
      }
      this.loading = false;
    },
    (err: any) => {
      this.loading = false;
    })
  }

  get f() { return this.depositSheetForm.controls; }

  
  uploadHandler(event){
    let elem = event.target; 
    if(elem.files.length > 0){
    }
  }

  saveUpdateDepositSheet(data: any){
    this.loading = true;
    
    data['more_lender_data'].forEach(element => {
      element['lender_id']=element['lender_id'].id;
    });
    let url = apiUrl.depositSpreadSheet+'/'+this.property_id;
    this.commonApplicationService.post(url, data)
        .subscribe(
            data => {
              if(data.status=='success'){
                this.depositSheetForm.reset();
                this.deposit_lender=[];
                this.deposit_link=[];
                this.getDepositSheet();
                this.alertService.success(data.message);  
                
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

  removeDepositSheet(id){

      if(confirm("Are you sure want to delete record ?")){
        let url = apiUrl.depositSpreadSheet+'/'+id;
        this.commonApplicationService.delete(url).subscribe(response => {
          if(response.status=='success'){
            
            this.depositSheetInfo = this.depositSheetInfo.filter(item => item.id !== id);
            this.calculateBlance();
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

  editdepositSheet(deposit){

    this.depositSheetForm.get('id').setValue(deposit.id);
    this.depositSheetForm.get('deposit_date').setValue((deposit.deposit_date != null)? {jsdate:new Date(deposit.deposit_date)}: null );
    this.depositSheetForm.get('transaction').setValue(deposit.transaction);
    this.depositSheetForm.get('in_amount_bidding').setValue(deposit.in_amount_bidding);
    this.depositSheetForm.get('out_amount_bidding').setValue(deposit.out_amount_bidding);
    this.depositSheetForm.get('llc_name').setValue(deposit.llc_name);
    this.depositSheetForm.get('sp_number').setValue(deposit.sp_number);
    this.depositSheetForm.get('county').setValue(deposit.county);
    
    this.deposit_lender=deposit.deposit_lender;
    this.deposit_link=deposit.deposit_link;

  }

  calculateBlance(){
    let runningBalance:number=0;
    this.depositSheetInfo.forEach(element => {

      let inAmount:number=0; let outAmount:number=0;
      element['deposit_lender'].forEach(account => {
          if(account.trans_type=='deposit'){
            inAmount+=+account.amount;
          }
          if(account.trans_type=='withdrawal'){
            outAmount+=+account.amount;
          }
      });

      element.in_amount_bidding=inAmount;
      element.out_amount_bidding=outAmount;

      runningBalance=(+runningBalance)+(+element.in_amount_bidding+element.out_amount_bidding);
      element.running_balance=runningBalance;
     
    });
  }

  calTotalAmount(key){
    let totalAmount:any=0;
    for(var i=0; i<this.depositSheetInfo.length; i++){
      totalAmount=parseFloat(totalAmount)+parseFloat(this.depositSheetInfo[i][key]?this.depositSheetInfo[i][key]:0); 
    }
    return totalAmount.toFixed(2);
  }

  findLender(lender,depositLender){
    let itemIndex = depositLender.findIndex(item => item.lender_id == lender.id);
    if(itemIndex >= 0){
      return depositLender[itemIndex].amount;
    }else{
      return 0;
    }
  }
}
