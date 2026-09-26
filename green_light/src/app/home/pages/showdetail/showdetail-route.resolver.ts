import { Injectable } from '@angular/core';
import { Resolve,Router, ActivatedRoute } from '@angular/router';
import { ActivatedRouteSnapshot } from '@angular/router';

import {CommonApplicationService} from '@shared-service/_services';
import {StorageService} from '@shared-service/_services/storage.service';
import {apiUrl} from '@config/api-url';

@Injectable()
export class ShowdetailRouteResolver implements Resolve<any> {

  constructor( private router: Router,
                private activeRoute: ActivatedRoute,
               private storageService:StorageService,
               private commonApplicationService:CommonApplicationService) {}

  resolve(route: ActivatedRouteSnapshot, ) {
      
    let user_info=this.storageService.get('user_info');
        
    let property_id = route.params.property_id;
    if(!property_id){
      property_id=route.parent.params.property_id;
    }

    let url = apiUrl.home_common+'/'+property_id;
    this.commonApplicationService.get(url).subscribe(response =>{
        if(!response.row){
          this.storageService.setHard('isNotExist',true);
          this.router.navigate(['home/dashboard']);  
        }else{
          this.storageService.setHard('property_info',response.row);
        }
    },error=>{
      
    })
    // if(user_info && (user_info['current_role']=='home_buyer' || user_info['current_role']=='wholesale_buyer')){
    //     let property_id = route.params.property_id;
    //     let url = apiUrl.is_invited+'/'+property_id;
    //     this.commonApplicationService.post(url).subscribe(response =>{
    //           if(response.status!='success'){
    //             //this.router.navigate(['home/dashboard']);  
    //           } 
    //     },error=>{
  
    //     })
       
    // }
    
  }
}