import { Pipe, PipeTransform } from '@angular/core';

@Pipe({
    name: 'filter'
})

export class FilterPipe implements PipeTransform {
    
        transform(items: any[], attr: any[]): any {
                
                let filterArray:any=[];
                items.forEach(item=>{
                        if(attr.includes(item.category_type)){
                                filterArray.push(item.total_amount[0]);
                        }
               })
                return filterArray;
        }

}