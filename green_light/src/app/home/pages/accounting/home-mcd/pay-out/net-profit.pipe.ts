import { Pipe, PipeTransform } from '@angular/core';
import { CommunicationService } from '@shared-service/_services';

@Pipe({
    name: 'netProfit'
})

export class NetprofitPipe implements PipeTransform {
        
        constructor(private communicationService:CommunicationService) { }
        
    
        transform(items: any[], attr: any[]): any {
                if(items){
                
                        let filterArray:any=[];
                        items.forEach(item=>{
                                if(attr.includes(item.cat_type)){
                                        filterArray.push(item);
                                }
                        })
                        return filterArray.reduce((a, b) => parseFloat(a) + parseFloat(b['actual_amount']), 0)
                       
                       
                        // let totalNetProfit= (  (
                        //         (items['totalSellingPriceBtoc']*1) - 
                        //         (items['totalPriceBtoC']*1) + 
                        //         (items['totalCreditReceivedBtoC']*1) +
                        //          (items['totalIncomeAdjustmentBtoC']*1)+
                        //          (items['totalOtherIncome']*1)
                        //          )-
                        //          ((items['totalPurchaseAmountAtoB']*1 )+ 
                        //          (items['totalAmountAtoB']*1 )+ 
                        //          (items['totalAmountCreditReceive']*1)+ 
                        //          (items['totalPurchaseAdjuestment']*1)+
                        //          (items['totalFeeAmount']*1)+ 
                        //          (items['totalNonHudAmount']*1))
                        // ).toFixed(2);

                        // if(attr!='beforeFinance'){
        
                        //         let netProfit=parseFloat(totalNetProfit)-items['totalFinancingCost'];
                        //         this.communicationService.setNetProfit(netProfit);
                        //         return netProfit;
                        // }else{
        
                        //         return totalNetProfit;    
                        // }
                            
                }
              

        }

}