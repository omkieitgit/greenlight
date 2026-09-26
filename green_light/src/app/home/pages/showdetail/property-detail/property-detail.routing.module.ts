import { NgModule, Component } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { CommonModule } from '@angular/common';

import {PropertyDetailComponent} from './property-detail.component';


const routes: Routes = [
        { 
          path: '', 
          component: PropertyDetailComponent, 
          data: { title: 'Search Page'}
        },  
               
];

@NgModule({
  imports: [CommonModule,RouterModule.forChild(routes)],
  exports: [RouterModule],
  providers: [
  ],
})


export class PropertyDetailRoutingModule { }
