/**
 * Created on 09/09/18 new HttpClient class extended with token function.
 */
import { HttpClient, HttpErrorResponse, HttpHeaders, HttpParams } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable} from 'rxjs';

export interface IRequestOptions {
headers?:HttpHeaders;
observe?: 'body';
params?:HttpParams;
reportProgress?:boolean;
responseType?: 'json';
withCredentials?:boolean;
body?: any;
}

export function applicationHttpClientCreator(http: HttpClient) {
  return new ExtendedHttpClientService(http);
}

@Injectable()

export class ExtendedHttpClientService {

  // Extending the HttpClient through the Angular DI.
  public constructor(public http: HttpClient) {}

  /**
   * GET request
   * @param {string} endPoint, it doesn't need / in front of the end point
   * @returns {Observable<Object>}
   */
  public get<T>(endPoint: string, options?: IRequestOptions): Observable<T> {
          //console.log('New HttpClient Get Call');
          return this.http.get<T>(endPoint, options);
                //    .catch((err: HttpErrorResponse) => {
                //         // The response body may contain clues as to what went wrong,
                //         //console.log(`Error Code ${err.status}, Body Is: ${err.error}`);
                //         return Observable.throw(err);
                //     });
  }

  /**
   * POST request
   * @param {string} The end point of the api
   * @param {Object} The body of the request.
   * @param {IRequestOptions} the options of the request like headers, body, etc.
   * @returns {Observable<Object>} The response.
   */
  public post<T>(endPoint: string, params: Object, options?: IRequestOptions): Observable<T> {
            //console.log('New HttpClient Post Call');
            return this.http.post<T>(endPoint, params, options);
        //     .catch((err: HttpErrorResponse) => {
        //         // The response body may contain clues as to what went wrong,
        //         //console.log(`Error Code ${err.status}, Body Is: ${err.error}`);
        //         return Observable.throw(err);
        //     });
  }

  /**
   * PUT request
   * @param {string} The end point of the api
   * @param {Object} The body of the request.
   * @param {IRequestOptions} the options of the request like headers, body, etc.
   * @returns {Observable<Object>} The response.
   */
  public put<T>(endPoint: string, params: Object, options?: IRequestOptions): Observable<T> {
            //console.log('New HttpClient puT Call');
            return this.http.put<T>(endPoint, params, options);
        //     .catch((err: HttpErrorResponse) => {
        //         // The response body may contain clues as to what went wrong,
        //         //console.log(`Error Code ${err.status}, Body Is: ${err.error}`);
        //         return Observable.throw(err);
        //     });
  }

  /**
   * DELETE request
   * @param {string} The end point of the api
   * @param {Object} The body of the request.
   * @param {IRequestOptions} the options of the request like headers, body, etc.
   * @returns {Observable<Object>} The response.
   */
  public delete<T>(endPoint: string, params: Object, options?: IRequestOptions): Observable<T> {
            //console.log('New HttpClient Post Call');
            return this.http.delete<T>(endPoint, params);
        //     .catch((err: HttpErrorResponse) => {
        //         // The response body may contain clues as to what went wrong,
        //         //console.log(`Error Code ${err.status}, Body Is: ${err.error}`);
        //         return Observable.throw(err);
        //     });
  }

getRefreshToken(): Observable<string> {
          return this.http.get('/token', {responseType: 'text'})
  }

}//End of class
