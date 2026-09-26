import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { MatDialogModule} from '@angular/material/dialog';

import {MatFormFieldModule }  from '@angular/material/form-field';
import {MatInputModule}  from '@angular/material/input';
import {MatIconModule}  from '@angular/material/icon';
import {MatAutocompleteModule}  from '@angular/material/autocomplete';
import {MatButtonModule}  from '@angular/material/button';
import {MatProgressSpinnerModule}  from '@angular/material/progress-spinner';
import {MatListModule}  from '@angular/material/list';
import {MatChipsModule}  from '@angular/material/chips';


@NgModule({
  declarations: [],
  imports: [
    CommonModule,
    MatFormFieldModule,
    MatInputModule,
    MatIconModule, 
    MatAutocompleteModule, 
    MatButtonModule, 
    MatProgressSpinnerModule,
    MatListModule,
    MatChipsModule,
    MatDialogModule,
    MatListModule,
  ],
  exports:[
    CommonModule,
    MatFormFieldModule,
    MatInputModule,
    MatIconModule, 
    MatAutocompleteModule, 
    MatButtonModule, 
    MatProgressSpinnerModule,
    MatListModule,
    MatChipsModule,
    MatDialogModule,
    MatListModule, 
  ]
})
export class MaterialLibModule { }
