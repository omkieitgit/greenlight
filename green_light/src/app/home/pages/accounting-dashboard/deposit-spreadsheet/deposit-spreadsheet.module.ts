import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { DepositListComponent } from './deposit-list/deposit-list.component';

import {DepositSpreadsheetRoutingModule} from './deposit-spreadsheet.routing.module';
import {DirectivesModule} from '@shared-modules/directives/directives.module';
import { AttentionWarningModule } from '@shared-modules/attention-warning/attention-warning.module';
import { ReactiveFormsModule } from '@angular/forms';
@NgModule({
  declarations: [
    DepositListComponent
  ],
  imports: [
    CommonModule,
    DepositSpreadsheetRoutingModule,
    DirectivesModule,
    AttentionWarningModule,
    ReactiveFormsModule,

  ]
})
export class DepositSpreadsheetModule { }
