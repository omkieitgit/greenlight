import { NgModule, Component } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { CommonModule } from '@angular/common';
import { ShowAlarmComponent } from './show-alarm/show-alarm.component';


const routes: Routes = [
        // { 
        //   path: '', component: AlarmCalendarComponent, 
        //   data: { title: 'Search'}  ,
        //   //resolve: { message: SearchResolver }
        // },   
        {path: '',component:ShowAlarmComponent,data:{title:'Show Alarm'} },
      
];

@NgModule({
  imports: [CommonModule,RouterModule.forChild(routes)],
  exports: [RouterModule],
  providers: [
    //SearchResolver
  ],
})


export class AlarmRoutingModule { }
