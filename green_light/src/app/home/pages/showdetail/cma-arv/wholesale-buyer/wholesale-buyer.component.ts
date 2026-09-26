import { Component, Injectable, OnDestroy,ViewChild,Input,Output, OnInit, EventEmitter, Renderer2, ComponentFactoryResolver, ViewContainerRef } from '@angular/core';
import { FormControl, NgForm, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router,ActivatedRoute } from '@angular/router';
import { CommonApplicationService } from '../../../../../shared/_services';
import { CommonActivityService } from '../../../../../shared/_services';
import { AlertService } from '../../../../../shared/_services';
import { apiUrl } from '../../../../../config/api-url';
import {IMyDpOptions, IMyDateModel} from 'mydatepicker';


@Component({
  selector: 'app-wholesale-buyer',
  templateUrl: './wholesale-buyer.component.html',
  styleUrls: ['./wholesale-buyer.component.css']
})
export class WholesaleBuyerComponent implements OnInit {

  
  homeBuyersForm: FormGroup;
  homeBuyersFormTotal: FormGroup;
  loading = false;
  loadingMessage: any;
  propErr: boolean = false;
  propErrMsg: any = "";
  invalidFields: any;
  submitted = false;
  property_id: string;
  result: any;
  whole_sale_buyer_count:number=1;
  amount: number = 0.00;
 
  est_amount_received: number = 0.00;
  act_amount_received: number = 0.00;
  est_percent_received: number = 0.00;
  act_percent_received: number = 0.00;
  est_profit_received: number = 0.00;
  act_profit_received: number = 0.00;
  est_payoff_amount_received: number = 0.00;
  act_payoff_amount_received: number = 0.00;

  wholesale_buyer_list:any;
  sthb_total:any;
  openPanel:boolean=false;
  estProfileAmount:number;
  //public homeBuyerData : HomeBuyerModel;
  //public homeBuyerBlankModel : HomeBuyerModel;

  @ViewChild('wholesaleParent', { read: ViewContainerRef }) container: ViewContainerRef;
  @Output() notifyChild: EventEmitter<number> = new EventEmitter<number>();
  
  constructor(private formBuilder: FormBuilder,
              private route: ActivatedRoute,
              private commonApplicationService: CommonApplicationService,
              private commonActivityService: CommonActivityService,
              private alertService: AlertService) { }

    public myDatePickerOptions: IMyDpOptions = { dateFormat: 'yyyy-mm-dd',firstDayOfWeek : 'su'};

    onDateChanged(event: IMyDateModel) {
        return event.formatted;
    }

    ngOnInit() {
      this.property_id = this.route.snapshot.paramMap.get('property_id');
      if(this.property_id !== undefined){
       
      }
    }

    panelExpand(flag){
      if(!this.openPanel){
        this.getShtbTotal();
        this.getWholesaleBuyer();
        this.openPanel = true;
      }
    }

    getWholesaleBuyer(){
      let url = apiUrl.wholesale_buyer_n+'/'+this.property_id+'/all';
      this.commonApplicationService.get(url).subscribe(response =>{
        this.loadingMessage = false;
        if(response !== undefined){
          this.wholesale_buyer_list=response.data;
        }
      })
    }

    getShtbTotal(){
      let url = apiUrl.sthb_total+'/'+this.property_id;
      this.commonApplicationService.get(url).subscribe(response =>{
        this.loadingMessage = false;
        if(response !== undefined){
          this.sthb_total=response.data;
          this.initialize();
          this.propErr = true;
        }
      })
      
    }

    get f() { return this.homeBuyersFormTotal.controls; }
  

    initialize(){
      this.homeBuyersFormTotal = this.formBuilder.group({
        house_id:[this.property_id],
        est_amount:[this.sthb_total?this.sthb_total.est_amount:this.amount,Validators.required],
        act_amount:[this.sthb_total?this.sthb_total.act_amount:this.amount,Validators.required],
        est_percent:[this.sthb_total?this.sthb_total.est_percent:this.amount,Validators.required],
        act_percent:[this.sthb_total?this.sthb_total.act_percent:this.amount,Validators.required],
        est_profit:[this.sthb_total?this.sthb_total.est_profit:this.amount,Validators.required],
        act_profit:[this.sthb_total?this.sthb_total.act_profit:this.amount,Validators.required],
        est_payoff_amount:[this.sthb_total?this.sthb_total.est_payoff_amount:this.amount,Validators.required],      
        act_payoff_amount:[this.sthb_total?this.sthb_total.act_payoff_amount:this.amount,Validators.required]
      });
      
      this.estProfileAmount=this.sthb_total?this.sthb_total.est_profit:'0';
    }

    updateTotal() {
      this.homeBuyersFormTotal.patchValue({act_amount: this.act_amount_received});
      this.homeBuyersFormTotal.patchValue({act_percent: this.act_percent_received});
      this.homeBuyersFormTotal.patchValue({act_profit: this.act_profit_received});
      this.homeBuyersFormTotal.patchValue({act_payoff_amount: this.act_payoff_amount_received});
    }

    /*----------------------------- Property validation --------------------------------*/
    validateForm(data: any) {  
      this.submitted=true;      
      this.result = this.commonActivityService.getFullFormDataWithDateFormatted(data);
      this.invalidFields = this.commonActivityService.findInvalidControls(data);
      console.log("Form Invalid Filleds = ",this.invalidFields);
      this.submitted = true;
      console.log('niform');
      if(this.homeBuyersFormTotal.invalid) { 
        console.log('Form is invalid, Fill all fields.');
        //this.propertyForm.get('prop_address').markAsTouched();      
        return;
      }    
      // FORM SUBMITTED
      this.saveWholeSaleDetails(this.result);  
    }
    /*----------------------------- Property validation --------------------------------*/

    /*----------------------------- Save stratgegy --------------------------------*/
    saveWholeSaleDetails(data: any){
      this.loading = true;
      //console.log("savingdata>"+this.propertyForm);
      console.log("savingdata>"+data);
      let url = apiUrl.sthb_total+'/'+this.property_id;
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
    /*----------------------------- Save stratgegy --------------------------------*/

    onChangeValue($event){
      this.homeBuyersFormTotal.controls.act_amount.setValue(parseFloat(this.homeBuyersFormTotal.controls.act_amount.value)+parseFloat($event._act_amount));
      this.homeBuyersFormTotal.controls.act_percent.setValue(parseFloat(this.homeBuyersFormTotal.controls.act_percent.value)+parseFloat($event._act_percent));
      this.homeBuyersFormTotal.controls.act_profit.setValue(parseFloat(this.homeBuyersFormTotal.controls.act_profit.value)+parseFloat($event._act_profit));
      this.homeBuyersFormTotal.controls.act_payoff_amount.setValue(parseFloat(this.homeBuyersFormTotal.controls.act_payoff_amount.value)+parseFloat($event._act_payoff_amount));
    }
}
