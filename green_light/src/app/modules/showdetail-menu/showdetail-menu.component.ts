import { Component, OnInit, Input } from '@angular/core';
import { NgbTabChangeEvent } from '@ng-bootstrap/ng-bootstrap';
import { Router } from '@angular/router';

@Component({
  selector: 'app-showdetail-menu',
  templateUrl: './showdetail-menu.component.html',
  styleUrls: ['./showdetail-menu.component.css']
})
export class ShowdetailMenuComponent implements OnInit {

  @Input() property_id:any;
  @Input() slug_address:any;
  @Input() activeClass:string;
  
  First:string='First';
  Second:string='Second';


  constructor(private router:Router) { }

  ngOnInit() {
  }

  onTabChange($event: NgbTabChangeEvent) {
    if ($event.nextId === 'first') {
      this.router.navigateByUrl('/home/showdetail/'+this.property_id+'/'+this.slug_address);
    } else if ($event.nextId === 'second') {
      this.router.navigateByUrl('/home/showdetail/'+this.property_id+'/'+this.slug_address+'/accounting');
    }
  }

}
