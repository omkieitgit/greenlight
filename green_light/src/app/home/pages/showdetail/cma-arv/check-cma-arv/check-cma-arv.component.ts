import { Component, OnInit,Input, HostListener, HostBinding, Renderer2 } from '@angular/core';
import { FormControl, NgForm, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { cmaArvModel, cma_arv_data } from '../cma-arv.model';
import { CommonApplicationService,CommunicationService } from '../../../../../shared/_services';
import { CommonActivityService } from '../../../../../shared/_services';
import { Router,ActivatedRoute }    from '@angular/router';
import {IMyDpOptions, IMyDateModel} from 'mydatepicker';
import { apiUrl } from '../../../../../config/api-url';
import { AlertService } from '../../../../../shared/_services';
import { StorageService } from '../../../../../shared/_services/storage.service';
import {MatDialog} from '@angular/material/dialog';
import {DaysOnMarketComponent} from './days-on-market/days-on-market.component';
import { Subject } from "rxjs";
import { CommonHelper } from '../../../../../shared/_utils/CommonHelper';
import { DatePipe } from '@angular/common';
import { SqftCalculationComponent } from './sqft-calculation/sqft-calculation.component';

@Component({
  selector: 'app-check-cma-arv',
  templateUrl: './check-cma-arv.component.html',
  styleUrls: ['./check-cma-arv.component.css']
})
export class CheckCmaArvComponent implements OnInit {

  @Input() cma_arv: any;
  @Input() cma_arv_type:string;
  @Input() cma_title:string;

  cmaArvData : cmaArvModel;
  cmaArvForm: FormGroup;
  result: any;
  propErr: boolean = false;
  form_group_name:string;
  submitted:boolean=false;
  @Input() property_id:string;
  loading:boolean=false;
  cma_arv_info:any;
  disabled_date:boolean=false;
  userRole:any;
  openPanel:boolean=false;
  collapse:boolean=true;

  notes_btn:any;
  notes_detail:any;
  resetFormSubject: Subject<boolean> = new Subject<boolean>();

  user_info:any;
  property_info:any;
  sqftCalBtn:boolean=false;

  constructor(private formBuilder: FormBuilder,
              private commonActivityService :CommonActivityService,
              private commonApplicationService:CommonApplicationService,
              private route: ActivatedRoute,
              private alertService:AlertService,
              private storageService:StorageService,
              private dialog:MatDialog,
              private communicationService:CommunicationService,
              private renderer: Renderer2,
              private datePipe:DatePipe) {
    this.cmaArvData = new cmaArvModel();
   }

  ngOnInit() {
    this.user_info= this.storageService.get("user_info");
    this.userRole = this.user_info['current_role'];
    this.property_info=this.storageService.getHard('property_info');
    if(this.property_info.property_type==2 &&  this.property_info.specific_property_type==21)
    {
      this.sqftCalBtn=true; 
    }
    this.initialize();
    this.cmaArvForm.valueChanges.subscribe((req) => {
      let fieldInfo=true;
      // let result = <any>this.commonActivityService.getFullFormData(this.cmaArvForm);
      const userStr = JSON.stringify(req);
      JSON.parse(userStr, (key, value) => {
        if((value=="" || value==null) && key!='user_id' && key!='date'){
          fieldInfo=false;
        }
      });

      if(fieldInfo==true){
        let userName=this.user_info['first_name']+' '+this.user_info['last_name'];
        this.cmaArvForm.controls.user_id.setValue(userName, {onlySelf: true, emitEvent: false});
        let d = new Date();
        this.cmaArvForm.patchValue({date:{date: {year: d.getFullYear(), month: d.getMonth() + 1, day: d.getDate()}}},{onlySelf: true, emitEvent: false});
      
      }
    });

    this.cmaArvNotes();

  }



  cmaArvNotes(){
    this.notes_btn={'note_name':'mortgage_notes','noteBtn':true,'noteDetail':false,'lien_type':this.cma_arv_type,'cma_arv_type':this.cma_arv_type};
    this.notes_detail={'note_name':'mortgage_notes','noteBtn':false,'noteDetail':true,'lien_type':this.cma_arv_type};
    
  }

  updatedNotes(event){
    if(event)
      this.resetFormSubject.next(true);
  }

  panelExpand(flag){
    if(!this.openPanel){
      this.openPanel = true;
    }
  }

  showHidePanel($event){
      this.collapse=$event;
  }
  
  initialize(){
      // From validation
      console.log("INITLITING THE FORM................................");
      let cma_arv=this.getInfoDetails(this.cma_arv_type);
      
      let d = new Date();
      if(cma_arv && cma_arv.date!="")
        d = new Date(cma_arv.date);

      this.cmaArvForm = this.formBuilder.group({
          specific_demand: [cma_arv!=null?cma_arv.specific_demand:'',Validators.required],
          general_demand: [cma_arv!=null?cma_arv.general_demand:'',Validators.required],    
          days_on_market: [cma_arv!=null?cma_arv.days_on_market:'',Validators.required],
          phase_renovation: [cma_arv!=null?cma_arv.phase_renovation:'',Validators.required],
          price_sqft_sale_comps_from: [cma_arv!=null?cma_arv.price_sqft_sale_comps_from:'',Validators.required],
          price_sqft_sale_comps_to: [cma_arv!=null?cma_arv.price_sqft_sale_comps_to:'',Validators.required],
          ssd_sale_comps: [cma_arv!=null?cma_arv.ssd_sale_comps:'',Validators.required],
          gsd_sale_comps: [cma_arv!=null?cma_arv.gsd_sale_comps:'',Validators.required],
          rent_gsd:[cma_arv!=null?cma_arv.rent_gsd:'',Validators.required],
          price_sqft_sold_comps_from:[cma_arv!=null?cma_arv.price_sqft_sold_comps_from:'',Validators.required],
          price_sqft_sold_comps_to:[cma_arv!=null?cma_arv.price_sqft_sold_comps_to:'',Validators.required],
          ssd_sold_comps:[cma_arv!=null?cma_arv.ssd_sold_comps:'',Validators.required],
          gsd_sold_comps:[cma_arv!=null?cma_arv.gsd_sold_comps:'',Validators.required],
          rental_comps_map:[cma_arv!=null?cma_arv.rental_comps_map:'',Validators.required],
          p1_value:[cma_arv!=null?cma_arv.p1_value:'',Validators.required],
          p2_value:[cma_arv!=null?cma_arv.p2_value:'',Validators.required],
          p3_value:[cma_arv!=null?cma_arv.p3_value:'',Validators.required],
          rents_zestimate:[cma_arv!=null?cma_arv.rents_zestimate:'',Validators.required],
          p1_adom:[cma_arv!=null?cma_arv.p1_adom:'',Validators.required],
          p2_adom:[cma_arv!=null?cma_arv.p2_adom:'',Validators.required],
          p3_adom:[cma_arv!=null?cma_arv.p3_adom:'',Validators.required],
          rental_rate:[cma_arv!=null?cma_arv.rental_rate:'',Validators.required],
          comp_url_1:[cma_arv!=null?cma_arv.comp_url_1:'',Validators.required],
          comp_url_2:[cma_arv!=null?cma_arv.comp_url_2:'',Validators.required],
          comp_url_3:[cma_arv!=null?cma_arv.comp_url_3:'',Validators.required],
          comp_url_4:[cma_arv!=null?cma_arv.comp_url_4:'',Validators.required],
          recommended_cma_arv:[cma_arv!=null?cma_arv.recommended_cma_arv:'',Validators.required],
          wholetail_value:[cma_arv!=null?cma_arv.wholetail_value:'',Validators.required],
          user_id:[cma_arv!=null && cma_arv.user?cma_arv.user.first_name+' '+cma_arv.user.last_name:''],
          date:[(cma_arv!=null && cma_arv.date != null)? {jsdate: CommonHelper.getConvertDate(cma_arv.date)}:null],
          id:[cma_arv!=null?cma_arv.id:'',''],
          info_added_by:[],
   
    });
    if(cma_arv && (cma_arv.user!=null || cma_arv.user!=undefined)){
      if(this.dateValidation(cma_arv.date) ==false && this.userRole!='admin')
      {
        this.cmaArvForm.controls['user_id'].disable();
        this.disabled_date=true;
      }
    }
    //this.commonActivityService.isDisabled("CMA_ARV", this.cmaArvForm);
  }

  dateValidation(date) {

    var b = new Date(date); 
    b.setMonth(b.getMonth() + 6);  //subtract 6 month from current date 
    var d = new Date(); // today date
    if (d > b) {
        return true;
    } else {
        return false;
    }
}


  public myDatePickerOptions: IMyDpOptions = { dateFormat: 'mm/dd/yyyy',firstDayOfWeek : 'su'};

  onDateChanged(event: IMyDateModel) {
 //     if(this.cma_arv_type=='wholesale_buyer'){
        this.saveUpdateSingleCmaArv({'name':'date','value':this.datePipe.transform(event.formatted,'yyyy-MM-dd')});
  //    }
      return event.formatted;
  }
  get f() { return this.cmaArvForm.controls; }

  getInfoDetails(cmaArvType){

    if(this.cma_arv){
      for(let i=0; i<this.cma_arv.length; i++){
        if(this.cma_arv[i]['info_added_by']==cmaArvType){
          this.cma_arv_info=this.cma_arv[i];
          return this.cma_arv[i];
        }
      }
    }
    
  }


    // Property Validation
    validateForm(data: any) { 

      let result = this.commonActivityService.getFullFormDataWithDateFormatted(data);
      console.log(result);
      this.submitted = true;
      if (this.cmaArvForm.invalid) { 
        return;
      }
      
      this.saveUpdateCmaArv(result);
      // do something else
    }
    saveUpdateCmaArv(data){
      this.loading=true;
      data['house_id']=this.property_id;
      data['info_added_by']=this.cma_arv_type;
      
      let cma_date='';
      if(data.date){
          cma_date=data.date;
      }
      
      if(this.userRole!='admin'){
        if(data.user_id && 
          this.dateValidation(cma_date) ==false 
          && this.cmaArvForm.controls.user_id.status!="DISABLED"){
          data['date']=cma_date;
          data['user_id']=this.user_info['id'];
        }else{
          delete data['user_id'];
          delete data['date'];
        }
      }else{
        if(data.user_id){
          data['user_id']=this.user_info['id'];
        }
        
      }

      let url = apiUrl.cma_arv+this.property_id;
      this.commonApplicationService.put(url, data)
          .subscribe(
              data => {
                this.loading=false;
                this.alertService.success(data.message);  
                if(this.userRole!='admin'){
                  this.cmaArvForm.controls['user_id'].disable();
                  this.disabled_date=true;
                }
              },
              error => {
                this.loading = false;
                this.alertService.common(error);
              }
          ); 
    }

    cma_user(element){
      if(!element.hasAttribute('disabled')){
        let userName=this.user_info['first_name']+' '+this.user_info['last_name'];
        this.cmaArvForm.controls.user_id.setValue(userName);
        this.saveUpdateSingleCmaArv({'name':'user_id','value':this.user_info['id'],'el':element});
      }
    }
    
    daysOnMarket(){
      var data={'cma_arv_type':this.cma_arv_type,'property_id':this.property_id}
      let dialogRef=this.dialog.open(DaysOnMarketComponent,{ width: '800px',data:data,disableClose:true})
      dialogRef.afterClosed().subscribe(result => {
        if(result){
          this.cmaArvForm.get('days_on_market').setValue(result);
         
        }
      })
    } 

    final_check(){
      this.communicationService.publishFinalCheck(true);
    }

    calculatePVal($event,fieldName){
      let proInfo= this.storageService.getHard('property_info');
      $event.target.value=$event.target.value*proInfo.calculate_pvalue;
      this.cmaArvForm.get(fieldName).setValue($event.target.value);
    }

    get isFinalCheck(){
      let finalcheckAccess=['admin','third_dca','chief_dca'];
      if(finalcheckAccess.indexOf(this.userRole) !== -1){
       return true;
      }
      return false;
    }

    

    saveCmaArvInfo(event){
     //   if(this.cma_arv_type=='wholesale_buyer')
          this.saveUpdateSingleCmaArv(event);
    }


    saveUpdateSingleCmaArv(data){
      
      if(data['name']!='date'){
        this.addLoader(data['el']);
        this.removeElement(data['el'],'saved-icon');
      }
      let saveInfo:any={'name':data['name'],'value':data['value']}
      saveInfo['id']=this.cmaArvForm.get('id').value;
      saveInfo['house_id']=this.property_id;
      saveInfo['info_added_by']=this.cma_arv_type;

      let url = apiUrl.cma_arv_single+'/'+this.property_id;
      this.commonApplicationService.put(url, saveInfo)
          .subscribe(
              response => {
                if(response['status']=='failed'){
                  this.removeElement(data['el'],'loader-icon');
                  this.alertService.error(response['message']);
                }else{
                  if(data['name']!='date'){
                    this.addElement(data['el']);
                    this.removeElement(data['el'],'loader-icon');
                  }
                  if(this.userRole!='admin' && saveInfo['name']=='user_id'){
                    this.cmaArvForm.controls['user_id'].disable();
                  }
                  if(this.userRole!='admin' && saveInfo['name']=='date'){
                    this.disabled_date=true;
                  }
                }
                

              },
              error => {
                this.loading = false;
                this.alertService.common(error);
              }
          ); 
    }

    addElement(el) {
      const p: HTMLParagraphElement = this.renderer.createElement('i');
      p.classList.add('saved-icon','fas' ,'fa-sm', 'fa-check');
      this.renderer.appendChild(el.parentElement, p)
    }

    addLoader(el){
      const p: HTMLParagraphElement = this.renderer.createElement('i');
      p.classList.add('loader-icon','fa-spin','fas' ,'fa-sm', 'fa-spinner');
      this.renderer.appendChild(el.parentElement, p)
    }

    removeElement(el,className)
    {
      if(el.parentElement.querySelector('.'+className))
        el.parentElement.querySelector('.'+className).remove();
    }

    sqftCalculation(){
      var data={'cma_arv_type':this.cma_arv_type,'property_id':this.property_id}
      this.dialog.open(SqftCalculationComponent,{width:'600px',data:data});
    }
}
