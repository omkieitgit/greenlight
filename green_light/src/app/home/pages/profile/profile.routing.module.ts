import { NgModule, Component } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { CommonModule } from '@angular/common';

import {ProfileComponent} from './profile.component';
import {ChangePasswordComponent} from './change-password/change-password.component';
import { PropertyInviteSettingComponent } from './property-invite-setting/property-invite-setting.component';
import {ChangeRoleComponent} from './change-role/change-role.component';
import {WorkprofileComponent} from './workprofile/workprofile.component';
import { SubtoInviteSettingComponent } from './subto-invite-setting/subto-invite-setting.component';


const routes: Routes = [
        { path: '', component: ProfileComponent, data: { title: 'Profile'}},
        { path: 'property_invite_setting',  component:PropertyInviteSettingComponent},
        { path:'profile',component:ProfileComponent, data: { title: 'Profile'}},
        { path: 'change-password', component: ChangePasswordComponent, data: { title: 'Change Password'} },
        {path: 'change-role',component:ChangeRoleComponent,data:{title:'Change Role'}},
        {path: 'workprofile',component:WorkprofileComponent,data:{title:'Workprofile'}}, 
        { path: 'subto-setting',  component:SubtoInviteSettingComponent},
                       
];

@NgModule({
  imports: [CommonModule,RouterModule.forChild(routes)],
  exports: [RouterModule],
  providers: [
  ],
})


export class ProfileRoutingModule { }
