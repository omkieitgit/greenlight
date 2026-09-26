import { Component, OnInit,Input,OnChanges } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { StorageService } from '../../../../../shared/_services/storage.service';

@Component({
  selector: 'app-incidental-costs',
  templateUrl: './incidental-costs.component.html',
  styleUrls: ['./incidental-costs.component.css']
})
export class IncidentalCostsComponent implements OnInit,OnChanges {

  property_config:any;
  sub_category:any;
  @Input() property_id:string;
  loading:boolean=false;
  totalAmount:number=0;

  @Input() incidentalInfo;
  @Input() clientRenovation;
  @Input() openPanel;

  constructor() { 

              }

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
    this.incidentalInfo.forEach(info=>{
       this.totalAmount=this.totalAmount+parseFloat(info.amount);
    })
   }

   isValidURL(string) {
    var res = string.match(/(http(s)?:\/\/.)?(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)/g);
    return (res !== null)
  };


}
