import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import {MatDialogModule,}       from '@angular/material/dialog';
import {MatFormFieldModule}  from '@angular/material/form-field';
import {MatInputModule}  from '@angular/material/input';
import {MatIconModule}  from '@angular/material/icon';
import {MatAutocompleteModule}  from '@angular/material/autocomplete';
import {MatButtonModule}  from '@angular/material/button';
import {MatProgressSpinnerModule}  from '@angular/material/progress-spinner';
import {MatListModule}  from '@angular/material/list';

import { ReactiveFormsModule } from '@angular/forms';

import {PropertyButtonModule} from '../property-button/property-button.module';


import { PropertExportComponent } from './propert-export.component';
import { EmailComponent } from './email/email.component';
import { PrintComponent } from './print/print.component';
import { ExportDialogComponent } from './export-dialog/export-dialog.component';
import { ExportTypeComponent } from './export-type/export-type.component';

@NgModule({
  imports: [
    CommonModule,
    MatFormFieldModule,
    MatInputModule,
    MatIconModule, 
    MatAutocompleteModule, 
    MatButtonModule, 
    MatProgressSpinnerModule,
    MatListModule,
    MatDialogModule,
    ReactiveFormsModule,
    

    PropertyButtonModule
  ],
  declarations: [
    PropertExportComponent,
    EmailComponent,
    PrintComponent,
    ExportDialogComponent,
    ExportTypeComponent,
  ],
  exports:[
    PropertExportComponent,
    EmailComponent,
    PrintComponent,
    ExportDialogComponent,
    ExportTypeComponent,
  ],
  entryComponents:[
    ExportDialogComponent,
    ExportTypeComponent,
  ]
})
export class PropertyExportModule { }
