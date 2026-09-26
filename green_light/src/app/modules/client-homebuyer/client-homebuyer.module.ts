import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';

import {DirectivesModule} from '@shared-modules/directives/directives.module';

import { HomeBuyerComponent } from './home-buyer/home-buyer.component';
import { HomeBuyerCategoryComponent } from './home-buyer-category/home-buyer-category.component';
import { HomebuyerPayoutComponent } from './homebuyer-payout/homebuyer-payout.component';
import { CurrencyFormatPipe } from '@shared-modules/directives/currency-format.pipe';
import { HomeBuyerService } from './home-buyer.service';
import { SumPipe } from '@shared-modules/directives/sum.pipe';
import { FilterPipe } from './filter.pipe';
import { SubTotalComponent } from './sub-total/sub-total.component';
import { PanelModule } from 'app/components/panel/panel.module';
import { MyDatePickerModule } from 'mydatepicker';
import { AdditionFieldComponent } from './addition-field/addition-field.component';
import { ClientRanovationBudgetComponent } from './client-ranovation-budget/client-ranovation-budget.component';
import { MailingComponent } from './mailing/mailing.component';



@NgModule({
  declarations: [
    HomeBuyerComponent,
    HomeBuyerCategoryComponent, 
    HomebuyerPayoutComponent, 
    SubTotalComponent, 
    AdditionFieldComponent,
    ClientRanovationBudgetComponent,
    MailingComponent
  ],
  imports: [
    CommonModule,
    DirectivesModule,
    FormsModule,
    ReactiveFormsModule,
    PanelModule,
    MyDatePickerModule
  ],
  exports:[
    HomeBuyerComponent
  ],
  providers:[
    CurrencyFormatPipe,
    HomeBuyerService,
    SumPipe,
    FilterPipe
  ]
})
export class ClientHomebuyerModule { }
