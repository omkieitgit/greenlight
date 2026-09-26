import { Component, Injectable, OnDestroy, OnInit, Renderer2,ViewChild,ElementRef} from '@angular/core';
import { NgbDateAdapter, NgbDateStruct } from '@ng-bootstrap/ng-bootstrap';
import { FormControl, NgForm, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { first } from 'rxjs/operators';
import { CommonApplicationService,CommonActivityService,AlertService } from '../../../shared/_services';
import { apiUrl } from '../../../config/api-url';
import { Router,ActivatedRoute } from '@angular/router';
import { StorageService } from '../../../shared/_services/storage.service';
import {TermsDefinitionComponent} from '../terms-definition/terms-definition.component';
import {MatDialog, MatDialogRef, MAT_DIALOG_DATA} from '@angular/material/dialog';
//import {WholesaleNotesComponent} from '../wholesale-notes/wholesale-notes.component';
import {DataComponent} from './data/data.component';
import { DeviceDetectorService } from 'ngx-device-detector';
import {leinObj,compsObj,compTypeObj,hoaLienObj,taxLienObj,otherLienObj,urlObj,compsNameObj} from './quickview-data';

import {SlugifyPipe} from '../../../modules/directives/slugify.pipe';

import * as jspdf from 'jspdf';  
import html2canvas from 'html2canvas';
import { Subject } from "rxjs";

// for ngb datepicker adapter
@Injectable()
export class NgbDateNativeAdapter extends NgbDateAdapter<Date> {

  fromModel(date: Date): NgbDateStruct {
    return (date && date.getFullYear) ? {year: date.getFullYear(), month: date.getMonth() + 1, day: date.getDate()} : null;
  }

  toModel(date: NgbDateStruct): Date {
    return date ? new Date(date.year, date.month - 1, date.day) : null;
  }
}

@Component({
 selector: 'quick-view',
 templateUrl: './quick-view-private.html',
 providers: [{provide: NgbDateAdapter, useClass: NgbDateNativeAdapter}]
 })


export class QuickViewComponent implements OnInit{

  public isCollapsed = true;
  quickViewForm: FormGroup; 
  property_id:string;
  result:any;  
  propErr: boolean = false;
  propErrMsg: any = "";  
  loanType: any; 
  property_config: any;
  isFavourite:boolean=false;
  loader:boolean=false;
  loanTypeList:any;
  wholeSaleNote:any;
  map_data:any;
  device:string;
  address:any;
  notes:string='notes';
  notes_btn:any={'note_name':'buyer','noteBtn':true,'noteDetail':false};
  notes_detail:any={'note_name':'buyer','noteBtn':false,'noteDetail':true};
  resetFormSubject: Subject<boolean> = new Subject<boolean>();
  


  lien_obj:any;
  comp_obj:any;
  comp_type:any;
  hoalien_obj:any;
  otherlien_obj:any;
  taxlien_obj:any;
  urlObj:any;
  compsName:any;

  checkBoxField=[
    'lien_foreclosing',
    'no_str_no_appt',
    'defective_lien',
  ]


  @ViewChild('content') content: ElementRef;

  constructor(private formBuilder:FormBuilder, 
              private commonActivityService:CommonActivityService,
              private commonApplicationService:CommonApplicationService,
              private alertService:AlertService,
              private router: Router,
              private route: ActivatedRoute,
              private storageService:StorageService,
              private dialog:MatDialog,
              private deviceService: DeviceDetectorService,
              private slugPipe:SlugifyPipe) { }


  ngOnInit() {
    this.property_config =  this.storageService.get("property_config");
    this.property_id = this.route.snapshot.paramMap.get('property_id');
    // Load info details
    if(this.property_id !== undefined){
      this.getQuickViewDetails();     
      this.device=this.deviceService.device;
    }
    this.lien_obj=leinObj;
    this.comp_obj=compsObj;
    this.comp_type=compTypeObj;
    this.hoalien_obj=hoaLienObj;
    this.taxlien_obj=taxLienObj;
    this.otherlien_obj=otherLienObj;
    this.urlObj=urlObj;
    this.compsName=compsNameObj;
    
  }

  asIsOrder(a, b) {
    return 1;
  }

  updatedNotes(event){
    if(event){
      this.resetFormSubject.next(true);
    }
  }
  
  /*----------------------------- Get property Info Details --------------------------------*/
  getQuickViewDetails(){
      this.loader=true;
      let url = apiUrl.private_quick_view+this.property_id;
      this.commonApplicationService.get(url).subscribe(response => {
        if(response.status=='failed'){
          this.storageService.setHard('isNotExist',true);
          this.router.navigate(['home/dashboard']);  
        }
        this.result = response.data;
        if(this.result !== undefined){
         
          this.property_id=this.result.house_id;
          this.loanTypeList = this.property_config.loan_type;
          this.isFavourite=this.result.isFavourite;   
          this.address=this.result.address+' '+this.result.city+' '+this.result.state+' '+this.result.zip;   

          this.map_data = {
            data: this.address,
            lat: this.result.geo?this.result.geo.latitude:'',
            lng: this.result.geo?this.result.geo.longitude:'',
        };


          this.address=encodeURI(this.address);
          this.loader=false;
        }
      },
      (err: any) => {
        this.propErrMsg = "Error occured, Please try again later!";
        this.propErr = true;
      })
  }

  getData(key:string){
    return (key != null && key != "" && key) ? key : "Not available";
  }

  getLienData(lien,key){
      if(lien != null && lien != "" && lien){
        let keyName='';
        if(key=='loan_type'){
          keyName=this.loanTypeList[lien[key]]
        }
        else if(this.checkBoxField.indexOf(key)!==-1){
          keyName=lien[key]==1?'Yes':'No';
        }
        else if(key=='sub_lien_position'){
          keyName=lien[key]==1?'Superior Lien':lien[key]==2?'Infirior Lien':'';
        }
        else{
          keyName=lien[key];
        }
       
        return (key != null && key != "") ? (keyName != null && keyName != "")? keyName: "Not available": "Not available";
      }
      else{
        return "Not available";
      }
    
  }

  login(){
    this.router.navigate(['/login']);
  }

  terms_definition(){
    this.dialog.open(TermsDefinitionComponent,{ width: '800px',disableClose:true} );

  }


  data_info(){
    let propertyInfo=this.result;
    
    var l1='';
    if(propertyInfo.first_liens){
       l1=propertyInfo.first_liens.amortization_loan_estimate_balance+propertyInfo.first_liens.est_late_payment_and_fees;
    }
    var l2='';
    if(propertyInfo.second_liens){
      l2=propertyInfo.second_liens.amortization_loan_estimate_balance+propertyInfo.second_liens.est_late_payment_and_fees;
    }

    var data={'address':propertyInfo.address+' '+propertyInfo.city+' '+propertyInfo.state+' '+propertyInfo.zip,
              'county':propertyInfo.county,
              'cma_arv':propertyInfo.last_cma_arv_recommendations?propertyInfo.last_cma_arv_recommendations.recommended_cma_arv:0,
              'rental_rate':propertyInfo.last_cma_arv_recommendations?propertyInfo.last_cma_arv_recommendations.rental_rate:0,
              'est_equity':propertyInfo.first_liens.est_equity,'l1':l1,'l2':l2,
              'monthly_principal_payment':propertyInfo.first_liens.amortization_monthly_principal_payment,
              'owner_name':propertyInfo.last_owner_info?propertyInfo.last_owner_info.full_name:'',
              'whole_sale_note':this.wholeSaleNote};
    
    
    this.dialog.open(DataComponent,{ width: '600px',data:data,disableClose:true} );
  }

  public captureScreen() {
    const data = document.getElementById('contentToConvert');
    html2canvas(data).then(canvas => {
      const imgWidth = 208;
      const pageHeight = 295;
      const imgHeight = canvas.height * imgWidth / canvas.width;
      const heightLeft = imgHeight;
      const contentDataURL = canvas.toDataURL('image/png');
      const pdf = new jspdf('p', 'mm', 'a4'); 
      const position = 0;
      pdf.addImage(contentDataURL, 'PNG', 0, position, imgWidth, imgHeight);
      pdf.save('invoice.pdf'); 
    });
  }



  

  getCompResult(compResult,comp){
    return compResult.filter(x => x.info_added_by == comp)[0];
  }

  propertyDetail(){
    this.router.navigateByUrl('/home/showdetail/'+this.result.house_id+'/'+this.slugPipe.transform(this.result.address));
  }

  printPropertyInfo(divName) {
    var printContents = document.getElementById(divName).innerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
  }

 
  urlInfo(value){
    if(this.urlObj.indexOf(value) === -1){
      return true;
    }else{
      return false;
    }
  }
  
}