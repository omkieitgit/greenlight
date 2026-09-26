import { Injectable } from '@angular/core';
import { DatePipe } from '@angular/common';

@Injectable({
  providedIn: 'root'
})
export class AmortizationService {
  final_result:any;
  mod_result:any;
  amortizationInfo:any=[];

  constructor(private datePipe:DatePipe) { }

  amortization_calculate(data){
    
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
    // $("#tbl_int_pge").html(int_pge.toFixed(2)+" %");
    //$("#tbl_loan_pge").html((100-int_pge.toFixed(2))+" %");

    var emi_str = emi?emi.toFixed(2).toString().replace(/,/g, ""):'';
    var loanAmount_str = loanAmount.toString().replace(/,/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    var full_str = full.toFixed(2).toString().replace(/,/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    var int_str = interest.toFixed(2).toString().replace(/,/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    
    //$('#' + lien + 'Lien_mo').val(emi_str);

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
    // detailDesc += "</tbody>";
    var calculatedDate = 0;
    if (str_date != "")
    {
        var d = new Date(str_date);
        d.setMonth(d.getMonth() - 10);
        var back_date = (d.getMonth() + 1) + '/' + d.getDate() + '/' + d.getFullYear();
        calculatedDate = this.diff_months((back_date), currentDate);
    }
    else if ((_nos_date != "" && typeof _nos_date !=='undefined') && str_date=="")
    {
        var d = new Date((_nos_date));
        d.setMonth(d.getMonth() - 14);
        var back_date = (d.getMonth() + 1) + '/' + d.getDate() + '/' + d.getFullYear();
        calculatedDate = this.diff_months((back_date), currentDate);

    }
    else if (_sale_date != ""  && str_date=="")
    {
        var d = new Date((_sale_date));
        d.setMonth(d.getMonth() - 18);
        var back_date = (d.getMonth() + 1) + '/' + d.getDate() + '/' + d.getFullYear();
        calculatedDate = this.diff_months((back_date), currentDate);
    }

    if (isNaN(int_dd) || int_dd < 0)
    { // Willow ask to add condition if it is negative
        var int_dd = 0;
    }
    if (isNaN(pre_dd))
    {
        var pre_dd = 0;
    }
    if (isNaN(end_dd) || end_dd < 0)
    { // Willow ask to add condition if it is negative
        var end_dd = 0;
    }

    var attonyFee = 2000;
    var estimated_payment = (calculatedDate) * parseFloat(emi_str) + (attonyFee);
    
    //var cma_cva = cma_arv.replace(/,/g, "").replace('$', "");

    var Lien_late_attorney_fee = (end_dd) + (estimated_payment);
    var est_equity = parseFloat(cma_arv) - (Lien_late_attorney_fee);

   // if(data.no_str_no_appt != true)
    //{
      this.final_result={monthly_interest:int_dd?int_dd.toFixed(2):0,
                        monthly_principle:pre_dd?pre_dd.toFixed(2):0,
                        late_attorney_fee:!data.no_str_no_appt?(Lien_late_attorney_fee?Lien_late_attorney_fee.toFixed(2):0):0,
                        est_equity:est_equity?est_equity.toFixed(2):0,
                        estimated_payment:!data.no_str_no_appt?(estimated_payment?estimated_payment.toFixed(2):0):0,
                        est_loan_balance:end_dd?end_dd.toFixed(2):0,
                        loan_term:numberOfMonths?Math.round(numberOfMonths / 12):0,
                        monthly_payment:emi_str?emi_str:0
                        };
   // }

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

  viewAmortization(data){
    var loanAmount =data.lien_amount?data.lien_amount:0;
    var date1 = data.date_recorded?data.date_recorded:'';
    var date2 = data.maturity_date?data.maturity_date:'';
    var str_date =  data.str_date?data.str_date:'';
    var _sale_date=data._sale_date?data._sale_date:'';


    var datediff = this.diff_months(date1, date2);

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
    var loanAmount_str = loanAmount.toString().replace(/,/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    var full_str = full.toFixed(2).toString().replace(/,/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    var int_str = interest.toFixed(2).toString().replace(/,/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",");
   

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

    var current_date = (currentDate.getMonth() + 1) + '/' + currentDate.getDate() + '/' + currentDate.getFullYear();


    var bb = parseInt(loanAmount);
    var currentDate = new Date();
    var int_dd = 0;
    var pre_dd = 0;
    var end_dd = 0;
    var month_names_short = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    
    for (var j = 0; j <= numberOfMonths; j++)
    {

        var jan312009 = new Date(date1);
        var eightMonthsFromJan312009 = jan312009.setMonth(jan312009.getMonth() + j);
        var year = new Date(eightMonthsFromJan312009);
        //console.log();
        int_dd = bb * ((rateOfInterest / 100) / 12);
        pre_dd = emi - int_dd;
        end_dd = bb - pre_dd;
        bb = bb - pre_dd;
        this.amortizationInfo.push({'year':year.getUTCFullYear(),
                                    'month':month_names_short[year.getMonth()],
                                    'bb':bb?bb.toFixed(2):0,
                                    'emi':emi?emi.toFixed(2):0,
                                    'pre_dd':pre_dd?pre_dd.toFixed(2):0,
                                    'int_dd':int_dd?int_dd.toFixed(2):0,
                                    'end_dd':end_dd?end_dd.toFixed(2):0
                                });
    }

    return this.amortizationInfo;
   

  }

  get_checked_info(check_by,checkType){
    var checkHtml='';
    if(check_by){
        for(var i=0; i<check_by.length; i++){
            if(check_by[i].check_type==checkType){
                checkHtml+='<span>'+check_by[i].user.first_name+' -'+ this.datePipe.transform(check_by[i].created_at,'MM/dd/yyyy, h:mm a')+'</span>';
            }
        }
    }
    return checkHtml;
  }



  modification_calculate(data){
   
    var loanAmount =data.modification_lien_amount?data.modification_lien_amount:0;
    var date1 = data.modification_date?data.modification_date:'';
    var date2 = data.modification_maturity_date?data.modification_maturity_date:'';
    var str_date =  data.str_date?data.str_date:'';
    var cma_arv=data.cma_arv?data.cma_arv:0;
    var _sale_date=data.sale_date?data.sale_date:'';
    var _nos_date=data.nos_date?data.nos_date:'';;
    var rateOfInterest = data.modification_annual_interest?data.modification_annual_interest:0;


    var datediff = this.diff_months(date1, date2);

    var numberOfMonths = datediff;
   
    var monthlyInterestRatio = (rateOfInterest / 100) / 12;

    var top = Math.pow((1 + monthlyInterestRatio), numberOfMonths);
    var bottom = top - 1;
    var sp = top / bottom;
    var emi = ((loanAmount * monthlyInterestRatio) * sp);
    var full = numberOfMonths * emi;
    var interest = full - loanAmount;
    var int_pge = (interest / full) * 100;
    var emi_str = emi?emi.toFixed(2).toString().replace(/,/g, ""):'';
    if(emi_str=="" && emi_str===undefined){
        emi_str='0';
    }
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
    else if (_nos_date && str_date=="")
    {
        var d = new Date(_nos_date);
        d.setMonth(d.getMonth() - 14);
        var back_date = (d.getMonth() + 1) + '/' + d.getDate() + '/' + d.getFullYear();
        calculatedDate = this.diff_months(back_date, currentDate);

    }
    else if (_sale_date  && str_date=="")
    {
        var d = new Date(_sale_date);
        d.setMonth(d.getMonth() - 18);
        var back_date = (d.getMonth() + 1) + '/' + d.getDate() + '/' + d.getFullYear();
        calculatedDate = this.diff_months(back_date, currentDate);
    }

    var attonyFee = 2000;
    var estimated_payment = calculatedDate * parseFloat(emi_str) + attonyFee;

    
    this.mod_result={est_late_payment_fee:!data?.no_str_no_appt?(estimated_payment?estimated_payment.toFixed(2):0):0,
                    mod_month_payment:emi_str?emi_str:0,
                    mod_loan_term:Math.round(numberOfMonths / 12),
                    loan_est_balance:end_dd?end_dd.toFixed(2):0,
                    };
    
    return this.mod_result;

  }
}


