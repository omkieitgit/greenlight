// Core Module
import { Router, NavigationEnd, ActivatedRoute } from '@angular/router';

import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { BrowserModule, Title }    from '@angular/platform-browser';
import { AppRoutingModule }        from './app-routing.module';
import { NgbModule }               from '@ng-bootstrap/ng-bootstrap';
import { NgModule, APP_INITIALIZER }                from '@angular/core';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
//import { MatSortModule, MatTableModule }    from '@angular/material';
import * as global from './config/globals';

// Main Component
import { AppComponent }          from './app.component';
// Component Module
 import { AgmCoreModule }        from '@agm/core';
import { LightboxModule } from 'ngx-lightbox';
import { EmbedVideo } from 'ngx-embed-video';

import { HttpClientModule, HTTP_INTERCEPTORS, HttpClient } from '@angular/common/http';
import {ExtendedHttpClientService,applicationHttpClientCreator} from './shared/_services/extened-http-client.service';
import { LoaderInterceptorService } from './shared/_services/loader-interceptor.service';
import { LoaderComponent }  from './home/pages/loader/loader';
// used to create fake backend
import { fakeBackendProvider } from './shared/_helpers';
import { AuthGuard } from './shared/_guards';
import { AlertService, AuthenticationService, UserService,CommonActivityService,CommonApplicationService,CommunicationService} from './shared/_services';
import {RecaptchaModule,RecaptchaFormsModule} from 'ng-recaptcha';
// Pages
import { DeviceDetectorModule } from 'ngx-device-detector';

import { AppInitService } from './app-init.service';
export function initializeApp1(appInitService: AppInitService) {
  return (): Promise<any> => { 
    return appInitService.Init();
  }
}
@NgModule({
  declarations: [
    AppComponent,
    LoaderComponent,
  ],
  imports: [
    AppRoutingModule,
    AgmCoreModule.forRoot({ apiKey: 'AIzaSyC5gJ5x8Yw7qP_DqvNq3IdZi2WUSiDjskk' }),
    BrowserAnimationsModule,
    BrowserModule,
    FormsModule,
    NgbModule,
    ReactiveFormsModule,
    HttpClientModule,
    LightboxModule,
    EmbedVideo.forRoot(),
    DeviceDetectorModule.forRoot(),
    RecaptchaModule,
    RecaptchaFormsModule

  ],
  providers: [ 
    Title,
    AuthGuard,
    AlertService,
    AuthenticationService,
    UserService,
    CommonApplicationService,
    CommonActivityService,
    CommunicationService,
    {
          provide: HTTP_INTERCEPTORS,
          useClass: LoaderInterceptorService,
          multi: true,
    },
    fakeBackendProvider,
    { provide: ExtendedHttpClientService, useClass: ExtendedHttpClientService },
    // To provide the extended new HttpClient modules
    {
      provide: ExtendedHttpClientService,
      useFactory: applicationHttpClientCreator,
      deps: [HttpClient]
    },
    AppInitService,
    { provide: APP_INITIALIZER,
      useFactory: initializeApp1, 
      deps: [AppInitService], 
      multi: true
    }
  ],
  bootstrap: [ AppComponent ]
})

export class AppModule {
  // constructor(private router: Router, private titleService: Title, private route: ActivatedRoute) {
  //   router.events.subscribe((e) => {
  //     if (e instanceof NavigationEnd) {
  //       var title = 'Color Admin | ' + this.route.snapshot.firstChild.data['title'];
  //       this.titleService.setTitle(title);
  //     }
  //   });
  // }
}
