import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule, Routes} from '@angular/router';
import {DirectivesModule} from '../../modules/directives/directives.module';

import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import {ShowdetailMenuComponent} from './showdetail-menu.component';

@NgModule({
  imports: [
    CommonModule,
    NgbModule,
    RouterModule,
    DirectivesModule
  ],
  declarations: [
    ShowdetailMenuComponent
  ],
  exports:[
    ShowdetailMenuComponent
  ]
})
export class ShowdetailMenuModule { }
