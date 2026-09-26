import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';


import { LinkDirective } from './link.directive';
import {HasAccessDirective} from './disable-element.directive';
import {HideElementDirective} from './hide-element.directive';
import { SlugifyPipe } from './slugify.pipe';
import { CurrencyFormatPipe } from './currency-format.pipe'; //import it from your path
import {TextValColorDirective} from './text-val-color.directive';
import { HideContentDirective } from './hide-content.directive';
import {SafeHtmlPipe} from './safe-html.pipe';
import {SafePipe} from './safe.pipe';
import {ArraySortPipe} from './array-sort.pipe';
import {TruncatePipe} from './truncate.pipe';
import {MatchHeightDirective} from './match-height.directive';
import {ReplacePipe} from './replace.pipe';
import { OnBlurSaveDirective } from './on-blur-save.directive';
import {SumPipe} from './sum.pipe';
import { DepositLinkPipe } from './deposit-link.pipe';
import { DepositSheetPipe } from './deposit-sheet.pipe';
@NgModule({
  imports: [
    CommonModule
  ],
  declarations: [
    LinkDirective,
    HasAccessDirective,
    HideElementDirective,
    SlugifyPipe,
    CurrencyFormatPipe,
    TextValColorDirective,
    HideContentDirective,
    SafeHtmlPipe,
    SafePipe,
    ArraySortPipe,
    TruncatePipe,
    MatchHeightDirective,
    ReplacePipe,
    OnBlurSaveDirective,
    SumPipe,
    DepositSheetPipe,
    DepositLinkPipe,
  ],
  exports: [
    LinkDirective,
    HasAccessDirective,
    HideElementDirective,
    SlugifyPipe,
    CurrencyFormatPipe,
    TextValColorDirective,
    HideContentDirective,
    SafeHtmlPipe,
    SafePipe,
    ArraySortPipe,
    TruncatePipe,
    MatchHeightDirective,
    ReplacePipe,
    OnBlurSaveDirective,
    SumPipe,
    DepositSheetPipe,
    DepositLinkPipe,
  ],
  providers:[
    CurrencyFormatPipe,
    SlugifyPipe
  ]
})
export class DirectivesModule { }
