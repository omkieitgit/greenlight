import { Component, OnInit,Input } from '@angular/core';

@Component({
  selector: 'app-propety-amount',
  templateUrl: './propety-amount.component.html',
  styleUrls: ['./propety-amount.component.css']
})
export class PropetyAmountComponent implements OnInit {

  @Input() wholesale_buyer_n_total;
  
  constructor() { }

  ngOnInit() {
  }

}
