import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { ChartModule, HIGHCHARTS_MODULES } from 'angular-highcharts';

import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { MyDatePickerModule } from 'mydatepicker';

import {ReportRoutingModule} from './report.routing.module';
import {AttentionWarningModule} from '../../../modules/attention-warning/attention-warning.module';
import { PanelModule }       from '../../../components/panel/panel.module';
import {DirectivesModule} from '../../../modules/directives/directives.module';
import {LoaderModule} from '../../../modules/loader/loader.module';

import { ReportComponent } from './report/report.component';
import {UserReportComponent} from './user-report/user-report.component';
import {AaReportComponent} from './aa-report/aa-report.component';
import { CountyReportComponent } from './county-report/county-report.component';
//import * as highmaps from 'highcharts/modules/map.src';
//import {AaDetailReportComponent} from './aa-detail-report/aa-detail-report.component';
@NgModule({
  imports: [
    CommonModule,
    ChartModule,
    FormsModule,
     ReactiveFormsModule,
     MyDatePickerModule,
     DirectivesModule,
     LoaderModule,

    ReportRoutingModule,
    AttentionWarningModule,
    PanelModule
  ],
  declarations: [
    ReportComponent,
    UserReportComponent,
    AaReportComponent,
    CountyReportComponent
  ],
  providers: [
   // { provide: HIGHCHARTS_MODULES, useFactory: () => [ highmaps ] }
  ]
})
export class ReportModule { }
