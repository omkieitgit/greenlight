import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
//import { base_url, userDetail } from '../../../config/api-url';
import { apiUrl } from '../../../config/api-url';
import {StorageService} from '../storage.service';

import { User } from '../../_models';

@Injectable()
export class UserService {
    constructor(private http: HttpClient,private storageService:StorageService) { }

    getUserDetail(){
        let url =apiUrl.user_detail;
        return this.http.get(url);
    }

    getAll() {
        return this.http.get<User[]>(`/users`);
    }

    getById(id: number) {
        return this.http.get(`/users/` + id);
    }

    register(user: User) {
        return this.http.post(apiUrl.contact_us, user);
    }

    contact(user: User) {
        return this.http.post(apiUrl.contact_us, user);
    }

    checkout(user: User) {
        return this.http.post(`/users/checkout`, user);
    }

    update(user: User) {
        return this.http.put(`/users/` + user.id, user);
    }

    delete(id: number) {
        return this.http.delete(`/users/` + id);
    }
}