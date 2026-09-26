import { DatePipe } from '@angular/common';
import { Component, EventEmitter, Input, OnInit, Output } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { MatDialog } from '@angular/material/dialog';
import { apiUrl } from '@config/api-url';
import { AlertService, CommonActivityService, CommonApplicationService } from '@shared-service/_services';
import { IMyDateModel, IMyDpOptions } from 'mydatepicker';
import { ClientW9Component } from '../../client-w9/client-w9.component';
import { TradesmanTrackingModel,TimesObj } from './tradesman-tracking.model';
import { TrandesmanUserComponent } from './trandesman-user/trandesman-user.component';
@Component({
  selector: 'app-tradesman-tracking',
  templateUrl: './tradesman-tracking.component.html',
  styleUrls: ['./tradesman-tracking.component.css']
})
export class TradesmanTrackingComponent implements OnInit {

  tradesmanTrackingForm:FormGroup;
  submitted: boolean;
  openPanel: boolean;
  tradesmanTracking:TradesmanTrackingModel[];
  @Input() property_id: any;
  @Output() updatetracking= new EventEmitter<boolean>();
  timeObject:any;
  loading: boolean;
  todayDate:string;
  hours=new Array(24);
  minitus=new Array(60);
  afternoonTotalTime:any=0;
  morningTotalTime:any=0;
  tradesmanUsers:TradesmanUserModel[];
  tradesmanSummery:any;
  user:any;

  constructor(private formBuilder:FormBuilder,
              private commonActivityService:CommonActivityService,
              private commonApplicationService:CommonApplicationService,
              private alertService:AlertService,
              private datePipe:DatePipe,
              private dialog:MatDialog) { }

  ngOnInit(): void {
    this.timeObject=TimesObj;
    this.getTodateDate();
    this.tradesmanTrackingForm = this.formBuilder.group({ 
      id:[],
      name:['',[Validators.required]],
      working_date:['',[Validators.required]],
      arrival_time:['',[Validators.required]],
      morning_time_in:['0'],
      morning_time_out:['0'],
      afternoon_time_in:['0'],
      afternoon_time_out:['0'],
      morning_time_min_in:['0'],
      morning_time_min_out:['0'],
      afternoon_time_min_in:['0'],
      afternoon_time_min_out:['0'],
      total_work_hours:[''],
      description:[''],
      remarks:[],
      ssn_number:[],
    });
  }
  public myDatePickerOptions: IMyDpOptions = { dateFormat: 'mm/dd/yyyy',firstDayOfWeek : 'su'};
  onDateChanged(event: IMyDateModel) {
      return event.formatted;
  }
  validateForm(data){
    let result = this.commonActivityService.getFullFormDataWithDateFormatted(data);
    this.submitted = true;
    console.log('niform');
    if(this.tradesmanTrackingForm.invalid) { 
      return;
    }    
    // FORM SUBMITTED
    console.log('form submitted');
    this.saveUpdateTradesmanTracking(result);  
  }

  panelExpand(flag){
    if(!this.openPanel && this.property_id !== undefined){
      this.openPanel = true;
      this.getTradesmanTracking();
    }
  }

  getTradesmanTracking(){
    this.loading=true;
    let url = apiUrl.tradesmanTracking+'/'+this.property_id;
    this.commonApplicationService.get(url).subscribe(response => {
      if(response['row']){
        this.tradesmanTracking = response['row']['tradesman_tracking'];
        this.tradesmanUsers = response['row']['tradesman_users'];
        this.tradesmanSummery = response['row']['tradesman_summery'];

      }
      this.loading = false;
    },
    (err: any) => {
      this.loading = false;
    })
  }

  get f() { return this.tradesmanTrackingForm.controls; }

  
  uploadHandler(event){
    let elem = event.target; 
    if(elem.files.length > 0){
    }
  }

  mergeHoursMins(data){
    data['morning_time_in']=data['morning_time_in']+':'+data['morning_time_min_in'];
    data['morning_time_out']=data['morning_time_out']+':'+data['morning_time_min_out'];
    data['afternoon_time_in']=data['afternoon_time_in']+':'+data['afternoon_time_min_in'];
    data['afternoon_time_out']=data['afternoon_time_out']+':'+data['afternoon_time_min_out'];
    data['name']=data['name']?.id?data['name']?.id:data['name'];
    return data;
  }

