import { NgModule, Component } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { CommonModule } from '@angular/common';

import {DepositListComponent} from './deposit-list/deposit-list.component';

const routes: Routes = [
        { 
          path: '', component: DepositListComponent, 
          data: { title: 'Deposit-spreadsheet'}  ,
        }           
];

@NgModule({
  imports: [CommonModule,RouterModule.forChild(routes)],
  exports: [RouterModule],
  providers: [
  ],
})


export class DepositSpreadsheetRoutingModule { }
