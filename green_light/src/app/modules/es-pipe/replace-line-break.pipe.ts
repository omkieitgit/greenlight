import { Pipe, PipeTransform } from '@angular/core';

@Pipe({
  name: 'replaceLineBreak'
})
export class ReplaceLineBreakPipe implements PipeTransform {

  transform(value: any, ...args: any[]): any {
    return value.replace(/\n/g, '<br/>');
  }

}
