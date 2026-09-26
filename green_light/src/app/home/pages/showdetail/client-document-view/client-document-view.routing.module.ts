import { NgModule, Component } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { CommonModule } from '@angular/common';

import {ClientDocumentViewComponent} from './client-document-view.component';


const routes: Routes = [
        { path: '', component: ClientDocumentViewComponent, 
    
        }           
];

@NgModule({
  imports: [CommonModule,RouterModule.forChild(routes)],
  exports: [RouterModule],
  providers: [
  ],
})


export class ClientDocumentViewRoutingModule { }
