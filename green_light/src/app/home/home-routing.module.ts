import { NgModule, Component } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { CommonModule } from '@angular/common';

//import { HomePage } from '../pages/home/home';
import { HomeComponent } from "./home.component";
import { Dashboard } from './pages/dashboard/dashboard';
import { SpinputComponent } from './pages/spinput/spinput.component';


import {HomebuyerDashboardComponent} from './pages/homebuyer-dashboard/homebuyer-dashboard.component';
import { QuickViewComponent } from './pages/quick-view/quick-view.component';

import {MyFavouriteComponent} from './pages/my-favourite/my-favourite.component';
import {BuyItListComponent} from './pages/buy-it-list/buy-it-list.component';
import {QuickinputComponent} from './pages/quickinput/quickinput.component';
import {ShowAlarmComponent} from './pages/alarm/show-alarm/show-alarm.component';
//import {CalendarPage} from './pages/alarm/calendar/calendar';

import {HomeResolver} from './home-route.resolver';
import { EsGuideComponent } from './pages/es-guide/es-guide.component';

const routes: Routes = [
    
  { path: '', component: HomeComponent, 
    children: [{ path: '',  component:Dashboard , resolve: { message: HomeResolver }},
    			     { path: 'dashboard', component: Dashboard, data: { title: 'Dashboard'}  , resolve: { message: HomeResolver }},
               { path: 'guide', component: EsGuideComponent, data: { title: 'Es-guide'}  , resolve: { message: HomeResolver }},

               { path: 'spinput', component: SpinputComponent, data: { title: 'Sp Input'}  , resolve: { message: HomeResolver }},
            
               { path: 'quickinput', component: QuickinputComponent, data: { title: 'Quick-input'}  , resolve: { message: HomeResolver }},
               { path: 'quickinput/:property_id/:slug_address', component: QuickinputComponent, data: { title: 'Quick-input'}  , resolve: { message: HomeResolver }},
              
               { path: 'homebuyer', component: HomebuyerDashboardComponent, data: { title: 'Home buyer Page'}  , resolve: { message: HomeResolver }},
              // { path: 'quick-view/:token', component: QuickViewComponent, data: { title: 'Quick View'} },
               { path: 'quick-view/:property_id/:slug_address', component: QuickViewComponent, data: { title: 'Quick View'}  , resolve: { message: HomeResolver }},
               { path: 'my-favourite', component: MyFavouriteComponent, data: { title: 'My Favourite'}  , resolve: { message: HomeResolver }},
               //{ path: 'buy-it', component: BuyItListComponent, data: { title: 'Buy It List'}  , resolve: { message: HomeResolver }},
               
               
               //{path :'calendar',component:CalendarPage , resolve: { message: HomeResolver }},
               { 
                path: 'alarm', 
                loadChildren: './pages/alarm/alarm.module#AlarmModule', 
                data: { title: 'Alarm Page'}  ,
                //resolve: { message: HomeResolver }
                },
               { 
                  path: 'showdetail/:property_id/:slug_address', 
                  loadChildren: './pages/showdetail/showdetail.module#ShowdetailModule', 
                  data: { title: 'Search Page'}  ,
                  resolve: { message: HomeResolver }
                },
                { 
                  path: 'showdetail/:property_id', 
                  loadChildren: './pages/showdetail/showdetail.module#ShowdetailModule', 
                  data: { title: 'Search Page'}  ,
                  resolve: { message: HomeResolver }
                },
               { 
                path:'search',
                loadChildren: './pages/search/search.module#SearchModule', 
                data: { title: 'search'}, 
                resolve: { message: HomeResolver }
               },
               { 
                  path:'profile',
                  loadChildren: './pages/profile/profile.module#ProfileModule', 
                  data: { title: 'Profile'}, 
                  resolve: { message: HomeResolver }
                },
              
              //  { 
              //    path:'is_agree',
              //    loadChildren:'../agreement/agreement.module#AgreementModule', 
              //   },
                 { 
                  path:'report',
                  loadChildren: './pages/report/report.module#ReportModule',
                  resolve: { message: HomeResolver } 
                 },
                { 
                  path:'accounting',
                  loadChildren: './pages/accounting-dashboard/accounting-dashboard.module#AccountingDashboardModule',
                  resolve: { message: HomeResolver } 
                 },
                 { 
                  path:'vehicle',
                  loadChildren: './pages/vehicle/vehicle.module#VehicleModule',
                  resolve: { message: HomeResolver } 
                 }
                 
    ]
  }
  
  
  //{ path: 'dashboard/v1', component: DashboardV1Page, data: { title: 'Dashboard V1'} },
];

@NgModule({
  imports: [CommonModule,RouterModule.forChild(routes)],
  exports: [RouterModule],
  providers: [
    HomeResolver
  ],
})



export class HomeRoutingModule { }
