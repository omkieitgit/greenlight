import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

//Module used in payout
import { LoaderModule } from '@shared-modules/loader/loader.module';
import { PanelModule } from 'app/components/panel/panel.module';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { MaterialLibModule } from '@shared-modules/material-lib/material-lib.module';
import { MyDatePickerModule } from 'mydatepicker';
import { DirectivesModule } from '@shared-modules/directives/directives.module';

//pipe used in payout
import { NetprofitPipe } from './net-profit.pipe';
import { CurrencyFormatPipe } from '@shared-modules/directives/currency-format.pipe';
import { SumPipe } from '@shared-modules/directives/sum.pipe';

//Component used in payout
import { NonHudComponent } from './non-hud/non-hud.component';
import { PayOutComponent } from './pay-out.component';
import { PayoutInfoComponent } from './payout-info/payout-info.component';
import { PurchaseAdjustmentsComponent } from './purchase-adjustments/purchase-adjustments.component';
import { PayoutCategoryComponent } from './payout-category/payout-category.component';
import { FeesComponent } from './fees/fees.component';
import { DistributionMemberComponent } from './distribution-member/distribution-member.component';
import { NetProfitComponent } from './net-profit/net-profit.component';
import { MoreFieldComponent } from './more-field/more-field.component';
import { MemberComponent } from './distribution-member/member/member.component';
import { PayoutCategoryListComponent } from './payout-category-list/payout-category-list.component';
import {NgxPrintModule} from 'ngx-print';


@NgModule({
  declarations: [
    NonHudComponent,
    PayOutComponent,
    PayoutInfoComponent,
    PurchaseAdjustmentsComponent,
    PayoutCategoryComponent,
    FeesComponent,
    NetProfitComponent,
    MoreFieldComponent,
    DistributionMemberComponent,
    MemberComponent,
    NetprofitPipe,
    PayoutCategoryListComponent,
    
  ],
  imports: [
    CommonModule,
    DirectivesModule,
    LoaderModule,
    PanelModule,
    FormsModule,
    ReactiveFormsModule,
    MaterialLibModule,
    MyDatePickerModule,
    NgxPrintModule,
    
  ],
  providers:[
    CurrencyFormatPipe,
    NetprofitPipe,
    SumPipe,
  ],
  exports:[
    PayOutComponent
  ]
})
export class PayOutModule { }
