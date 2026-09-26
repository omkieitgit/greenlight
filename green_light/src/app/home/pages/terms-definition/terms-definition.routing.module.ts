import { NgModule, Component } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { CommonModule } from '@angular/common';

import {TermsDefinitionComponent} from './terms-definition.component';


const routes: Routes = [
        { path: '', component: TermsDefinitionComponent, 
    
        }           
];

@NgModule({
  imports: [CommonModule,RouterModule.forChild(routes)],
  exports: [RouterModule],
  providers: [
  ],
})


export class TermsDefinitionRoutingModule { }
