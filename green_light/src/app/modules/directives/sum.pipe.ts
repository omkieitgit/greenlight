import { Pipe, PipeTransform } from '@angular/core';

@Pipe({
    name: 'sum'
})

export class SumPipe implements PipeTransform {
    
        transform(items: any[], attr: string): any {
                if(items){
                        return items.reduce((a, b) => parseFloat(a) + parseFloat(b[attr]?b[attr]:0), 0);
                }else{
                        return 0;
                }
        }

}