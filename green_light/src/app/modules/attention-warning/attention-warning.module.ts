import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { AttentionWarningComponent } from './attention-warning.component';
import { RouterModule } from '@angular/router';

import {DirectivesModule} from '../directives/directives.module';

@NgModule({
  imports: [
    CommonModule,
    RouterModule,
    DirectivesModule
  ],
  declarations: [AttentionWarningComponent],
  exports:[AttentionWarningComponent]
})
export class AttentionWarningModule { }
