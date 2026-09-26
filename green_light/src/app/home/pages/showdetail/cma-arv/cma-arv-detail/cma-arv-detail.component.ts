import { Component, OnInit,ViewContainerRef,ViewChild,ComponentFactoryResolver, Input} from '@angular/core';
import { FormControl, NgForm, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router,ActivatedRoute }    from '@angular/router';
import { apiUrl } from '@config/api-url';
import { CommonApplicationService,CommonActivityService,MessageService,AlertService,CommunicationService} from '@shared-service/_services';
import { cmaArvModel } from '../cma-arv.model';
import { Subscription } from 'rxjs';
import {StorageService} from '@shared-service/_services/storage.service';
import {CommonHelper} from '@shared-service/_utils/CommonHelper';

@Component({
  selector: 'app-cma-arv-detail',
  templateUrl: './cma-arv-detail.component.html',
  styleUrls: ['./cma-arv-detail.component.css']
})
export class CmaArvDetailComponent implements OnInit {

  @Input() property_id;
  cmaArvForm: FormGroup;
  submitted = false;
  // ngbdatepicker
  model1: Date;
  model2: Date;
  propErr: boolean = false;
  propErrMsg: any = "";
  loadingMessage: any;
  result: any;
  modifyBtn : boolean = false;
  ownerInfoForm: FormGroup;
  cmaArvData : cmaArvModel;
  result1: any;

  wholesale_buyer:string='wholesale_buyer';
  first_dtc:string='first_dtc';
  second_dca:string='second_dca';
  third_dca:string='third_dca';
  final_dca:string='final_dca';
  cma_arv:any;
  openPanel:boolean = false;

  subscribedData: any;
  real_info: any;
  subscription: Subscription;

  cma_arv_notes:string='cma_arv';
  invalidFields:any;

  cma_arv_recomndation:number=0;
  finalCheck:boolean=false;
  property_config:any;
  acrsValue:string='';

  @ViewChild('cmaArvSections', { read: ViewContainerRef }) container: ViewContainerRef;
  
  constructor(private formBuilder: FormBuilder,
              private commonActivityService :CommonActivityService,
              private commonApplicationService:CommonApplicationService,
              private router: Router,
              private route: ActivatedRoute,
              private alertService:AlertService,
              private _cfr: ComponentFactoryResolver,
              private messageService: MessageService,
              private CommunicationService:CommunicationService,
              private storageService:StorageService) { 
                this.cmaArvData = new cmaArvModel();
                
              }

  meridian = true;
  toggleMeridian() {
      this.meridian = !this.meridian;
  }

  ngOnInit() {
    this.property_config =  this.storageService.get("property_config");
    //this.subscription = this.messageService.getArrData().subscribe(message => { this.subscribedData = message; });
    this.subscribedData= this.storageService.getHard('property_info');
    this.real_info= this.storageService.getHard('real_info');
    
    //this.cmaArvForm = this.formBuilder.group({});
    if(this.property_id !== undefined){
      this.propErr = true;
      //if(this.openPanel){
        this.getCmaArvInfo();
       
     // }
    }

    this.CommunicationService.getFinalCheckDCA().subscribe(response=>{
      if(response==true){
        this.finalCheck=true;
      }
    });
  }

 
  /**getCmaArvInfo
   * get infromation from API
   */
  getCmaArvInfo(){
    this.loadingMessage = true;
    //this.commonApplicationService.get(url,data,sucess_message,error_message);
    let url = apiUrl.cma_arv+this.property_id;
    this.commonApplicationService.get(url).subscribe(response =>{
      this.loadingMessage = false;
      this.cma_arv = response['data'];
      let finalDTC=this.cma_arv.find(cma=>cma.info_added_by=='final_dca');
      if(finalDTC) this.finalCheck=true;
      if(this.cma_arv !== undefined){
        this.propErr=false;
      }
      this.getCmaArvRecommendation();
      this.initialize();
    })
  }

  initialize(){
      // From validation
      if(this.subscribedData.property_type==2 &&  this.subscribedData.specific_property_type==21)
      {
        this.acrsValue=(this.subscribedData.lot_acreage_sf/43560).toFixed(2);
      }
      let propertyType='';
      let specialProType='';
      if(this.subscribedData.property_type>0){
        propertyType=this.property_config.property_types[this.subscribedData.property_type];
        specialProType=this.property_config.specific_property_types[this.subscribedData.property_type][propertyType];
        specialProType=specialProType[this.subscribedData.specific_property_type];
      }
      


      this.cmaArvForm = this.formBuilder.group({
        total_living_sqft: [this.subscribedData.total_living_sqft?CommonHelper.convertInt(this.subscribedData.total_living_sqft):0],
        total_sqft: [this.subscribedData.total_living_sqft?CommonHelper.convertInt(this.subscribedData.total_living_sqft):0],
        year_built: [this.subscribedData.year_built?this.subscribedData.year_built:0],
        beds: [this.subscribedData.bed?this.subscribedData.bed:0],
        bath: [this.subscribedData.bath?this.subscribedData.bath:0],
        lot_acres_sqft:[this.subscribedData.lot_acreage_sf?this.subscribedData.lot_acreage_sf:0],
        property_type: [propertyType],
        specific_property_type:[specialProType],
        zestimate:[this.subscribedData.zestimate?CommonHelper.convertInt(this.subscribedData.zestimate):0],
        county_value:[this.subscribedData.county_value?this.subscribedData.county_value:0],
        cost_per_sqft:[this.subscribedData.cost_per_sqft?this.subscribedData.cost_per_sqft:0],
        cma_arv:[this.subscribedData.recommended_cma_arv?this.subscribedData.recommended_cma_arv:0],
        conservative_sale_price:[this.subscribedData.recommended_cma_arv?this.subscribedData.recommended_cma_arv:0],
        sub_to_property:[this.subscribedData?.subto_property?.sub_to_property]
    });

    //this.commonActivityService.isDisabled("CMA_ARV", this.cmaArvForm);
  }

