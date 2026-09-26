import { Injectable } from '@angular/core';
import { Router, CanActivate, ActivatedRouteSnapshot, RouterStateSnapshot } from '@angular/router';
import { StorageService } from '../_services/storage.service';
@Injectable()

export class AuthGuard implements CanActivate {

    constructor(private router: Router, private storageService: StorageService) { }

    canActivate(route: ActivatedRouteSnapshot, state: RouterStateSnapshot) {
        let token = this.storageService.get('token');
        console.log("API tokent here"+token);
        if (token != "") {
            // logged in so return true
            return true;
        }

        // not logged in so redirect to login page with the return url
        this.router.navigate(['/login'], { queryParams: { returnUrl: state.url }});
        return false;
    }
}