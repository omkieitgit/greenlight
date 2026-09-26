import { Injectable } from '@angular/core';
import {StorageService} from './shared/_services/storage.service';

import { Resolve,Router,NavigationEnd } from '@angular/router';

import { ActivatedRouteSnapshot } from '@angular/router';
import { environment } from 'environments/environment';
import { apiUrl, CommonApplicationService } from '@shared-service/_services';

@Injectable()
export class EsResolver implements Resolve<any> {

  
  constructor(private storageService: StorageService, private router: Router,private commonApplicationService: CommonApplicationService) {}
  
  resolve(route: ActivatedRouteSnapshot) {
        let token=this.storageService.get('token');
        if(!token){
          this.storageService.setHard("referal_url",route['_routerState']['url']);
          this.storageService.removeHardKey('token_detail'); 
          window.location.href=environment.base_url+'/login';  
        }
  }
}