  getInfoDetails(cma_arv){

    if(this.result){
      for(let i=0; i<this.result.length; i++){
        if(this.result[i]['info_added_by']==cma_arv){
          return this.result[i];
        }
      }
    }
    
  }

  
  // Property Validation
  validateForm(data: any) { 
    let result = this.commonActivityService.getFullFormDataWithDateFormatted(data);
    this.invalidFields = this.commonActivityService.findInvalidControls(data);
    console.log(result);
    this.submitted = true;
    if (this.cmaArvForm.invalid) { 
      return;
    }
    this.saveUpdateCmaArv(result);
      // do something else
  }

  get f() { return this.cmaArvForm.controls; }

  // Save info detail
  saveCmaArv(data: any){
    //console.log("savingdata>"+this.propertyForm);
    console.log("savingdata>"+data);
    data['house_id']=this.property_id;
   
    let url = apiUrl.cma_arv;
    this.commonApplicationService.post(url, data)
        .subscribe(
            data => {
              this.modifyBtn = false;
            },
            error => {
                //this.error_message=true;
                //this.message=error['message'];
                //this.alertService.error(error);
                //this.loading = false;
            }
        ); 
  }

  // Save info detail
  updateCmaArv(data: any){
    //console.log("savingdata>"+this.propertyForm);
    console.log("savingdata>"+data);
    console.log(this.property_id);
    data['house_id']=this.property_id;
    let url = apiUrl.cma_arv+this.property_id+'/all';
    console.log(url);
    this.commonApplicationService.put(url, data)
        .subscribe(
            data => {
              this.modifyBtn = false;
            },
            error => {
                //this.error_message=true;
                //this.message=error['message'];
                //this.alertService.error(error);
                //this.loading = false;
            }
        ); 
  }


  saveUpdateCmaArv(data){
     //console.log("savingdata>"+this.propertyForm);
     console.log("savingdata>"+data);
     console.log(this.property_id);
     data['house_id']=this.property_id;
     data['info_added_by']=this.storageService.get('user_info')['current_role'];
     console.log(data);
     let url = apiUrl.cma_arv+this.property_id;
     this.commonApplicationService.put(url, data)
         .subscribe(
             data => {
               this.modifyBtn = false;
               this.messageService.sendArrData({"cma_arv":data.cma_arv });
               this.alertService.success(data.message);  
             
             },
             error => {
                 //this.error_message=true;
                 //this.message=error['message'];
                 //this.alertService.error(error);
                 //this.loading = false;
             }
         ); 
  }

  getCmaArvRecommendation(){

    for(var i=0; i<this.cma_arv.length; i++){
      if(this.cma_arv[i]['info_added_by']=='wholesale_buyer' && this.cma_arv[i]['recommended_cma_arv']!=""){
        this.cma_arv_recomndation=this.cma_arv[i]['recommended_cma_arv'];
      }else if(this.cma_arv[i]['info_added_by']=='third_dca' && this.cma_arv[i]['recommended_cma_arv']!=""){
        this.cma_arv_recomndation=this.cma_arv[i]['recommended_cma_arv'];
      }else if(this.cma_arv[i]['info_added_by']=='second_dca' && this.cma_arv[i]['recommended_cma_arv']!=""){
        this.cma_arv_recomndation=this.cma_arv[i]['recommended_cma_arv'];
      }else if(this.cma_arv[i]['info_added_by']=='first_dtc' && this.cma_arv[i]['recommended_cma_arv']!=""){
        this.cma_arv_recomndation=this.cma_arv[i]['recommended_cma_arv'];
      }
    }
  }
   
  autoSave(data){
    this.commonActivityService.addLoader(data['el']);
    this.commonActivityService.removeElement(data['el'],'saved-icon');
    let saveInfo:any={'name':data['name'],'value':data.el.checked?1:0}
    let url = apiUrl.subto_property+'/'+this.property_id;
    this.commonApplicationService.put(url, saveInfo)
        .subscribe(
            response => {
              if(response['status']=='failed'){
                this.commonActivityService.removeElement(data['el'],'loader-icon');
                this.alertService.error(response['message']);
              }else{
                  this.commonActivityService.addElement(data['el']);
                  this.commonActivityService.removeElement(data['el'],'loader-icon');
              }
            },
            error => {
              this.alertService.common(error);
            }
        ); 
  }
/*----------------------------- Get property Info Details --------------------------------*/

}
