import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import {ClientDocumentViewRoutingModule} from './client-document-view.routing.module';
import {DirectivesModule} from '@shared-modules/directives/directives.module';
import {LoaderModule} from '@shared-modules/loader/loader.module';
import {AttentionWarningModule} from '@shared-modules/attention-warning/attention-warning.module';
import {PropertyButtonModule} from '@shared-modules/property-button/property-button.module';
import {ShowdetailMenuModule} from '@shared-modules/showdetail-menu/showdetail-menu.module';


import { PanelModule }       from '../../../../components/panel/panel.module';
import {DocumentModule} from '@shared-modules/document/document.module';
import {ClientDocumentViewComponent} from './client-document-view.component';

@NgModule({
  imports: [
    CommonModule,
    ClientDocumentViewRoutingModule,
    DirectivesModule,
    LoaderModule,
    AttentionWarningModule,
    PropertyButtonModule,
    ShowdetailMenuModule,
    DocumentModule,
    PanelModule
  ],
  declarations: [
    ClientDocumentViewComponent
  ]
})
export class ClientDocumentViewModule { }
