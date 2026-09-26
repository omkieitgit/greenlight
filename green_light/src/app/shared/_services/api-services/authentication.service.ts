import { Injectable } from '@angular/core';
import { HttpClient,HttpHeaders } from '@angular/common/http';
import { map } from 'rxjs/operators';
//import { base_url, auth_login,forgot_password } from '../../../config/api-url';
import { apiUrl } from '../../../config/api-url';

@Injectable()

export class AuthenticationService {

    constructor(private http: HttpClient ) { }
        

    login(email_id: string, password: string) {
       // let data={ email: email_id, password: password };
        return this.http.post<any>(apiUrl.auth_login, { email: email_id, password: password });
       // return this.http.post(base_url+auth_login,
        //    data,{ headers: headers});
    }

    logout() {
        // remove user from local storage to log user out
        localStorage.removeItem('currentUser');
    }

    forgot(email_id: string) {
        return this.http.post<any>(apiUrl.forgot_password, { email: email_id});
    }

    reset_password(content: any) {
        return this.http.post<any>(apiUrl.reset_password, content);
    }

}