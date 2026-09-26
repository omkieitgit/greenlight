import { Component, OnInit, Input } from '@angular/core';
import { FormControl, NgForm,FormArray, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ActivatedRoute }    from '@angular/router';
import { apiUrl,scraperApiUrl } from '@config/api-url';
//import {formConstants} from '@config/forms-constants';
import { CommonApplicationService,AlertService,CommonActivityService } from '@shared-service/_services';
import {IMyDpOptions, IMyDateModel} from 'mydatepicker';

@Component({
  selector: 'app-client-invoice',
  templateUrl: './client-invoice.component.html',
  styleUrls: ['./client-invoice.component.css']
})
export class ClientInvoiceComponent implements OnInit {

  @Input() property_id:string;
  clientInvoiceResult:any=new Array;
  openPanel:boolean=false; 
  loading:boolean=false;
  section_type: string = 'invoices';
  loadingRenovation:boolean=false;
  amountPaid:number=0;
  amountBilled:number=0;
  lenderList:any=new Array;
  lenderByAmount:any=new Array;
  recipients_info:any= new Array;
  orderBy:string='DESC';

  constructor(private route: ActivatedRoute,
              private commonApplicationService: CommonApplicationService,
              private commonActivityService: CommonActivityService,
              private alertService: AlertService
              ) { }

  ngOnInit() {   

  }

  panelExpand(flag){
    if(!this.openPanel){
      this.getClientInvoices();
      this.openPanel = true;
    }
  }

  getClientInvoices(){
    this.loading=true;
    let url = apiUrl.client_renovation+'/'+this.property_id+'/'+this.section_type+'/'+this.orderBy;
    this.commonApplicationService.get(url).subscribe(response => {
      this.clientInvoiceResult = response['data'].client_renovation;
      this.lenderList = response['data'].lender_list;
      this.lenderByAmount = response['data'].lender_by_amount;
      this.recipients_info= response['data'].recipients_info;
      this.loading = false;
      this.loadingRenovation=true;
      
    },
    (err: any) => {
      this.loading = false;
      this.loadingRenovation=true;
    })
  }

  updateInvoiceInfo($event){
    if($event){
      this.getLenderByAmount();
    }
  }

  getLenderByAmount(){
    this.loading=true;
    let url = apiUrl.client_renovation_lender+'/'+this.property_id+'/'+this.section_type;
    this.commonApplicationService.get(url).subscribe(response => {
      this.lenderByAmount = response['data'].lender_by_amount;
      this.loading = false;
      this.loadingRenovation=true;
      
    },
    (err: any) => {
      this.loading = false;
      this.loadingRenovation=true;
    })
  }

  getRenovationOrderBy(orderBy){
    this.orderBy=orderBy;
    this.getClientInvoices();
  }

}
