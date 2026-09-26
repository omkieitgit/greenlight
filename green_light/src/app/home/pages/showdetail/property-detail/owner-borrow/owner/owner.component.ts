import { Component, OnInit, Input,Output,EventEmitter } from '@angular/core';
import { FormArray, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ActivatedRoute }    from '@angular/router';
import {OwnerModel} from './owner.model';
import {IMyDpOptions, IMyDateModel} from 'mydatepicker';
import { CommonApplicationService,AlertService,CommonActivityService,CommunicationService } from '@shared-service/_services';
import { apiUrl,fixedUrl } from '@config/api-url';
import { StorageService } from '@shared-service/_services/storage.service';


interface LooseObject {
  [key: string]: any
}

@Component({
  selector: 'owner',
  templateUrl: './owner.html',
})

export class OwnerComponent  implements OnInit{

  @Input() owner_info:any;
  @Input() document_owner:any;
  @Input() owner_borrower_info:any;

  property_id:any;
  ownerInfoForm: FormGroup;
  loading:boolean=false;
  loadResult:boolean=false;
  owner:'owner';

  submittedOwner = false;
  result: any;
  ownerResult:any;
  ownerData : OwnerModel;
  record: LooseObject = {};
  modifyBtn : boolean = false;
  owner_id:string='';
  owners = [1];
  owner_info_data=[1]; 
  orderForm: FormGroup;
  items: FormArray;
  property_config: any= {};
  property_document_type_list: string[];
  document_property: any[];

  beenVerifiedUrl:string=fixedUrl.beenverified_owner_url;
  pacerUrl:string=fixedUrl.pacer_url;
  validRow:number;
  owner_data:any;

  @Output() ownerInfo=new EventEmitter<any>();

  owner_notes:string='owner';

  no_pacer_result:boolean=false;
  currentIndex:number;

  public documentDataOwner:Array<any> =  [];
  public documentDataBorrower:Array<any> =  [];
    // ----------- Document upload config --------------------//
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

  onDateChanged(event: IMyDateModel) {
    return event.formatted;
  }


  ngOnInit() {
    this.property_id = this.route.snapshot.paramMap.get('property_id');
    this.initialize();
    this.ownerData.Owner=this.owner_info;          
    this.populateDataInFormOwner();

    let ownerForm=this.ownerInfoForm.get('owner_info_data');
   // for(var i=0; i<ownerForm.length; i++){ 
      ownerForm.valueChanges.subscribe(val => {
        if(val){
          this.ownerInfo.emit(val);
        }
      });
   // }
  }

  initialize(){
    this.ownerInfoForm = this.formBuilder.group({
      owner_info_data: this.formBuilder.array([this.ownerFields()]),     
      house_id:[this.property_id],
      no_pacer_result:[this.owner_borrower_info?this.owner_borrower_info.no_pacer_result:0],
      is_owner_same_property_address:[this.owner_borrower_info?this.owner_borrower_info.is_owner_same_property_address:0],
    });

    if(this.owner_borrower_info && this.owner_borrower_info.no_pacer_result){
      this.no_pacer_result=true;
    }
  }
  
  ownerFields() : FormGroup
  {    
    return this.formBuilder.group({
      full_name: ['', Validators.required],
      full_address: ['', Validators.required],
      email: [''],
      phone: [''],
      deed_bp_instrument: [''],
      deed_recorded_date: [''],
      beenverified_url: [this.beenVerifiedUrl],
      pacer_url:[this.pacerUrl],
      id:[''],
      house_id:[this.property_id],
    });
  }

  addOwnerInputField() : void
  {
   const control = <FormArray>this.ownerInfoForm.controls.owner_info_data;
   control.push(this.ownerFields());
  }
  
  removeOwnerInputField(i : number, id : number) : void
  {
      if(confirm("Are you sure want to delete record ?")){
           const control = <FormArray>this.ownerInfoForm.controls.owner_info_data;
           control.removeAt(i);
      }else{
        return;
      }
      if(id){
        let url = apiUrl.owner+'/'+id;
        this.commonApplicationService.delete(url).subscribe(response => {
        if(response !== undefined){             
             this.alertService.success(response.message); 
        }
        },
        (err: any) => {
          this.alertService.common(err); 
        })
      }
  }



