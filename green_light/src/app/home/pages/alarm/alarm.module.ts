import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { AlarmRoutingModule } from './alarm.routing.module';
import {AttentionWarningModule} from '@shared-modules/attention-warning/attention-warning.module';
import { MaterialLibModule } from '@shared-modules/material-lib/material-lib.module';


//import { AlarmCalendarComponent } from './alarm-calendar/alarm-calendar.component';
//import { AlarmEventComponent } from './alarm-calendar/alarm-event/alarm-event.component';
import { ShowAlarmComponent } from './show-alarm/show-alarm.component';
import { SharedComponentsModule } from '@shared-modules/shared-components/shared-components.module';

@NgModule({
  declarations: [
    //AlarmCalendarComponent,
    //AlarmEventComponent,
    ShowAlarmComponent
  ],
  imports: [
    CommonModule,
    AlarmRoutingModule,
    AttentionWarningModule,
    MaterialLibModule,
    SharedComponentsModule
  ]
})
export class AlarmModule { }
