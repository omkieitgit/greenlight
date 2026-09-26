import { group, animate, query, style, trigger, transition, state } from '@angular/animations';
import { Component, Input, Output, EventEmitter, ElementRef, HostListener, ViewChild, OnInit, AfterViewChecked } 		 from '@angular/core';
import * as global 	from '../../config/globals';
import pageMenus from '../../config/page-menus';
import pageSettings from '../../config/page-settings';

@Component({
  selector: 'sidebar',
  templateUrl: './sidebar.component.html',
  animations: [
    trigger('expandCollapse', [
      state('expand', style({ height: '*', overflow: 'hidden', display: 'block' })),
      state('collapse', style({ height: '0px', overflow: 'hidden', display: 'block' })),
      state('active', style({ height: '*', overflow: 'hidden', display: 'block' })),
      transition('expand <=> collapse', animate(100)),
      transition('active => collapse', animate(100))
    ])
  ]
})

export class SidebarComponent implements AfterViewChecked {
  navProfileState = 'collapse';
  @ViewChild('sidebarScrollbar') private sidebarScrollbar: ElementRef;
	@Output() toggleSidebarMinified = new EventEmitter<boolean>();
	@Output() hideMobileSidebar = new EventEmitter<boolean>();
	@Input() pageSidebarTransparent;
	@Input() pageSidebarMinified;

	menus = pageMenus;
	pageSettings = pageSettings;

	mobileMode;
	desktopMode;
	scrollTop;

  toggleNavProfile() {
    if (this.navProfileState == 'collapse') {
      this.navProfileState = 'expand';
    } else {
      this.navProfileState = 'collapse';
    }
  }

	toggleMinified() {
		this.toggleSidebarMinified.emit(true);
		this.scrollTop = 0;
	}

	expandCollapseSubmenu(currentMenu, allMenu, active) {
		for (let menu of allMenu) {
			if (menu != currentMenu) {
				menu.state = 'collapse';
			}
		}
		if (currentMenu.state == 'expand' || (active.isActive && !currentMenu.state)) {
			currentMenu.state = 'collapse';
		} else {
			currentMenu.state = 'expand';
		}
	}

	@HostListener('document:click', ['$event'])
  clickout(event) {
    if(!this.eRef.nativeElement.contains(event.target)) {
		  this.hideMobileSidebar.emit(true);
    }
  }

  @HostListener('scroll', ['$event'])
  onScroll(event) {
    this.scrollTop = (this.pageSettings.pageSidebarMinified) ? event.srcElement.scrollTop + 40 : 0;
    if (typeof(Storage) !== 'undefined') {
      localStorage.setItem('sidebarScroll', event.srcElement.scrollTop);
    }
  }

  @HostListener('window:resize', ['$event'])
  onResize(event) {
    if (window.innerWidth <= 767) {
      this.mobileMode = true;
      this.desktopMode = false;
    } else {
      this.mobileMode = false;
      this.desktopMode = true;
    }
  }

  ngAfterViewChecked() {
    if (typeof(Storage) !== 'undefined' && localStorage.sidebarScroll) {
      if (this.sidebarScrollbar && this.sidebarScrollbar.nativeElement) {
        this.sidebarScrollbar.nativeElement.scrollTop = localStorage.sidebarScroll;
      }
    }
  }

  constructor(private eRef: ElementRef) {
    if (window.innerWidth <= 767) {
      this.mobileMode = true;
      this.desktopMode = false;
    } else {
      this.mobileMode = false;
      this.desktopMode = true;
    }
  }
}
