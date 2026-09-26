import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HttpClientModule } from '@angular/common/http';
import { RouterModule } from '@angular/router';
import {NgxPaginationModule} from 'ngx-pagination'; // <-- import the module
import { ReactiveFormsModule } from '@angular/forms';

import {PanelModule} from '../../components/panel/panel.module';
import { MyDatePickerModule } from 'mydatepicker';

import {PropertyButtonModule} from '../property-button/property-button.module';
import {DirectivesModule} from '../directives/directives.module';
import {PropertyExportModule} from '../property-export/property-export.module';
import {DocumentModule} from '@shared-modules/document/document.module';


import {PropertyDetailComponent} from './property-detail/property-detail.component';
import {SearchResultComponent} from './search-result/search-result.component';
import {PropertyQueueComponent} from './property-queue/property-queue.component';
import {HereMapComponent} from './here-map/here-map.component';

import { BidderComponent }      from './sale/bidder/bidder.component';
import { SaleComponent }        from './sale/sale.component';
import { SaleDateComponent } from './sale/sale-date/sale-date.component';
import { BidderOneComponent } from './sale/bidder-one/bidder-one.component';

import { FavouriteComponent } from './favourite/favourite.component';
import { MergeComponentComponent } from './merge-component/merge-component.component';
import { MaterialLibModule } from '@shared-modules/material-lib/material-lib.module';
import { AlarmMeComponent } from './alarm-me/alarm-me.component';

@NgModule({
  imports: [
    CommonModule,
    HttpClientModule,
    RouterModule,
    NgxPaginationModule,
    ReactiveFormsModule,
    MyDatePickerModule,

    PropertyButtonModule,
    DirectivesModule,
    PropertyExportModule,
    PanelModule,
    DocumentModule,
    MaterialLibModule
  ],
  declarations: [
    PropertyDetailComponent,
    SearchResultComponent,
    PropertyQueueComponent,
    HereMapComponent,

    SaleComponent,
    BidderComponent,
    SaleDateComponent,
    BidderOneComponent,
    FavouriteComponent,
    MergeComponentComponent,
    AlarmMeComponent,
    
  ],
  exports:[
    PropertyDetailComponent,
    SearchResultComponent,
    PropertyQueueComponent,
    HereMapComponent,

    SaleComponent,
    BidderComponent,
    SaleDateComponent,
    BidderOneComponent,
    FavouriteComponent,
    AlarmMeComponent
  ]
})
export class SharedComponentsModule { }
