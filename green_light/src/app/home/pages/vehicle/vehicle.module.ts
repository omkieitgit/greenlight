import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { VehicleInputComponent } from './vehicle-input/vehicle-input.component';
import {VehicleInfoComponent} from './vehicle-info/vehicle-info.component';
import {VehicleSaleInfoComponent} from './vehicle-sale-info/vehicle-sale-info.component';

import { VehicleRoutingModule } from './vehicle.routing.module';
import { AttentionWarningModule } from '@shared-modules/attention-warning/attention-warning.module';
import {DirectivesModule} from '@shared-modules/directives/directives.module';
import { PanelModule }      from '../../../components/panel/panel.module';
import { ReactiveFormsModule } from '@angular/forms';
import { MyDatePickerModule } from 'mydatepicker';
import { VehicleCmaArvComponent } from './vehicle-cma-arv/vehicle-cma-arv.component';
import { VehicleNosComponent } from './vehicle-nos/vehicle-nos.component';
import { VehicleDashboardComponent } from './vehicle-dashboard/vehicle-dashboard.component';
import { CreateVehicleComponent } from './create-vehicle/create-vehicle.component';


@NgModule({
  declarations: [
    VehicleInputComponent,
    VehicleInfoComponent,
    VehicleSaleInfoComponent,
    VehicleCmaArvComponent,
    VehicleNosComponent,
    VehicleDashboardComponent,
    CreateVehicleComponent
  ],
  imports: [
    CommonModule,
    VehicleRoutingModule,
    AttentionWarningModule,
    DirectivesModule,
    PanelModule,
    ReactiveFormsModule,
    MyDatePickerModule
  ]
})
export class VehicleModule { }
