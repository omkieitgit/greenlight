import { Component, Input,OnInit, OnChanges } from '@angular/core';
import { FormArray, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ActivatedRoute }    from '@angular/router';
import {OwnerModel} from '../owner/owner.model';
import {IMyDpOptions, IMyDateModel} from 'mydatepicker';
import { CommonApplicationService,AlertService,CommonActivityService,CommunicationService } from '@shared-service/_services';
import { apiUrl } from '@config/api-url';
import { StorageService } from '@shared-service/_services/storage.service';



@Component({
  selector: 'app-borrow',
  templateUrl: './borrow.component.html',
  styleUrls: ['./borrow.component.css'],
})


export class BorrowComponent implements OnInit,OnChanges {
 

  @Input() borrower_info:any;
  @Input() document_borrower:any;
  @Input() owner_borrower_info:any;
  
  property_id:any;

  borrowerInfoForm: FormGroup;
  loading:boolean=false;
  loadResult:boolean=false;
  borrow:string='borrow';
  
  borrowerDocForm: FormGroup;

  borrower_document_info:any;

  
  property_document_type_list: string[];
  borrowerResult:any;
 
  borrowers=[1];
  borrow_info_data=[1]; 
  // Borrower
  result: any;
  invalidFields: any;
  property_config: any= {};
  document_property: any[];
  modifyBtn : boolean = false;
  submittedBorrower:boolean=false;
  ownerData : OwnerModel;
  validRow:number;
  currentIndex:number;
  loadingRow:boolean=false;

  /* Notes info */
  borrower_notes:string='borrower';
 
  public documentDataBorrower:Array<any> =  [];

  @Input() owner_data:any;

  constructor(private formBuilder: FormBuilder,
              private commonActivityService :CommonActivityService,
              private commonApplicationService:CommonApplicationService,
              private storageService:StorageService,
              private communicationService:CommunicationService,
              private route:ActivatedRoute,
              private alertService:AlertService) { 
                this.ownerData=new OwnerModel();
                this.property_config =  this.storageService.get("property_config");
                this.property_document_type_list = this.property_config.property_document_type;
              }
  
  public myDatePickerOptions: IMyDpOptions = { dateFormat: 'yyyy-mm-dd',firstDayOfWeek : 'su'};

  ngOnInit() {
    this.property_id = this.route.snapshot.paramMap.get('property_id');
    this.initialize();
    this.ownerData.Borrow=this.borrower_info;
    this.borrower_document_info=this.document_borrower;
    this.populateDataInFormBorrower();
    
  }

  ngOnChanges(){
      this.owner_data;
  }
 

  onDateChanged(event: IMyDateModel) {
    return event.formatted;
  }
  
  initialize(){
    this.borrowerInfoForm = this.formBuilder.group({
      borrow_info_data: this.formBuilder.array([this.borrowFields()]),  
      house_id:[this.property_id],
      is_borrower_same_owner_address:[this.owner_borrower_info?this.owner_borrower_info.is_borrower_same_owner_address:0],
      is_borrower_same_owner_name:[this.owner_borrower_info?this.owner_borrower_info.is_borrower_same_owner_name:0],
    });
  }

  borrowFields(): FormGroup
  {
    return this.formBuilder.group({
      full_name: ['', Validators.required],
      full_address: ['', Validators.required],
      email: [''],
      phone: [''],
      id:[''],
      house_id:[this.property_id]
    });
  }

  addBorrowInputField() : void
  {
   const control = <FormArray>this.borrowerInfoForm.controls.borrow_info_data;
   control.push(this.borrowFields());
  }
  
