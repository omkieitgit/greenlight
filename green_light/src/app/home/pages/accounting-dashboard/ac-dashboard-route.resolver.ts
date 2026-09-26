import { Injectable } from '@angular/core';
import {StorageService } from '@shared-service/_services/storage.service'

import { Resolve,Router } from '@angular/router';

import { ActivatedRouteSnapshot } from '@angular/router';

@Injectable()
export class AcDashboardRouteResolver implements Resolve<any> {
  
  constructor( private router: Router,private storageService:StorageService) {}

  resolve(route: ActivatedRouteSnapshot) {

    let user_info=this.storageService.get('user_info');
    if(user_info && user_info['current_role']!='accounting' && user_info['current_role']!='admin'){
      this.router.navigate(['home/dashboard']);  
    }
        
  }
}