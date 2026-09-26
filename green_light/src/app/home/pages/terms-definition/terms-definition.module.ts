import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import{TermsDefinitionRoutingModule} from './terms-definition.routing.module';
import {AttentionWarningModule} from '../../../modules/attention-warning/attention-warning.module';
import {PropertyButtonModule} from '../../../modules/property-button/property-button.module';
import {ShowdetailMenuModule} from '../../../modules/showdetail-menu/showdetail-menu.module';
import {MatDialogModule}       from '@angular/material/dialog';

import{TermsDefinitionComponent} from './terms-definition.component';

@NgModule({
  imports: [
    CommonModule,
    TermsDefinitionRoutingModule,
    AttentionWarningModule,
    PropertyButtonModule,
    ShowdetailMenuModule,
    MatDialogModule
  ],
  declarations: [
    TermsDefinitionComponent
  ],
  entryComponents: [
    TermsDefinitionComponent
  ],
  exports:[
    TermsDefinitionComponent
  ]
})
export class TermsDefinitionModule { }
