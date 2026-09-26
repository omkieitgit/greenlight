import { Injectable } from '@angular/core';
import { DatePipe } from '@angular/common';
@Injectable()
export class CommonHelper {
    static filterUserName(userInfo: Object) { 
        
        if(userInfo && userInfo['user'] != null)
        {
            return (userInfo['user']['first_name']?userInfo['user']['first_name']:'')
            + " " + (userInfo['user']['first_name']?userInfo['user']['last_name']:'')
            
        }
        else
        {
            return '_ _';
        }

    }

    static getConvertDate(date){
        
        if(date.indexOf(' ')!==-1){
            date=date.split(' ')[0];
        }
        let checkDate=new Date(date);
        if (Object.prototype.toString.call(checkDate) === "[object Date]") {
            if (isNaN(checkDate.getTime())) { 
                 //let usaTime = new Date(date).toUTCString(); 
                 console.log(date+' USA time: ' + date); 
                 return new Date();
            } 
            else {
                 let usaTime = new Date(date+'T08:00:00').toUTCString(); 
                 console.log(date+' USA date: ' + usaTime); 
                 return new Date(usaTime);
            }
          } 
          else {
            return null;
          }
    }

    static dateFormate(date){
        let dObj = null;
        if(date !== undefined && date != null){
              let dDate = this.getConvertDate(date);
              let formated_date=dDate.getMonth() + 1+'/'+dDate.getDate()+'/'+dDate.getFullYear();
              dObj = {
                date: {
                    year: dDate.getFullYear(),
                    month: dDate.getMonth() + 1,
                    day: dDate.getDate()},
                    formatted:formated_date
                }
        }else{
            dObj = null;
        }
        return dObj;
    }

    static convertInt(val){
        return Math.floor(val);
    }
}