import { Component,Input, OnInit } from '@angular/core';
import { CommonApplicationService,AlertService} from '../../../shared/_services';
import { apiUrl } from '../../../config/api-url';
@Component({
  selector: 'app-add-to-alarm',
  templateUrl: './add-to-alarm.component.html',
  styleUrls: ['./add-to-alarm.component.css']
})
export class AddToAlarmComponent implements OnInit {
  
  @Input() property_id;
  loader:boolean=false;
  constructor(private commonApplicationService:CommonApplicationService,
              private alertService:AlertService,) { }

  ngOnInit() {
  }

  alarm_me(e){
    e.preventDefault();
    e.stopPropagation();
    this.loader=true;
    let url = apiUrl.alarm_me+this.property_id;
    this.commonApplicationService.post(url).subscribe(response => {
      if(response !== undefined){        
        this.alertService.success(response.message); 
        this.loader=false;
      }
    },
      (err: any) => {
        this.alertService.common(err);    
      })
  }
}
