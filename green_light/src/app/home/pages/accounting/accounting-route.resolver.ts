import { Injectable } from '@angular/core';
import {StorageService } from '../../../shared/_services/storage.service'

import { Resolve,Router } from '@angular/router';

import { ActivatedRouteSnapshot } from '@angular/router';

@Injectable()
export class AccountingResolver implements Resolve<any> {

  accountingTabAccessRole:any=["wholesale_buyer","home_buyer","sub_to","admin","accounting"];
  viewOnlyAccess:any=["wholesale_buyer","home_buyer","sub_to"];

  constructor( private router: Router,private storageService:StorageService) {}

  resolve(route: ActivatedRouteSnapshot) {

    let user_info=this.storageService.get('user_info');
    if(user_info && this.accountingTabAccessRole.indexOf(user_info['current_role'])==-1){
      this.router.navigate(['home/dashboard']);  
    }else{
      if(this.viewOnlyAccess.indexOf(user_info['current_role'])!==-1){
        this.storageService.setHard('ac_view_access',true);
      }else{
        this.storageService.setHard('ac_view_access',false);
      }
    }
        
  }
}