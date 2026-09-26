import { Injectable } from '@angular/core';
import {StorageService } from '../shared/_services/storage.service'

import { Resolve,Router } from '@angular/router';

import { ActivatedRouteSnapshot } from '@angular/router';

@Injectable()
export class HomeResolver implements Resolve<any> {
  constructor( private router: Router,private storageService:StorageService) {}

  resolve(route: ActivatedRouteSnapshot) {

    // let is_agree=this.storageService.get('is_agree');
    // let is_popup=this.storageService.get('is_popup');
    // let user_info=this.storageService.get('user_info');
    // if(user_info && (is_agree!='1' || is_popup!='1')){
    //   this.router.navigate(['home/is_agree']);  
    // }
        
  }
}