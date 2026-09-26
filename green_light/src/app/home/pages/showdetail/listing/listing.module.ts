import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import {ListingComponent} from './listing.component';
import {MlsListingComponent} from './mls-listing/mls-listing.component';
import {BrokerComponent} from './broker/broker.component';

import {ListingRoutingModule} from './listing.routing.module';
import {PanelModule} from '../../../../components/panel/panel.module';
import {DocumentModule} from '@shared-modules/document/document.module';


import { ListingDocumentComponent } from './listing-document/listing-document.component';
import { ListingInfoComponent } from './listing-info/listing-info.component';

@NgModule({
  declarations: [
    ListingComponent,
    MlsListingComponent,
    BrokerComponent,
    ListingDocumentComponent,
    ListingInfoComponent
  ],
  imports: [
    CommonModule,
    ListingRoutingModule,
    PanelModule,
    DocumentModule
  ]
})
export class ListingModule { }
