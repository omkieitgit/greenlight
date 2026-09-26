import { Component, OnInit,Inject,ElementRef,ViewChild } from '@angular/core';
import {MatDialog, MatDialogRef, MAT_DIALOG_DATA} from '@angular/material/dialog';
import { FormGroup,FormBuilder,FormArray} from '@angular/forms';
import {IMyDpOptions, IMyDateModel,IMyInputFieldChanged} from 'mydatepicker';
import { DOCUMENT } from '@angular/common'; 
import { apiUrl } from '../../../../../../config/api-url';
import { CommonApplicationService, CommonActivityService, AlertService } from '../../../../../../shared/_services';
import { json } from 'd3';
import { CommonHelper } from '../../../../../../shared/_utils/CommonHelper';

@Component({
  selector: 'app-days-on-market',
  templateUrl: './days-on-market.component.html',
  styleUrls: ['./days-on-market.component.css']
})
export class DaysOnMarketComponent implements OnInit {

  property_id:number;
  cma_type:string;
  dayOnMarketForm:FormGroup;
  totalDuration:any=0;
  loading:boolean=false;
  showForm:boolean=false;
  adom_result:any;


  constructor( @Inject(MAT_DIALOG_DATA) public data,
              private formBuilder:FormBuilder,
              public dialogRef: MatDialogRef<DaysOnMarketComponent>,
              @Inject(DOCUMENT) private document: any,
              private commonApplicationService:CommonApplicationService,
              private commonActivityService:CommonActivityService,
              private alertService:AlertService) { }

  ngOnInit() {

    this.property_id=this.data.property_id;
    this.cma_type=this.data.cma_arv_type;
    if(this.property_id !== undefined){
      this.initalize();
      this.getCmaArvInfo();
    }
   
  }

  ngAfterContentInit(){
   // this.getCmaArvInfo();
  }

  public myDatePickerOptions: IMyDpOptions = { dateFormat: 'mm/dd/yyyy',firstDayOfWeek : 'su'};
 
  
  onInputFieldChanged(event: IMyInputFieldChanged) {
    
    let total_duration:number=0;
    let count=0;
      for(var i=0; i< this.dayOnMarketForm.controls.dom_info_data["controls"].length; i++){
        var daysOnMarket=this.dayOnMarketForm.controls.dom_info_data["controls"][i];
        
        var enter_listed_date= daysOnMarket.controls.enter_listed_date.value.formatted;
        var enter_sold_date=daysOnMarket.controls.enter_sold_date.value.formatted;

        if(enter_listed_date && enter_sold_date){
          var days = this.datediff(this.parseDate(enter_listed_date),this.parseDate(enter_sold_date));
          
          if(isNaN(days)==true)
            days=0;
                  
          total_duration= days+total_duration;
          
          let totalCalDay=this.document.querySelector('.cal_days_'+i);
          if(totalCalDay){
            totalCalDay.innerHTML=days;   
          }
          count++;  
             
        }
        
      }

      if(total_duration){
        this.totalDuration=(total_duration/count).toFixed(2);
        this.document.getElementsByClassName('total_avg')[0].innerHTML=this.totalDuration; 
      }
   }

  onDateChanged(event: IMyDateModel) {
    if(event){
      return event.formatted;
    }
  }
  
  getCmaArvInfo(){
    this.loading = true;
    let url = apiUrl.cma_arv_adom+'/'+this.property_id;
    this.commonApplicationService.get(url).subscribe(response =>{
      this.loading = false;
      this.showForm=true;
      if(response['data'].length>0){
        this.adom_result = response['data'];
        this.adom_result=this.adom_result.filter(adom_type=>(adom_type.type==this.cma_type));
        this.populateDataInAdom();
      }
    })
  }


  initalize(){
    this.dayOnMarketForm = this.formBuilder.group({
      dom_info_data: this.formBuilder.array([
        this.formBuilder.group({
          enter_listed_date: [''],
          enter_sold_date: [''],
          
        })
      ]),
      type:[] 
    });
  }

  addMoreField() : void
  {
   const control = <FormArray>this.dayOnMarketForm.controls.dom_info_data;
   control.push(this.formBuilder.group({
      enter_listed_date: [''],
      enter_sold_date: ['']
    }));
  }

  populateDataInAdom(){
    let adom_data=JSON.parse(this.adom_result[0].json);
    if(adom_data.length){
        if(adom_data.length == 1){
             this.fillAdomForm(0,adom_data);
        }else{
            for(var i=0; i<adom_data.length; i++ ){
              this.addMoreField(); 
              this.fillAdomForm(i,adom_data);
            }
        }
    }     
  }

  fillAdomForm(index:number,adom_data){
    let adomInfo=this.dayOnMarketForm.get('dom_info_data')['controls'];
    adomInfo[index].controls.enter_listed_date.setValue(CommonHelper.dateFormate(adom_data[index].enter_listed_date));
    adomInfo[index].controls.enter_sold_date.setValue(CommonHelper.dateFormate(adom_data[index].enter_sold_date));
   
  }

  datediff(first, second) {
    // Take the difference between the dates and divide by milliseconds per day.
    // Round to nearest whole number to deal with DST.
    return Math.round((second-first)/(1000*60*60*24));
  }

  parseDate(str) {
    if(str){
      var mdy = str.split('/');
      return new Date(mdy[2], mdy[0]-1, mdy[1]);
    }
  }
  
  saveInfo(){

    let adomForm=this.dayOnMarketForm.get('dom_info_data')['controls'];

    let adom_date=[];
    for(let i=0; i<adomForm.length; i++){
      let adom = this.commonActivityService.getFullFormDataWithDateFormatted(adomForm[i]);
      if(adom['enter_listed_date'] && adom['enter_sold_date'])
        adom_date.push(adom);
    }
    let input = new FormData();
    input.append("type", this.cma_type);
    input.append("json", JSON.stringify(adom_date));

    let url = apiUrl.cma_arv_adom+'/'+this.property_id;
    this.commonApplicationService.post(url, input)
    .subscribe(
        response => {
          this.alertService.success(response.message);  
          this.loading = false;
          this.dialogRef.close(this.totalDuration);
        },
        error => {
          this.loading = false;
          this.alertService.common(error);  
        }
    ); 
    
  }

  
}
