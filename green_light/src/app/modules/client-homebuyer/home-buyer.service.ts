import { Injectable } from '@angular/core';
import {CurrencyFormatPipe } from '@shared-modules/directives/currency-format.pipe';
import {BehaviorSubject} from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class HomeBuyerService {

  private total_a_to_b = new BehaviorSubject<any>(0);

  constructor(  private cp:CurrencyFormatPipe) { }

  calculationDiff(est_value,act_value,diff_value){
    let cal_val=this.cp.detransform({'est_value':est_value.value,'act_value':act_value.value,'diff_value':diff_value.value});
    let diff_val=cal_val.est_value-cal_val.act_value;
    diff_value.value=this.cp.transformVal(diff_val);
    est_value.value=this.cp.transformVal(cal_val.est_value);
    //act_value.value=this.cp.transformVal(cal_val.act_value);

    if(diff_val>0){
      diff_value.classList.remove('text-danger');
      diff_value.className= diff_value.className.concat(" text-success");
    }else{
      diff_value.classList.remove('text-success');
      diff_value.className= diff_value.className.concat(" text-danger");
    }
  }

  calculationPercent(percent,fieldVal,cma_arv){
    let cal_val=this.cp.detransformVal(cma_arv.value);
    var per_val = (percent.value*cal_val)/100;
    fieldVal.value=this.cp.transformVal(per_val);;
  }

  calculateTotalCost(className){
    let total_cost=0;
    const cost_b_to_c=document.querySelectorAll("."+className);
    [].forEach.call(cost_b_to_c, (e)=>{
       const estVal=this.cp.detransformVal(e.value);
       if(estVal){
        total_cost += parseFloat(estVal);
       }
     });
     return total_cost;
  }

  setTotalAtoB(totalAtoB){
    this.total_a_to_b.next(totalAtoB);
  }

  getTotalAtoB(){
    return this.total_a_to_b.asObservable();
  }

  
}
