import { NgModule, Component } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { CommonModule } from '@angular/common';

import {AccountingComponent} from './accounting.component';

import {AccountingResolver} from './accounting-route.resolver';

const routes: Routes = [
        { 
          path: '', component: AccountingComponent, 
          data: { title: 'Accounting'}  ,
          resolve: { message: AccountingResolver }
        }           
];

@NgModule({
  imports: [CommonModule,RouterModule.forChild(routes)],
  exports: [RouterModule],
  providers: [
    AccountingResolver
  ],
})


export class AccountingRoutingModule { }
