import { Component, OnInit, Input } from '@angular/core';

@Component({
  selector: 'app-mobile-loader',
  templateUrl: './mobile-loader.component.html',
  styleUrls: ['./mobile-loader.component.css']
})
export class MobileLoaderComponent implements OnInit {
 
  @Input() mobileLoader:boolean=false;
  
  constructor() { }

  ngOnInit() {
  }

}
