import { Component, OnInit, Input,OnChanges } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { StorageService } from '../../../../../shared/_services/storage.service';


@Component({
  selector: 'app-renovation-costs',
  templateUrl: './renovation-costs.component.html',
  styleUrls: ['./renovation-costs.component.css']
})
export class RenovationCostsComponent implements OnInit,OnChanges {

  @Input() property_id:string;
  property_config:any;
  sub_category:any;
  loading:boolean=false;
  totalAmount:number=0;
  @Input() renovationInfo;
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
    this.renovationInfo.forEach(info=>{
       this.totalAmount=this.totalAmount+parseFloat(info.amount);
    })
   }

   isValidURL(string) {
    var res = string.match(/(http(s)?:\/\/.)?(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)/g);
    return (res !== null)
  }

}
