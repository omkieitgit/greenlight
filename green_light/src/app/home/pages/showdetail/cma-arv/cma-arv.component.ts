import { Component,OnInit} from '@angular/core';
import { Router,ActivatedRoute } from '@angular/router';
import { apiUrl } from '../../../../config/api-url';
import { CommonApplicationService } from '../../../../shared/_services';
import { StorageService } from '../../../../shared/_services/storage.service';

@Component({
 selector: 'cma-arv',
 templateUrl: './cma-arv.html',
 })

export class CmaarvComponent  implements OnInit{
  
  slug_address:any;
  property_id:any;
  commanPropertyInfo:any;
  loadCmaArv:boolean=false;
  propertyInfo: any;

  constructor(private route: ActivatedRoute,
              private router: Router,
              private commonApplicationService:CommonApplicationService,
              private storageService:StorageService){}
 
  ngOnInit() {

    this.property_id = this.route.parent.snapshot.parent.params.property_id;
   // this.slug_address =this.route.parent.snapshot.parent.params.slug_address;
    this.getPropertyInfo();
  }

  getPropertyInfo(){
    //this.commanPropertyInfo= this.storageService.getHard('property_info');
    if(!this.commanPropertyInfo){
      let url = apiUrl.home_common+'/'+this.property_id;
      this.commonApplicationService.get(url).subscribe(response =>{
          this.storageService.setHard('property_info',response['row']);
          this.propertyInfo=response['row'];
          this.loadCmaArv=true;
      },error=>{
        this.loadCmaArv=true;
      })
    }
    
  }

}