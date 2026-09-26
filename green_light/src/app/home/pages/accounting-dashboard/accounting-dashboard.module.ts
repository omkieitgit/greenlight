import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { AttentionWarningModule } from '@shared-modules/attention-warning/attention-warning.module';
import { AccountingDashboardRouting } from './accounting-dashboad.routing.module';
import { AcDashboardComponent } from './ac-dashboard/ac-dashboard.component';
import { DirectivesModule } from '@shared-modules/directives/directives.module';
import { MscFormListComponent } from './msc-form-list/msc-form-list.component';
import { ReactiveFormsModule } from '@angular/forms';
import { ReScheduleComponent } from './re-schedule/re-schedule.component';



@NgModule({
  declarations: [
    AcDashboardComponent,
    MscFormListComponent,
    ReScheduleComponent
  ],
  imports: [
    CommonModule,
    AttentionWarningModule,
    AccountingDashboardRouting,
    DirectivesModule,
    ReactiveFormsModule,
  ]
})
export class AccountingDashboardModule { }
