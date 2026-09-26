import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import {HomeRoutingModule} from "./home-routing.module";
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import * as global from '../config/globals';
import { BrowserModule, Title }    from '@angular/platform-browser';

// Main Component
import {HomeComponent}           from "./home.component";
import { HeaderComponent }       from '../components/header/header.component';
import { TopMenuComponent }      from '../components/top-menu/top-menu.component';
import { FooterComponent }       from '../components/footer/footer.component';
import { SidebarComponent }       from '../components/sidebar/sidebar.component';


// Component Module
import { NgbModule }            from '@ng-bootstrap/ng-bootstrap';

import { CalendarModule }       from 'angular-calendar';
//import { FullCalendarModule }   from 'ng-fullcalendar';
//import { FullCalendarModule } from '@fullcalendar/angular'; // must go before plugins


//import { Ng2TableModule }       from 'ngx-datatable/ng2-table';
import {NgxDatatableModule} from '@swimlane/ngx-datatable';
import { PanelModule }      from '../components/panel/panel.module';
import {AlertModule}        from "../alert.module";

import {MatDialogModule,}       from '@angular/material/dialog';
import { MatSortModule } from '@angular/material/sort';
import {MatTableModule}  from '@angular/material/table';
import {MatFormFieldModule}  from '@angular/material/form-field';
import {MatInputModule}  from '@angular/material/input';
import {MatIconModule}  from '@angular/material/icon';
import {MatAutocompleteModule}  from '@angular/material/autocomplete';
import {MatButtonModule}  from '@angular/material/button';
import {MatProgressSpinnerModule}  from '@angular/material/progress-spinner';
import {MatListModule}  from '@angular/material/list';
import {MatChipsModule}  from '@angular/material/chips';



import {AutosizeModule} from 'ngx-autosize';
import {AutocompleteLibModule} from 'angular-ng-autocomplete';
//import { TagInputModule } from 'ngx-chips';


import {PropertyButtonModule} from '@shared-modules/property-button/property-button.module';
import {CommonNotesModule} from '@shared-modules/common-notes/common-notes.module';
import {DirectivesModule} from '@shared-modules/directives/directives.module';
import {AttentionWarningModule} from '@shared-modules/attention-warning/attention-warning.module';
import {PropertySearchModule} from '@shared-modules/property-search/property-search.module';
import {LoaderModule} from '@shared-modules/loader/loader.module';
import {DocumentModule} from '@shared-modules/document/document.module';
import {ShowdetailMenuModule} from '@shared-modules/showdetail-menu/showdetail-menu.module';
import {SharedComponentsModule} from '@shared-modules/shared-components/shared-components.module';
import {PropertyExportModule} from '@shared-modules/property-export/property-export.module';

// Pages
import { QuickViewComponent }  from './pages/quick-view/quick-view.component';
import { SpinputComponent }    from './pages/spinput/spinput.component';
import {Dashboard} from './pages/dashboard/dashboard';
//Services

import { CommonApplicationService } from '../shared/_services';
import { CommonActivityService } from '../shared/_services';

import { HttpClientModule, HTTP_INTERCEPTORS, HttpClient } from '@angular/common/http';
import {ExtendedHttpClientService,applicationHttpClientCreator} from '../shared/_services/extened-http-client.service';
import { LoaderInterceptorService } from '../shared/_services/loader-interceptor.service';
// used to create fake backend
import { fakeBackendProvider } from '../shared/_helpers';
import { AuthGuard } from '../shared/_guards';
import { AlertService, AuthenticationService, UserService } from '../shared/_services';
import {FileUploadModule} from 'primeng/fileupload';
import { MyDatePickerModule } from 'mydatepicker';
import {NgxPaginationModule} from 'ngx-pagination'; // <-- import the module
import { HomebuyerDashboardComponent } from './pages/homebuyer-dashboard/homebuyer-dashboard.component';


import { DataComponent } from './pages/quick-view/data/data.component';
import { MyFavouriteComponent } from './pages/my-favourite/my-favourite.component';
import { BuyItListComponent } from './pages/buy-it-list/buy-it-list.component';
import { QuickinputComponent } from './pages/quickinput/quickinput.component';

import {CurrencyPipe} from '@angular/common';

import { PropetyAmountComponent } from './pages/propety-amount/propety-amount.component';
import { HomebuyerPropertyDetailComponent } from './pages/homebuyer-dashboard/homebuyer-property-detail/homebuyer-property-detail.component';
import {HomebuyerPropertyService} from './pages/homebuyer-dashboard/homebuyer-property.service';

import { AddPropertyComponent } from './pages/quickinput/add-property/add-property.component';
import { CommonHelper } from '../shared/_utils/CommonHelper';

import { DatePipe } from '@angular/common';
import { EsGuideComponent } from './pages/es-guide/es-guide.component';
import { TruncatePipe } from '@shared-modules/directives/truncate.pipe';
import { EsGuideDetailComponent } from './pages/es-guide/es-guide-detail/es-guide-detail.component';


@NgModule({

  imports: [
    HomeRoutingModule,
    CommonModule,
    HttpClientModule,
    CalendarModule,
    
    FormsModule,
    NgbModule,
    ReactiveFormsModule,
    //SlimLoadingBarModule.forRoot(),
    MatSortModule,
    MatTableModule,
    //Ng2TableModule,
    FileUploadModule,
    AlertModule,
    MyDatePickerModule,
    MatDialogModule,
    NgxPaginationModule,
    MatInputModule,
    MatAutocompleteModule,
    MatFormFieldModule,
    MatButtonModule,
    MatProgressSpinnerModule,
    AutosizeModule,
    AutocompleteLibModule,
    //TagInputModule,
    MatListModule,
    MatIconModule,
    NgxDatatableModule,
    
    PropertyButtonModule,
    CommonNotesModule,
    PanelModule,
    DirectivesModule,
    AttentionWarningModule,
    PropertySearchModule,
    LoaderModule,
    DocumentModule,
    MatChipsModule,
    ShowdetailMenuModule,
    SharedComponentsModule,
    PropertyExportModule
  ],
  declarations: [
    
    HomeComponent,
    HeaderComponent,
    TopMenuComponent,
    FooterComponent,
    QuickViewComponent,
    SpinputComponent,
    Dashboard,
    HomebuyerDashboardComponent,
    DataComponent,
    MyFavouriteComponent,
    BuyItListComponent,
    QuickinputComponent,
    PropetyAmountComponent,
    HomebuyerPropertyDetailComponent,
    AddPropertyComponent,
    EsGuideComponent,
    EsGuideDetailComponent,
    SidebarComponent
  ],
  
  providers: [ 
    DatePipe,
    CommonApplicationService,
    CommonActivityService,
    Title,
    AuthGuard,
    AlertService,
    AuthenticationService,
    UserService,
    CommonHelper,
  //  { provide: HTTP_INTERCEPTORS, useClass: JwtInterceptor, multi: true },
    //{ provide: HTTP_INTERCEPTORS, useClass: ErrorInterceptor, multi: true },
    // provider used to create fake backend
    fakeBackendProvider,
    { provide: ExtendedHttpClientService, useClass: ExtendedHttpClientService },
    // To provide the extended new HttpClient modules
    {
      provide: ExtendedHttpClientService,
      useFactory: applicationHttpClientCreator,
      deps: [HttpClient]
      },
      {
            provide: HTTP_INTERCEPTORS,
            useClass: LoaderInterceptorService,
            multi: true,
      },
      CurrencyPipe,
      TruncatePipe,
      //PropertyAcquisitionService,
      HomebuyerPropertyService,
  ]
})
export class HomeModule {
  
}
