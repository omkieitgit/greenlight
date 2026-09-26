import { Pipe, PipeTransform } from '@angular/core';
@Pipe({name: 'replaceLinkFromTag'})
export class replaceLinkFromTag implements PipeTransform {
  transform(value: string): string {
        let replacePattern1 = /(\b(https?|ftp):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/gim;
        return value.replace(replacePattern1, '<a href="$1" target="_blank">$1</a>');
  }
}