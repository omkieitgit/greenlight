
import {timer as observableTimer,  Subscription ,  Observable, Subject } from 'rxjs';
import { Component, OnInit, OnDestroy } from '@angular/core';
import { AlertService } from '../_services';

@Component({
    selector: 'alert',
    templateUrl: './alert.component.html',
  })

  
export class AlertComponent implements OnInit, OnDestroy {
    private subscription: Subscription;
    private timer: Observable<any>;
    message: any;

    constructor(private alertService: AlertService) { }

    ngOnInit() {
        console.log("Alert component has been initlilnzed***************************");
        this.subscription = this.alertService.getMessage().subscribe(message => { 
            this.message = message; 
            this.setTimer();
        });
    }

    ngOnDestroy() {
        this.subscription.unsubscribe();
    }

     public setTimer(){
        this.timer        = observableTimer(5000); // 5000 millisecond means 5 seconds
        this.subscription = this.timer.subscribe(() => {
           this.message = false;
        });
      }
}