  saveUpdateTradesmanTracking(data:any){
    this.loading = true;
    data=this.mergeHoursMins(data);
    let url = apiUrl.tradesmanTracking+'/'+this.property_id;
    this.commonApplicationService.post(url, data)
        .subscribe(
            data => {
              if(data.status=='success'){
                
                let tradesman_tracking=data?.data?.tradesman_tracking;
                let tradesman_summery=data?.data?.tradesman_summery[0];
             
                let itemIndex = this.tradesmanTracking.findIndex(item => item.id == tradesman_tracking.id);
                if(itemIndex >= 0){
                  this.tradesmanTracking[itemIndex] =tradesman_tracking;
                }else{
                  this.tradesmanTracking.push(tradesman_tracking);
                }

                let sumIndex = this.tradesmanSummery.findIndex(item => item.name == tradesman_summery.name);
                if(sumIndex >= 0){
                  this.tradesmanSummery[sumIndex] = tradesman_summery;
                }else{
                  this.tradesmanSummery.push(tradesman_summery);
                }
                let userIndex=this.tradesmanUsers.findIndex(item => item.id == tradesman_tracking.user.id);
                if(userIndex ==-1){
                  this.tradesmanUsers.push(tradesman_tracking.user);
                }

                this.morningTotalTime=0;
                this.afternoonTotalTime=0;
                this.alertService.success(data.message);  
               // this.updatetracking.emit(true);
                this.tradesmanTrackingForm.reset();
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

  removeTradesmanTracking(id){

      if(confirm("Are you sure want to delete record ?")){
        let url = apiUrl.tradesmanTracking+'/'+id;
        this.commonApplicationService.delete(url).subscribe(response => {
          if(response.status=='success'){
            this.tradesmanTracking = this.tradesmanTracking.filter(item => item.id !== id);
            this.updatetracking.emit(true);
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

  editTradesmanTracking(tracking){
    
    this.tradesmanTrackingForm.get('id').setValue(tracking.id);
    this.tradesmanTrackingForm.get('name').setValue(tracking.user);
    this.tradesmanTrackingForm.get('working_date').setValue(tracking.working_date?{jsdate: new Date(tracking.working_date)}:null);
    this.tradesmanTrackingForm.get('arrival_time').setValue(tracking.arrival_time);
    this.tradesmanTrackingForm.get('morning_time_in').setValue(this.splitTime(tracking.morning_time_in,0));
    this.tradesmanTrackingForm.get('morning_time_out').setValue(this.splitTime(tracking.morning_time_out,0));
    this.tradesmanTrackingForm.get('afternoon_time_in').setValue(this.splitTime(tracking.afternoon_time_in,0));
    this.tradesmanTrackingForm.get('afternoon_time_out').setValue(this.splitTime(tracking.afternoon_time_out,0));
    this.tradesmanTrackingForm.get('total_work_hours').setValue(tracking.total_work_hours);
    this.tradesmanTrackingForm.get('description').setValue(tracking.description);
    this.tradesmanTrackingForm.get('remarks').setValue(tracking.remarks);
    this.tradesmanTrackingForm.get('morning_time_min_in').setValue(this.splitTime(tracking.morning_time_in,1));
    this.tradesmanTrackingForm.get('morning_time_min_out').setValue(this.splitTime(tracking.morning_time_out,1));
    this.tradesmanTrackingForm.get('afternoon_time_min_in').setValue(this.splitTime(tracking.afternoon_time_in,1));
    this.tradesmanTrackingForm.get('afternoon_time_min_out').setValue(this.splitTime(tracking.afternoon_time_out,1));
    this.tradesmanTrackingForm.get('ssn_number').setValue(tracking?.user?.w_info?.social_security_number)

    this.getMorningDiffTime();
    this.getDiffTime();
    this.user=tracking?.user;
  }

  splitTime(time,index){
    return time.split(":")[index];
  }

  getTodateDate(){
    let todayDate=new Date();
    let today_date:string=todayDate.getFullYear()+'-'+todayDate.getMonth()+'-'+todayDate.getDate();
    this.todayDate=today_date;
  }

  getDiffTime(){
    
    //create date format
    this.afternoonTotalTime=0;
    let inHour=this.tradesmanTrackingForm.get('afternoon_time_in').value;
    let inMins=this.tradesmanTrackingForm.get('afternoon_time_min_in').value;
    let outHour=this.tradesmanTrackingForm.get('afternoon_time_out').value;
    let OutMin=this.tradesmanTrackingForm.get('afternoon_time_min_out').value;
    
    if((inHour || inMins) && (outHour || OutMin)){
      let timeStart:any = new Date(this.todayDate+" " + inHour+':'+inMins);
      let  timeEnd:any = new Date(this.todayDate+" " + outHour+':'+OutMin);
      this.afternoonTotalTime= timeEnd - timeStart;          
    }
    let totalWorkHour=parseInt(this.morningTotalTime)+this.afternoonTotalTime;
    let totalTimeHours=this.msToTime(totalWorkHour);
    this.tradesmanTrackingForm.get('total_work_hours').setValue(totalTimeHours);
               
  }

  getMorningDiffTime(){
    
    //create date format
    this.morningTotalTime=0;
    let inHour=this.tradesmanTrackingForm.get('morning_time_in').value;
    let inMins=this.tradesmanTrackingForm.get('morning_time_min_in').value;
    let outHour=this.tradesmanTrackingForm.get('morning_time_out').value;
    let OutMin=this.tradesmanTrackingForm.get('morning_time_min_out').value;
    
    if(((inHour && outHour !='0') || inMins) && ((outHour && inHour!='0') || OutMin)){
      let timeStart:any = new Date(this.todayDate+" " + inHour+':'+inMins);
      let  timeEnd:any = new Date(this.todayDate+" " + outHour+':'+OutMin);
      this.morningTotalTime = timeEnd - timeStart;          
    }
     let totalWorkHour=parseInt(this.morningTotalTime)+this.afternoonTotalTime;
     let totalTimeHours=this.msToTime(totalWorkHour);
     this.tradesmanTrackingForm.get('total_work_hours').setValue(totalTimeHours);
               
  }


   msToTime(duration) {
    let minutes:string|number = Math.floor((duration / (1000 * 60)) % 60);
    let hours:string|number = Math.floor((duration / (1000 * 60 * 60)) % 24);
    hours = (hours < 10) ? "0" + hours : hours;
    minutes = (minutes < 10) ? "0" + minutes : minutes;
    return hours + ":" + minutes;
  }
  displayFn(tradesman?: any): string | undefined {
    return tradesman ? tradesman.username : tradesman;
  }
  selectedUser(){
    this.user=this.tradesmanTrackingForm.get('name').value;
    this.tradesmanTrackingForm.get('ssn_number').setValue(this.user?.w_info?.social_security_number)
  }

  addW9Form(){
      let data={property_id:this.property_id,user:this.user};
      let dialogRef=this.dialog.open(ClientW9Component,{width:'800px',data:data});
      dialogRef.afterClosed().subscribe(result => {
        if(result){
          this.user=result;
          this.tradesmanTrackingForm.get('ssn_number').setValue(this.user?.w_info?.social_security_number)
        }
         
      });
  }

  viewW9Form(){
    let url = apiUrl.tradesmanW9;
    let options = {
      headers: { "Content-Type": "application/json", Accept: "application/pdf" },
      responseType: "blob"
    };
    this.commonApplicationService.postDownload(url,{user_id:this.user.id}, options).subscribe(response => {
        const fileURL = URL.createObjectURL(response);
        window.open(fileURL, '_blank');
    },
    (err: any) => {
      this.alertService.common(err);
    })
  }

  addTradsman(){
    let dialogRef=this.dialog.open(TrandesmanUserComponent,{data:{'tradesmanUserList':this.tradesmanUsers},width:'500px'});
    dialogRef.afterClosed().subscribe(result => {
      if(result)
        this.tradesmanUsers=result;
    });
  }

  sendEmail(){
    let url = apiUrl.sendW9Email+'/'+this.property_id;
    this.commonApplicationService.post(url,{user_id:this.user.id}).subscribe(response => {
      if(response){
        this.alertService.success(response.message);
      }
    },(err: any) => {
      this.alertService.common(err);
    });
  }

}


export class TradesmanUserModel{
  id:number;
  username:string|number;
}
