import { Component, OnInit, Input, EventEmitter, Output,ViewChild,ElementRef  } from '@angular/core';
import { FormControl, NgForm, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router,ActivatedRoute } from '@angular/router';
import { CommonApplicationService } from '../../../../../../shared/_services';
import { CommonActivityService } from '../../../../../../shared/_services';
import { AlertService } from '../../../../../../shared/_services';
import { apiUrl,scraperApiUrl } from '../../../../../../config/api-url';
import {IMyDpOptions, IMyDateModel} from 'mydatepicker';
import { StorageService } from '../../../../../../shared/_services/storage.service';

@Component({
  selector: 'whole-sale-multi',
  templateUrl: './whole-sale-multi.component.html'
})
export class WholeSaleMultiComponent implements OnInit {
  @Input() wholesale: any;
  @Input() whole_sale_buyer_count:number;

  @Output() totalWholeSaleBuyer: EventEmitter<any> = new EventEmitter();
  
  @Output() sendActAmount: EventEmitter<number> = new EventEmitter();
  @Output() sendActProfit: EventEmitter<number> = new EventEmitter();
  @Output() sendActPercent: EventEmitter<number> = new EventEmitter();
  @Output() sendActPayoff: EventEmitter<number> = new EventEmitter();

  homeBuyersForm: FormGroup;
  homeBuyersFormTotal: FormGroup;
  loading:boolean = false;
  remove_loading:boolean=false;
  loadingMessage: any;
  propErr: boolean = false;
  propErrMsg: any = "";
  invalidFields: any;
  submitted = false;
  property_id: string;
  result: any;
  wholesale_buyer_id:number;
  actProfileAmount:number=0;

  amount:number=0;
  
  constructor(private formBuilder: FormBuilder,
    private router: Router,
    private route: ActivatedRoute,
    private commonApplicationService: CommonApplicationService,
    private commonActivityService: CommonActivityService,
    private alertService: AlertService,
    private storageService:StorageService,) { }

    public myDatePickerOptions: IMyDpOptions = { dateFormat: 'yyyy-mm-dd',firstDayOfWeek : 'su'};

    onDateChanged(event: IMyDateModel) {
        return event.formatted;
    }

    ngOnInit() {
      this.property_id = this.route.snapshot.paramMap.get('property_id');
      // Load info details
      if(this.property_id !== undefined){
        this.propErr = true;
        this.initialize();  
      }
    }
    get f() { return this.homeBuyersForm.controls; }

  initialize(){
     this.homeBuyersForm = this.formBuilder.group({
      house_id:[this.property_id],
      wholesale_buyer_n_id:[this.wholesale?this.wholesale.wholesale_buyer_n_id:''],
      user_id: [this.storageService.get("user_info")['id']],
      name:[this.wholesale?this.wholesale.name:'',Validators.required],
      email:[this.wholesale?this.wholesale.email:'',[Validators.required,Validators.email]],
      receive_update:[this.wholesale?this.wholesale.receive_update:'0'],
      est_amount:[this.wholesale?this.wholesale.est_amount:this.amount],
      est_percent:[(this.wholesale && this.wholesale.est_percent)?this.wholesale.est_percent:this.amount],
      est_profit:[(this.wholesale && this.wholesale.est_profit)?this.wholesale.est_profit:this.amount],
      est_payoff_amount:[this.wholesale?this.wholesale.est_payoff_amount:this.amount],
      act_amount:[this.wholesale?this.wholesale.act_amount:this.amount],
      act_percent:[this.wholesale?this.wholesale.act_percent:this.amount],
      act_profit:[(this.wholesale && this.wholesale.act_profit)?this.wholesale.act_profit:this.amount],
      act_payoff_amount:[this.wholesale?this.wholesale.act_payoff_amount:this.amount],
    });
    this.wholesale_buyer_id=this.wholesale?this.wholesale.wholesale_buyer_n_id:'';
    this.actProfileAmount=(this.wholesale && this.wholesale.act_profit)?this.wholesale.act_profit:this.amount;
  }

/*----------------------------- Property validation --------------------------------*/
  validateForm(data: any) {        
    this.result = this.commonActivityService.getFullFormDataWithDateFormatted(data);
    this.invalidFields = this.commonActivityService.findInvalidControls(data);
    this.submitted = true;
    if(this.homeBuyersForm.invalid) { 
      return;
    }    
    if(this.result.wholesale_buyer_n_id !== undefined && this.result.wholesale_buyer_n_id != null && this.result.wholesale_buyer_n_id > 0){
        this.updateWholesaleDetails(this.result);  
      }else{
        this.saveWholeSaleDetails(this.result);  
      }
    
  }
/*----------------------------- Property validation --------------------------------*/

/*----------------------------- Save stratgegy --------------------------------*/
  saveWholeSaleDetails(data: any){
    this.loading = true;
    let url = apiUrl.wholesale_buyer_n;
    this.commonApplicationService.post(url, data)
        .subscribe(
            response => {
              this.homeBuyersForm.controls.wholesale_buyer_n_id.setValue(response.data.id);
              this.alertService.success(response.message);  
              this.loading = false;
            },
            error => {
                this.loading = false;
                this.alertService.common(error);
            }
        ); 
  }

