import { Component, OnInit, Input, SimpleChanges } from '@angular/core';
import { apiUrl } from '../../../../config/api-url';
import { CommonApplicationService } from '../../../../shared/_services';

@Component({
  selector: 'app-user-report',
  templateUrl: './user-report.component.html',
  styleUrls: ['./user-report.component.css']
})
export class UserReportComponent implements OnInit {

  @Input() requestData:any;
  limit:number=10;
  offset:number=0;
  reportResult:any=[];
  moreBtn:boolean=true;
  loader:boolean=false;
  loadingContent:boolean=false;
  user_report_chart:any;

  constructor(private commonApplicationService:CommonApplicationService) { }

  ngOnInit(): void {
    
  }

  ngOnChanges(changes: SimpleChanges) {
    if(changes.requestData.currentValue && this.requestData){
      this.loadingContent=true;
      this.limit=10;
      this.offset=0;
      this.moreBtn=true;
      this.reportResult=[];
      this.getUserReport();
    }
    
  }

  getUserReport(){
    let data=this.requestData;
    data['limit']=this.limit;
    data['offset']=this.offset;
    let url=apiUrl.viewMore;
    
    this.commonApplicationService.getSearch(url, { params: data })
    .subscribe(response => {
         
        if(response.data.length>0){
          for(let i=0; response.data.length>i; i++){
            this.reportResult.push(response.data[i]);
          }    
        }
           
        if(response.data.length != this.limit){
          this.moreBtn=false;
        } 
        this.offset=(this.offset+response.data.length);

        this.loader=false;
        this.loadingContent=false;
        this.userReportChart(response);
      },
      error => {
        this.loader=false;
        this.loadingContent=false;
      }
    );
  }

  userReportChart(response){

  }

  getloadmorepages(){
    this.loader=true;
    this.getUserReport();
  }

}
