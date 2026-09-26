import { Component, OnInit,Inject } from '@angular/core';
import {AmortizationService} from '../amortization.service';
import {MatDialog, MatDialogRef, MAT_DIALOG_DATA} from '@angular/material/dialog';


@Component({
  selector: 'app-amortization',
  templateUrl: './amortization.component.html',
  styleUrls: ['./amortization.component.css']
})
export class AmortizationComponent implements OnInit {
  amortizationTable:any;
  offset: number = 1;
  totalRecord:number;
  searchField:any;
  limit:number=10;

  constructor(private amortizationService:AmortizationService,@Inject(MAT_DIALOG_DATA) public data,
  ) { }

  ngOnInit() {
    this.amortizationTable=this.amortizationService.viewAmortization(this.data);
    this.totalRecord=this.amortizationTable.length;
    console.log(this.amortizationTable); 
  }

}
