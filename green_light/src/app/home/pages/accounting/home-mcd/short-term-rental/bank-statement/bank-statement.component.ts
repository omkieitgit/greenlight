import { Component, Input, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { AlertService, apiUrl, CommonActivityService, CommonApplicationService } from '@shared-service/_services';
import { IMyDateModel, IMyDpOptions } from 'mydatepicker';
import { bankStatementModel } from '../short-term-rental.model';

@Component({
  selector: 'app-bank-statement',
  templateUrl: './bank-statement.component.html',
  styleUrls: ['./bank-statement.component.css']
})
export class BankStatementComponent implements OnInit {

  bankStatementForm:FormGroup;
  submitted: boolean;
  openPanel: boolean;
  bankStatementList:bankStatementModel[];
  @Input() property_id: any;
  //@Output() updateShartTermRental= new EventEmitter<boolean>();

  loading: boolean;

  constructor(private formBuilder:FormBuilder,
              private commonActivityService:CommonActivityService,
              private commonApplicationService:CommonApplicationService,
              private alertService:AlertService) { }

  ngOnInit(): void {
    this.bankStatementForm = this.formBuilder.group({ 
      id:[],
      trans_date:['',[Validators.required]],
      trans_desc:[''],
      amount:[''],
      deposit_in:[''],
      link:[],
      listing:[],
      trans_type:[],
      renter_tr:[],
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
    if(this.bankStatementForm.invalid) { 
      return;
    }    
    // FORM SUBMITTED
    console.log('form submitted');
    this.saveBankStatement(result);  
  }

  panelExpand(flag){
    if(!this.openPanel && this.property_id !== undefined){
      this.openPanel = true;
      this.getBankStatement();
    }
  }

  getBankStatement(){
    this.loading=true;
    let url = apiUrl.bankStatement+'/'+this.property_id;
    this.commonApplicationService.get(url).subscribe(response => {
      if(response['row']){
        this.bankStatementList = response['row'];
      }
      this.loading = false;
    },
    (err: any) => {
      this.loading = false;
    })
  }

  get f() { return this.bankStatementForm.controls; }

  
  uploadHandler(event){
    let elem = event.target; 
    if(elem.files.length > 0){
    }
  }

  saveBankStatement(data: any){

    this.loading = true;
    let url = apiUrl.bankStatement+'/'+this.property_id;
    this.commonApplicationService.post(url, data)
        .subscribe(
            data => {
              if(data.status=='success'){

                let itemIndex = this.bankStatementList.findIndex(item => item.id == data.data.id);
                if(itemIndex >= 0){
                  this.bankStatementList[itemIndex] = data.data;
                }else{
                  this.bankStatementList.push(data.data);
                }
                
                this.alertService.success(data.message);  
                //this.updateShartTermRental.emit(true);
                this.bankStatementForm.reset();
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

  removeBankStatement(id){

      if(confirm("Are you sure want to delete record ?")){
        let url = apiUrl.bankStatement+'/'+id;
        this.commonApplicationService.delete(url).subscribe(response => {
          if(response.status=='success'){
            this.bankStatementList = this.bankStatementList.filter(item => item.id !== id);
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

  editBankStatement(rental){

    this.bankStatementForm.get('id').setValue(rental.id);
    this.bankStatementForm.get('trans_desc').setValue(rental.trans_desc);
    this.bankStatementForm.get('amount').setValue(rental.amount);
    this.bankStatementForm.get('trans_date').setValue(rental.trans_date?{jsdate: new Date(rental.trans_date)}:null);
    this.bankStatementForm.get('deposit_in').setValue(rental.deposit_in);
    this.bankStatementForm.get('listing').setValue(rental.listing);
    this.bankStatementForm.get('trans_type').setValue(rental.trans_type);
    this.bankStatementForm.get('renter_tr').setValue(rental.renter_tr);
    this.bankStatementForm.get('link').setValue(rental.link);

  }

  calTotalAmount(key){
    let totalAmount:any=0;
    if(this.bankStatementList){
      for(var i=0; i<this.bankStatementList.length; i++){
        totalAmount=parseFloat(totalAmount)+parseFloat(this.bankStatementList[i][key]?this.bankStatementList[i][key]:0); 
      }
    }
    return totalAmount.toFixed(2);
  }

}
