import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

/*Module*/
import {AccountingRoutingModule} from './accounting.routing.module';
import { PanelModule }       from '../../../components/panel/panel.module';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import {FileUploadModule} from 'primeng/fileupload';
import { MyDatePickerModule } from 'mydatepicker';

//import { Ng2TableModule } from 'ngx-datatable/ng2-table';
import {NgxDatatableModule} from '@swimlane/ngx-datatable';
import {NgxPaginationModule} from 'ngx-pagination';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';


import {MatFormFieldModule }  from '@angular/material/form-field';
import {MatInputModule}  from '@angular/material/input';
import {MatIconModule}  from '@angular/material/icon';
import {MatAutocompleteModule}  from '@angular/material/autocomplete';
import {MatButtonModule}  from '@angular/material/button';
import {MatProgressSpinnerModule}  from '@angular/material/progress-spinner';
import {MatListModule}  from '@angular/material/list';
import {MatChipsModule}  from '@angular/material/chips';
import {MatSelectModule} from '@angular/material/select';
import {MatTooltipModule} from '@angular/material/tooltip';


import {DirectivesModule} from '../../../modules/directives/directives.module';
import {LoaderModule} from '../../../modules/loader/loader.module';
import {AttentionWarningModule} from '../../../modules/attention-warning/attention-warning.module';
import {PropertyButtonModule} from '../../../modules/property-button/property-button.module';
import {ShowdetailMenuModule} from '../../../modules/showdetail-menu/showdetail-menu.module';


/*Accounting Component*/
import {AccountingComponent} from './accounting.component';

//import { ClientBiddingComponent } from './client-bidding/client-bidding.component';
import { ClientDocumentComponent } from './client-document/client-document.component';
//import { NonHudComponent } from './pay-out/non-hud/non-hud.component';
import { ClientW9Component } from './client-w9/client-w9.component';
//import { PayOutComponent } from './pay-out/pay-out.component';

import { IncidentalCostsComponent } from './reno-inc-carry-costs/incidental-costs/incidental-costs.component';
import { CarryCostsComponent } from './reno-inc-carry-costs/carry-costs/carry-costs.component';
import { RenovationCostsComponent } from './reno-inc-carry-costs/renovation-costs/renovation-costs.component';
import { RenoIncCarryCostsComponent } from './reno-inc-carry-costs/reno-inc-carry-costs.component';
import { AccountingDocumentComponent } from './accounting-document/accounting-document.component';
import { InlineEditComponent }     from '../inline-edit/inline-edit.component';
import { SortingComponent }    from '../showdetail/sorting/sorting.component';
import {HomeMcdComponent} from './home-mcd/home-mcd.component';
import {LenderComponent} from './home-mcd/lender/lender.component';
import { CurrencyFormatPipe } from '@shared-modules/directives/currency-format.pipe';

//import {PayoutCategoryComponent} from './pay-out/payout-category/payout-category.component';
//import {PayoutInfoComponent} from './pay-out/payout-info/payout-info.component';
//import {PurchaseAdjustmentsComponent} from './pay-out/purchase-adjustments/purchase-adjustments.component';
//import {FeesComponent} from './pay-out/fees/fees.component';
import {TrustLedgerComponent} from './home-mcd/trust-ledger/trust-ledger.component';
import { MatDialogModule, MAT_DIALOG_DATA, MatDialogRef} from '@angular/material/dialog';
//import {NetProfitComponent} from './pay-out/net-profit/net-profit.component';
//import {MoreFieldComponent} from './pay-out/more-field/more-field.component';
//import {DistributionMemberComponent} from './pay-out/distribution-member/distribution-member.component';
//import {MemberComponent} from './pay-out/distribution-member/member/member.component';
import {TradesmanTrackingComponent} from './home-mcd/tradesman-tracking/tradesman-tracking.component';
import {ShortTermRentalComponent} from './home-mcd/short-term-rental/short-term-rental.component';
import {BankStatementComponent} from './home-mcd/short-term-rental/bank-statement/bank-statement.component';
import {DepositSheetComponent} from './home-mcd/deposit-sheet/deposit-sheet.component';
import { CategoryByCostComponent } from './reno-inc-carry-costs/category-by-cost/category-by-cost.component';
import {BurnRateComponent} from './reno-inc-carry-costs/burn-rate/burn-rate.component';
import {TimeTrackingModule} from '@shared-modules/time-tracking/time-tracking.module';
import {DepositLinkComponent} from './home-mcd/deposit-sheet/deposit-link/deposit-link.component';
import {DepositLenderComponent} from './home-mcd/deposit-sheet/deposit-lender/deposit-lender.component';
import {DepositLenderCatComponent} from './home-mcd/deposit-sheet/deposit-lender/deposit-lender-cat/deposit-lender-cat.component';
import {AccountingTrackingComponent} from './home-mcd/accounting-tracking/accounting-tracking.component';

