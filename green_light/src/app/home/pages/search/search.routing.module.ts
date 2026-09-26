import { NgModule, Component } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { CommonModule } from '@angular/common';

import {SearchComponent} from './search.component';

import {SearchResolver} from './search-route.resolver';

const routes: Routes = [
        { 
          path: '', component: SearchComponent, 
          data: { title: 'Search'}  ,
          resolve: { message: SearchResolver }
        }           
];

@NgModule({
  imports: [CommonModule,RouterModule.forChild(routes)],
  exports: [RouterModule],
  providers: [
    SearchResolver
  ],
})


export class SearchRoutingModule { }
