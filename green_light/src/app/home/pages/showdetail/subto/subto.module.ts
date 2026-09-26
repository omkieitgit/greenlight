import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import {SubtoRoutingModule} from './subto.routing.module';

import { SubtoPropertyComponent } from './subto-property/subto-property.component';
import { CommonNotesModule } from '@shared-modules/common-notes/common-notes.module';
import { DocumentModule } from '@shared-modules/document/document.module';
import { ReactiveFormsModule } from '@angular/forms';



@NgModule({
  declarations: [
    SubtoPropertyComponent
  ],
  imports: [
    CommonModule,
    SubtoRoutingModule,
    CommonNotesModule,
    DocumentModule,
    ReactiveFormsModule

  ]
})
export class SubtoModule { }
