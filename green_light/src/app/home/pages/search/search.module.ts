import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { NgbModule }  from '@ng-bootstrap/ng-bootstrap';
import { ReactiveFormsModule } from '@angular/forms';

import {MatFormFieldModule}  from '@angular/material/form-field';
import {MatInputModule}  from '@angular/material/input';
import {MatIconModule}  from '@angular/material/icon';
import {MatAutocompleteModule}  from '@angular/material/autocomplete';
import {MatButtonModule}  from '@angular/material/button';
import {MatProgressSpinnerModule}  from '@angular/material/progress-spinner';
import {MatListModule}  from '@angular/material/list';

import { MyDatePickerModule } from 'mydatepicker';

import {SearchRoutingModule} from './search.routing.module';
import {AttentionWarningModule} from '../../../modules/attention-warning/attention-warning.module';
import {LoaderModule} from '../../../modules/loader/loader.module';
import {PropertyButtonModule} from '../../../modules/property-button/property-button.module';
import {SharedComponentsModule} from '../../../modules/shared-components/shared-components.module';
import {DirectivesModule} from '../../../modules/directives/directives.module';

import {SearchComponent}       from './search.component';
import {QuickSearchComponent}  from './quicksearch/quicksearch';
import {AdvancedSearchComponent} from './advancedsearch/advancedsearch';
import { SearchDetailComponent } from './search-detail/search-detail.component';
//import { SearchResultComponent } from './search-result/search-result.component';

@NgModule({
  imports: [
    CommonModule,
    NgbModule,
    ReactiveFormsModule,
    MatFormFieldModule,
    MatInputModule,
    MatIconModule, 
    MatAutocompleteModule, 
    MatButtonModule, 
    MatProgressSpinnerModule,
    MatListModule,
    MyDatePickerModule,
    
    SearchRoutingModule,
    AttentionWarningModule,
    LoaderModule,
    PropertyButtonModule,
    SharedComponentsModule,
    DirectivesModule
  ],
  declarations: [
    SearchComponent,
    QuickSearchComponent,
    AdvancedSearchComponent,
    SearchDetailComponent
  ]
})
export class SearchModule { }
