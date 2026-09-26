import { DatePipe } from '@angular/common';
import { Component, EventEmitter, Input, OnInit, Output } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { AlertService, apiUrl, CommonActivityService, CommonApplicationService, CommunicationService } from '@shared-service/_services';
import { IMyDateModel, IMyDpOptions } from 'mydatepicker';

@Component({
  selector: 'app-accounting-tracking',
  templateUrl: './accounting-tracking.component.html',
  styleUrls: ['./accounting-tracking.component.css']
})
export class AccountingTrackingComponent implements OnInit {

  accountingTrackingForm:FormGroup;
  submitted: boolean;
  openPanel: boolean;
  accountingTracking:accountingTrackingModel[];
  @Input() property_id: any;
  loading: boolean;
  todayDate:string;
  accountingSummery:any;

  constructor(private formBuilder:FormBuilder,
              private commonActivityService:CommonActivityService,
              private commonApplicationService:CommonApplicationService,
              private alertService:AlertService,
              private communicationService:CommunicationService) { }

  ngOnInit(): void {
    
    this.accountingTrackingForm = this.formBuilder.group({ 
      id:[],
      working_date:['',[Validators.required]],
      start_time:['',[Validators.required]],
      end_time:[],
      total_time:[]
    });

    this.communicationService.getAcTimeTracking().subscribe(response=>{
      if(response){
        let itemIndex = this.accountingTracking.findIndex(item => item.id == response.id);
        if(itemIndex == -1){
          this.getaccountingTracking();
          // let totalTime=response.end_time-response.start_time;
          // response.start_time=this.getTime(response.start_time);
          // response.end_time=this.getTime(response.end_time);
          // response.total_time=this.getTotalTime(totalTime);
          // this.accountingTracking.push(response);

          //console.log(this.accountingSummery);
        }
      }
    })
  }

  public myDatePickerOptions: IMyDpOptions = { dateFormat: 'mm/dd/yyyy',firstDayOfWeek : 'su'};
  onDateChanged(event: IMyDateModel) {
      return event.formatted;
  }
  validateForm(data){
    let result = this.commonActivityService.getFullFormDataWithDateFormatted(data);
    this.submitted = true;
    if(this.accountingTrackingForm.invalid) { 
      return;
    }    
    // FORM SUBMITTED
    console.log('form submitted');
    this.saveUpdateaccountingTracking(result);  
  }

  panelExpand(flag){
    if(!this.openPanel && this.property_id !== undefined){
      this.openPanel = true;
      this.getaccountingTracking();
    }
  }

  getaccountingTracking(){
    this.loading=true;
    let url = apiUrl.acTimeTracking+'/'+this.property_id;
    this.commonApplicationService.get(url).subscribe(response => {
      if(response['row']){
        this.accountingTracking = response['row']['time_tracking_list'];
        if(this.accountingTracking){
          this.accountingTracking.forEach(element => {

            let totalTime=element.end_time-element.start_time;
            element.start_time=this.getTime(element.start_time);
            element.end_time=this.getTime(element.end_time);
            element.total_time=this.getTotalTime(totalTime);
          });
        }
        this.accountingSummery = response['row']['time_tracking_summery'];
        if(this.accountingSummery){
          this.accountingSummery.forEach(element1 => {
            element1.total_time=this.getTotalTime(element1.total_time);
          });
        }
      }
      this.loading = false;
    },
    (err: any) => {
      this.loading = false;
    })
  }

  get f() { return this.accountingTrackingForm.controls; }


  saveUpdateaccountingTracking(data:any){
    this.loading = true;

    data['start_time']=new Date(data['working_date']+' '+data['start_time']).getTime();
    data['end_time']=new Date(data['working_date']+' '+data['end_time']).getTime();
    data['total_time']=data['end_time']-data['start_time'];
    let url = apiUrl.acTimeTracking+'/'+this.property_id;
    this.commonApplicationService.post(url, data)
        .subscribe(
            data => {
              if(data.status=='success'){
                let totalTime=data.data.end_time-data.data.start_time;
                data.data.start_time=this.getTime(data.data.start_time);
                data.data.end_time=this.getTime(data.data.end_time);
                data.data.total_time=this.getTotalTime(totalTime);

                let itemIndex = this.accountingTracking.findIndex(item => item.id == data.data.id);
                if(itemIndex >= 0){
                  this.accountingTracking[itemIndex] = data.data;
                }else{
                  this.accountingTracking.push(data.data);
                }
                this.alertService.success(data.message);  
                this.accountingTrackingForm.reset();
                this.submitted=false;
              }else{
                this.alertService.error(data.message); 
              }
              this.loading = false;
            },
            error => {
              this.loading = false;
              this.alertService.common(error); 
            }
        ); 
  }

  removeaccountingTracking(id){

      if(confirm("Are you sure want to delete record ?")){
        let url = apiUrl.acTimeTracking+'/'+id;
        this.commonApplicationService.delete(url).subscribe(response => {
          if(response.status=='success'){
            this.accountingTracking = this.accountingTracking.filter(item => item.id !== id);
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

  editaccountingTracking(tracking){
    
    this.accountingTrackingForm.get('id').setValue(tracking.id);
    this.accountingTrackingForm.get('working_date').setValue(tracking.working_date?{jsdate: new Date(tracking.working_date)}:null);
    this.accountingTrackingForm.get('start_time').setValue(tracking.start_time);
    this.accountingTrackingForm.get('end_time').setValue(tracking.end_time);
    this.accountingTrackingForm.get('total_time').setValue(tracking.total_time);
    
  }

  getDiffTime(working_date,startTime,endTime){

    let start_time=new Date(working_date+' '+startTime).getTime();
    let end_time=new Date(working_date+' '+endTime).getTime();
    let totalTime=end_time-start_time;
    this.accountingTrackingForm.get('total_time').setValue(this.getTotalTime(totalTime));
  }

  getTime(value) {

    let calTime='';
    if(value){
      let datTime=new Date(parseInt(value));
      let minutes = datTime.getMinutes(); //Math.floor(value / 60);
      let hours = datTime.getHours();//Math.floor(minutes / 60);
      let seconds = datTime.getSeconds();//Math.floor(value % 60);

      calTime= hours + ":" + minutes + ":" + seconds;
    }
    return calTime;

  }

  getTotalTime(value) {

    let calTime='';
    if(value){
      value=value/1000;
      let hours = Math.floor(value / 3600);
      let minutes = Math.floor((value % 3600)/ 60);
      let seconds = Math.floor(value % 60);
      calTime= (hours>9?hours:'0'+hours) + ":" + (minutes>9?minutes:'0'+minutes)  + ":" + (seconds>9?seconds:'0'+seconds) ;
    }
    return calTime;

  }
}


export class accountingTrackingModel{
  id:number;
  name:string;
  working_date:string;
  start_time:any;
  end_time:any;
  total_time:string;
}