  updateWholesaleDetails(data: any){
    this.loading = true;
    let url = apiUrl.wholesale_buyer_n+'/'+this.result.wholesale_buyer_n_id;
    this.commonApplicationService.put(url, data)
        .subscribe(
            data => {
              this.alertService.success(data.message);  
              this.loading = false;
            },
            error => {
                this.loading = false;
                this.alertService.common(error);
            }
        ); 
  }

  deleteWholesaleDetails(data: any){
    this.remove_loading = true;
    this.result = this.commonActivityService.getFullFormDataWithDateFormatted(data);
    let url = apiUrl.wholesale_buyer_n+'/'+this.result.wholesale_buyer_n_id;
    this.commonApplicationService.delete(url, data)
        .subscribe(
            data => {
              this.alertService.success(data.message);  
              this.remove_loading = false;
              var liElements = document.querySelectorAll("panel[id^='wholesale_homebuyer_"+this.result.wholesale_buyer_n_id+"']");
              var nonExistentFirstElement = liElements[0]; 
              nonExistentFirstElement.remove(); 
            },
            error => {
                this.remove_loading = false;
                this.alertService.error(data.message);  
            }
        ); 
  }
/*----------------------------- Save stratgegy --------------------------------*/
 
/*----------------------------- Calculation --------------------------------*/
  calcActAmt(_act_amount){
    //console.log("Actamt"+act_amount);
    var net_profit = 10; //make_string_to_decimal($('#hb_est_project_net_payout').val()) * 0.5; // Need to be calc from strategy form
    var net_profit2 = 12; //make_string_to_decimal($('#hb_abt_project_net_payout').val()) * 0.5; // Need to be calc from strategy form

    let hb_act_total_cost_a_b  = 50; // Need to be fetched from A to B compo
    let _act_percent = ((_act_amount / hb_act_total_cost_a_b) * 100);
    var _act_profit = (net_profit2 * (_act_percent / 100));

    let  _act_payoff_amount = parseFloat(_act_amount) + parseFloat(_act_profit.toString());
    this.homeBuyersForm.patchValue({act_percent: parseFloat(_act_percent.toFixed(2))});
    this.homeBuyersForm.patchValue({act_profit: _act_profit.toFixed(2)});
    this.homeBuyersForm.patchValue({act_payoff_amount: _act_payoff_amount.toFixed(2)});

    // Emit all to parent    
   
    var totalAmount={'_act_profit':_act_profit,'_act_percent':parseFloat(_act_percent.toFixed(2)),
                  '_act_payoff_amount':_act_payoff_amount.toFixed(2),'_act_amount':_act_amount};
    this.totalWholeSaleBuyer.emit(totalAmount);
  }

}

