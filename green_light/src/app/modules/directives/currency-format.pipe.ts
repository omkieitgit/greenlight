import { Pipe, PipeTransform } from '@angular/core';
import { CurrencyPipe } from '@angular/common';
@Pipe({
  name: 'currencyFormat'
})
export class CurrencyFormatPipe implements PipeTransform {

  
  transform(result: any, args?: any): any {
    Object.keys(result)
    .forEach(key => {
      if(result[key]!="" && result[key]!='house_id' && isNaN(result[key])===false && key.indexOf('percent')==-1){
        var cp = new CurrencyPipe("en-US");
        result[key]=cp.transform(result[key]);
      }
    });
    return result;
  } 

  detransform(result){
    Object.keys(result)
    .forEach(key => {
      if(result[key]!="" && result[key]!=null){
        result[key]=result[key].replace(/\$|,+/g, '');
      }
    });
    return result;
  }

  detransformVal(result){
    return result.replace(/\$|,+/g, '');;
  }

  transformVal(result: any, args?: any): any {
    
    if(isNaN(result)===false){
      var cp = new CurrencyPipe("en-US");
      result=cp.transform(result);
      return result;
    }
    
  } 

}
