import { Component, EventEmitter, Input, OnInit, Output } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { apiUrl } from '@config/api-url';
import { AlertService, CommonActivityService, CommonApplicationService } from '@shared-service/_services';
import { IMyDateModel, IMyDpOptions } from 'mydatepicker';
import { TrustLedgerModel } from './trust-ledger.model';

@Component({
  selector: 'app-trust-ledger',
  templateUrl: './trust-ledger.component.html',
  styleUrls: ['./trust-ledger.component.css']
})
export class TrustLedgerComponent implements OnInit {

  lenderForm:FormGroup;
  submitted: boolean;
  openPanel: boolean;
  trustLedgerInfo:TrustLedgerModel[];
  @Input() property_id: any;
  @Output() updateLender= new EventEmitter<boolean>();
  prevBalance:number=0;
  loading: boolean;

  constructor(private formBuilder:FormBuilder,
              private commonActivityService:CommonActivityService,
              private commonApplicationService:CommonApplicationService,
              private alertService:AlertService) { }

  ngOnInit(): void {
    this.lenderForm = this.formBuilder.group({ 
      id:[],
      statement_date:['',[Validators.required]],
      transaction:[''],
      payee:[''],
      description:[''],
      deposit:['',[Validators.pattern("^[0-9]+(.[0-9]{0,2})?$")]],
      payment:['',[Validators.pattern("^[0-9]+(.[0-9]{0,2})?$")]],
      balance:[''],
      doc_org_name:['']
    });

    this.lenderForm.get("deposit").valueChanges.subscribe(dep=>{
      let payment=this.lenderForm.get('payment').value;
      this.lenderForm.get('balance').setValue(+(dep-payment));
    });
    this.lenderForm.get("payment").valueChanges.subscribe(payment=>{
      let deposit=this.lenderForm.get('deposit').value;
      this.lenderForm.get('balance').setValue(+(deposit-payment));
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
    if(this.lenderForm.invalid) { 
      return;
    }    
    // FORM SUBMITTED
    console.log('form submitted');
    this.saveUpdateLenderStatement(result);  
  }

  panelExpand(flag){
    if(!this.openPanel && this.property_id !== undefined){
      this.openPanel = true;
      this.getLenderStatement();
    }
  }

  getLenderStatement(){
    this.loading=true;
    let url = apiUrl.lender_statement+'/'+this.property_id;
    this.commonApplicationService.get(url).subscribe(response => {
      if(response['row']){
        this.trustLedgerInfo = response['row'];
        this.calculateBlance();
      }
      this.loading = false;
    },
    (err: any) => {
      this.loading = false;
    })
  }

  get f() { return this.lenderForm.controls; }

  
  uploadHandler(event){
    let elem = event.target; 
    if(elem.files.length > 0){
    }
  }

  saveUpdateLenderStatement(data: any){
    this.loading = true;
    let input = new FormData();
    input.append("statement_date", data['statement_date']);
    input.append("transaction", data['transaction']); 
    input.append("payee", data['payee']);
    input.append("description", data['description']); 
    input.append("deposit", data['deposit']?data['deposit']:0);
    input.append("payment", data['payment']?data['payment']:0); 
    input.append("balance", data['balance']?data['balance']:0);
    input.append("id", data['id']);
    input.append("doc_org_name", data['doc_org_name']);
    // if(document != "" && document != undefined){
    //   input.append('document_statement',document.files[0]);
    // }
    
    let url = apiUrl.lender_statement+'/'+this.property_id;
    this.commonApplicationService.post(url, input)
        .subscribe(
            data => {
              if(data.status=='success'){

                let itemIndex = this.trustLedgerInfo.findIndex(item => item.id == data.data.id);
                if(itemIndex >= 0){
                  this.trustLedgerInfo[itemIndex] = data.data;
                }else{
                  this.trustLedgerInfo.push(data.data);
                }
                this.calculateBlance();
                this.alertService.success(data.message);  
                this.updateLender.emit(true);
                this.lenderForm.reset();
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

  removeLenderStatement(id){

      if(confirm("Are you sure want to delete record ?")){
        let url = apiUrl.lender_statement+'/'+id;
        this.commonApplicationService.delete(url).subscribe(response => {
          if(response.status=='success'){
            this.trustLedgerInfo = this.trustLedgerInfo.filter(item => item.id !== id);
            this.calculateBlance();
            this.updateLender.emit(true);
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

  editLenderStatement(lender){

    this.lenderForm.get('id').setValue(lender.id);
    this.lenderForm.get('statement_date').setValue((lender.statement_date != null)? {jsdate:new Date(lender.statement_date)}: null );
    this.lenderForm.get('transaction').setValue(lender.transaction);
    this.lenderForm.get('payee').setValue(lender.payee);
    this.lenderForm.get('description').setValue(lender.description);
    this.lenderForm.get('deposit').setValue(lender.deposit);
    this.lenderForm.get('payment').setValue(lender.payment);
    this.lenderForm.get('balance').setValue(lender.balance);
    this.lenderForm.get('doc_org_name').setValue(lender.doc_org_name);

  }

  calculateBlance(){
    let remaningBalace:number=0;
    this.trustLedgerInfo.forEach(element => {
      remaningBalace=(+remaningBalace)+(+element.balance);
      element.remaningBalace=remaningBalace;
    });
  }
 

}
