import { Injectable } from '@angular/core';
import { Router, NavigationStart } from '@angular/router';
import { Observable, Subject } from 'rxjs';

@Injectable()
export class AlertService {
    private subject = new Subject<any>();
    private keepAfterNavigationChange = false;

    constructor(private router: Router) {
        // clear alert message on route change
        router.events.subscribe(event => {
            if (event instanceof NavigationStart) {
                if (this.keepAfterNavigationChange) {
                    // only keep for a single location change
                    this.keepAfterNavigationChange = false;
                } else {
                    // clear alert
                    this.subject.next();
                }
            }
        });
    }

    common(obj: any, keepAfterNavigationChange = false) {

        this.keepAfterNavigationChange = keepAfterNavigationChange;

        if(obj && obj.status && obj.status =='success'){
            this.subject.next({ type: 'success', text: [obj.message] });
        } 
        else if(obj && obj.status && obj.status =='failed'){
            // if object obj.message
            if(obj instanceof Object)
            {
                // if object obj.message
                if(obj.message instanceof Object)
                {
                    this.subject.next({ type: 'error', text: obj.message });
                }
                // If string obj.message
                else{
                    this.subject.next({ type: 'error', text: [obj.message] });
                }
            
            }
            // If string obj.message
            else
            {
                this.subject.next({ type: 'error', text: [obj] });
            }
        }
        // 404, 503, 502, 501
        else {
            // if object obj.error
            if(obj && obj.error instanceof Object)
            {
                // if object obj.error.message
                if(obj.error.message instanceof Object)
                {
                    this.subject.next({ type: 'error', text: obj.error.message });
                }
                // If string obj.error.message
                else{
                    this.subject.next({ type: 'error', text: [obj.error.message] });
                }
            
            }
            else if(typeof obj  === "string")
            {
                this.subject.next({ type: 'error', text: [obj] });
            }
            // If string obj.message
            else
            {
                this.subject.next({ type: 'error', text: [obj.error] });
            }
        }
    }
    success(message: string, keepAfterNavigationChange = false) {
        this.keepAfterNavigationChange = keepAfterNavigationChange;
        this.subject.next({ type: 'success', text: [message] });
    }

    error(message: any, keepAfterNavigationChange = false) {

        // check if object 
        // if(message instanceof Object)
        // {
        //     let msgString = '';
        //     if(message.message instanceof Object){
        //         for (const msg of message.message) { 
        //             msgString += '<p>'+msg+'</p>';
        //         }
        //     }

        //     this.keepAfterNavigationChange = keepAfterNavigationChange;
        //     this.subject.next({ type: 'error', text: msgString });
        // }
        // else
        // {
        //     this.keepAfterNavigationChange = keepAfterNavigationChange;
           
        // }
        console.log(message)
        if(message instanceof Object)
        {
            if(message.error && message.error  instanceof Object){
                this.subject.next({ type: 'error', text: message.error.message });
            }
            else
            this.subject.next({ type: 'error', text: message.message });
        }
      
        else
        {
            this.subject.next({ type: 'error', text: [message] });
        }
        
    }

    getMessage(): Observable<any> {
        return this.subject.asObservable();
    }
}