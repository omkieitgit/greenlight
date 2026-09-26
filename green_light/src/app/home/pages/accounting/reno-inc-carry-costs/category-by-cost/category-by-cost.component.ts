import { Component, Input, OnInit } from '@angular/core';

@Component({
  selector: 'app-category-by-cost',
  templateUrl: './category-by-cost.component.html',
  styleUrls: ['./category-by-cost.component.css']
})
export class CategoryByCostComponent implements OnInit {

  property_config:any;
  sub_category:any;
  @Input() property_id:string;
  loading:boolean=false;
  totalAmount:number=0;

  @Input() categoryByInfo;
  @Input() clientRenovation;
  @Input() openPanel;
  constructor() { }

  ngOnInit() {
    
    if(this.property_id !== undefined){
      this.getTotal();
    }
    
  }

  ngOnChanges(){
    if(this.clientRenovation){
      this.getTotal();
    }
  }

  getTotal()
  {
    this.totalAmount=0;
    this.categoryByInfo.forEach(info=>{
       this.totalAmount=this.totalAmount+parseFloat(info.total_amount);
    })
   }


}
