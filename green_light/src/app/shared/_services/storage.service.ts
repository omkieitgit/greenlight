import { Injectable} from '@angular/core';

@Injectable({
    providedIn: 'root'
})
export class StorageService {

    constructor() {}

    dataStore = {};
    storage = localStorage;
    storageHard = sessionStorage;
    //storage = this.dataStore;

    prefix: string = 'es';

    set(key, value) {
        this.storage[`${this.prefix}-${key}`] = JSON.stringify(value);
        // this.storage.setItem(`${this.prefix}-${key}`, JSON.stringify(value));
    }

    get(key) {
        let obj = this.storage[`${this.prefix}-${key}`];
        if (obj) {
            return JSON.parse(this.storage[`${this.prefix}-${key}`]);
        }
        return false;
        // return JSON.parse(this.storage.getItem(`${this.prefix}-${key}`));
    }

    setHard(key, value) {
        this.storageHard[`${this.prefix}-${key}`] = JSON.stringify(value);
        // this.storage.setItem(`${this.prefix}-${key}`, JSON.stringify(value));
    }

    removeHardKey(key){
        this.storageHard.removeItem(`${this.prefix}-${key}`);
    }

    getHard(key) {
        let obj = this.storageHard[`${this.prefix}-${key}`];
        if (obj) {
            return JSON.parse(this.storageHard[`${this.prefix}-${key}`]);
        }
        return false;
        // return JSON.parse(this.storage.getItem(`${this.prefix}-${key}`));
    }

    update(key, modifyKey, value) {
        if (!value) {
            value = null;
        }
        let obj = this.storage[`${this.prefix}-${key}`];
        if (obj) {
            obj = (JSON.parse(obj));
            let objectString = "obj" + modifyKey + "=" + value;
            eval(objectString);


            this.storage[`${this.prefix}-${key}`] = JSON.stringify(obj);
        } else {
            return false;
        }
    }

    updateKey(key, modifyKey, value) {
        if (!value) {
            value = null;
        }
        let obj = this.storage[`${this.prefix}-${key}`];
        obj = (JSON.parse(obj));
        obj[`${modifyKey}`] = value;
        this.storage[`${this.prefix}-${key}`] = JSON.stringify(obj);
    }

    remove(key){
        this.storage.removeItem[`${this.prefix}-${key}`];
    }
}