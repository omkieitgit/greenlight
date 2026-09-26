import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import {TimeTrackingComponent} from './time-tracking.component';

import {MatDialogModule,}       from '@angular/material/dialog';
import {MatButtonModule}  from '@angular/material/button';
import { NgIdleKeepaliveModule } from '@ng-idle/keepalive'; // this includes the core NgIdleModule but includes keepalive providers for easy wireup
import { MomentModule } from 'angular2-moment'; // optional, provides moment-style pipes for date formatting
import { HoursMinutesSeconds } from './hours-minutes-seconds';


@NgModule({
  declarations: [
    TimeTrackingComponent,
    HoursMinutesSeconds
  ],
  imports: [
    CommonModule,
    MatDialogModule,
    MatButtonModule,
    MomentModule,
    NgIdleKeepaliveModule.forRoot()
  ],
  exports:[
    TimeTrackingComponent,
    HoursMinutesSeconds
  ],
  entryComponents:[
    TimeTrackingComponent
  ],

})
export class TimeTrackingModule { }
