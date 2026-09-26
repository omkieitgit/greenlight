import { NgModule } from '@angular/core';
import { CommonModule, DatePipe } from '@angular/common';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import {LoaderModule} from '../loader/loader.module';
import { MyDatePickerModule } from 'mydatepicker';
import {DirectivesModule} from '../directives/directives.module';

//import { Ng2TableModule } from 'ng2-table/ng2-table';

import { NgxDatatableModule } from '@swimlane/ngx-datatable';

import {NgxPaginationModule} from 'ngx-pagination';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';

import { DocumentComponent } from './document.component';
import { DocumentListComponent } from './document-list/document-list.component';

@NgModule({
  imports: [
    CommonModule,
    LoaderModule,
    FormsModule,
    ReactiveFormsModule,
    MyDatePickerModule,
    DirectivesModule,
    //Ng2TableModule,
    NgxDatatableModule,
    NgxPaginationModule,
    NgbModule
  ],
  declarations: [DocumentComponent, DocumentListComponent],
  exports:[DocumentComponent,DocumentListComponent],
  providers:[
    DatePipe
  ]

})
export class DocumentModule { }
