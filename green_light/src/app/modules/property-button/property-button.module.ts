import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import {MatDialogModule,} from '@angular/material/dialog';
import {MatButtonModule}  from '@angular/material/button';
import {AutosizeModule} from 'ngx-autosize';

import {DirectivesModule} from '../../modules/directives/directives.module';
import { RouterModule, Routes} from '@angular/router';


import { PropertyButtonComponent } from './property-button.component';
import { InstitutionalLenderComponent } from './institutional-lender/institutional-lender.component';
import { DepositeLenderComponent } from './deposite-lender/deposite-lender.component';
import { TitleSearchComponent } from './title-search/title-search.component';
import { GetPictureComponent } from './get-picture/get-picture.component';
import { BuyItComponent } from './buy-it/buy-it.component';
import { AreaInviteComponent } from './area-invite/area-invite.component';
import { PassOnItComponent } from './pass-on-it/pass-on-it.component';

import {AddMeBtnComponent} from './add-me-btn/add-me-btn.component';
import {AddMeComponent} from './add-me-btn/add-me/add-me.component';
import {AddToAlarmComponent} from './add-to-alarm/add-to-alarm.component';
import { WholesaleRetailComponent } from './wholesale-retail/wholesale-retail.component';
import { WholesaleRetailDialogComponent } from './wholesale-retail/wholesale-retail-dialog/wholesale-retail-dialog.component';
import {PropertyActionComponent} from './property-action/property-action.component';
import { SubToComponent } from './sub-to/sub-to.component';

@NgModule({
  imports: [
    CommonModule,
    FormsModule, 
    ReactiveFormsModule,
    MatDialogModule,
    MatButtonModule,
    AutosizeModule,
    DirectivesModule,
    RouterModule
  ],
  declarations: [
    PropertyButtonComponent,
    InstitutionalLenderComponent,
    DepositeLenderComponent,
    TitleSearchComponent,
    GetPictureComponent,
    BuyItComponent,
    AreaInviteComponent,
    PassOnItComponent,
    BuyItComponent,
    AreaInviteComponent,
    PassOnItComponent,
    AddMeBtnComponent,
    AddMeComponent,
    AddToAlarmComponent,
    WholesaleRetailComponent,
    WholesaleRetailDialogComponent,
    PropertyActionComponent,
    SubToComponent
  ],
  entryComponents: [
    InstitutionalLenderComponent,
    DepositeLenderComponent,
    TitleSearchComponent,
    GetPictureComponent,
    BuyItComponent,
    AreaInviteComponent,
    PassOnItComponent,
    BuyItComponent,
    AreaInviteComponent,
    PassOnItComponent,
    AddMeComponent,
    WholesaleRetailDialogComponent,
  ],
  exports:[
    PropertyButtonComponent,
    AddMeBtnComponent,
    AddToAlarmComponent,
    WholesaleRetailComponent,
    PropertyActionComponent
  ]
})
export class PropertyButtonModule { }
