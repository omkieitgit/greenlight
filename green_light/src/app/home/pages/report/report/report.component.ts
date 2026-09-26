import { Component, OnInit } from '@angular/core';
import { FormControl, FormBuilder, FormGroup, Validators } from '@angular/forms';
import {IMyDpOptions, IMyDateModel} from 'mydatepicker';
import { Chart,MapChart } from 'angular-highcharts';
import { apiUrl } from '../../../../config/api-url';
import { CommonApplicationService, CommonActivityService, AlertService } from '../../../../shared/_services';
import UserReportSetting from '../user-report';
import {saveAs} from 'file-saver';
import { StorageService } from '../../../../shared/_services/storage.service';

@Component({
  selector: 'app-report',
  templateUrl: './report.component.html',
  styleUrls: ['./report.component.css']
})
export class ReportComponent implements OnInit {

  
  reportForm:FormGroup;
  reportResult:any=[];

  userReportObj:any;
  user_report_chart:any;

  mapChart:any;
  limit=10;
  offset=0;
  loader:boolean=false;
  moreBtn:boolean=true;
  loadingContent:boolean=false;

  userReportRequest:any;
  isClickViewMore:boolean=false;

  reportTitle:any={"nos_by": "NOS REPORT",
                  "im_by": "IMAGE TEAM REPORT",
                  "trustee_caller": "TRUSTEE REPORT",
                  "first_dtc": "DTC REPORT",
                  "second_dca": "SECOND DCA REPORT",
                  "third_dca": "THIRD DCA REPORT",
                  "chief_dca": "CHIEF DCA REPORT",
                  "auction_by": "AUCTION REPORT",}

  constructor(private formBuilder: FormBuilder,
              private commonApplicationService:CommonApplicationService,
              private commonActivityService:CommonActivityService,
              private alertService:AlertService,
              private storageService:StorageService) { }

  ngOnInit() {
    this.userReportObj=UserReportSetting;

    var date = new Date();
    var firstDay = new Date(date.getFullYear(), date.getMonth(), 1);
    var lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0);

    let currentRole=this.storageService.get('user_info')['current_role'];
    let userType=currentRole;
    if(currentRole=='admin' || currentRole=='accounting')
      userType='nos_by'

    this.reportForm = this.formBuilder.group({
      user_type: [userType],
      from: [{jsdate: firstDay}],
      to: [{jsdate: lastDay}],
      keywords: [''], 
      report_type: [''],
    });
    this.showUserReport();
  }

  public myDatePickerOptions: IMyDpOptions = { dateFormat: 'mm/dd/yyyy',firstDayOfWeek : 'su'};

  onDateChanged(event: IMyDateModel) {
    return event.formatted;
  }

  showUserReport(){
    this.loadingContent=true;
    this.limit=10;
    this.offset=0;
    this.moreBtn=true;
    this.reportResult=[];
    this.isClickViewMore=false;
    this.userReportObj.series[0].data=[];
    this.getUserReport();
  }

  getUserReport(){

    let data=this.commonActivityService.getFullFormDataWithDateFormatted(this.reportForm);
    data['limit']=this.limit;
    data['offset']=this.offset;
    let url=apiUrl.report;
    
    this.commonApplicationService.getSearch(url, { params: data })
    .subscribe(response => {
         if(response.status!='failed'){

            if(response.data['row'].length>0){
              for(let i=0; response.data['row'].length>i; i++){
                this.reportResult.push(response.data['row'][i]);
              }    
            }
              
            if(response.data['row'].length != this.limit){
              this.moreBtn=false;
            } 
            this.offset=(this.offset+response.data['row'].length);
            this.userReportChart(response);
         }
        this.loader=false;
        this.loadingContent=false;
        
      },
      error => {
        this.loader=false;
        this.loadingContent=false;
      }
    );
  }

  userReportChart(obj) {
  
    this.userReportObj.title.text=this.reportTitle[this.reportForm.get('user_type').value];
    for (var value in obj['data']['row'])
    {
       // if(value < report_limit -1) // show only less one records
        //tr_html += tr_box(obj['data']['row'][value])
        // Update highchart bar graph
       
        let data=[obj['data']['row'][value]['first_name'],parseInt(obj['data']['row'][value]['total_records'])];
        this.userReportObj.series[0].data.push(data);
    }

    console.log( this.userReportObj);
    
     this.user_report_chart = new Chart(this.userReportObj);
    
  }

  // showMapChat(){
  //   this.mapChart = new MapChart({ options });
  // }

  getloadmorepages(){
    this.loader=true;
    this.getUserReport();
  }

  exportMyReport(user_id=""){
    
    let result=this.commonActivityService.getFullFormDataWithDateFormatted(this.reportForm);
    
    if(user_id){
      result['uid']=user_id;
    }
    
    let url=apiUrl.exportMe;
    // application/pdf
    // application/vnd.ms-excel, 
    // application/csv
    let options = {
      headers: { "Content-Type": "application/json", Accept: "application/pdf" },
      responseType: "blob",
      params: result
    };
    this.commonApplicationService.getSearch(url,options)
    .subscribe(
        response => {
          var headers = response.headers;
          var filename = "";
          try{
            var contentDisposition= headers.get('Content-Disposition');
           filename = contentDisposition.split(';')[1].split('filename')[1].split('=')[1].trim();
            console.log(filename);
          }
          catch(e)
                {
                  filename = "Es_"+new Date().getTime()+".xls";
                }
          saveAs(response, filename);
          console.log(options);
          console.log(response);
        },
        
        error => {
          //Rativardhan: Only for PDF,image,CSV,XLS this situation we will take error from statusText
          this.alertService.error(error.statusText);  
        }
    ); 
  }

  viewMore(user_id,name){
    let result=this.commonActivityService.getFullFormDataWithDateFormatted(this.reportForm);
    if(user_id){
      result['uid']=user_id;
      result['name']=name;
    }
    this.isClickViewMore=true;
    this.userReportRequest=result;
  }
}
