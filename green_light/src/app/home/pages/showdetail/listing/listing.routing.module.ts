import { NgModule, Component } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { CommonModule } from '@angular/common';

import {ListingComponent} from './listing.component';


const routes: Routes = [
        { path: '', component: ListingComponent, 
    
        }           
];

@NgModule({
  imports: [CommonModule,RouterModule.forChild(routes)],
  exports: [RouterModule],
  providers: [
  ],
})


export class ListingRoutingModule { }
