import { Pipe, PipeTransform } from '@angular/core';

@Pipe({
  name: 'depositLink'
})
export class DepositLinkPipe implements PipeTransform {

  transform(value: any, ...args: any[]): string {
      let itemIndex = value.findIndex(item => item.link_name == args[0]);
      if(itemIndex >= 0){
        return value[itemIndex].link?'<a  href="'+value[itemIndex].link+'" target="_blank">View</a>':'' ;
      }else{
        return '';
      }
  }

}
