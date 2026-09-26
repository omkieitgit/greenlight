import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';


/*ES Modules*/
import { PanelModule }       from '../../../components/panel/panel.module';
import {AlertModule}            from "../../../alert.module";
import {PropertyButtonModule} from '@shared-modules/property-button/property-button.module';
import {DirectivesModule} from '@shared-modules/directives/directives.module';
import {AttentionWarningModule} from '@shared-modules/attention-warning/attention-warning.module';
import {LoaderModule} from '@shared-modules/loader/loader.module';
import {ShowdetailRoutingModule} from './showdetail.routing.module';
import {ShowdetailMenuModule} from '@shared-modules/showdetail-menu/showdetail-menu.module';
import {SharedComponentsModule} from '@shared-modules/shared-components/shared-components.module';


/* Show detail component*/
import {Showdetail}  from '../showdetail/showdetail';


@NgModule({
  declarations: [
    Showdetail
  ],

  imports: [
    CommonModule,

    ShowdetailRoutingModule,
    PropertyButtonModule,
    DirectivesModule,
    AttentionWarningModule,
    LoaderModule,
    ShowdetailMenuModule,
    SharedComponentsModule
  ]
})
export class ShowdetailModule { }
