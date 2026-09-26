import { Component,Input, OnInit } from '@angular/core';
import { Router, NavigationEnd, ActivatedRoute } from '@angular/router';
import {breadcrumbName} from './breadcrumb';
@Component({
  selector: 'app-attention-warning',
  templateUrl: './attention-warning.component.html',
  styleUrls: ['./attention-warning.component.css']
})
export class AttentionWarningComponent implements OnInit {

  breadcrumb:string='';
  breadCrumbFlag:boolean=false;
  expand:boolean=false;
  constructor(private route: ActivatedRoute,
              private router: Router) {
   }

  ngOnInit() {
    
    this.route.url.subscribe((value)=>{
      if(value && value[0]){
        this.breadcrumb=breadcrumbName[value[0].path];
        this.breadCrumbFlag=true;
      }
    });
   
  }

  toggleList(expand){
    if(!expand)
      this.expand=true;
  }

}
