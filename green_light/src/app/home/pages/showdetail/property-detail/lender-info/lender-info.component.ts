import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { apiUrl, CommonApplicationService } from '@shared-service/_services';

@Component({
  selector: 'app-lender-info',
  templateUrl: './lender-info.component.html',
  styleUrls: ['./lender-info.component.css']
})
export class LenderInfoComponent implements OnInit {
  property_id: string;
  lender_list:any;
  loading:boolean=false;
  client_name:any;
  deed_info:number;
  burnrate_info:any;
  estimate_info:any;

  constructor(private commonApplicationService:CommonApplicationService,
              private route: ActivatedRoute,
              ) { }

  ngOnInit(): void {
    this.property_id = this.route.snapshot.paramMap.get('property_id');
    this.getLenderInfo();
  }


  /*----------------------------- Get property Info Details --------------------------------*/
  getLenderInfo(){
      this.loading=true;
      let url = apiUrl.lender_info+"/"+this.property_id;
      this.commonApplicationService.get(url).subscribe(response => {
        this.lender_list = response?.data?.lender_list;
        this.client_name = response?.data?.client_info?.client_info?.client_name;
        this.deed_info = response?.data?.deed_info;
        this.burnrate_info = response?.data?.burnrate_info;
        this.estimate_info = response?.data?.estimate_info;
        this.loading=false;
      },
        (err: any) => {
          this.loading = false;
        })
  }
  /*----------------------------- Get property Info Details --------------------------------*/

}
