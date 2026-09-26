import { Component, OnInit,Input } from '@angular/core';
import { CommonApplicationService,AlertService } from '@shared-service/_services';
import { apiUrl } from '@config/api-url';
import { StorageService } from '@shared-service/_services/storage.service';
import { Router,ActivatedRoute } from '@angular/router';

@Component({
  selector: 'app-favourite',
  templateUrl: './favourite.component.html',
  styleUrls: ['./favourite.component.css']
})
export class FavouriteComponent implements OnInit {
  @Input() isFavourite:boolean;
  loader:boolean=false;
  property_id:string;

  constructor(private route: ActivatedRoute,
    private storageService:StorageService,
    private alertService:AlertService,
    private commonApplicationService:CommonApplicationService) { }

  ngOnInit() {
    this.property_id = this.route.snapshot.paramMap.get('property_id');

  }


  add_favourite(){

    let url = apiUrl.favourite+'add/'+this.property_id;
    this.commonApplicationService.post(url).subscribe(response => {
      if(response !== undefined){             
        this.isFavourite=true;
        console.log(this.isFavourite);                                                                                                           
        this.alertService.success(response.message); 
      }
    },
      (err: any) => {
        this.alertService.common(err);    
      })
  }

  remove_favourite(){
    let url = apiUrl.favourite+'remove/'+this.property_id;
    this.commonApplicationService.post(url).subscribe(response => {
      if(response !== undefined){        
        this.isFavourite=false;                                                                                                           
        this.alertService.success(response.message); 
      }
    },
      (err: any) => {
        this.alertService.common(err);    
      })
  }

}
