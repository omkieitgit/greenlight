import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import {AutocompleteLibModule} from 'angular-ng-autocomplete';
import { MyDatePickerModule } from 'mydatepicker';

import {PropertySearchComponent} from './property-search.component';

import {MatFormFieldModule}  from '@angular/material/form-field';
import {MatInputModule}  from '@angular/material/input';
import {MatIconModule}  from '@angular/material/icon';
import {MatAutocompleteModule}  from '@angular/material/autocomplete';
import {MatButtonModule}  from '@angular/material/button';
import {MatProgressSpinnerModule}  from '@angular/material/progress-spinner';
import {MatListModule}  from '@angular/material/list';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    ReactiveFormsModule,
    AutocompleteLibModule,
    MyDatePickerModule,
    
    MatAutocompleteModule,
    MatProgressSpinnerModule,
    MatIconModule,
    MatFormFieldModule,
    MatInputModule,
    MatListModule
  ],
  declarations: [
    PropertySearchComponent
  ],
  exports: [
    PropertySearchComponent
  ]
})
export class PropertySearchModule { }
