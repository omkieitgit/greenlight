import { Component, OnInit,Input, Output, EventEmitter, SimpleChanges, ChangeDetectorRef, ChangeDetectionStrategy, ViewContainerRef, ViewChild, TemplateRef, Inject } from '@angular/core';
import { FormArray, FormBuilder, FormGroup, Validators ,FormControl} from '@angular/forms';
import { ActivatedRoute }    from '@angular/router';
import { apiUrl } from '@config/api-url';
import { CommonApplicationService,AlertService,CommonActivityService,CommunicationService } from '@shared-service/_services';
import {IMyDateModel,IMyDpOptions} from 'mydatepicker';
import { StorageService } from '@shared-service/_services/storage.service';
import { MatDialog, MatDialogRef, MAT_DIALOG_DATA } from '@angular/material/dialog';
import { RenovationCategoryComponent } from '../renovation-category/renovation-category.component';
import { Category } from '../renovation-category.model';
import { map, startWith } from 'rxjs/operators';
import { Observable } from 'rxjs';
import { ClientMscFormComponent } from '../client-msc-form/client-msc-form.component';
import {saveAs} from 'file-saver';

@Component({
  selector: 'app-common-renovation',
  templateUrl: './common-renovation.component.html',
  styleUrls: ['./common-renovation.component.css']
})
export class CommonRenovationComponent implements OnInit {

  clientRenovationForm:FormGroup;
  @Input() property_id:string;
  loading:boolean=false;
  result: any=new Array;
  invalidFields: any;
  submitted: boolean = false;
  summed:number=0;
  user_roles:string;
  isInvoiceLock:boolean=false;
  categoryList:Category[];

  @Input() renovationResult;
  @Input() section_type;
  @Input() lenderList;
  @Input() lenderByAmount;
  @Input() recipients_info;

  amountPaid:number=0;
  amountBilled:number=0;
  @Output() addUpdateInvoice = new EventEmitter<boolean>();
  filteredOptions: Observable<Category[]>[] = [];
  editAccess:boolean=false;
  sortByDsc:boolean=true;
  columnName:string='invoice_date';
  sortByAmountDsc:boolean=true;
  expand:boolean=false;
  notes_index:number;
  exportInvoice:boolean=false;
  property_info:any;

  @Output() updateOrderBy = new EventEmitter<string>();
  loading_inovice_read: boolean;

  constructor(private fb: FormBuilder,
              private commonApplicationService: CommonApplicationService,
              private commonActivityService: CommonActivityService,
              private alertService: AlertService,
              private storageService:StorageService,
              private communicationService:CommunicationService,
              private dailog:MatDialog,   
              private cdRef: ChangeDetectorRef   
              
              ) { }

  ngOnInit() {
    this.editAccess=this.storageService.getHard('ac_view_access');
    let user=this.storageService.get("user_info");
    this.property_info=this.storageService.getHard("property_info");
    this.user_roles=user.current_role;
    this.clientRenovationForm = this.fb.group({
        id:[''],
        invoice_date: ['', Validators.required],
        amount: [''],
        paid_date: [''],  
        paid: [false],
        invoice_lock:[false],  
        bank_deposit_url: [''],  
        bank_statement_url: [''],
        funder:[''],
        invoice_url:[],
        house_id:[this.property_id],
        totalSubcatAmount:[''],
        'clientRenovationDetail':this.fb.array([
          this.clientRenovationDetail()
        ])
    });
    this.ManageNameControl(0);
    //this.populateDataInForm();
    //this.calculateTotal();
    this.categoryList=this.storageService.getHard('inovice_category_list');
    if(!this.categoryList){
      this.getCategory();
    }
    
  }
 
  private _filter(name: string): Category[] {
    if(typeof name !== 'object'){
      const filterValue = name.toLowerCase();
      return this.categoryList.filter(option => option.category_name.toLowerCase().indexOf(filterValue) === 0);

    }
  }
 
  public trackById = (_: number, item: any) => item.id; // or userId, whatever is the unique identifier
  
  public trackByCatId=(_: number, item: any) => item.id;
  
  ngOnChanges(changes: SimpleChanges){
    if(changes.renovationResult){
      this.renovationResult=changes.renovationResult.currentValue;
      //this.calculateTotal();
    }
  }

  calculateTotal(){
    this.amountPaid=0;
    this.amountBilled=0;
    this.renovationResult.forEach(element => {
      this.amountPaid +=+ element.amount;
      element.renovation_detail.forEach(element => {
        this.amountBilled +=parseFloat(element.amount);
      });
    });
  }

