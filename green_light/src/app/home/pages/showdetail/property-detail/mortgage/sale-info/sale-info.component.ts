import { Component, OnInit,Inject } from '@angular/core';
import {MatDialog, MatDialogRef, MAT_DIALOG_DATA} from '@angular/material/dialog';
import { DatePipe } from '@angular/common';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ActivatedRoute } from '@angular/router';
import {IMyDpOptions, IMyDateModel} from 'mydatepicker';

import { CommonApplicationService,AlertService,CommonActivityService } from '@shared-service/_services';
import { apiUrl } from '@config/api-url';
import { StorageService } from '@shared-service/_services/storage.service';
import { CommonHelper } from '@shared-service/_utils/CommonHelper';


@Component({
  selector: 'app-sale-info',
  templateUrl: './sale-info.component.html',
  styleUrls: ['./sale-info.component.css']
})
export class SaleInfoComponent implements OnInit {

  // ----------------------------------- Document upload config ---------------------------------------//
  public columns:Array<any> = [
    {title: 'Document Type', name: 'document_type'},
    {title: 'Doc Name', name: 'org_name'},
    {title: 'Other Name', name: 'other_name'},
    {title: 'Case No', name: 'case_number'},
    {title: 'Document Date', name: 'document_date'},
 //   {title: 'Uploaded By', name: 'added_by'},
     {title: 'Uploaded Date', name: 'created_at', sort: 'desc'},
  ];
  saleInfoForm: FormGroup;
  loading = false;
  loadingMessage: any;
  sale_status_option: string [];
  property_id: string;
  
  sale_type:any;

  public page:number = 1;
  public itemsPerPage:number = 10;
  public maxSize:number = 5;
  public numPages:number = 1;
  public docLength:number = 0;

  public config:any = {
    paging: true,
    sorting: {columns: this.columns},
    filtering: {filterString: ''},
    className: ['table-striped', 'table-bordered', 'm-b-0']
  };
  property_document_type_list:any;
  documentDataSale:any=[];
  
  constructor(@Inject(MAT_DIALOG_DATA) public data,
              private datePipe:DatePipe,
              private storageService:StorageService,
              private formBuilder: FormBuilder,
              private route: ActivatedRoute,
              private commonApplicationService: CommonApplicationService,
              private commonActivityService: CommonActivityService,
              private alertService: AlertService,) { }
  

  submitted = false;
  ngOnInit() {
    this.property_id = this.route.snapshot.paramMap.get('property_id');
    let property_config =  this.storageService.get("property_config");
    if(property_config !== null){
        this.property_document_type_list = property_config.sale_doc_type;
        this.sale_status_option= property_config.sale_status;
        this.sale_type= property_config.sale_type;
       

    }
    
    setTimeout(()=>{ 
      this.formatDocumentsSale();
    }, 10);
    this.intilize();
    console.log(this.data);
  }

  intilize(){

    this.saleInfoForm= this.formBuilder.group({
      sale_date: [(this.data.sale_date != null)? {jsdate: this.changeTimezone(this.data.sale_date)}: null,Validators.required],
      sale_status: [this.data.sale_status,[Validators.required]],
      sale_type: [this.data.sale_type,[Validators.required]],
      house_id:[this.data.house_id],
      sale_id:[this.data.sale_id],
    });
  }
  

  public myDatePickerOptions: IMyDpOptions = { dateFormat: 'mm/dd/yyyy',firstDayOfWeek : 'su'};

  onDateChanged(event: IMyDateModel) {
        return event.formatted;
    }

  formatDocumentsSale(){
    let pDoc =  this.data.document_sale;
    for(var i =0; i<this.data.document_sale.length; i++){
      
      var dt = new Date(pDoc[i].created_at * 1000);
      let dayStr = (dt.getDate() <=9)? "0"+dt.getDate() : dt.getDate();
      let month = dt.getMonth()+1;
      let monStr = (month <=9)? "0"+month : month;

      const newDoc = {
        document_date:this.datePipe.transform(pDoc[i].document_date,'MM/dd/yyyy'),
        created_at: monStr+"/"+dayStr+'/'+dt.getFullYear(),
        document_type:this.property_document_type_list[pDoc[i].document_type],
        delete:'<i class="fas  fa-trash-alt fa-lg cal-date" aria-hidden="true"></i>',
        org_name :'<a  href="'+pDoc[i].url+'" target="_blank">'+pDoc[i].org_name+'</a>',
        other_name :'<a  href="'+pDoc[i].url+'" target="_blank">'+pDoc[i].other_name+'</a>',
      };
      this.documentDataSale.push(newDoc);
          // pDoc[i].added_by = pDoc[i].user.first_name+" "+pDoc[i].user.last_name;
    }
    
  }
  validatesaleInfoForm(data){
    let result = this.commonActivityService.getFullFormDataWithDateFormatted(data);
    this.submitted = true;
    if(this.saleInfoForm.invalid) { 
      console.log('Form is invalid, Fill all fields.');
      //this.propertyForm.get('prop_address').markAsTouched();      
      return;
    }    
    this.updateSaleDetails(result);  // Update sale 
  }


// ----------------------------------- Update Sale Form  ------------------------------//
 updateSaleDetails(data: any){
    this.loading = true;

    let url = apiUrl.sale_detail+"/"+data.sale_id;
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

  changeTimezone(date) { 
    return CommonHelper.getConvertDate(date);
  }

  

}
