import { Injectable } from '@angular/core';
import {StorageService } from '../../../shared/_services/storage.service'

import { Resolve,Router } from '@angular/router';

import { ActivatedRouteSnapshot } from '@angular/router';

@Injectable()
export class SearchResolver implements Resolve<any> {
  constructor( private router: Router,private storageService:StorageService) {}

  resolve(route: ActivatedRouteSnapshot) {

    let user_info=this.storageService.get('user_info');
    if(user_info && (user_info['current_role']=='home_buyer' || user_info['current_role']=='wholesale_buyer')){
      this.router.navigate(['home/dashboard']);  
    }
        
  }
}