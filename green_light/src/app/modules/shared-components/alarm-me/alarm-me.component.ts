import { Component, Inject, OnInit } from '@angular/core';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material/dialog';
import { apiUrl } from '@config/api-url';
import { AlertService, CommonApplicationService } from '@shared-service/_services';
import { StorageService } from '@shared-service/_services/storage.service';

@Component({
  selector: 'app-alarm-me',
  templateUrl: './alarm-me.component.html',
  styleUrls: ['./alarm-me.component.css']
})
export class AlarmMeComponent implements OnInit {

  alarmList:any;
  loading:boolean=true;
  constructor(private commonApplicationService:CommonApplicationService,
              private alertService:AlertService,
              private dialogRef:MatDialogRef<AlarmMeComponent>,
              @Inject(MAT_DIALOG_DATA) public data,
              private storageService:StorageService) { }

  ngOnInit(): void {
    this.alarmList=this.data;
  }

  getAlarmMe(){
    let url = apiUrl.alarmMe;
    this.commonApplicationService.get(url).subscribe(response => {
      if(response !== undefined){             
        this.alarmList=response.data;
      }
      this.loading=false;
    },
      (err: any) => {
        this.alertService.common(err);   
        this.loading=false; 
      })
  }

  dontReminderAlarm(alarm_id){
    let url = apiUrl.alarm_me+alarm_id;
    this.commonApplicationService.delete(url).subscribe(response => {
      if(response !== undefined){             
        this.alarmList = this.alarmList.filter(item => item.alarm_id != alarm_id);
      }
    },
      (err: any) => {
        this.alertService.common(err);    
      })
  }

  closeDialog(){
    let url = apiUrl.updateAlarmMe;
    this.commonApplicationService.put(url).subscribe(response => {
      if(response !== undefined){  
        this.storageService.setHard('alarmClosed',true);
        this.dialogRef.close();        
      }
    },
      (err: any) => {
        this.alertService.common(err);    
      })
  }

}
