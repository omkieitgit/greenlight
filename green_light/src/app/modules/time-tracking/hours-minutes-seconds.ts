import { Injectable, Pipe } from '@angular/core';

@Pipe({
  name: 'hoursMinutesSeconds'
})
@Injectable()
export class HoursMinutesSeconds {

  transform(value, args?) {
    
    let hours = Math.floor(value / 3600);
    let minutes = Math.floor((value % 3600)/ 60);
    let seconds = Math.floor(value % 60);
   
    return hours + " hrs, " + minutes + " mins, " + seconds + " secs";

  }

}