  removeBorrowInputField(i : number,  id : number) : void
  {

    if(confirm("Are you sure want to delete record ?")){
      const control = <FormArray>this.borrowerInfoForm.controls.borrow_info_data;
     
      if(id){
        let url = apiUrl.borrower+'/'+id;
         this.commonApplicationService.delete(url).subscribe(response => {
           if(response !== undefined){ 
              let index=this.borrower_info.findIndex(items => items.id ===id);
              if (index > -1) {
                this.borrower_info.splice(index, 1);
              }
              control.removeAt(i);
              this.alertService.success(response.message); 
           }
         },
         (err: any) => {
           this.alertService.error("Error occured, Please try again later!");
         })
     }else{
        control.removeAt(i);
     }
    }
  }

 
 // ----------------------------------- Get details Borrower ---------------------------------------// 

// ----------------------------------- Fill Form Data ---------------------------------------// 
  populateDataInFormBorrower(){
    if(this.borrower_info.length){
        if(this.borrower_info.length == 1){
            // If data is for fist row then fill
             this.fillBorrowerForm(0);
        }else{
            //If data is for fist row then fill
            this.fillBorrowerForm(0);

            for(var i=1; i<this.borrower_info.length; i++ ){
                this.addBorrowInputField();
                this.fillBorrowerForm(i);
            }
        }
    }     
  }

  fillBorrowerForm(index:number){
      this.borrowerInfoForm.get('borrow_info_data')['controls'][index].controls.full_name.setValue(this.borrower_info[index].full_name);
      this.borrowerInfoForm.get('borrow_info_data')['controls'][index].controls.id.setValue(this.borrower_info[index].id);
      this.borrowerInfoForm.get('borrow_info_data')['controls'][index].controls.full_address.setValue(this.borrower_info[index].full_address);
      this.borrowerInfoForm.get('borrow_info_data')['controls'][index].controls.email.setValue(this.borrower_info[index].email);
      this.borrowerInfoForm.get('borrow_info_data')['controls'][index].controls.phone.setValue(this.borrower_info[index].phone);
     // this.borrowerInfoForm.get('borrow_info_data')['controls'][index].controls.phone2.setValue(this.borrower_info[index].phone2);      
  }



 // ----------------------------------- Fill Form Data ---------------------------------------// 


// ----------------------------------- Form Validation Borrower ---------------------------------------//
validateBorrowerForm(data: any,i: number) {  
  if(this.property_id=="" || this.property_id==null || this.property_id === undefined){
    this.alertService.error("Please save property detail.");  
    return;
  } 
  this.validRow=i;
  data.controls.house_id.setValue(this.property_id);
  this.result = this.commonActivityService.getFullFormDataWithDateFormatted(data);
  this.invalidFields = this.commonActivityService.findInvalidControls(data);
  console.log(this.borrowerInfoForm);

  this.submittedBorrower = true;
  if(this.borrowerInfoForm.get('borrow_info_data')['controls'][i].invalid) { 
    return;
  }    
  // FORM SUBMITTED
  console.log("Form Data = ",this.result);
  console.log('form submitted');
  this.saveOwnerInformation();
  if(this.result.id !== undefined && this.result.id != null && this.result.id > 0){
      this.updateBorrowerInfoDetails(this.result,i);
    }else{
      this.createBorrowerInfoDetails(this.result,i);
    }
  
}

// ----------------------------------- Form Validation Borrower---------------------------------------//  

get fb() { return this.borrowerInfoForm.controls; }
get f() { return this.borrowerDocForm.controls; }

// ----------------------------------- Create Borrower ---------------------------------------//  
createBorrowerInfoDetails(data: any,index: number){
  let url = apiUrl.borrower;
  this.commonApplicationService.post(url, data)
      .subscribe(
          data => {
            this.modifyBtn = false;
            this.alertService.success(data.message); 
            this.borrowerInfoForm.get('borrow_info_data')['controls'][index].controls.id.setValue(data.row.id);          
          },
          error => {
            this.alertService.common(error); 
          }
      ); 
}


  // ----------------------------------- Update Borrower ---------------------------------------//  
  updateBorrowerInfoDetails(data: any,index: number){
    console.log("savingdata>"+data);
    console.log(this.property_id);
    //data['house_id']=this.property_id;
    let url = apiUrl.borrower+'/'+data.id;
    console.log(url);
    this.commonApplicationService.put(url, data)
        .subscribe(
          response => {
              this.modifyBtn = false;
              this.alertService.success(response.message); 
              let itemIndex = this.borrower_info?this.borrower_info.findIndex(item => item.id == response.data.id):'-1';
              if(itemIndex >= 0){
                this.borrower_info[itemIndex] = response.data;
              }else{
                this.borrower_info.push(response.data);
              }               
            },
            error => {
              this.alertService.common(error); 
            }
        ); 
  }
  
