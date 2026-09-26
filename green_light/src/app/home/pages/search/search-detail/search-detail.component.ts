import { Component, OnInit,Input } from '@angular/core';

@Component({
  selector: 'app-search-detail',
  templateUrl: './search-detail.component.html',
  styleUrls: ['./search-detail.component.css']
})
export class SearchDetailComponent implements OnInit {

  @Input() searchResult:any;
  @Input() loading:boolean;
  @Input() totalRecord:number;
  @Input() searchField:any;
  @Input() limit:number;
  @Input() map_data:any;
  constructor() { }

  ngOnInit() {
  }

}
