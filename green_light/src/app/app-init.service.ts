import { Injectable }  from '@angular/core';
import { StorageService } from '@shared-service/_services/storage.service';
import { environment } from 'environments/environment';
import { CommonApplicationService, apiUrl } from './shared/_services';

 
@Injectable()
export class AppInitService {
 
    constructor(private commonApplicationService:CommonApplicationService
                ,private storageService:StorageService) {
    }
    
    Init() {
 
        return new Promise<void>((resolve, reject) => {

                let token_detail=sessionStorage.getItem('es-token_detail');
                if(!token_detail){
                        let url = apiUrl.token;
                        this.commonApplicationService.get(url).subscribe(response => {
                                if(response.token && response.data){
                                        sessionStorage.setItem('es-token_detail','1');
                                        this.storageService.set("token", response.token);
                                        this.storageService.set("user_info",response.data);
                                        this.storageService.set("is_agree", response?.data?.is_agree);
                                        this.storageService.set("is_popup", response?.data?.is_popup);
                                        this.storageService.set("property_config", response.property_config);
                                }
                                resolve();
                        },
                        error => {
                                if (error.status >= 400) {
                                        window.location.href=environment.base_url+'/login';
                                }
                        });  
                }else{
                        resolve();     
                }
                 
        });
    }

    
}