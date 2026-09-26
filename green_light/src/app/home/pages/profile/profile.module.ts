import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import {ProfileRoutingModule} from './profile.routing.module';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';


import {MatFormFieldModule}  from '@angular/material/form-field';
import {MatInputModule}  from '@angular/material/input';
import {MatIconModule}  from '@angular/material/icon';
import {MatAutocompleteModule}  from '@angular/material/autocomplete';
import {MatButtonModule}  from '@angular/material/button';
import {MatProgressSpinnerModule}  from '@angular/material/progress-spinner';
import {MatListModule}  from '@angular/material/list';
import {MatChipsModule}  from '@angular/material/chips';


import {DirectivesModule} from '../../../modules/directives/directives.module';
import {LoaderModule} from '../../../modules/loader/loader.module';
import {AttentionWarningModule} from '../../../modules/attention-warning/attention-warning.module';


import { ProfileComponent } from './profile.component';
import { ChangePasswordComponent } from './change-password/change-password.component';
import { PropertyInviteSettingComponent } from './property-invite-setting/property-invite-setting.component';
import { ChangeRoleComponent } from './change-role/change-role.component';
import { WorkprofileComponent } from './workprofile/workprofile.component';
import { SubtoInviteSettingComponent } from './subto-invite-setting/subto-invite-setting.component';


@NgModule({
  imports: [
    CommonModule,
    ProfileRoutingModule,
    FormsModule, 
    ReactiveFormsModule,
    /*Mat*/
    MatFormFieldModule,
    MatInputModule,
    MatIconModule, 
    MatAutocompleteModule, 
    MatButtonModule, 
    MatProgressSpinnerModule,
    MatListModule,
    MatChipsModule,
    
     /*Custom*/
     DirectivesModule,
     LoaderModule,
     AttentionWarningModule
  ],
  declarations: [
    ProfileComponent,
    ChangePasswordComponent,
    PropertyInviteSettingComponent,
    ChangeRoleComponent,
    WorkprofileComponent,
    SubtoInviteSettingComponent
  ]
})
export class ProfileModule { }