  borrowerNameChk(e){
    let borrowInfo=this.borrowerInfoForm.get('borrow_info_data')['controls'];
    if(e.target.checked){
      for(let i=0; i<borrowInfo.length; i++){
        if(borrowInfo[i]!==undefined && this.owner_data[i])
        {
          borrowInfo[i].controls.full_name.setValue(this.owner_data[i].full_name);
        }
      }
    }else{
      for(let i=0; i<borrowInfo.length; i++){
        if(borrowInfo[i]!==undefined)
        {
          borrowInfo[i].controls.full_name.setValue("");
        }
      }
    }
  }

  borrowerAddChk(e){

    let borrowInfo=this.borrowerInfoForm.get('borrow_info_data')['controls'];
    if(e.target.checked){
      for(let i=0; i<borrowInfo.length; i++){
        if(borrowInfo[i]!==undefined && this.owner_data[i])
        {
          borrowInfo[i].controls.full_address.setValue(this.owner_data[i].full_address);
        }
      }
    }else{
      for(let i=0; i<borrowInfo.length; i++){
        if(borrowInfo[i]!==undefined)
        {
          borrowInfo[i].controls.full_address.setValue("");
        }
      }
    }
    
  }  

  saveOwnerInformation(){
    let ownerInfo:any = this.commonActivityService.getFullFormDataWithDateFormatted(this.borrowerInfoForm);
    
    let data:any={'is_borrower_same_owner_name':0,"is_borrower_same_owner_address":0,'house_id':this.property_id};
    if(ownerInfo.is_borrower_same_owner_name){
      data.is_borrower_same_owner_name=1;
    }
    if(ownerInfo.is_borrower_same_owner_address){
      data.is_borrower_same_owner_address=1;
    }
    let url = apiUrl.owner_borrow_info;
    this.commonApplicationService.post(url, data)
        .subscribe( data => { },
            error => { }
        ); 
  }
  /******************** FILE UPLOAD FUNCTIONALITY BORROWER *******************/
  autoSave(data){

    this.commonActivityService.addLoader(data['el']);
    this.commonActivityService.removeElement(data['el'],'saved-icon');
    
    let index=data.el.parentElement.parentElement.getAttribute('id');
    let id = this.borrowerInfoForm.get('borrow_info_data')['controls'][index].controls.id.value;
    let saveInfo:any={'id':id,'name':data['name'],'value':data['value']};
    
    if(!id && !this.loading && this.currentIndex!=index){
      this.loading=true;
      this.currentIndex=index;
      this.updateBorrowerInfo(saveInfo,data);
    }else if(!id){
      data.el.value='';
      this.borrowerInfoForm.disable();
      this.commonActivityService.removeElement(data['el'],'loader-icon');
    }
    if(id){
      this.updateBorrowerInfo(saveInfo,data);
    }
    
  }

  updateBorrowerInfo(saveInfo,data){
      let url = apiUrl.single_record_borrower+'/'+this.property_id;
      this.commonApplicationService.put(url, saveInfo)
      .subscribe(
          response => {
            if(response['status']=='failed'){
              this.commonActivityService.removeElement(data['el'],'loader-icon');
              this.alertService.error(response['message']);
            }else{
                let itemIndex = this.borrower_info?this.borrower_info.findIndex(item => item.id == response.data.id):'-1';
                if(itemIndex >= 0){
                  this.borrower_info[itemIndex] = response.data;
                }else{
                  this.borrower_info.push(response.data);
                }
                this.commonActivityService.addElement(data['el']);
                this.commonActivityService.removeElement(data['el'],'loader-icon');
                let index=data.el.parentElement.parentElement.getAttribute('id');
                this.borrowerInfoForm.get('borrow_info_data')['controls'][index].controls.id.setValue(response.data.id);              
                this.borrowerInfoForm.enable();
            }
            this.loading=false;
          },
          error => {
            this.commonActivityService.removeElement(data['el'],'loader-icon');
            this.loading = false;
            this.alertService.common(error);
            this.borrowerInfoForm.enable();
          }
      ); 
  }
}
