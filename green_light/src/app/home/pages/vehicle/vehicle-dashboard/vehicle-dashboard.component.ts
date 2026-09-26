import { Component, OnInit } from '@angular/core';
import { apiUrl, CommonApplicationService } from '@shared-service/_services';

@Component({
  selector: 'app-vehicle-dashboard',
  templateUrl: './vehicle-dashboard.component.html',
  styleUrls: ['./vehicle-dashboard.component.css']
})
export class VehicleDashboardComponent implements OnInit {
  vehicleInfo:any;
  vehicle_make_info: any;
  constructor(  private commonApplicationService:CommonApplicationService) { }

  ngOnInit(): void {
    this.getInfoDetails();
  }

 
  panelExpand($event){

  }

  getInfoDetails(){
      let url = apiUrl.vehicle;
      this.commonApplicationService.get(url).subscribe(response => {
          if(response){
            this.vehicleInfo=response.data?.vehicle_sale_info;
          }
      });
  }

}