  public myDatePickerOptions: IMyDpOptions = { dateFormat: 'mm/dd/yyyy',firstDayOfWeek : 'su'};
  onDateChanged(event: IMyDateModel) {
    return event.formatted;
  }

  clientRenovationDetail() {
    return this.fb.group({
      id:[''],
      sub_category: ['', Validators.required],
      description: [''], 
      amount: [''],
      classification:[''],
      recipients_id:[''],
      msc_form_id:['']
    })
  }

  clientRenovationDetailMore() {
    const control = <FormArray>this.clientRenovationForm.get('clientRenovationDetail');
    control.push(this.clientRenovationDetail());
    this.ManageNameControl(control.length - 1);
  }

  ManageNameControl(index: number) {
    var arrayControl = this.clientRenovationForm.get('clientRenovationDetail') as FormArray;
    this.filteredOptions[index] = arrayControl.at(index).get('sub_category').valueChanges
    .pipe(startWith(''),map(value => this._filter(value)) ); 
  }


  removeClientRenovationField( id : number) : void
  {
    if(confirm("Are you sure want to delete record ?")){
      let url = apiUrl.client_renovation+'/'+id;
      this.commonApplicationService.delete(url).subscribe(response => {
        if(response.status=='success'){
          this.renovationResult = this.renovationResult.filter(item => item.id !== id);
          this.addUpdateInvoice.emit(true);
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

  editRenovationField(invoice){
    let renovation =this.clientRenovationForm.controls;
    
    renovation.amount.setValue(invoice.amount);
    renovation.id.setValue(invoice.id);
    renovation.paid.setValue(invoice.paid);
    renovation.invoice_lock.setValue(invoice.invoice_lock);
    renovation.invoice_url.setValue(invoice.invoice_url);
    renovation.bank_deposit_url.setValue(invoice.bank_deposit_url);
    renovation.bank_statement_url.setValue(invoice.bank_statement_url);
    renovation.funder.setValue(invoice.funder_info);
    renovation.invoice_date.setValue((invoice.invoice_date != null)? {jsdate:new Date(invoice.invoice_date)}: null);
    renovation.paid_date.setValue((invoice.paid_date != null)? {jsdate:new Date(invoice.paid_date)}: null);

    renovation.totalSubcatAmount.setValue(this.calculateSubTotal(invoice.renovation_detail));

    this.clientRenovationDetailInfo(0,invoice.renovation_detail);
    
    this.isInvoiceLock=false;
    if(this.section_type=='invoices' && this.user_roles!='admin' && invoice.invoice_lock){
      this.isInvoiceLock=true;
      this.invoiceLocked(renovation);
    }
  }

  clientRenovationDetailInfo(index,renovationDetail){
    const control = <FormArray>this.clientRenovationForm.controls.clientRenovationDetail;
    control.clear();
    if(renovationDetail && renovationDetail.length>0){
      for(var i=0; i<renovationDetail.length; i++ ){
        this.clientRenovationDetailMore();
        let detail=this.clientRenovationForm.get('clientRenovationDetail')['controls'][i]?.controls;
        detail['id'].setValue(renovationDetail[i].id);
        detail['sub_category'].setValue(renovationDetail[i].category);
        detail['description'].setValue(renovationDetail[i].description);
        detail['amount'].setValue(renovationDetail[i].amount);
        detail['classification'].setValue(renovationDetail[i].classification);
        detail['recipients_id'].setValue(renovationDetail[i]?.recipient?.recipients_id);
        detail['msc_form_id'].setValue(renovationDetail[i]?.recipient?.id);
      }
    }else{
      this.clientRenovationDetailMore();
    }

   
  }

  validateForm(data: any, index: number) {
    data.get('house_id').setValue(this.property_id);
    this.result = this.commonActivityService.getFullFormDataWithDateFormatted(data);
    this.invalidFields = this.commonActivityService.findInvalidControls(data);
    this.submitted = true;
    if(this.invalidFields.length > 1) { 
      this.alertService.common('Form is invalid, Fill all fields.');    
      return;
    }    
    // FORM SUBMITTED
    if(this.result.id !== undefined && this.result.id != null && this.result.id > 0){
      this.updateRenovationInfo(this.result,index);
    }else{
      this.saveRenovationInfo(this.result,index);
    }
    
  }

  formData(data){
    // var fileBankDepo = bankDepo.files[0];
    // var fileBankStat = bankState.files[0];
    let input = new FormData();
     input.append("id", data['id']);
     input.append("amount", data['amount']);
     input.append("house_id", data['house_id']);
     input.append("invoice_date", data['invoice_date']);
     input.append("paid", data['paid']?'1':'0');
     input.append("paid_date", data['paid_date']);
     input.append("invoice_url", data['invoice_url']?data['invoice_url']:'');
     input.append("bank_deposit_url", data['bank_deposit_url']?data['bank_deposit_url']:'');
     input.append("bank_statement_url", data['bank_statement_url']?data['bank_statement_url']:'');
     input.append("funder", data['funder']?.id);
     input.append("section_type", this.section_type);
     input.append("invoice_lock", data['invoice_lock']?'1':'0');
     data['clientRenovationDetail'].forEach(element => {
      element.sub_category=element.sub_category.id;
     });

     input.append("renovation_detail_data",  JSON.stringify(data['clientRenovationDetail']));
   
     return input;
  }

  saveRenovationInfo(data, index){
    
    let input =this.formData(data);
    this.loading=true;
    let uploadUrl = apiUrl.client_renovation;
    this.commonApplicationService.post(uploadUrl, input)
    .subscribe(res => {
              this.alertService.success(res.message); 
              this.loading=false;
              this.updateInfoAfterSave(res);
              this.communicationService.setUpdateRenovation(true);
            },
            error => { 
              this.loading=false;
              this.alertService.common(error); 
            }
      );
  }

  updateInfoAfterSave(res){

    let client_renovation=res?.data['client_renovation'];
    let lender_by_amount=res?.data['lender_by_amount']?res?.data['lender_by_amount'][0]:'';

    let itemIndex = this.renovationResult.findIndex(item => item.id == client_renovation.id);
    if(itemIndex >= 0){
      this.renovationResult[itemIndex] =client_renovation;
    }else{
      this.renovationResult.push(client_renovation);
    }
    if(lender_by_amount){
      let infoIndex=this.lenderByAmount.findIndex(info=>info.id==lender_by_amount?.id);
      if(infoIndex >= 0){
        this.lenderByAmount[infoIndex] = lender_by_amount;
      }else{
        this.lenderByAmount.push(lender_by_amount);
      }
    }
    
    this.clientRenovationForm.reset();
    this.alertService.success(res.message);  

  }
  
  updateRenovationInfo(data, index){
    let input =this.formData(data);
    this.loading=true;
    let uploadUrl = apiUrl.client_renovation_update;
    this.commonApplicationService.post(uploadUrl, input)
    .subscribe(res => {
              this.loading=false;
              this.alertService.success(res.message); 
              this.updateInfoAfterSave(res);
              this.communicationService.setUpdateRenovation(true);
            },
            error => {
              this.loading=false;
              //his.alertService.error("Error occured in upload."); 
              this.alertService.common(error); 
            }
      );
  }

  /******************** FILE UPLOAD FUNCTIONALITY *******************/
  uploadHandler(event){
    let elem = event.target;  //line 2 
   if(elem.files.length > 0){
     //this.clientRenovationForm['controls']['property_file'].setErrors({'required': false});
   }
 }

  invoiceLocked(renovation){
    
    if(renovation){
      Object.keys(renovation).forEach(field=>{
        let renovationField=renovation[field];
        
        if (renovationField instanceof FormControl) {
          renovationField.disable();
        }else{
          Object.keys(renovationField['controls']).forEach(detail=>{
            const controls = renovationField['controls'][detail];
            Object.keys(controls['controls']).forEach(det=>{
              let renovationDetail=controls.get(det);
              if (renovationDetail instanceof FormControl) {
                renovationDetail.disable();
              }
            });
          }); 
        }
      });
    }
  }

  isEmptyObject(obj) {
    return (obj && (Object.keys(obj).length === 0));
  }

  categoryDialog(){
    const dialogRef =this.dailog.open(RenovationCategoryComponent,{ width: '800px',data:{categoryList:this.categoryList}});
    dialogRef.afterClosed().subscribe(result => {
      if(result)
        this.categoryList=result;
    });
  }

  getCategory(){
    let url = apiUrl.renovationCategory;
    this.commonApplicationService.get(url).subscribe(response => {
      this.categoryList=response.data;
    },
    (err: any) => {})
  }

  calculateSubTotal($event){
    let subCatTotal:number=0;
    $event.forEach(element => {
      subCatTotal +=+ element.amount;
    });
    return subCatTotal;
  }

  calTotalLenderInvoiceAmount(key){
    let totalAmount:any=0;
    for(var i=0; i<this.lenderByAmount.length; i++){
      totalAmount=parseFloat(totalAmount)+parseFloat(this.lenderByAmount[i][key]?this.lenderByAmount[i][key]:0); 
    }
    return totalAmount.toFixed(2);
  }

  displayFn(category?: any): string | undefined {
    return category ? category.category_name : undefined;
  }
  displayFunder(lender?: any): string | undefined {
    return lender ? lender.lender_name : undefined;
  }
  
  // viewSubCat(subCatDetail){
  //   const dialogRef = this.dailog.open(RenovationDetailComponent, {
  //     width: '400px',
  //     data: {renovation_detail:subCatDetail}
  //   });
  // }
  clientMscForm(index){
    let detail=this.clientRenovationForm.get('clientRenovationDetail')['controls'][index]?.controls;

      let data={property_id:this.property_id,amount:detail.amount.value,recipients_info:this.recipients_info};
      let dialog=this.dailog.open(ClientMscFormComponent,{width:'600px',data:data});
      dialog.afterClosed().subscribe(result => {
        if(result)
          this.recipients_info=result;
      });
  }

  getInvoice(columnName){
    
    
    if(columnName=='amount'){
      this.sortByAmountDsc=!this.sortByAmountDsc;
    }
    if(columnName=='invoice_date'){
      this.sortByDsc=!this.sortByDsc;
    }
    this.renovationResult=this.renovationResult.sort((a, b)=>{
      let aColumn:any=a.invoice_date;
      let bColumn:any=b.invoice_date;
      if(columnName=='amount'){
         aColumn=(a?.total_renovation_amount?.total_amount || 0);
         bColumn=(b?.total_renovation_amount?.total_amount || 0);
         return this.sortByAmountDsc?(bColumn-aColumn):(aColumn-bColumn);
      }
      if(columnName=='invoice_date'){
        return this.sortByDsc?(new Date(bColumn).getTime()- new Date(aColumn).getTime()):(new Date(aColumn).getTime()- new Date(bColumn).getTime());
      }
      // if(columnName=='category'){
      //   aColumn=(a?.renovation_detail?.total_amount || '');
      //   bColumn=(b?.renovation_detail?.total_amount || '');

      //    this.renovationResult.filter(eachVal => {
      //     let opt = eachVal.renovation_detail.some((
      //         { category }) => category
      //         .some(({ category_name }) => category_name === 'A'));
      //     return opt;
      //   })
      // }

    });
    //
    //this.columnName=columnName;
    //this.updateOrderBy.emit({'columnName':columnName,'orderBy':this.sortByDsc?'DESC':'ASC'});
  }
  sortRecursive(data) {
    if (data[0]) {
      data.forEach( (element) => {
        if (element.eserviceSettings) {
          element.eserviceSettings.sort((a, b) =>  a.sortOrderNumber - b.sortOrderNumber);
          this.sortRecursive(element.eserviceSettings);
        }
      });
    }
  }

  addInvoiceHistory(invoice_id){
    this.loading_inovice_read=true;
    let url = apiUrl.invoiceHistory;
    this.commonApplicationService.post(url, {'invoice_id':invoice_id,'house_id':this.property_id})
    .subscribe(res => {
              let index=this.renovationResult.findIndex(item=>item.id==res.data.invoice_id);
              let index2=this.renovationResult[index].invoice_history.findIndex(history=>history.id==res.data.id);
              if(index2==-1){
                this.renovationResult[index].invoice_history.push(res.data);
              }
              this.alertService.success(res.message); 
              this.loading_inovice_read=false;
            },
            error => { 
              this.loading_inovice_read=false;
              this.alertService.common(error); 
            }
      );
  }

  toggleList(index){
    this.notes_index=index;
  }

  exportRenovation(){
    this.exportInvoice=true;
    let url = apiUrl.export_renovation;
    let options = {
      headers: { "Content-Type": "application/json", Accept: "application/pdf" },
      responseType: "blob"
    };
    let data={
      house_id:this.property_id,
      section_type:this.section_type,
    };
    this.commonApplicationService.postDownload(url,data, options).subscribe(response => {
        //const fileURL = URL.createObjectURL(response);
        //window.open(fileURL, '_blank');
        this.exportInvoice = false;
        var filename = this.property_info?.address+"_"+this.section_type;
        saveAs(response, filename);
       
    },
    (err: any) => {
      this.alertService.common(err);
      this.exportInvoice = false;
    });

  } 

}



