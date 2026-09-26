import { Component, OnInit } from '@angular/core';
import { Router,ActivatedRoute }    from '@angular/router';
import { AlertService,CommonApplicationService, apiUrl } from '../../../shared/_services';
import {QuickInputModel} from './quickinput.model';

@Component({
  selector: 'app-quickinput',
  templateUrl: './quickinput.component.html',
  styleUrls: ['./quickinput.component.css']
})
export class QuickinputComponent implements OnInit {

  property_id: string;
  infoData:QuickInputModel;
  loading:boolean=true;

  constructor(private router: Router,
    private route: ActivatedRoute,
    private alertService: AlertService,
    private commonApplicationService:CommonApplicationService) { 
      this.infoData=new QuickInputModel();
    }

  ngOnInit() {
    this.property_id = this.route.snapshot.paramMap.get('property_id');
    if(this.property_id){
      this.getInfoDetails();
    }else{
      this.loading=false;
    }
  }

  getInfoDetails(){
      let url = apiUrl.property_info+"/"+this.property_id+"/all";
      this.commonApplicationService.get(url).subscribe(response => {
        let result = response['row'];
        if(result !== undefined ){   
            this.infoData = result.property_info;
            this.loading=false;
        }
      },
        (err: any) => {
          this.alertService.common("Error occured, Please try again later!");
          this.loading=false;
        })
    }

}
