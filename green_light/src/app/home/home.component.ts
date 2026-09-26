import { Component,NgZone, HostListener, Renderer2, OnInit,ViewEncapsulation } from '@angular/core';
import { Title }     from '@angular/platform-browser';
import { Router, NavigationEnd, NavigationStart, ActivatedRoute } from '@angular/router';
import pageSettings from '../config/page-settings';
import * as global from '../config/globals';
import { StorageService } from '../shared/_services/storage.service';
import { MatDialog } from '@angular/material/dialog';
import { AlarmMeComponent } from '@shared-modules/shared-components/alarm-me/alarm-me.component';
import { apiUrl } from '@config/api-url';
import { CommonApplicationService } from '@shared-service/_services';

@Component({
  selector: 'app-home',
  templateUrl: './home.component.html',
  encapsulation: ViewEncapsulation.None,
  styleUrls: ['./home.component.css','../../assets/css/default/style.min.css',
  '../../assets/css/default/style-responsive.min.css',
  '../../assets/css/default/theme/default.css']
})

export class HomeComponent implements OnInit {

  pageSettings;
  mobileLoader:boolean=false;
  ngZone: NgZone;
  buyerRole:any =["wholesale_buyer","home_buyer"];

  

  

	// window scqroll
  pageHasScroll;
  @HostListener('window:scroll', ['$event'])
  onWindowScroll($event) {
    var doc = document.documentElement;
    var top = (window.pageYOffset || doc.scrollTop)  - (doc.clientTop || 0);
    if (top > 0) {
      this.pageHasScroll = true;
    } else {
      this.pageHasScroll = false;
    }
  }

  // set page minified
  onToggleSidebarMinified(val: boolean):void {
  	if (this.pageSettings.pageSidebarMinified) {
  		this.pageSettings.pageSidebarMinified = false;
  	} else {
  		this.pageSettings.pageSidebarMinified = true;
    }
    this.storageService.set('hideSideBar',this.pageSettings.pageSidebarMinified);
	}

  // set page riqght collapse
  onToggleSidebarRight(val: boolean):void {
  	if (this.pageSettings.pageSidebarRightCollapsed) {
  		this.pageSettings.pageSidebarRightCollapsed = false;
  	} else {
  		this.pageSettings.pageSidebarRightCollapsed = true;
  	}
	}

  // hide mobile sidebar
  onHideMobileSidebar(val: boolean):void {
    if (this.pageSettings.pageMobileSidebarToggled) {
      if (this.pageSettings.pageMobileSidebarFirstClicked) {
        this.pageSettings.pageMobileSidebarFirstClicked = false;
      } else {
  		  this.pageSettings.pageMobileSidebarToggled = false;
      }
    }
	}

  // toggle mobile sidebar
  onToggleMobileSidebar(val: boolean):void {
    if (this.pageSettings.pageMobileSidebarToggled) {
  		this.pageSettings.pageMobileSidebarToggled = false;
    } else {
  		this.pageSettings.pageMobileSidebarToggled = true;
  		this.pageSettings.pageMobileSidebarFirstClicked = true;
    }
	}


  // hide right mobile sidebar
  onHideMobileRightSidebar(val: boolean):void {
    if (this.pageSettings.pageMobileRightSidebarToggled) {
      if (this.pageSettings.pageMobileRightSidebarFirstClicked) {
        this.pageSettings.pageMobileRightSidebarFirstClicked = false;
      } else {
  		  this.pageSettings.pageMobileRightSidebarToggled = false;
      }
    }
	}

  // toggle right mobile sidebar
  onToggleMobileRightSidebar(val: boolean):void {
    if (this.pageSettings.pageMobileRightSidebarToggled) {
  		this.pageSettings.pageMobileRightSidebarToggled = false;
    } else {
  		this.pageSettings.pageMobileRightSidebarToggled = true;
  		this.pageSettings.pageMobileRightSidebarFirstClicked = true;
    }
	}

  constructor(private titleService: Title, 
             //private slimLoadingBarService: SlimLoadingBarService , 
             private router: Router, 
             private renderer: Renderer2,
             private dialog:MatDialog,
             private commonApplicationService:CommonApplicationService,
             private storageService:StorageService) {
    router.events.subscribe((e) => {
			if (e instanceof NavigationStart) {
			  if (window.innerWidth < 768) {
			    this.pageSettings.pageMobileSidebarToggled = false;
			  }
				// if (e.url != '/') {
				// 	slimLoadingBarService.progress = 50;
				// 	slimLoadingBarService.start();
				// }
			}
			if (e instanceof NavigationEnd) {
				// if (e.url != '/') {
				// 	setTimeout(function() {
				// 		slimLoadingBarService.complete();
				// 	}, 300);
				// }
			}
    });

    this.router.events.subscribe((e) => {
      if (e instanceof NavigationStart) {
        this.mobileLoader=true;
      }
      if (e instanceof NavigationEnd) {
        this.mobileLoader=false;
      }
    });
  }
  ngOnInit() {
    // page settings
    this.pageSettings = pageSettings;

    if(this.storageService.get('hideSideBar')){
        this.pageSettings.pageSidebarMinified=true;
    }
    this.outSideAngularApp();
    let user_info=this.storageService.get('user_info');
    if(!this.storageService.getHard('alarmClosed') && user_info){
      this.getAlarmMe();
    }
  }

  getAlarmMe(){
    let url = apiUrl.alarmMe;
    this.commonApplicationService.get(url).subscribe(response => {
      if(response !== undefined && response?.data?.length>0){             
        this.dialog.open(AlarmMeComponent,{width:'800px',data:response.data});
      }else{
        this.storageService.setHard('alarmClosed',true);
      }
    },
      (err: any) => { })
  }
  outSideAngularApp(){
   // this.ngZone.runOutsideAngular(() => {
         var chatDiv = document.getElementById("chat-application");  
         if (chatDiv && chatDiv.style.display !== "none" && 
            
            (this.buyerRole.indexOf(this.storageService.get("user_info")['current_role']) === -1)
            ) {  
              chatDiv.style.display = "none";  
         }  
   // });

  }
}
