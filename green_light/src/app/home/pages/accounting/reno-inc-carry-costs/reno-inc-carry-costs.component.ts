import { Component, OnInit, Input } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { CommonApplicationService,CommunicationService} from '../../../../shared/_services';
import { apiUrl } from '../../../../config/api-url';

@Component({
  selector: 'app-reno-inc-carry-costs',
  templateUrl: './reno-inc-carry-costs.component.html',
  styleUrls: ['./reno-inc-carry-costs.component.css']
})
export class RenoIncCarryCostsComponent implements OnInit {

  @Input() property_id:string;
  @Input() viewAccessOnly;
  openPanel:boolean;
  loading:boolean=false;
  ric_cost_data:any;
  burnRatelist:any;
  clientRenovation:boolean=false;


  constructor( private route: ActivatedRoute,
               private commonApplicationService:CommonApplicationService,
               private communicationService:CommunicationService) { }

  ngOnInit() {

    this.communicationService.getUpdateRenovation().subscribe(response=>{
      if(response && this.openPanel){
        this.clientRenovation=response;
        this.getRenoIncCarry();
      }
    });
  }
  
  panelExpand(flag){
    if(!this.openPanel){
      this.openPanel=true;
      this.getRenoIncCarry();
      this.getBurnRate();
    }
  }



  getRenoIncCarry(){
  this.loading=true;
  let url = apiUrl.rci_costs+this.property_id;
  this.commonApplicationService.get(url)
    .subscribe(
        data => {
          this.ric_cost_data=data.data;
          this.loading = false;
        },
        error => {
            this.loading = false;
        }
    ); 
  }

  getBurnRate(){
    this.loading=true;
    let url = apiUrl.burnRate+'/'+this.property_id;
    this.commonApplicationService.get(url)
      .subscribe(
          data => {
            this.burnRatelist=data.row;
            this.loading = false;
          },
          error => {
              this.loading = false;
          }
      );  
  }

  updateBurnRateInfo($event){
      if($event){
        this.getRenoIncCarry();
        this.getBurnRate();
      }
  }

}
