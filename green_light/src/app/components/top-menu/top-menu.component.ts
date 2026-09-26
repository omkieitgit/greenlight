import { group, animate, query, style, trigger, transition, state } from '@angular/animations';
import { Component, Output, EventEmitter, AfterViewInit, ViewChild } 		 from '@angular/core';
import pageSettings from '../../config/page-settings';
import pageMenus from '../../config/page-menus';
import * as global 			 from '../../config/globals';
import {CommunicationService, apiUrl, CommonApplicationService} from '../../shared/_services';
import {environment} from '../../../environments/environment';
import { StorageService } from '@shared-service/_services/storage.service';
@Component({
  selector: 'top-menu',
  templateUrl: './top-menu.component.html'
})

export class TopMenuComponent implements AfterViewInit {
  @ViewChild('topMenuContainer') topMenuContainer;

  menus = pageMenus;
  navControlLeft = false;
  navControlRight = false;
  navWidth = 0;
  navMarginLeft = 0;
  navMarginRight = 0;
  pageSettings = pageSettings;
  currentRole:any;
  user_roles:any;
  loader:boolean=false;
  
  constructor(private communicationService:CommunicationService,
              private storageService:StorageService,
              private commonApplicationService:CommonApplicationService) { 
    this.currentRole=this.storageService.get('user_info')['current_role'];
    this.user_roles=this.storageService.get('user_info')['user_roles'];
  }
  
  controlLeft() {
    var widthLeft = this.navMarginLeft;
    var containerWidth = this.topMenuContainer.nativeElement.clientWidth;
    var finalScrollWidth = 0;

    if (widthLeft <= containerWidth) {
      finalScrollWidth = 0;
      this.navControlLeft = false;
    } else {
      finalScrollWidth = widthLeft - containerWidth + 88;
    }

    if (!document.body.classList.contains('rtl-mode')) {
      this.navMarginLeft = finalScrollWidth;
      this.navMarginRight = 0;
      this.navControlRight = true;
    } else {
      this.navMarginRight = finalScrollWidth;
      this.navMarginLeft = 0;
      this.navControlRight = true;
    }
  }

  controlRight() {
    var containerWidth = this.topMenuContainer.nativeElement.clientWidth - 88;
    var widthLeft = this.navWidth + (-this.navMarginLeft) - containerWidth;
    var finalScrollWidth = 0;

    if (widthLeft <= containerWidth) {
      finalScrollWidth = widthLeft - (-this.navMarginLeft) + 128;
      this.navControlRight = false;
    } else {
      finalScrollWidth = containerWidth - (-this.navMarginLeft) - 128;
    }

    if (finalScrollWidth !== 0) {
      if (!document.body.classList.contains('rtl-mode')) {
        this.navMarginLeft = finalScrollWidth;
        this.navMarginRight = 0;
      } else {
        this.navMarginRight = finalScrollWidth;
        this.navMarginLeft = 0;
      }
      this.navControlLeft = true;
    }
  }

  ngAfterViewInit() {
    
    setTimeout(() => {
      var windowWidth = this.topMenuContainer.nativeElement.clientWidth - 128;
      var listFullWidth = 0;
      var listPrevWidth = 0;
      var listActive = false;

      const navList = Array.from(document.querySelectorAll('.top-menu .nav > li'));
      navList.forEach(function(list) {
        const elm = list as any;
        listFullWidth += elm.offsetWidth;
        listPrevWidth += (!listActive) ? elm.offsetWidth : 0;
        listActive = (elm.classList.contains('active')) ? true : listActive;
      });

      this.navWidth = listFullWidth;

      listPrevWidth = (!listActive) ? 0 : listPrevWidth;

      if (listPrevWidth >= windowWidth) {
          var finalScrollWidth = listPrevWidth - windowWidth + 128;
          if (!document.body.classList.contains('rtl-mode')) {
            this.navMarginLeft = finalScrollWidth;
            this.navMarginRight = 0;
          } else {
            this.navMarginRight = finalScrollWidth;
            this.navMarginLeft = 0;
          }
      }

      this.navControlRight = (listPrevWidth != listFullWidth && listFullWidth >= windowWidth) ? true : false;
      this.navControlLeft = (listPrevWidth >= windowWidth && listFullWidth >= windowWidth) ? true : false;
    });
  }

	expandCollapseSubmenu(currentMenu, allMenu, active) {
		if (currentMenu.state == 'expand' || (active.isActive && !currentMenu.state)) {
			currentMenu.state = 'collapse';
		} else {
			currentMenu.state = 'expand';
		}
  }
  
  saveSchoolDetails(event){
    this.loader=true;
    //console.log("savingdata>"+this.propertyForm);
    let input = new FormData();
    input.append("role_id", event.target.value);
    let url = apiUrl.change_role;
    this.commonApplicationService.post(url, input)
        .subscribe(
            data => {
              this.storageService.set("user_info",data.data);
              //this.alertService.success(data.message);  
              this.loader = false;
              window.location.reload();
              // this.router.navigateByUrl(this.router.url, {skipLocationChange: true}).then(() =>{
              //   this.router.navigate(['/home/dashboard']);
              // });
            },
            error => {
              //this.alertService.common(error); 
            }
        ); 
  }
}