  // ----------------------------------- Fill Form Data ---------------------------------------// 
  populateDataInFormOwner(){
    if(this.owner_info.length){
        if(this.owner_info.length == 1){
            // If data is for fist row then fill
             this.fillOwnerForm(0);
        }else{
            //If data is for fist row then fill
            this.fillOwnerForm(0);

            for(var i=1; i<this.owner_info.length; i++ ){
                this.addOwnerInputField();
                this.fillOwnerForm(i);
            }
        }
    }     
  }

  fillOwnerForm(index:number){

      let ownerForm=this.ownerInfoForm.get('owner_info_data')['controls'][index].controls;

      ownerForm.full_name.setValue(this.owner_info[index].full_name);
      ownerForm.id.setValue(this.owner_info[index].id);
      ownerForm.full_address.setValue(this.owner_info[index].full_address);
      ownerForm.email.setValue(this.owner_info[index].email);
      ownerForm.phone.setValue(this.owner_info[index].phone);
      ownerForm.deed_bp_instrument.setValue(this.owner_info[index].deed_bp_instrument);
      let deedData = this.owner_info[index].deed_recorded_date;
      let dObj = null;
      if(deedData !== undefined && deedData != null){
            let dDate = new Date(deedData);
            dObj = {
              date: {
                  year: dDate.getFullYear(),
                  month: dDate.getMonth() + 1,
                  day: dDate.getDate()}
              }
      }else{
          dObj = null;
      }
      ownerForm.deed_recorded_date.setValue(dObj);
      
      if(this.owner_info[index].beenverified_url && this.owner_info[index].beenverified_url!==undefined){
        this.beenVerifiedUrl=this.owner_info[index].beenverified_url;
      }
      ownerForm.beenverified_url.setValue(this.beenVerifiedUrl);
  }


 // ----------------------------------- Fill Form Data ---------------------------------------// 

// ----------------------------------- Form Validation Owner ---------------------------------------//
  validateOwnerForm(data: any,i: number) {
    if(this.property_id=="" || this.property_id==null || this.property_id === undefined){
      this.alertService.error("Please save property detail.");  
      return;
    }

    this.validRow=i;
    data.controls.house_id.setValue(this.property_id);
    this.result = this.commonActivityService.getFullFormDataWithDateFormatted(data);
    this.submittedOwner = true;
    if(this.ownerInfoForm.get('owner_info_data')['controls'][i].invalid) { 
      return;
    }    
    
    this.saveOwnerInformation();

    if(this.result.id !== undefined && this.result.id != null && this.result.id > 0){
        this.updateOwnerInfoDetails(this.result,i);
      }else{
        this.createOwnerInfoDetails(this.result,i);
      }
  }

// ----------------------------------- Form Validation Owner---------------------------------------//  


// ----------------------------------- Form Validation Borrower---------------------------------------//  

  get f() { return this.ownerInfoForm.controls; }


// ----------------------------------- Single Save---------------------------------------//  
  updateRecords(ele){
    this.record[ele] = this.ownerData[ele];
    this.saveModifiedValues(this.record);
  }

  // Save Mulit Fields in Once
  saveModifiedValues(data: any) {
    let result = this.commonActivityService.getModifiedOnly(data);
    console.log("dirtyValues"+result);
    this.saveOwnerInfoDetails(result);
    //return result;
  }

  // Save info detail
  saveOwnerInfoDetails(data: any){
    //console.log("savingdata>"+this.ownerInfoForm);
    console.log("savingdata>"+data);
    data['house_id']=this.property_id;
    let url = apiUrl.owner;
    this.commonApplicationService.post(url, data)
        .subscribe(
            data => {
              this.modifyBtn = false;
            },
            error => {
            }
        ); 
  }
// ----------------------------------- Single Save---------------------------------------//  

// ----------------------------------- Create Owner ---------------------------------------//  
  createOwnerInfoDetails(data: any,index: number){
    //console.log("savingdata>"+this.ownerInfoForm);
    console.log("savingdata>"+data);
    console.log(this.property_id);
    //data['house_id']=this.property_id;
    let url = apiUrl.owner;
    console.log(url);
    this.commonApplicationService.post(url, data)
        .subscribe(
            data => {
              this.modifyBtn = false;
              this.alertService.success(data.message); 
              this.ownerInfoForm.get('owner_info_data')['controls'][index].controls.id.setValue(data.row.id);
              //this.getOwnerInfo();               
            },
            error => {
              this.alertService.common(error);  
            }
        ); 
  }

