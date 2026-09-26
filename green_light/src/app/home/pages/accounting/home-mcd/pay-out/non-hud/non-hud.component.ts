import { Component, OnInit,Input, OnChanges, Output, EventEmitter } from '@angular/core';
import { CommonApplicationService ,CommunicationService} from '@shared-service/_services';
import { apiUrl } from '@config/api-url';
import { StorageService } from '@shared-service/_services/storage.service';


@Component({
  selector: 'app-non-hud',
  templateUrl: './non-hud.component.html',
  styleUrls: ['./non-hud.component.css']
})
export class NonHudComponent implements OnInit,OnChanges {

  @Input() property_id:string;
  @Input() openPanel:boolean;

   nonHubResult:any;
  loading:boolean=false;
  
  property_config:any;
  sub_category:any;
  totalNonHud:number=0;
  @Output() totalNonHudAmount = new EventEmitter<number>();

  constructor(private communicationService:CommunicationService,
              private storageService:StorageService,
              private commonApplicationService:CommonApplicationService) { }

  ngOnInit() {
    this.communicationService.getUpdateRenovation().subscribe(response=>{
      if(response && this.openPanel){
        this.getRenoIncCarry();
      }
    });

  }

  ngOnChanges() {
   
    this.nonHubResult=this.storageService.getHard('nun_hud_cat');
    if(this.openPanel && this.property_id && !this.nonHubResult){
      this.getRenoIncCarry();
    }else{
      if(this.nonHubResult){ 
        for(let i=0; i<this.nonHubResult.length; i++){
          this.totalNonHud += parseFloat(this.nonHubResult[i].total_amount);
        }
        this.totalNonHudAmount.emit(this.totalNonHud);
      }
    }
  }

  getRenoIncCarry(){
    this.loading=true;
    let url = apiUrl.inovice_non_hud+'/'+this.property_id;
    this.commonApplicationService.get(url)
      .subscribe(
          data => {
            this.nonHubResult=data.data;
            this.totalNonHud=0;
            if(this.nonHubResult){ 
              for(let i=0; i<this.nonHubResult.length; i++){
                this.totalNonHud += parseFloat(this.nonHubResult[i].total_amount);
              }
              this.totalNonHudAmount.emit(this.totalNonHud);
            }
            
            this.loading = false;
          },
          error => {
              this.loading = false;
          }
      ); 
  }

}
