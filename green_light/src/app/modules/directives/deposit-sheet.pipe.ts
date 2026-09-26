import { Pipe, PipeTransform } from '@angular/core';

@Pipe({
  name: 'depositSheet'
})
export class DepositSheetPipe implements PipeTransform {

  transform(value: any, ...args: any[]): number {
      
    if(args[0]=='checksum'){
      let total=0;
      value.forEach(element => {
            total+=+element.amount;
      });
      return total;
    }
    else if(args[0]=='totalChecksum'){
      let total=0;
      value.forEach(element => {
        element.deposit_lender.forEach(ele => {
            total+=+ele.amount;
        });
      });
      return total;

    }
    else if(args[1]=='grandTotal'){
        let total=0;
        value.forEach(element => {
          element.deposit_lender.forEach(ele => {
            if(ele.lender_id==args[0]){
              total+=+ele.amount;
            }
          });
        });
        return total;

      }else{
        let itemIndex = value.findIndex(item => item.lender_id == args[0]);
        if(itemIndex >= 0){
          return value[itemIndex].amount;
        }else{
          return 0;
        }
      }
     
  }

}
