import { NgModule, Component } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { CommonModule } from '@angular/common';

import {ReportComponent} from './report/report.component';
import {AaReportComponent} from './aa-report/aa-report.component';

import{ReportResolver} from './report-route.resolver';
import { CountyReportComponent } from './county-report/county-report.component';

const routes: Routes = [
        { 
          path: '', component: ReportComponent, 
          data: { title: 'Report'}  ,
          resolve: { message: ReportResolver }
        }, 
        // { 
        //   path: 'aa-report', component: AaReportComponent, 
        //   data: { title: 'Report'}  ,
        //   //resolve: { message: ReportResolver }
        // },
        
        { 
          path: 'county', component: CountyReportComponent, 
          data: { title: 'Report'}  ,
          //resolve: { message: ReportResolver }
        }  
                
];

@NgModule({
  imports: [CommonModule,RouterModule.forChild(routes)],
  exports: [RouterModule],
  providers: [
    ReportResolver
  ],
})


export class ReportRoutingModule { }
