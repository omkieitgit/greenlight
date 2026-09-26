import { Component, OnInit, Input,ViewChild } from '@angular/core';
import { FormControl, FormBuilder, FormGroup, Validators } from '@angular/forms';
import {IMyDpOptions, IMyDateModel} from 'mydatepicker';
import {DatePipe} from '@angular/common';
import {apiUrl} from '../../config/api-url';
import { CommonApplicationService, AlertService } from '../../shared/_services';
import { StorageService } from '../../shared/_services/storage.service';
import { CommonHelper } from '../../shared/_utils/CommonHelper';
import { DocumentListComponent } from './document-list/document-list.component';

@Component({
  selector: 'app-document',
  templateUrl: './document.component.html',
  styleUrls: ['./document.component.css']
})
export class DocumentComponent implements OnInit {
  
  @Input() property_id:any;
  @Input() document_type_list:any;
  @Input() document_info:any;
  @Input() document_url:any;
  @Input() mortgage_id:any;
  @Input() doc_count_id:any=0;
  @Input() field_option:any={'other_name':true,'case_no':true};
  @Input() doc_primary_key:string='id';
  @Input() bidder_id:any;
  @Input() sale_id:any;
  @Input() documentType:any;


  documentForm:FormGroup;
  uploadLoading:boolean=false;
  public documentData:Array<any> =  [];
  showPropertyList:boolean = false;



