import { Injectable } from '@angular/core';
import { Observable, Subject } from 'rxjs';

@Injectable({ providedIn: 'root' })
export class MessageService {
    private subject = new Subject<any>();

    sendMessage(message: string) {
        this.subject.next({ text: message });
    }

    clearMessage() {
        this.subject.next();
    }

    getMessage(): Observable<any> {
        return this.subject.asObservable();
    }

    sendArrData(data: any) {
        this.subject.next(data);
    }

    clearArrData() {
        this.subject.next();
    }

    getArrData(): Observable<any> {
        return this.subject.asObservable();
    }
}