import {ClientRenovationModule} from '@shared-modules/client-renovation/client-renovation.module';
//import { NetprofitPipe } from './pay-out/net-profit.pipe';
import {ClientHomebuyerModule} from '@shared-modules/client-homebuyer/client-homebuyer.module';
import { SumPipe } from '@shared-modules/directives/sum.pipe';
//import { PayoutCategoryListComponent } from './pay-out/payout-category-list/payout-category-list.component';
import { TrandesmanUserComponent } from './home-mcd/tradesman-tracking/trandesman-user/trandesman-user.component';
import { McdOtherInfoComponent } from './home-mcd/mcd-other-info/mcd-other-info.component';
import { PayOutModule } from './home-mcd/pay-out/pay-out.module';
import {MscFormComponent} from './msc-form/msc-form.component';
import { AccessMcdUserModule } from './access-mcd-user/access-mcd-user.module';
import { NgxPrintModule } from 'ngx-print';
@NgModule({
  imports: [
    CommonModule,
    AccountingRoutingModule,
    PanelModule,
    FormsModule,
    ReactiveFormsModule,
    MyDatePickerModule,
    FileUploadModule,
    //Ng2TableModule,
    NgxDatatableModule,
    NgxPaginationModule,
    NgbModule,

    /*Mat*/
    MatFormFieldModule,
    MatInputModule,
    MatIconModule, 
    MatAutocompleteModule, 
    MatButtonModule, 
    MatProgressSpinnerModule,
    MatListModule,
    MatChipsModule,
    MatDialogModule,
    MatSelectModule,
    MatTooltipModule,

    /*Custom*/
    DirectivesModule,
    LoaderModule,
    AttentionWarningModule,
    PropertyButtonModule,
    ShowdetailMenuModule,
    TimeTrackingModule,
    ClientRenovationModule,
    ClientHomebuyerModule,
    PayOutModule,
    AccessMcdUserModule,
    NgxPrintModule
  ],
  declarations: [
    AccountingComponent,
    ClientDocumentComponent,
    ClientW9Component,
    IncidentalCostsComponent,
    CarryCostsComponent,
    RenovationCostsComponent,
    RenoIncCarryCostsComponent,
    AccountingDocumentComponent,
    InlineEditComponent,
    SortingComponent,
    HomeMcdComponent,
    LenderComponent,
    TrustLedgerComponent,
    // NonHudComponent,
    // PayOutComponent,
    // PayoutInfoComponent,
    // PurchaseAdjustmentsComponent,
    // PayoutCategoryComponent,
    // FeesComponent,
    // NetProfitComponent,
    // MoreFieldComponent,
    // DistributionMemberComponent,
    // MemberComponent,
    // NetprofitPipe,
    // PayoutCategoryListComponent,
    TradesmanTrackingComponent,
    ShortTermRentalComponent,
    BankStatementComponent,
    DepositSheetComponent,
    CategoryByCostComponent,
    BurnRateComponent,
    DepositLinkComponent,
    DepositLenderComponent,
    DepositLenderCatComponent,
    AccountingTrackingComponent,
    TrandesmanUserComponent,
    McdOtherInfoComponent,
    MscFormComponent
  ],
  providers:[
    CurrencyFormatPipe,
    SumPipe,
    {provide:MatDialogRef , useValue:{} },
    { provide: MAT_DIALOG_DATA, useValue: {} }
  ]
})
export class AccountingModule { 

}