   // ----------- Document upload config --------------------//
   public columns:Array<any> = [
    {title: 'Document Type', name: 'document_type'},
    {title: 'Doc Name', name: 'org_name'},
    //{title: 'Other Name', name: 'other_name'},
    //{title: 'Case No', name: 'case_number'},
    {title: 'Document Date', name: 'document_date'},
    {title: 'Uploaded By', name: 'added_by'},
    {title: 'Uploaded Date', name: 'created_at', sort: 'desc'},
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

  @ViewChild('documentListChanged') documentListChanged: DocumentListComponent;   

  constructor(private formBuilder: FormBuilder,
              private datePipe:DatePipe,
              private commonApplicationService:CommonApplicationService,
              private alertService:AlertService,
              private storageService:StorageService) { }

  ngOnInit() {

    if(this.field_option.other_name){
      this.columns.push({title: 'Other Name', name: 'other_name'});
    }
    if(this.field_option.case_no){
      this.columns.push({title: 'Case No', name: 'case_number'});
    }

    let userRole=this.storageService.get("user_info")['current_role'];
    let buyerRole =["wholesale_buyer","home_buyer"];
    console.log(buyerRole.indexOf(userRole));
    if(buyerRole.indexOf(userRole) === -1){
        this.columns.push({title: 'Action', name: 'delete'});
    }
    
    this.documentForm = this.formBuilder.group({
      property_document_type: [''],
      doc_other_name: [''],
      prop_document_date: [''],
      case_no: [''],
      property_file: ['']
    });
    this.formatDocuments();
  }

  public myDatePickerOptions: IMyDpOptions = { dateFormat: 'mm/dd/yyyy',firstDayOfWeek : 'su'};

   // Formatting documents
   formatDocuments(){

    let pDoc =  this.document_info;
    if(pDoc && pDoc.length>0){
      for(var i =0; i<pDoc.length; i++){
        var dt = new Date(pDoc[i].created_at * 1000);
        pDoc[i].id=pDoc[i][this.doc_primary_key];
        pDoc[i].document_date = this.datePipe.transform(pDoc[i].document_date,'MM/dd/yyyy');
        pDoc[i].created_at =this.datePipe.transform(dt,'MM/dd/yyyy');
        pDoc[i].document_type = this.document_type_list[pDoc[i].document_type];
        pDoc[i].delete = '<i class="fas  fa-trash-alt fa-lg cal-date" aria-hidden="true"></i>';
        pDoc[i].org_name = '<a  href="'+pDoc[i].url+'" target="_blank">'+pDoc[i].org_name+'</a>';
        pDoc[i].other_name = '<a  href="'+pDoc[i].url+'" target="_blank">'+pDoc[i].other_name+'</a>';
        pDoc[i].added_by = CommonHelper.filterUserName(pDoc[i]); 
      
      }
     // this.document_property = pDoc;
      this.documentData = pDoc;
      this.length = this.documentData.length;
      this.showPropertyList = true;
    }
    
    //this.onChangeTable(this.config);
  }



  onDateChanged(event: IMyDateModel) {
    return event.formatted;
  }

  get f() { return this.documentForm.controls; }

  /******************** FILE UPLOAD FUNCTIONALITY *******************/
  uploadHandler(event){
    let elem = event.target;  //line 2 
    if(elem.files.length > 0){
      this.documentForm['controls']['property_file'].setErrors({'required': false});
    }
  }

  fileUploader(fileUploading, event):void{  

    let uploadErr = 0;
    if(!this.documentForm['controls']['property_document_type'].value){
      this.documentForm.setControl('property_document_type', new FormControl('', Validators.required));
      this.documentForm['controls']['property_document_type'].setErrors({'required': true});
      uploadErr = 1;
    }

    if(fileUploading.files.length == 0){
      this.documentForm.setControl('property_file', new FormControl('', Validators.required));
      this.documentForm['controls']['property_file'].setErrors({'required': true});
      uploadErr = 1;
    }

    if(uploadErr) return;

    var fileToUpload = fileUploading.files[0];
    let documentDate = "";
    if(this.documentForm['controls']['prop_document_date'].value){
      var d = this.documentForm['controls']['prop_document_date'].value;
      documentDate = this.datePipe.transform(d.formatted,'yyyy-MM-dd');
    }

    // Today's Date
    let todayDate = this.datePipe.transform(new Date(),'MM/dd/yyyy'); 
    let input = new FormData();
    input.append("document_type",  this.documentForm['controls']['property_document_type'].value);
    input.append("case_number", this.documentForm['controls']['case_no'].value);
    input.append("other_name", this.documentForm['controls']['doc_other_name'].value);
    input.append("document_date", documentDate);
    input.append("house_id", this.property_id);
    input.append("document", fileToUpload);

    if(this.bidder_id){
      input.append("bidder_id", this.bidder_id);
    }

    if(this.sale_id){
      input.append("sale_id", this.sale_id);
    }
    if(this.mortgage_id){
      input.append("mortgage_id", this.mortgage_id);
    }

    if(this.documentType){
      input.append("documentType", this.documentType);
    }
  
  this.uploadLoading=true;
  let url = apiUrl[this.document_url];
  this.commonApplicationService.post(url, input).subscribe(res => {
        
        let response:any=[];
        if(res.hasOwnProperty('row'))
        {
          response=res.row;
        }
        else if(res.hasOwnProperty('data'))
        {
          response=res.data;
        }
        else{
          response=res;
        } 
        console.log(response);   
        this.alertService.success(res.message); 
        const newDoc = {
          id:response.id,
          document_type: this.document_type_list[this.documentForm['controls']['property_document_type'].value],
          org_name: '<a  href="'+response.url+'" target="_blank">'+fileToUpload.name+'</a>',
          other_name: '<a  href="'+response.url+'" target="_blank">'+this.documentForm['controls']['doc_other_name'].value+'</a>',
          document_date: this.datePipe.transform(documentDate,'MM/dd/yyyy'),
          added_by: this.storageService.get("user_info")['first_name']+" "+this.storageService.get("user_info")['last_name'],
          created_at: todayDate,
          case_number: this.documentForm['controls']['case_no'].value,
          delete:  '<i class="fas  fa-trash-alt fa-lg cal-date" aria-hidden="true"></i>'
        };
        
        this.documentData.push(newDoc);
        this.length = this.documentData.length;
        // if(this.showPropertyList)
        this.showPropertyList = true;
        this.uploadLoading=false;
       
        this.documentForm.reset();
        this.documentForm['controls']['property_file'].setErrors({'required': false});
        if(this.showPropertyList)
          this.documentChange();
      
      },
          error => {
            this.alertService.common(error); 
            this.uploadLoading=false;

          }
    );
  }

  documentChange() {
   this.documentListChanged.listen();
  }


rowDelete(row:any){

  let id='';
  if(row.id){
    id=row.id;
  }else{
    id=row[this.doc_primary_key];
  }


  let url = apiUrl[this.document_url]+'/'+id;
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
      this.alertService.common("Error occured, Please try again later!"); 
     // this.loadingMessage = false;
    })

  }

}
