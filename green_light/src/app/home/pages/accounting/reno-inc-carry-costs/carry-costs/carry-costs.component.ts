import { Component, OnInit, Input, OnChanges } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { StorageService } from '../../../../../shared/_services/storage.service';

@Component({
  selector: 'app-carry-costs',
  templateUrl: './carry-costs.component.html',
  styleUrls: ['./carry-costs.component.css']
})
export class CarryCostsComponent implements OnInit,OnChanges {

  @Input() property_id:string;
  loading:boolean=false;
  property_config:any;
  sub_category:any;
  submitted:boolean=false;
  totalAmount:number=0;
  @Input() carryInfo;
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
    this.carryInfo.forEach(info=>{
       this.totalAmount=this.totalAmount+parseFloat(info.amount);
    })
   }
   isValidURL(string) {
    var res = string.match(/(http(s)?:\/\/.)?(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)/g);
    return (res !== null)
  }
}
