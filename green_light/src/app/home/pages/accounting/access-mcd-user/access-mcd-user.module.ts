import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { McdUserComponent } from './mcd-user/mcd-user.component';
import { LoaderModule } from '@shared-modules/loader/loader.module';
import { PanelModule } from 'app/components/panel/panel.module';
import { ReactiveFormsModule } from '@angular/forms';
import { CreateMcdUserComponent } from './mcd-user/create-mcd-user/create-mcd-user.component';
import { MaterialLibModule } from '@shared-modules/material-lib/material-lib.module';



@NgModule({
  declarations: [
    McdUserComponent,
    CreateMcdUserComponent
  ],
  imports: [
    CommonModule,
    LoaderModule,
    PanelModule,
    ReactiveFormsModule,
    MaterialLibModule,
  ],
  exports:[
    McdUserComponent
  ]
})
export class AccessMcdUserModule { }
