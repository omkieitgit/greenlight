import { Component, HostListener, Renderer2, OnInit } from '@angular/core';
import { NavigationEnd, Router } from '@angular/router';
import { StorageService } from '@shared-service/_services/storage.service';
declare let ga: Function;

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})

export class AppComponent implements OnInit {

  constructor(private storage:StorageService,public router: Router) {}

    ngOnInit( ) {
      
      let userInfo=this.storage.get('user_info');
      // this.router.events.subscribe(event => {
      //   if (event instanceof NavigationEnd) {
      //     ga('_trackEvent', 'User List', userInfo?userInfo.id+'-'+userInfo.first_name+' '+userInfo.last_name:"none",event.urlAfterRedirects);
      //     ga('send', 'pageview');
      //   }
      // });
   }
}
