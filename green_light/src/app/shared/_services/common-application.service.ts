import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
//import { base_url, property_info } from '../../config/api-url';
import { apiUrl } from '../../config/api-url';
import { ExtendedHttpClientService } from './extened-http-client.service';
import {Observable} from 'rxjs';
import {map, tap} from 'rxjs/operators';

@Injectable()
export class CommonApplicationService {
    constructor(private http: HttpClient,
                private httpClient: ExtendedHttpClientService) { }
    private body: any;

    get(url, data = {}){
       return this.httpClient.get<any>(url, this.body);
    }

    post(url, data = {}){ 
        return this.httpClient.post<any>(url, data);
    }

    postDownload(url, data = {}, options = {}){ 
        return this.httpClient.post<any>(url, data, options);
    }
    put(url, data = {}){
        return this.httpClient.put<any>(url, data);
    }

    delete(url, data = {}){
        return this.httpClient.delete<any>(url, data);
    }

    getSearch(url, data = {}){
        return this.httpClient.get<any>(url, data); 
    }

    search(url): Observable<any> {
        return this.http.get<any>(url)
        .pipe(
          tap((response: any) => {
            return response;
          })
          );
      }

}