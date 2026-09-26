import { Component, OnInit } from '@angular/core';
import { CommonApplicationService,CommonActivityService,AlertService } from '../../../shared/_services';
import { apiUrl } from '../../../config/api-url';
import { StorageService } from '../../../shared/_services/storage.service';

@Component({
  selector: 'app-my-favourite',
  templateUrl: './my-favourite.component.html',
  styleUrls: ['./my-favourite.component.css']
})
export class MyFavouriteComponent implements OnInit {

  my_favourite_property:any;
  offset: number = 1;
  totalRecord:number;
  searchField:any;
  limit:number=10;
  loading:boolean=true;

  constructor(private storageService:StorageService,
              private alertService:AlertService,
              private commonApplicationService:CommonApplicationService) { }
  //favourite
  ngOnInit() {
    this.get_all_favourite();
  }

  get_all_favourite(){
    this.loading=false;
    let url = apiUrl.favourite+'all';
    this.commonApplicationService.get(url).subscribe(response => {
      if(response !== undefined){             
       this.my_favourite_property=response.data;        
       this.loading=true;
       this.totalRecord=response.total;                                                                                                
      }
    },
      (err: any) => {
        this.alertService.common(err);    
      })
  }

  


}
