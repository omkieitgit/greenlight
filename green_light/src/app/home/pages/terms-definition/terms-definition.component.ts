import { Component, OnInit } from '@angular/core';
import { Router,ActivatedRoute } from '@angular/router';

@Component({
  selector: 'app-terms-definition',
  templateUrl: './terms-definition.component.html',
  styleUrls: ['./terms-definition.component.css']
})
export class TermsDefinitionComponent implements OnInit {
  
  slug_address:any;
  property_id:any;

  constructor(private route: ActivatedRoute) { }

  ngOnInit() {
    this.property_id = this.route.snapshot.paramMap.get('property_id');
    this.slug_address = this.route.snapshot.paramMap.get('slug_address');
  }

}
