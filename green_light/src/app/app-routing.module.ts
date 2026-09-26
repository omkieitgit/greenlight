import { NgModule } from '@angular/core';
import { RouterModule, Routes} from '@angular/router';
import { CommonModule } from '@angular/common';

// Home
import {EsResolver} from './route.resolver';

const routes: Routes = [
  { 
    path:'',loadChildren: './home/home.module#HomeModule',
    resolve: { message: EsResolver }
  },
  
  { 
    path:'home',loadChildren: './home/home.module#HomeModule',
    resolve: { message: EsResolver }
  },
  {path: '**',  redirectTo: '/', }
];

@NgModule({
  imports: [ CommonModule, RouterModule.forRoot(routes, { onSameUrlNavigation: 'reload', scrollPositionRestoration: 'top', relativeLinkResolution: 'legacy' }) ],
  exports: [ RouterModule ],
  providers: [
    EsResolver,
      
  ],
  declarations: []
})


export class AppRoutingModule { }
