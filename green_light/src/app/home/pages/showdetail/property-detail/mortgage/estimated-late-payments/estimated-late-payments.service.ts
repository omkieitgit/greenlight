import { Injectable } from '@angular/core';
import { Router, NavigationStart } from '@angular/router';
import { Observable, Subject } from 'rxjs';

@Injectable()
export class EstimateLatePaymentService {

    private subject = new Subject<any>();
    final_result:any;

    constructor(private router: Router) {
       
    }

    calculateEstimate(data){

        var loanAmount =data.lien_amount?data.lien_amount:0;
        var date1 = data.date_recorded?data.date_recorded:'';
        var date2 = data.maturity_date?data.maturity_date:'';
        var str_date =  data.str_date?data.str_date:'';
        var cma_arv=data.cma_arv?data.cma_arv:0;
        var _sale_date=data.sale_date?data.sale_date:'';
        var _nos_date=data.sale_date?data.nos_date:'';

        var datediff = this.diff_months(date1,date2);
        var numberOfMonths = datediff;
        var rateOfInterest =data.annual_interest;
        var monthlyInterestRatio = (rateOfInterest / 100) / 12;

        var top = Math.pow((1 + monthlyInterestRatio), numberOfMonths);
        var bottom = top - 1;
        var sp = top / bottom;
        var emi = ((loanAmount * monthlyInterestRatio) * sp);
        var full = numberOfMonths * emi;
        var interest = full - loanAmount;
        var int_pge = (interest / full) * 100;
    
        var emi_str = emi?emi.toFixed(2).toString().replace(/,/g, ""):'';
    
        var bb = parseInt(loanAmount);
        var currentDate = new Date();
        var numberOfMonCurrent = this.diff_months(date1, currentDate);
        var int_dd = 0;
        var pre_dd = 0;
        var end_dd = 0;
        for (var j = 1; j <= numberOfMonCurrent; j++)
        {
            int_dd = bb * ((rateOfInterest / 100) / 12);
            pre_dd = emi - int_dd;
            end_dd = bb - pre_dd;
            bb = bb - pre_dd;
        }
        var calculatedDate = 0;
        if (str_date != "")
        {
            var d = new Date(str_date);
            d.setMonth(d.getMonth() - 10);
            var back_date = (d.getMonth() + 1) + '/' + d.getDate() + '/' + d.getFullYear();
            calculatedDate = this.diff_months(back_date, currentDate);
        }
         // if check is checked then take date from
         else if (_nos_date && str_date=="")
         {
             var d = new Date(_nos_date);
             d.setMonth(d.getMonth() - 14);
             var back_date = (d.getMonth() + 1) + '/' + d.getDate() + '/' + d.getFullYear();
             calculatedDate = this.diff_months(back_date, currentDate);
             str_date = _nos_date;

         }
         else if (_sale_date && str_date=="")
         {
             var d = new Date(_sale_date);
             d.setMonth(d.getMonth() - 18);
             var back_date = (d.getMonth() + 1) + '/' + d.getDate() + '/' + d.getFullYear();
             calculatedDate = this.diff_months(back_date, currentDate);
             str_date = _sale_date;
         }


        if (isNaN(int_dd))
        {
            var int_dd = 0;
        }
        if (isNaN(pre_dd))
        {
            var pre_dd = 0;
        }
        if (isNaN(end_dd))
        {
            var end_dd = 0;
        }

        var attonyFee = 2000;
        var estimated_payment = Number(calculatedDate) * parseFloat(emi_str) + Number(attonyFee);
        //estimated_payment = Number(estimated_payment).toFixed(2);

        var lien_late_attorney_fee = Number(end_dd) + Number(estimated_payment);
        var est_equity = parseFloat(cma_arv) - Number(lien_late_attorney_fee);

        var date = new Date();
        var current_date = (date.getMonth() + 1) + '/' + date.getDate() + '/' + date.getFullYear();
        
        this.final_result={str_date:str_date?str_date:null,
                            back_date:back_date?back_date:null,
                            current_date:current_date?current_date:null,
                            calculatedDate:calculatedDate?calculatedDate:null,
                            emi_str:emi_str?emi_str:0,
                            attonyFee:attonyFee?attonyFee:0,
                            end_dd:end_dd?end_dd.toFixed(2).toString().replace(/,/g, ""):0,
                            estimated_payment:estimated_payment?estimated_payment.toFixed(2).toString().replace(/,/g, ""):0,
                            cma_cva:cma_arv?cma_arv:0,
                            lien_late_attorney_fee:lien_late_attorney_fee?lien_late_attorney_fee.toFixed(2).toString().replace(/,/g, ""):0,
                            est_equity:est_equity?est_equity.toFixed(2).toString().replace(/,/g, ""):0
                        };
        
        return this.final_result;
    }


  diff_months(start_date, end_date)
  {
      start_date = new Date(start_date);
      end_date= new Date(end_date);

      if (start_date == "" || end_date == "")
          return 0;
     var total_months = (end_date.getFullYear() - start_date.getFullYear()) * 12 + (end_date.getMonth() + 1 - start_date.getMonth())
      //console.log(total_months);
      return total_months;
  }
}