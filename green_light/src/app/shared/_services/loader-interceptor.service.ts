import { Injectable, NgZone } from '@angular/core';
import { HttpEvent, HttpInterceptor, HttpHandler, HttpRequest, HttpResponse, HttpHeaders } from '@angular/common/http';
import { Observable, pipe } from 'rxjs';
import { tap } from 'rxjs/operators';
import {StorageService} from './storage.service';
import { LoaderService } from './loader.service';
import { Router, ActivatedRouteSnapshot } from '@angular/router';
import {BootController} from '../../../boot-control';
import { environment } from 'environments/environment';

@Injectable({
  providedIn: 'root'
})
export class LoaderInterceptorService implements HttpInterceptor {

  constructor(private loaderService: LoaderService, 
              private storageService:StorageService,
              private router: Router,
              //private route: ActivatedRouteSnapshot,
              private ngZone: NgZone) { }
              
  intercept(req: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
    this.showLoader();
    let token=this.storageService.get("token");
    if(token !== undefined){
        var headers = new Headers();
       
        if(req.url.indexOf('hereapi.com') !== -1 || req.url.indexOf('api.here.com') !== -1){
           var reqData = req.clone();
        }else{
           var reqData = req.clone({
            headers: new HttpHeaders({
                'X-Requested-With': 'XMLHttpRequest',
                'Authorization':token
            })
          });
        }

        
        return next.handle(reqData).pipe(tap((event: HttpEvent<any>) => { 
          if (event instanceof HttpResponse) {
            this.onEnd();
          }
        },
          (err: any) => {
            console.log("status>>"+err.status);
            this.onEnd();
            if((err.status == 440 || err.status == 401) &&  req.url.indexOf('api.here.com') === -1){
              this.ngZone.runOutsideAngular(() => BootController.getbootControl().restart());
              if(!this.storageService.getHard('referal_url'))
              {
                console.log('Referal URL==>'+this.router.url);
                this.storageService.setHard("referal_url",this.router.url);
              }    
              this.storageService.removeHardKey('token_detail'); 
              window.location.href=environment.base_url+'/login';
              //window.location.href=this.router.navigate(['/login']);
              return false;
            }            
        }));
    }
  }
  private onEnd(): void {
    this.hideLoader();
  }
  private showLoader(): void {
    this.loaderService.show();
  }
  private hideLoader(): void { 
    this.loaderService.hide();
  }
}