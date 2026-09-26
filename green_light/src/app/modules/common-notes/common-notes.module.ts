import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import {MatDialogModule,}       from '@angular/material/dialog';
import {MatButtonModule}  from '@angular/material/button';

import {AutosizeModule} from 'ngx-autosize';

import {DirectivesModule} from '../directives/directives.module';
import {EsPipeModule} from '@shared-modules/es-pipe/es-pipe.module';

import {LoaderModule} from '../loader/loader.module';
import {CommonNotesComponent} from './common-notes.component';
import { CommonNotesDialogComponent } from './common-notes-dialog/common-notes-dialog.component';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    ReactiveFormsModule,
    MatDialogModule,
    MatButtonModule,
    AutosizeModule,
    DirectivesModule,
    EsPipeModule,
    LoaderModule
  ],
  
  declarations: [
    CommonNotesComponent, 
    CommonNotesDialogComponent
  ],
  entryComponents:[
    CommonNotesDialogComponent
  ],

  exports:[CommonNotesComponent]
})
export class CommonNotesModule { }
