import { NgModule, Component } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { CommonModule } from '@angular/common';
import { AcDashboardComponent } from './ac-dashboard/ac-dashboard.component';
import { AcDashboardRouteResolver } from './ac-dashboard-route.resolver';
import { MscFormListComponent } from './msc-form-list/msc-form-list.component';
import { ReScheduleComponent } from './re-schedule/re-schedule.component';



const routes: Routes = [
        { 
          path: '', component: AcDashboardComponent, 
          data: { title: 'Deposit-spreadsheet'}  ,
          resolve: { message: AcDashboardRouteResolver }
        },
        { 
          path:'deposit',
          loadChildren: './deposit-spreadsheet/deposit-spreadsheet.module#DepositSpreadsheetModule',
          resolve: { message: AcDashboardRouteResolver }
         }, 
         { 
          path: 'msc-list', component: MscFormListComponent, 
          data: { title: 'Msc-form'}  ,
          resolve: { message: AcDashboardRouteResolver }
        },  
        { 
          path: 'reschedule', component: ReScheduleComponent, 
          data: { title: 'Re-Schedule'}  ,
          resolve: { message: AcDashboardRouteResolver }
        },         
];

@NgModule({
  imports: [CommonModule,RouterModule.forChild(routes)],
  exports: [RouterModule],
  providers: [
    AcDashboardRouteResolver
  ],
})


export class AccountingDashboardRouting { }
