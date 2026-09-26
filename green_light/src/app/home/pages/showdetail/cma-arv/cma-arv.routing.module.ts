import { NgModule, Component } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { CommonModule } from '@angular/common';

import {CmaarvComponent} from './cma-arv.component';


const routes: Routes = [
        { path: '', component: CmaarvComponent, 
    
        }           
];

@NgModule({
  imports: [CommonModule,RouterModule.forChild(routes)],
  exports: [RouterModule],
  providers: [
  ],
})


export class CmaArvRoutingModule { }
