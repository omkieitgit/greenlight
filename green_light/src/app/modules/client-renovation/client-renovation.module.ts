import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { PanelModule } from '../../components/panel/panel.module';


import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { MyDatePickerModule } from 'mydatepicker';
import { MatDialogModule, MAT_DIALOG_DATA, MatDialogRef} from '@angular/material/dialog';

import { DirectivesModule } from '@shared-modules/directives/directives.module';
import { LoaderModule } from '@shared-modules/loader/loader.module';

import { ClientExpensesComponent } from './client-expenses/client-expenses.component';
import { ClientInvoiceComponent } from './client-invoice/client-invoice.component';
import { CommonRenovationComponent } from './common-renovation/common-renovation.component';
import {RenovationCategoryComponent} from './renovation-category/renovation-category.component';
import { CurrencyFormatPipe } from '@shared-modules/directives/currency-format.pipe';

import { MaterialLibModule } from '@shared-modules/material-lib/material-lib.module';
import { ClientMscFormComponent } from './client-msc-form/client-msc-form.component';
//import { PanelModule } from 'app/components/panel/panel.module';

@NgModule({
  declarations: [
    ClientExpensesComponent,
    ClientInvoiceComponent,
    CommonRenovationComponent,
    RenovationCategoryComponent,
    ClientMscFormComponent
  ],
  imports: [
    CommonModule,
    FormsModule,
    ReactiveFormsModule,
    MyDatePickerModule,
    DirectivesModule,
    LoaderModule,
    PanelModule,
    MaterialLibModule
  ],
  exports:[
    ClientExpensesComponent,
    ClientInvoiceComponent,
  ],
  providers:[
    CurrencyFormatPipe,
   // PropertyAcquisitionService,
    {provide:MatDialogRef , useValue:{} },
    { provide: MAT_DIALOG_DATA, useValue: {} }
  ]
})
export class ClientRenovationModule { }
