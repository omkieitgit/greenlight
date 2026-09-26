import { Component, OnInit,Inject } from '@angular/core';
import {MAT_DIALOG_DATA,MatDialogRef} from '@angular/material/dialog';
import {PropertyDetailModel,PropertyInfoModel, PriceHistory} from './scrapper.model';
import { Router, ActivatedRoute } from '@angular/router';
import { CommonApplicationService,AlertService,CommonActivityService } from '@shared-service/_services';
import { apiUrl } from '@config/api-url';
import {IMyDpOptions, IMyDateModel} from 'mydatepicker';
import {DatePipe} from '@angular/common';
import { CommonHelper } from '@shared-service/_utils/CommonHelper';
@Component({
  selector: 'app-scrapper',
  templateUrl: './scrapper.component.html',
  styleUrls: ['./scrapper.component.css']
})
export class ScrapperComponent implements OnInit {
  
  scrapper_data:any;
  property_id:string;
  scrapper_info:any;
  property_info:PropertyInfoModel;
  price_history:PriceHistory;
  propertyDetail:PropertyDetailModel[];

  constructor(@Inject(MAT_DIALOG_DATA) public data: any,
  private commonApplicationService: CommonApplicationService,
  private commonActivityService: CommonActivityService,
  private alertService:AlertService,
  private router:Router,
  public dialogRef: MatDialogRef<ScrapperComponent>

  ) { 
    this.property_info=new PropertyInfoModel();
    this.price_history=new  PriceHistory();
    this.propertyDetail=new Array();
  }
  ngOnInit() {
    this.scrapper_data=this.data;
  }
  updateScrapperData(){
    let data=this.scrapper_model();
    this.dialogRef.close(data);
  }


  updateSchoolInfo(){

    let data={'house_id':this.scrapper_data.property_id,
              'elementary_school':this.scrapper_data.schools.elementary.name,
              'middle_school':this.scrapper_data.schools.middle.name,
              'high_school':this.scrapper_data.schools.high.name};

    let url = apiUrl.update_school+'/'+this.scrapper_data.property_id;
    this.commonApplicationService.put(url, data)
        .subscribe( data => {
            },
            error => {
              this.alertService.common(error);  
            }
        ); 
  }
  scrapper_model(){
    this.property_info.address=this.scrapper_data.full_address;
    this.property_info.state=this.scrapper_data.state;
    this.property_info.zip=this.scrapper_data.zip_code;
    this.property_info.county=this.scrapper_data.county;
    this.scrapper_data.cooling_key?this.property_info.ac=this.scrapper_data.cooling_key:'';
    this.scrapper_data.heating_key?this.property_info.heating=this.scrapper_data.heating_key:'';
    this.scrapper_data.specific_property_types_key?this.property_info.specific_property_type=this.scrapper_data.specific_property_types_key:'';
    this.scrapper_data.built_year?this.property_info.year_built=this.scrapper_data.built_year:'';
    this.scrapper_data.baths?this.property_info.bath=this.scrapper_data.baths:'';
    this.scrapper_data.beds?this.property_info.bed=this.scrapper_data.beds:'';
    this.scrapper_data.lotize?this.property_info.total_living_sqft=this.scrapper_data.lotize:'';
    this.scrapper_data.lot_acres?this.property_info.lot_acreage_sf=this.scrapper_data.lot_acres:'';
    this.scrapper_data.stories?this.property_info.stories=this.scrapper_data.stories:'';
    this.scrapper_data.bedrooms?this.property_info.bonus_room=this.scrapper_data.bedrooms:'';
    this.scrapper_data.pool_spa_key?this.property_info.pool=this.scrapper_data.pool_spa_key:'';
    this.scrapper_data.city?this.property_info.city=this.scrapper_data.city:'';
    this.scrapper_data.specific_property_types?this.property_info.property_type=this.scrapper_data.specific_property_types:'';
    this.scrapper_data.exterior_key?this.property_info.ext_wall_type=this.scrapper_data.exterior_key:'';
    this.scrapper_data.garage_type_key?this.property_info.garage_types=this.scrapper_data.garage_type_key:'';
    this.property_info.property_description=this.scrapper_data.description?this.scrapper_data.description:'';
    this.property_info.legal_description=(this.scrapper_data.another_legal_discription && this.scrapper_data.another_legal_discription!==undefined)?this.scrapper_data.another_legal_discription:'';
    this.scrapper_data.half_baths?this.property_info.half_baths=this.scrapper_data.half_baths:'';
    this.scrapper_data.full_bath?this.property_info.full_bath=this.scrapper_data.full_bath:'';
   
    
    this.scrapper_data.schools.elementary?this.property_info.elementary_school=this.scrapper_data?.schools?.elementary?.name:'';
    this.scrapper_data.schools.middle?this.property_info.middle_school=this.scrapper_data?.schools?.middle?.name:'';
    this.scrapper_data.schools.high?this.property_info.high_school=this.scrapper_data?.schools?.high?.name:'';
    
    this.scrapper_data.schools.elementary?this.property_info.elementary_ranking=this.scrapper_data.schools.elementary.rank:'';
    this.scrapper_data.schools.middle?this.property_info.middle_ranking=this.scrapper_data.schools.middle.rank:'';
    this.scrapper_data.schools.high?this.property_info.high_ranking=this.scrapper_data.schools.high.rank:'';
    
    this.scrapper_data.schools.elementary?this.property_info.elementary_distance=this.scrapper_data.schools.elementary.distance:'';
    this.scrapper_data.schools.middle?this.property_info.middle_distance=this.scrapper_data.schools.middle.distance:'';
    this.scrapper_data.schools.high?this.property_info.high_distance=this.scrapper_data.schools.high.distance:'';
    
    this.property_info.price_history=this.scrapper_data.price_history;
    if(this.property_info?.price_history){
      this.property_info.price_history.forEach(element => {
        element.price_date=element.date;
      });
    }
    
    this.scrapper_info=this.property_info;
    return this.scrapper_info;
  }

  closeDialog() {
    this.dialogRef.close();
  }

}
