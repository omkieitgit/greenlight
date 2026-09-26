import { NgModule, Component } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { CommonModule } from '@angular/common';

import {Showdetail} from './showdetail';
import {ShowdetailRouteResolver} from './showdetail-route.resolver';

const routes: Routes = [
  { path: '', component: Showdetail, 
  children: [
    { 
      path: '', 
      loadChildren: './property-detail/property-detail.module#PropertyDetailModule', 
      data: { title: 'Search Page'},
      resolve: { message: ShowdetailRouteResolver }
    }, 
     { 
       path:'cma-arv',
       loadChildren: './cma-arv/cma-arv.module#CmaArvModule',
       resolve: { message: ShowdetailRouteResolver }
      },
      { 
        path:'accounting',
        loadChildren: '../accounting/accounting.module#AccountingModule',
        resolve: { message: ShowdetailRouteResolver }
       },
       { 
        path:'listing',
        loadChildren: './listing/listing.module#ListingModule',
        resolve: { message: ShowdetailRouteResolver }
       },
      { 
       path:'document',
       loadChildren: './client-document-view/client-document-view.module#ClientDocumentViewModule',
       resolve: { message: ShowdetailRouteResolver }
      },

      { 
       path:'terms',
       loadChildren: '../terms-definition/terms-definition.module#TermsDefinitionModule',
       resolve: { message: ShowdetailRouteResolver }
      },
      { 
        path:'subto',
        loadChildren: './subto/subto.module#SubtoModule',
        resolve: { message: ShowdetailRouteResolver }
       },
  ]}
        
               
];

@NgModule({
  imports: [CommonModule,RouterModule.forChild(routes)],
  exports: [RouterModule],
  providers: [
      ShowdetailRouteResolver
  ],
})


export class ShowdetailRoutingModule { }
