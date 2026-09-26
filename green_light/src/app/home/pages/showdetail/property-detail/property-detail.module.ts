import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import {MatDialogModule,} from '@angular/material/dialog';
import { MatSortModule } from '@angular/material/sort';
import {MatTableModule}  from '@angular/material/table';
import {MatFormFieldModule}  from '@angular/material/form-field';
import {MatInputModule}  from '@angular/material/input';
import {MatIconModule}  from '@angular/material/icon';
import {MatAutocompleteModule}  from '@angular/material/autocomplete';
import {MatButtonModule}  from '@angular/material/button';
import {MatProgressSpinnerModule}  from '@angular/material/progress-spinner';
import {MatListModule}  from '@angular/material/list';
import {MatChipsModule}  from '@angular/material/chips';

import {AutosizeModule} from 'ngx-autosize';
import {FileUploadModule} from 'primeng/fileupload';
import { MyDatePickerModule } from 'mydatepicker';
import {NgxPaginationModule} from 'ngx-pagination'; // <-- import the module
import { NgbModule }         from '@ng-bootstrap/ng-bootstrap';
import {NgxDatatableModule} from '@swimlane/ngx-datatable';

/*ES Module*/
import { PanelModule }       from '../../../../components/panel/panel.module';
import {AlertModule}            from "../../../../alert.module";
import {PropertyButtonModule} from '@shared-modules/property-button/property-button.module';
import {CommonNotesModule} from '@shared-modules/common-notes/common-notes.module';
import {DirectivesModule} from '@shared-modules/directives/directives.module';
import {AttentionWarningModule} from '@shared-modules/attention-warning/attention-warning.module';
import {PropertySearchModule} from '@shared-modules/property-search/property-search.module';
import {LoaderModule} from '@shared-modules/loader/loader.module';
import {DocumentModule} from '@shared-modules/document/document.module';
import {ShowdetailMenuModule} from '@shared-modules/showdetail-menu/showdetail-menu.module';
import {SharedComponentsModule} from '@shared-modules/shared-components/shared-components.module';
import {PropertyExportModule} from '@shared-modules/property-export/property-export.module';

import {PropertyDetailRoutingModule} from './property-detail.routing.module';
/*Property Detail Component*/
import {PropertyDetailComponent} from './property-detail.component';
import { InfoComponent }        from './info/info.component';
import { InjectDirective }      from './info/inject.directive';
import { OwnerComponent }       from './owner-borrow/owner/owner.component';

import { PicvideoComponent }    from './picvideo/picvideo.component';
import { MortgageComponent }    from './mortgage/mortgage.component';

import { ScrapperComponent } from './info/real-state/scrapper/scrapper.component';
import { PropInfoComponent }       from './info/prop-info/prop-info.component';
import { RealStateComponent }       from './info/real-state/real-state.component';
import { PriceHistoryComponent }       from './info/price-history/price-history.component';
import { AssessmentComponent }       from './info/assessment/assessment.component';
import { SchoolComponent }       from './info/school/school.component';
import { MortgateOtherPropTaxComponent }       from './mortgage/mortgate-other-prop-tax/mortgate-other-prop-tax.component';
import { LienHoaComponent }       from './mortgage/lien-hoa/lien-hoa.component';
import { LienOtherComponent }       from './mortgage/lien-other/lien-other.component';
import { TaxPropertyComponent }       from './mortgage/tax-property/tax-property.component';
import { EstimatedLatePaymentsComponent } from './mortgage/estimated-late-payments/estimated-late-payments.component';
import {EstimateLatePaymentService} from './mortgage/estimated-late-payments/estimated-late-payments.service';
import {AmortizationService} from './mortgage/amortization.service';
import { AmortizationComponent } from './mortgage/amortization/amortization.component';
import { DefectiveNotesComponent } from './mortgage/defective-notes/defective-notes.component';
import { BorrowComponent } from './owner-borrow/borrow/borrow.component';
import { DefectiveNotesDetailComponent } from './mortgage/defective-notes/defective-notes-detail/defective-notes-detail.component';
import { SaleInfoComponent } from './mortgage/sale-info/sale-info.component';
import { MoreAssessmentComponent } from './info/assessment/more-assessment/more-assessment.component';
import { LienComponent } from './mortgage/lien/lien.component';
import { OwnerBorrowComponent } from './owner-borrow/owner-borrow.component';
import { LienTaxComponent } from './mortgage/lien-tax/lien-tax.component';

//Invite Component
import { InviteComponent } from './invite/invite.component';
import { SendInviteComponent } from './invite/send-invite/send-invite.component';
import { InviteListComponent } from './invite/invite-list/invite-list.component';
import { InviteAllComponent } from './invite/invite-all/invite-all.component';
import { InvitedDataComponent } from './invite/invited-data/invited-data.component';
import { CountyUrlComponent } from './county-url/county-url.component';
import { CountyUrlDialogComponent } from './county-url/county-url-dialog/county-url-dialog.component';
import { LenderInfoComponent } from './lender-info/lender-info.component';
import { NgxPrintModule } from 'ngx-print';


@NgModule({
  declarations: [
    PropertyDetailComponent,
    InfoComponent,        
    InjectDirective,
    OwnerComponent,
    PicvideoComponent,
    MortgageComponent,
    PropInfoComponent,
    RealStateComponent,
    PriceHistoryComponent,
    AssessmentComponent,
    SchoolComponent,
    ScrapperComponent, //scrapper
    MortgateOtherPropTaxComponent,
    LienHoaComponent,
    LienOtherComponent,
    TaxPropertyComponent,
    EstimatedLatePaymentsComponent,
    AmortizationComponent,
    DefectiveNotesComponent,
    BorrowComponent,
    DefectiveNotesDetailComponent,
    SaleInfoComponent,
    MoreAssessmentComponent,
    LienComponent,
    OwnerBorrowComponent,
    LienTaxComponent,

    InviteComponent,
    SendInviteComponent,
    InviteListComponent,
    InviteAllComponent,
    InvitedDataComponent,
    CountyUrlComponent,
    CountyUrlDialogComponent,
    LenderInfoComponent
  ],
  imports: [
    CommonModule,
    FormsModule, 
    ReactiveFormsModule,

    AutosizeModule,
    MyDatePickerModule,
    FileUploadModule,
    NgxPaginationModule,
    NgbModule,
    NgxDatatableModule,

    MatDialogModule,
    MatInputModule,
    MatAutocompleteModule,
    MatFormFieldModule,
    MatButtonModule,
    MatProgressSpinnerModule,
    MatListModule,
    MatIconModule,
    MatChipsModule,

    PropertyDetailRoutingModule,
    PropertyButtonModule,
    CommonNotesModule,
    PanelModule,
    DirectivesModule,
    AttentionWarningModule,
    PropertySearchModule,
    LoaderModule,
    DocumentModule,
    ShowdetailMenuModule,
    SharedComponentsModule,
    PropertyExportModule,
    AlertModule,
    NgxPrintModule
  ],
  providers:[
    AmortizationService,
    EstimateLatePaymentService
  ]
})
export class PropertyDetailModule { }
