import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import {ReplaceLineBreakPipe} from './replace-line-break.pipe';
import { replaceLinkFromTag} from './replace-link.pipe';
@NgModule({
  declarations: [
    ReplaceLineBreakPipe,
    replaceLinkFromTag
  ],
  imports: [
    CommonModule
  ],
  exports:[
    ReplaceLineBreakPipe,
    replaceLinkFromTag
  ]
})
export class EsPipeModule { }
