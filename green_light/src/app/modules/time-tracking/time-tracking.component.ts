import { Component, Inject, Input, OnInit } from '@angular/core';
import { StorageService } from '@shared-service/_services/storage.service';
import { ProjectModel } from './project-model';
import {Idle, DEFAULT_INTERRUPTSOURCES} from '@ng-idle/core';
import {Keepalive} from '@ng-idle/keepalive';
import { apiUrl } from '@config/api-url';
import {MatDialogRef, MAT_DIALOG_DATA} from '@angular/material/dialog';
import { CommonActivityService, CommonApplicationService, CommunicationService } from '@shared-service/_services';
import { Subscription, timer } from 'rxjs';
import { takeUntil, takeWhile } from 'rxjs/operators';

@Component({
  selector: 'app-time-tracking',
  templateUrl: './time-tracking.component.html',
  styleUrls: ['./time-tracking.component.css']
})
export class TimeTrackingComponent implements OnInit {

  @Input() onpage:boolean=false;
  @Input() property_id:number;

  projects: any[] = [];
  projectActive = false;
  timerInterval: any;
  secondsElapsed: number = 0;
  
  project:any;
  projectTracking:any;
  subscription: Subscription;

  idleState = 'Not started.';
  timedOut = false;
  lastPing?: Date = null;
  title = 'angular-idle-timeout';
  endTime:any;
  startBtn:boolean=true;
  stopBtn:boolean=false;

  constructor(private storage:StorageService,
              private idle: Idle, 
              private keepalive: Keepalive,
              public dialogRef: MatDialogRef<TimeTrackingComponent>,
              @Inject(MAT_DIALOG_DATA) public data: any,
              private commonApplicationService:CommonApplicationService,
              private commonActivityService:CommonActivityService,
              private communicationService:CommunicationService) {
      //sets an idle timeout of 5 seconds, for testing purposes.
      idle.setIdle(600);
      // sets a timeout period of 5 seconds. after 10 seconds of inactivity, the user will be considered timed out.
      idle.setTimeout(300);
      // sets the default interrupts, in this case, things like clicks, scrolls, touches to the document
      idle.setInterrupts(DEFAULT_INTERRUPTSOURCES);
              
      
      
   }

  reset() {
    this.idle.watch();
    this.idleState = 'Started.';
    this.timedOut = false;
  }




  ngOnInit(): void {
   //this.property_id=this.data.property_id;
   //this.startTiming(false);
   
   this.project=this.storage.getHard('permatimerProjects');
   if(this.project && this.project.active){
     this.startAcWork();
   }else{
    this.project = new ProjectModel(this.property_id, new Date(),0, false);
   }
   this.idleTimeInfo();
  }

  idleTimeInfo(){
    this.idle.onIdleEnd.subscribe(() => {
      this.idleState = 'No longer idle.';
      console.log(this.idleState);
    });
    
    this.idle.onTimeout.subscribe((val) => {
      this.idleState = 'Timed out!';
      this.timedOut = true;
      this.stopTiming();
      console.log(val);
    });

    this.idle.onIdleStart.subscribe(() => {
      this.idleState = 'You\'ve gone idle!';
      this.endTime=new Date();
      console.log(this.idleState);
    });
    this.idle.onTimeoutWarning.subscribe((countdown) => this.idleState = 'You will time out in ' + countdown + ' seconds!');

    // sets the ping interval to 15 seconds
    // this.keepalive.interval(15);

    // this.keepalive.onPing.subscribe(() => {
    //   this.lastPing = new Date();
    //   console.log(this.lastPing);
    // });
  }

  idleTimeUnsub(){
    this.idle.onIdleEnd.unsubscribe();
    this.idle.onTimeout.unsubscribe();
    this.idle.onIdleStart.unsubscribe();
    this.idle.onTimeoutWarning.unsubscribe();
  }


  startAcWork(){
    
    this.startBtn=false;
    this.stopBtn=true;
    this.loadTime();
    this.reset();
    // const source = timer(1000,1000);
    // //if(this.subscription) this.subscription.unsubscribe();
    // this.subscription = source.subscribe(val => {
    //   console.log(val);
    //   this.acTrackingStart();
    // });

    this.timerInterval = setInterval((val) => {
      //console.log(val);
      this.acTrackingStart();
    }, 1000);
   
  }

  acTrackingStart(){
    if(this.project.active){
      let now = new Date();
      let timeDifference = now.getTime() - new Date(this.project.lastChecked).getTime();
      let seconds = timeDifference / 1000;
      this.secondsElapsed += seconds;
      this.project.addToTotalSeconds(seconds);
      this.project.setLastChecked(now);
      this.save(this.project,this.secondsElapsed);
    }else{
     // this.subscription.unsubscribe();
    }
  }


  newProject(){
    return new ProjectModel(this.property_id, new Date(), 0, false);
  }


  loadTime(): void {
    //let project=this.projectTracking;
    if(this.project.active){
      this.project = new ProjectModel(this.project.name, this.project.lastChecked,this.project.totalSeconds, this.project.active);
      //this.projects.push(this.project);
    }else{
      this.project = new ProjectModel(this.property_id, new Date(),0, true);
    }
    if(this.storage.getHard('permatimerTime')){
        this.secondsElapsed = this.storage.getHard('permatimerTime');
    };

  }

  save(project,seconds): void {
    this.storage.setHard('permatimerProjects', project);
    this.storage.setHard('permatimerTime', seconds);
  }

  stopTiming() {

    this.projectActive = false;
    this.project.setIsActive(false);
    this.timerInterval = false;
    this.secondsElapsed = 0;
    this.savetrackingTime();
    this.save(this.newProject(),0);
    this.idle.stop();
    clearInterval();
    this.startBtn=true;
    this.stopBtn=false;
  }

  ngOnDestroy() {
    console.log('timer destroyd');
    //this.subscription.unsubscribe();
  }

  

  savetrackingTime(){

    let startTime=new Date().getTime()-this.project.totalSeconds*1000;
    let endTime=this.endTime?this.endTime.getTime():new Date().getTime();
    let totalTime=endTime-new Date(startTime).getTime();

    let data={'working_date':this.getDateTime(new Date()),
            'start_time':new Date(startTime).getTime(),
            'end_time':endTime,
            'total_time':totalTime};
    let url = apiUrl.acTimeTracking+'/'+this.property_id;
    this.commonApplicationService.post(url, data)
    .subscribe(
      data => {
        if(data.status=='success'){
          this.communicationService.setAcTimeTracking(data.data);
        }
        //this.loading = false;
      },
      error => {
      // this.loading = false;
      // this.alertService.common(error); 
      }
    ); 
  }

   getDateTime(date){
    var dt = date;
    let dayStr = (dt.getDate() <=9)? "0"+dt.getDate() : dt.getDate();
    let month = dt.getMonth()+1;
    let monStr = (month <=9)? "0"+month : month;
    return dt.getFullYear()+"-"+monStr+"-"+dayStr;
  }

  

  

}
