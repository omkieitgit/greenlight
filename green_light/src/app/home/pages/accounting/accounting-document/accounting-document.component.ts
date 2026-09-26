import { Component, Injectable, OnDestroy,ViewChild, OnInit, Renderer2, ComponentFactoryResolver, Input, } from '@angular/core';
import { FormControl, NgForm, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { SortingComponent } from '../../showdetail/sorting/sorting.component';
import { CommonApplicationService } from '../../../../shared/_services';
import { apiUrl,scraperApiUrl } from '../../../../config/api-url';
import { AlertService } from '../../../../shared/_services';
import { CommonActivityService } from '../../../../shared/_services';
import { StorageService } from '../../../../shared/_services/storage.service';
import {IMyDpOptions, IMyDateModel} from 'mydatepicker';
import { Router,ActivatedRoute }    from '@angular/router';
import {DatePipe} from '@angular/common';
import { map, startWith } from 'rxjs/operators';
import { Observable } from 'rxjs';

@Component({
  selector: 'app-accounting-document',
  templateUrl: './accounting-document.component.html',
  styleUrls: ['./accounting-document.component.css']
})
export class AccountingDocumentComponent implements OnInit {
    openPanel:boolean = false;
  	clientDocumentForm: FormGroup;
    property_document_type_list: string[];
    categoryList:Category[];
    filteredOptions: Observable<Category[]>;


    //prop_document_date: string = null;
    document_property: any[];
    property_file: string;
    uploadedFiles: any[] = [];
    @Input() property_id:string;
    property_config: any= {};
    submitted = false;
    showPropertyList: boolean = false;
    modifyBtn : boolean = false;
    loadingMessage: any;   
    documentResult: any;
    errorMessage: string ="";
    loading:boolean=false;
    @Input() viewAccessOnly;

    // ----------- Document upload config --------------------//
    public columns:Array<any> = [
      {title: 'Document Type', name: 'document_type'},
      {title: 'Doc Name', name: 'org_name'},
     // {title: 'Other Name', name: 'other_name'},
      {title: 'Amount', name: 'amount'},
      {title: 'Document Date', name: 'document_date'},
      {title: 'Uploaded By', name: 'added_by'},
      {title: 'Uploaded Date', name: 'created_at', sort: 'desc'},
      {title: 'Action', name: 'delete'},
    ];

    public page:number = 1;
    public itemsPerPage:number = 10;
    public maxSize:number = 5;
    public numPages:number = 1;
    public length:number = 0;

    public config:any = {
      paging: true,
      sorting: {columns: this.columns},
      filtering: {filterString: ''},
      className: ['table-striped', 'table-bordered', 'm-b-0']
    };

    public documentData:Array<any> =  [];

	@ViewChild('documentListChanged') documentListChanged: SortingComponent; 
	constructor(private formBuilder: FormBuilder,
		private commonApplicationService: CommonApplicationService,
		private alertService: AlertService,
		private storageService:StorageService,
    private datePipe:DatePipe
		) {
		this.length = this.documentData.length;
        this.property_config =  this.storageService.get("property_config");
	        if(this.property_config !== null && this.property_config !== false){
            this.property_document_type_list = this.property_config.non_hud_expenditures_sub_cat;
	        }
        }

	documentChange() {
	this.documentListChanged.listen();
	}

	public myDatePickerOptions: IMyDpOptions = { dateFormat: 'mm/dd/yyyy',firstDayOfWeek : 'su'};

  	onDateChanged(event: IMyDateModel) {
        return event.formatted;
    }

    ngOnInit() {
    if(this.property_id !== undefined && this.openPanel){
    }
    this.initilize();
  }

  private _filter(name: string): Category[] {
    if(typeof name !== 'object'){
      const filterValue = name.toLowerCase();
      return this.categoryList.filter(option => option.category_name.toLowerCase().indexOf(filterValue) === 0);
    }
  }
 

  getCategory(){
    let url = apiUrl.renovationCategory;
    this.commonApplicationService.get(url).subscribe(response => {
      this.categoryList=response.data;
    },
    (err: any) => {})
  }

  initilize() {
    	this.clientDocumentForm = this.formBuilder.group({
      property_document_type: ['', Validators.required],
      property_file:[''],
      amount: ['', Validators.required],
      prop_document_date: ['', Validators.required],
  	});

  }

  panelExpand(flag){
    if(!this.openPanel){
      this.getDocumentInfo();
      this.categoryList=this.storageService.getHard('inovice_category_list');
      if(!this.categoryList){
        this.getCategory();
      }
      this.filterCategory();
      this.openPanel = true;
    }
  }
  filterCategory(){
    this.filteredOptions = this.clientDocumentForm.get('property_document_type').valueChanges
      .pipe(startWith(''),map(value => this._filter(value)) ); 
  }
   // ----------------------------------- Get details Owner ---------------------------------------// 
  getDocumentInfo(){
      this.loading = true;
      //this.commonApplicationService.get(url,data,sucess_message,error_message);
      let url = apiUrl.accounting_document+"/"+this.property_id;
      this.commonApplicationService.get(url).subscribe(response => {
        this.loading = false;
        this.documentResult = response['data'].accounting_document;
        this.formatDocuments();
      },
      (err: any) => {
        this.loading = false;
        this.errorMessage = "There are no posts pulled from the server!";
      })
  }

   // Formatting documents
    formatDocuments(){
      let pDoc =  this.documentResult;
      for(var i =0; i<pDoc.length; i++){
        var dt = new Date(pDoc[i].created_at * 1000);
        let dayStr = (dt.getDate() <=9)? "0"+dt.getDate() : dt.getDate();
        let month = dt.getMonth()+1;
        let monStr = (month <=9)? "0"+month : month;

        let userIndex=this.categoryList.findIndex(item => item.id == pDoc[i].document_type);
        let documantTypeName='';
        if(userIndex >=0){
          documantTypeName=this.categoryList[userIndex].category_name;
        }

        pDoc[i].created_at = monStr+"/"+dayStr+"/"+dt.getFullYear();
        pDoc[i].document_type = documantTypeName;
        pDoc[i].delete = '<i class="fas  fa-trash-alt fa-lg cal-date" aria-hidden="true"></i>';
        pDoc[i].org_name = '<a  href="'+pDoc[i].url+'" target="_blank">'+pDoc[i].org_name+'</a>';
        pDoc[i].document_date = this.datePipe.transform(pDoc[i].document_date,'MM/dd/yyyy');
        pDoc[i].added_by = pDoc[i].user.first_name;
      }

      this.document_property = pDoc;
      this.documentData = pDoc;
      this.length = this.documentData.length;
      //this.onChangeTable(this.config);
    }
  // ----------------------------------- Get details Owner ---------------------------------------// 


 

  /******************** FILE UPLOAD FUNCTIONALITY *******************/
  uploadHandler(event){
     let elem = event.target;  //line 2 
    if(elem.files.length > 0){
      this.clientDocumentForm['controls']['property_file'].setErrors({'required': false});
    }
  }

  fileUploader(fileUploading, event):void{  
    console.log('My File upload',event,fileUploading);
    this.submitted=true;
    let uploadErr = 0;
    if(!this.clientDocumentForm['controls']['property_document_type'].value){
      this.clientDocumentForm.setControl('property_document_type', new FormControl('', Validators.required));
      this.clientDocumentForm['controls']['property_document_type'].setErrors({'required': true});
      uploadErr = 1;
    }
    
    if(fileUploading.files.length == 0){
      this.clientDocumentForm.setControl('property_file', new FormControl('', Validators.required));
      this.clientDocumentForm['controls']['property_file'].setErrors({'required': true});
      uploadErr = 1;
    }

    if(uploadErr) return;

    var fileToUpload = fileUploading.files[0];
    let documentDate = "";
    if(this.clientDocumentForm['controls']['prop_document_date'].value != ""){
      var d = this.clientDocumentForm['controls']['prop_document_date'].value.formatted;//new Date();
      documentDate = this.datePipe.transform(d,'yyyy-MM-dd');
    }

    // Today's Date
    var dt = new Date();
    let dayStr = (dt.getDate() <=9)? "0"+dt.getDate() : dt.getDate();
    let month = dt.getMonth()+1;
    let monStr = (month <=9)? "0"+month : month;
    let todayDate = monStr+"/"+dayStr+"/"+dt.getFullYear();

    let input = new FormData();

    let documentType=this.clientDocumentForm['controls']['property_document_type'].value;

    input.append("document_type", documentType?.id?documentType.id:documentType);
    input.append("amount", this.clientDocumentForm['controls']['amount'].value);
    input.append("document_date", documentDate);
    input.append("house_id", this.property_id);
    input.append("document", fileToUpload);

    this.loading = true;
    this.commonApplicationService.post(apiUrl.accounting_document, input)
    .subscribe(res => {
        this.loading = false;
        this.alertService.success(res.message); 

        if(res?.category){
          this.categoryList.push(res?.category);
        }
        let userIndex=this.categoryList.findIndex(item => item.id == documentType?.id);
        let documantTypeName=documentType;
        if(userIndex >=0){
          documantTypeName=this.categoryList[userIndex].category_name;
        }
        
        const newDoc = {
          id:res.data.id,
          document_type: documantTypeName,
          org_name: '<a  href="'+res.data.url+'" target="_blank">'+fileToUpload.name+'</a>',
         // other_name: '<a  href="'+res.url+'" target="_blank">'+this.clientDocumentForm['controls']['doc_other_name'].value+'</a>',
          document_date: this.datePipe.transform(documentDate,'MM/dd/yyyy'),
          added_by: this.storageService.get("user_info")['first_name']+" "+this.storageService.get("user_info")['last_name'],
          created_at: todayDate,
          amount: this.clientDocumentForm['controls']['amount'].value,
          delete:  '<i class="fas  fa-trash-alt fa-lg cal-date" aria-hidden="true"></i>'
        };
        this.documentResult.push(newDoc);
        this.length = this.documentData.length;
        this.documentChange();
        this.clientDocumentForm.reset();
        this.filterCategory();
      },
      error => {
        this.loading = false;
        this.alertService.common(error);
      });
  }

  rowDelete(row:any){

    console.log("InfoCOmp>>",row);
      //this.commonApplicationService.get(url,data,sucess_message,error_message);
      let url = apiUrl.accounting_document+'/'+row.id;
      this.commonApplicationService.delete(url).subscribe(response => {
        if(response !== undefined){             
             this.alertService.success(response.message); 
              var index =this.documentData.indexOf(row);
              if (index !== -1) {
                 this.documentData.splice(index, 1);
              }
        }
        this.documentChange();
      },
        (err: any) => {
          this.alertService.common(err); 
        })

 }

  showProperty(e){
    console.log(e);
   console.log(e.target.checked);
  }
  get f() { return this.clientDocumentForm.controls; }
/******************** FILE UPLOAD FUNCTIONALITY *******************/
  displayFn(category?: any): string | undefined {
    return category ? category.category_name : category;
  }
}


export interface Category{
  id:number,
  category_name:string;   
}