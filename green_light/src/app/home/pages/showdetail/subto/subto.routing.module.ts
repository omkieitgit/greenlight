import { NgModule, Component } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { CommonModule } from '@angular/common';

import {SubtoPropertyComponent} from './subto-property/subto-property.component';


const routes: Routes = [
        { path: '', component: SubtoPropertyComponent}           
];

@NgModule({
  imports: [CommonModule,RouterModule.forChild(routes)],
  exports: [RouterModule],
  providers: [
  ],
})


export class SubtoRoutingModule { }
