import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import {AutosizeModule} from 'ngx-autosize';
import {FileUploadModule} from 'primeng/fileupload';
import { MyDatePickerModule } from 'mydatepicker';

import {MatDialogModule,}       from '@angular/material/dialog';
import {MatButtonModule}  from '@angular/material/button';


import {CmaArvRoutingModule} from './cma-arv.routing.module';
import {PropertyButtonModule} from '@shared-modules/property-button/property-button.module';
import {CommonNotesModule} from '@shared-modules/common-notes/common-notes.module';
import { PanelModule }       from '../../../../components/panel/panel.module';
import {DirectivesModule} from '@shared-modules/directives/directives.module';
import {LoaderModule} from '@shared-modules/loader/loader.module';
import {AttentionWarningModule} from '@shared-modules/attention-warning/attention-warning.module';
import {ShowdetailMenuModule} from '@shared-modules/showdetail-menu/showdetail-menu.module';


import {CmaarvComponent} from './cma-arv.component';
import { CmaArvDetailComponent } from './cma-arv-detail/cma-arv-detail.component';
import {CheckCmaArvComponent} from './check-cma-arv/check-cma-arv.component';
import { AmEmailComponent } from './am-email/am-email.component';
import { WholesaleBuyerComponent } from './wholesale-buyer/wholesale-buyer.component';
import { WholeSaleMultiComponent } from './wholesale-buyer/whole-sale-multi/whole-sale-multi.component';
import { DaysOnMarketComponent } from './check-cma-arv/days-on-market/days-on-market.component';
import { SqftCalculationComponent } from './check-cma-arv/sqft-calculation/sqft-calculation.component';
import { NgxPrintModule } from 'ngx-print';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    ReactiveFormsModule,
    MatDialogModule,
    MatButtonModule,
    AutosizeModule,
    FileUploadModule,
    MyDatePickerModule,

    PropertyButtonModule,
    CommonNotesModule,
    PanelModule,
    DirectivesModule,
    LoaderModule,
    CmaArvRoutingModule,
    ShowdetailMenuModule,
    AttentionWarningModule,
    NgxPrintModule
  ],
  declarations: [
    CmaArvDetailComponent,
    CmaarvComponent,
    CheckCmaArvComponent,
    AmEmailComponent,
    WholesaleBuyerComponent,
    WholeSaleMultiComponent,
    DaysOnMarketComponent,
    SqftCalculationComponent
  ],
  entryComponents:[
    AmEmailComponent,
    DaysOnMarketComponent
  ],
  exports:[
    CmaarvComponent
  ]

})
export class CmaArvModule { }
