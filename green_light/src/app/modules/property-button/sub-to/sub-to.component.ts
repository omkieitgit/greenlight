import { Component, OnInit, Inject } from '@angular/core';
import { apiUrl } from '@config/api-url';
import { CommonApplicationService } from '@shared-service/_services';
import { ActivatedRoute } from '@angular/router';
import {MatDialog, MAT_DIALOG_DATA, MatDialogRef} from '@angular/material/dialog';
import { StorageService } from '@shared-service/_services/storage.service';
import { CommonHelper } from '@shared-service/_utils/CommonHelper';

@Component({
  selector: 'app-sub-to',
  templateUrl: './sub-to.component.html',
  styleUrls: ['./sub-to.component.css']
})
export class SubToComponent implements OnInit {
 
  loader:boolean=false;
  property_id:any;
  propertyDetail:any;
  sale_type:any;

  constructor(private route: ActivatedRoute,
              private commonApplicationService:CommonApplicationService,
              @Inject(MAT_DIALOG_DATA) public data: any,
              private storageService:StorageService) { 

                this.property_id = this.data.property_id;
                this.getPropertyInfo();
              }

  ngOnInit(): void {
    this.sale_type =  this.storageService.get("property_config")['sale_type'];
  }

  getPropertyInfo(){
    let url = apiUrl.sub_to+'/'+this.property_id;
    this.commonApplicationService.get(url).subscribe(response =>{
        this.propertyDetail=response.row;
        this.propertyDetail.recommended_cma_arv=CommonHelper.convertInt(this.propertyDetail.recommended_cma_arv);
        this.propertyDetail.total_living_sqft=CommonHelper.convertInt(this.propertyDetail.total_living_sqft);
        this.propertyDetail.county_value=CommonHelper.convertInt(this.propertyDetail.county_value);
        
        this.loader=true;
    },error=>{
      this.loader=true;
    })
    
  }

}
