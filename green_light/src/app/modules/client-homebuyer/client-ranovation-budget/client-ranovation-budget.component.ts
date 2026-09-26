import { Component, OnInit, Input } from '@angular/core';
import { FormControl, NgForm, FormBuilder, FormGroup, Validators,FormArray } from '@angular/forms';
import { Router,ActivatedRoute } from '@angular/router';
import { CommonApplicationService,CommonActivityService,AlertService } from '@shared-service/_services';
import { apiUrl } from '@config/api-url';
import { IMyDateModel, IMyDpOptions } from 'mydatepicker';
import { DatePipe } from '@angular/common';
import { CurrencyFormatPipe } from '@shared-modules/directives/currency-format.pipe';

@Component({
  selector: 'app-client-ranovation-budget',
  templateUrl: './client-ranovation-budget.component.html',
  styleUrls: ['./client-ranovation-budget.component.css']
})
export class ClientRanovationBudgetComponent implements OnInit {
  propErr:boolean=false;
  clientRenovationBudgetForm:FormGroup;
  @Input() property_id:string;
  invalidFields:any;
  renovation_budget:any;
  propErrMsg:string;
  openPanel:boolean = false;
  loading:boolean=false;
  submitted:boolean=false;
  public myDatePickerOptions: IMyDpOptions = { dateFormat: 'mm/dd/yyyy',firstDayOfWeek : 'su'};
  @Input() totalPayoutAmount:any;

  constructor(private formBuilder:FormBuilder,
              private router: Router,
              private route: ActivatedRoute,
              private commonActivityService:CommonActivityService,
              private commonApplicationService:CommonApplicationService,
              private alertService:AlertService, 
              private datePipe:DatePipe,
              private cp:CurrencyFormatPipe
              ) { }


  ngOnInit() {
      if(this.property_id !== undefined){
        
      }
  }
       
  panelExpand(flag){
    if(!this.openPanel){
      this.openPanel=true;
      this.clientRenovation();
    }
  }

  clientRenovation(){
      this.loading=true;
      let url = apiUrl.renovation_budget+"/"+this.property_id;
      this.commonApplicationService.get(url).subscribe(response => {
        if(response !== undefined){
          this.renovation_budget = response.data;
          if(this.renovation_budget){
            let actual=this.totalPayoutAmount.find(a=>a.category_type=='nonhud');
            this.renovation_budget.actual_budget_renovation=actual?.total_amount[0]?.amount.toFixed(2);
          }
        }
        this.initialize();
        this.propErr=true;
        this.loading=false;
      },
      (err: any) => {
        this.propErrMsg = "Error occured, Please try again later!";
        this.alertService.common(err);
        this.propErr = true;
        this.loading=false;
      })
  }

  initialize(){
    this.clientRenovationBudgetForm = this.formBuilder.group({ 
      // receive_update:[this.renovation_budget.updated_at,[Validators.required]],
      house_id:[this.property_id,[Validators.required]],
      client_budget_renovation:[this.formatedValue(this.renovation_budget.client_budget_renovation),[Validators.required]],
      //client_day_renovation:[this.renovation_budget.client_day_renovation,[Validators.required]],
      budget_renovation:[this.formatedValue(this.renovation_budget.budget_renovation),[Validators.required]],
      lender_fund_renovation:[this.formatedValue(this.renovation_budget.lender_fund_renovation),[Validators.required]],
      est_day_finish:[this.renovation_budget.est_day_finish,[Validators.required]],
      actual_days_finish:[this.renovation_budget.actual_days_finish,[Validators.required]],
      est_budge_renovation:[this.formatedValue(this.renovation_budget.est_budge_renovation),[Validators.required]],
      actual_budget_renovation:[this.formatedValue(this.renovation_budget.actual_budget_renovation),[Validators.required]],
      
      renovation_start:[(this.renovation_budget.renovation_start != null)? {jsdate: new Date(this.renovation_budget.renovation_start)}: null,Validators.required],
      renovation_finish:[(this.renovation_budget.renovation_finish != null)? {jsdate: new Date(this.renovation_budget.renovation_finish)}: null,[Validators.required]]
      
    });
  }

  formatedValue(amount){
      return this.cp.transformVal(amount);
  }


  onDateChanged(event: IMyDateModel,dateInput) {
    let toDate=this.datePipe.transform(dateInput?.selectionDayTxt,'yyyy-MM-dd');
    let fromDate=this.datePipe.transform(event.formatted,'yyyy-MM-dd');
    this.clientRenovationBudgetForm.get('actual_days_finish').setValue(this.onInputFieldChanged(fromDate,toDate));
    return event.formatted;
  }

  onDateChanged1(event: IMyDateModel,dateInput) {

    let toDate=this.datePipe.transform(event.formatted,'yyyy-MM-dd');
    let fromDate=this.datePipe.transform(dateInput?.selectionDayTxt,'yyyy-MM-dd');
    this.clientRenovationBudgetForm.get('actual_days_finish').setValue(this.onInputFieldChanged(fromDate,toDate));
      return event.formatted;
  }
  /*----------------------------- Property validation --------------------------------*/
  validateForm(data: any) {    
    this.submitted=true; 
    let result = this.commonActivityService.getFullFormDataWithDateFormatted(data);
    this.invalidFields = this.commonActivityService.findInvalidControls(data);
    if(this.clientRenovationBudgetForm.invalid) { 
      console.log('Form is invalid, Fill all fields.');
      //this.propertyForm.get('prop_address').markAsTouched();      
      return;
    }    
    // FORM SUBMITTED
    console.log('form submitted');
    result['client_budget_renovation']=this.cp.detransformVal(result['client_budget_renovation']);
    result['actual_budget_renovation']=this.cp.detransformVal(result['actual_budget_renovation']);
    result['est_budge_renovation']=this.cp.detransformVal(result['est_budge_renovation']);
    result['lender_fund_renovation']=this.cp.detransformVal(result['lender_fund_renovation']);
    this.saveRanovationBudget(result);  
  }
/*----------------------------- Property validation --------------------------------*/

/*----------------------------- Save stratgegy --------------------------------*/
saveRanovationBudget(data: any){
    //console.log("savingdata>"+this.propertyForm);
    this.loading=true;
    data.house_id=this.property_id;
    let url = apiUrl.renovation_budget;
    this.commonApplicationService.post(url, data)
        .subscribe(
            data => {
              this.alertService.success(data.message); 
              this.loading=false; 
            },
            error => {
              this.alertService.common(error);
                this.loading=false;
            }
        ); 
  }

  get f() { return this.clientRenovationBudgetForm.controls; }

  onInputFieldChanged(fromdate,todate) {
    var days = this.datediff(this.parseDate(fromdate),this.parseDate(todate));
    if(days){
      return days;
    }else{return 0;}
  }

  datediff(first, second) {
    return Math.round((second-first)/(1000*60*60*24));
  }
  parseDate(str) {
    if(str){
      //var mdy = str.split('/');
      return new Date(str);
    }
  }
}
