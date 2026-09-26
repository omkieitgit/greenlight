import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { apiUrl } from '@config/api-url';
import { CommonApplicationService } from '@shared-service/_services';
import { StorageService } from '@shared-service/_services/storage.service';

@Component({
  selector: 'app-vehicle-input',
  templateUrl: './vehicle-input.component.html',
  styleUrls: ['./vehicle-input.component.css']
})
export class VehicleInputComponent implements OnInit {

  vehicleInfo:any;
  vehicle_make_info: any;
  vehicleId:number;
  loadVehicleForm:boolean=false;

  constructor( private route: ActivatedRoute,
               private router: Router,
               private commonApplicationService:CommonApplicationService) { }

  ngOnInit(): void {
    this.route.params.subscribe(params=>{
      this.vehicleId=params['vehicle_id'];
    });
    this.getInfoDetails();
  }

  getInfoDetails(){
      let url = apiUrl.vehicleDetail+'/'+this.vehicleId;
      this.commonApplicationService.get(url).subscribe(response => {
          if(response){
            this.vehicleInfo=response.data;
            this.loadVehicleForm=true;
          }
      });
  }
  
}
