import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import {MatProgressSpinnerModule} from '@angular/material/progress-spinner';

import {LoaderComponent} from './loader.component';
import { MobileLoaderComponent } from './mobile-loader/mobile-loader.component'
@NgModule({
  imports: [
    CommonModule,
    MatProgressSpinnerModule
  ],
  declarations: [
    LoaderComponent,
    MobileLoaderComponent
  ],
  exports:[
    LoaderComponent,
    MobileLoaderComponent
  ]
})
export class LoaderModule { }
