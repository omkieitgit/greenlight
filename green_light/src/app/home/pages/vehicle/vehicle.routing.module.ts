import { NgModule, Component } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { CommonModule } from '@angular/common';

import {VehicleInputComponent} from './vehicle-input/vehicle-input.component';
import { VehicleDashboardComponent } from './vehicle-dashboard/vehicle-dashboard.component';
import { CreateVehicleComponent } from './create-vehicle/create-vehicle.component';


const routes: Routes = [
        { path: '', component: VehicleDashboardComponent},   
        { path: 'detail/:vehicle_id', component: VehicleInputComponent}, 
        { path: 'create', component: CreateVehicleComponent}        
       
];

@NgModule({
  imports: [CommonModule,RouterModule.forChild(routes)],
  exports: [RouterModule],
  providers: [
  ],
})


export class VehicleRoutingModule { }