 // ----------------------------------- Create Owner ---------------------------------------//  

// ----------------------------------- Update Owner ---------------------------------------//  
  updateOwnerInfoDetails(data: any,index: number){
    //console.log("savingdata>"+this.ownerInfoForm);
    console.log("savingdata>"+data);
    console.log(this.property_id);
    //data['house_id']=this.property_id;
    let url = apiUrl.owner+'/'+data.id;
    console.log(url);
    this.commonApplicationService.put(url, data)
        .subscribe(
            data => {
              this.modifyBtn = false;
              this.alertService.success(data.message);                
            },
            error => {
              this.alertService.common(error);  
            }
        ); 
  }
  
  propertyAddressChk(e){
    var ownerInfoData=this.ownerInfoForm.get('owner_info_data')['controls'];
    if(e.target.checked){
      for(var i=0; i<ownerInfoData.length; i++){
        if(ownerInfoData[i]!==undefined)
        {
          ownerInfoData[i].controls.full_address.setValue(this.storageService.getHard('property_info')['address']);
        }
      }
    }else{
      for(var i=0; i<ownerInfoData.length; i++){
        if(ownerInfoData[i]!==undefined)
        {
          ownerInfoData[i].controls.full_address.setValue("");
        }
      }
    }
  }

  noPacerResult(e){
    if(e.target.checked){
      this.no_pacer_result=true;
    }else{
      this.no_pacer_result=false;
    }
  }

  saveonwerInfo(data: any){
  this.loading = true;
  data.house_id = this.property_id;
  console.log("savingdata>"+data);
  let url = apiUrl.owner+'/owner_borrow_info/'+this.property_id;
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

  saveOwnerInformation(){
    let ownerInfo:any = this.commonActivityService.getFullFormDataWithDateFormatted(this.ownerInfoForm);
    
    let data:any={'is_owner_same_property_address':0,'house_id':this.property_id,no_pacer_result:0};
    if(ownerInfo.is_owner_same_property_address){
      data.is_owner_same_property_address=1;
    }
    if(ownerInfo.no_pacer_result){
      data.no_pacer_result=1;
    }
    let url = apiUrl.owner_borrow_info;
    this.commonApplicationService.post(url, data)
        .subscribe( data => { },
            error => { }
        ); 
  }

  autoSave(data){

    this.commonActivityService.addLoader(data['el']);
    this.commonActivityService.removeElement(data['el'],'saved-icon');
    
    let index=data.el.parentElement.parentElement.getAttribute('id');
    let id = this.ownerInfoForm.get('owner_info_data')['controls'][index].controls.id.value;
    let saveInfo:any={'id':id,'name':data['name'],'value':data['value']};
   
    if(!id && !this.loading && this.currentIndex!=index){
      this.loading=true;
      this.currentIndex=index;
      this.updateOwnerInfo(saveInfo,data);
    }else if(!id){
      data.el.value='';
      this.commonActivityService.removeElement(data['el'],'loader-icon');
    }
    if(id){
      this.updateOwnerInfo(saveInfo,data);
    }
  }

  updateOwnerInfo(saveInfo,data){
      let url = apiUrl.single_record_owner+'/'+this.property_id;
      this.commonApplicationService.put(url, saveInfo)
      .subscribe(
          response => {
            if(response['status']=='failed'){
              this.commonActivityService.removeElement(data['el'],'loader-icon');
              this.alertService.error(response['message']);
            }else{
                this.commonActivityService.addElement(data['el']);
                this.commonActivityService.removeElement(data['el'],'loader-icon');
                let index=data.el.parentElement.parentElement.getAttribute('id');
                this.ownerInfoForm.get('owner_info_data')['controls'][index].controls.id.setValue(response.data);              
            }
            this.loading=false;
          },
          error => {
            this.commonActivityService.removeElement(data['el'],'loader-icon');
            this.loading = false;
            this.alertService.common(error);
          }
      ); 
  }

}