import { Component, OnInit } from '@angular/core';
import { FormGroup, FormBuilder } from '@angular/forms';
import {IMyDpOptions, IMyDateModel} from 'mydatepicker';
import { Chart,MapChart } from 'angular-highcharts';
import * as Highcharts from "highcharts/highmaps";
import Drilldown from 'highcharts/modules/drilldown';
import { apiUrl } from '@config/api-url';
import { CommonApplicationService, CommonActivityService } from '@shared-service/_services';
import { StorageService } from '@shared-service/_services/storage.service';
import { Router } from '@angular/router';
import { MatDialog } from '@angular/material/dialog';
import {saveAs} from 'file-saver';

Drilldown(Highcharts);

declare var require: any;
@Component({
  selector: 'app-aa-report',
  templateUrl: './aa-report.component.html',
  styleUrls: ['./aa-report.component.css']
})
export class AaReportComponent implements OnInit {
  reportForm:FormGroup;
  mapChart:any;
  chartOptions:any;
  resultData:any;
  buyerReport:any=[];
  saleReport:any;
  sale_type:any;

  limit=10;
  offset=0;
  loader:boolean=false;
  moreBtn:boolean=true;
  loadingContent:boolean=false;
  isClickViewMore:boolean=false;

 

  saleTypeDetailReport:any=[];
  moreSaleBtn:boolean=true;
  loadingSaleContent:boolean=false;
  saleOffset=0;
  sale_type_id:number;

  user_id:number;
  loadingBuyerContent:boolean=false;
  buyerOffset=0;
  moreBuyerBtn:boolean=true;
  buyerDetailReport:any=[];

  constructor(private formBuilder:FormBuilder,
              private commonApplicationService:CommonApplicationService,
              private storageService:StorageService,
              private router: Router,
              private commonActivityService:CommonActivityService,
             ) { }

  ngOnInit(): void {
    let currentRole=this.storageService.get('user_info')['current_role'];
    let aaReportAccess=['admin','home_buyer','wholesale_buyer','third_dca','chief_dca','sub_to']
    if(!aaReportAccess.includes(currentRole))
      this.router.navigate(['home/dashboard']);  
      
    this.sale_type=this.storageService.getHard('property_config')['sale_type'];
    var date = new Date();
    var firstDay = new Date(date.getFullYear(), date.getMonth(), 1);
    var lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0);


    this.reportForm = this.formBuilder.group({
      from: [{jsdate: firstDay}],
      to: [{jsdate: lastDay}],
      sale_date_from:[''],
      sale_date_to:[''],
    });


    this.getAaReport();
    this.getBuyerAaReport();
    this.getSaleAaReport();
    
    //@highcharts/map-collection/countries/ca/ca-all.geo.json

     
  }

  showUserReport(){
    this.loadingContent=true;
    this.limit=10;
    this.offset=0;
    this.saleOffset=0;
    this.buyerOffset=0;
    this.moreBtn=true;
    this.moreBuyerBtn=true;
    this.moreSaleBtn=true;
    this.buyerReport=[];
    this.saleTypeDetailReport=[];
    this.buyerDetailReport=[];
    this.isClickViewMore=false;


    this.getAaReport();
    this.getBuyerAaReport();
    this.getSaleAaReport();
  }

  viewMore(){
    this.loader=true;
    this.getBuyerAaReport();
  }

  getAaReport(){
   
    let data=this.commonActivityService.getFullFormDataWithDateFormatted(this.reportForm);
    let url =apiUrl['aa-report'];

    this.commonApplicationService.getSearch(url, { params: data })
    .subscribe(response => {
         if(response.status!='failed'){
            this.resultData=response.data;
            this.mapChartOption();
         }
      },
      error => {
        // this.loader=false;
        // this.loadingContent=false;
      }
    );
  }

  
  getBuyerAaReport(){
    let data=this.commonActivityService.getFullFormDataWithDateFormatted(this.reportForm);
    data['limit']=this.limit;
    data['offset']=this.offset;
    let url =apiUrl.aa_buyer_report;

    this.commonApplicationService.getSearch(url, { params: data })
    .subscribe(response => {
         if(response.status!='failed'){
            //this.buyerReport=response.data;
            if(response.data.length>0){
              for(let i=0; response.data.length>i; i++){
                this.buyerReport.push(response.data[i]);
              }    
            }
              
            if(response.data.length != this.limit){
              this.moreBtn=false;
            } 
            this.offset=(this.offset+response.data.length);
          
         }
          this.loader=false;
          //this.loadingContent=false;
      },
      error => {
        this.loader=false;
        //this.loadingContent=false;
      }
    );
  }


  public myDatePickerOptions: IMyDpOptions = { dateFormat: 'mm/dd/yyyy',firstDayOfWeek : 'su'};

  onDateChanged(event: IMyDateModel) {
    return event.formatted;
  }

  mapChartOption(){
    const caMapData = require("@highcharts/map-collection/countries/us/us-all.geo.json");
    const caMap = Highcharts.geojson(caMapData);
    console.log(caMap);
    
    // Set a random value on map
    caMap.forEach((el: any, i) => {
      let row=this.resultData.find(row=>{
        if(row.state==el.properties['postal-code'])
          return row;
        else
          return 0;
       
      });
      el.value = row?row.total:0;
      el.drilldown = el.properties["hc-key"];
      //el.county=this.countyResult;
    });
    

    this.chartOptions = {
      chart: {
        height: (8 / 24) * 100 + "%",
      },
      title: {
        text: "AA Report"
      },
      colorAxis: {
        min: 0,
        minColor: '#dadaaf',
        maxColor: '#616147'
      },

      mapNavigation: {
        enabled: true,
        buttonOptions: {
          verticalAlign: "bottom"
        }
      },
      plotOptions: {
        map: {
          states: {
            hover: {
              color: "#a5a562"
            }
          }
        }
      },
      series: [
        {
          name: "USA",
          data:caMap,
          joinBy:['name','name'],
          dataLabels: {
              enabled: true,
              format: '{point.properties.postal-code}',
              color: '#FFFFFF',
          },
        }
      ],
      drilldown: {}
    };

  
    this.mapChart = new MapChart(this.chartOptions);
  }

  

  aaReportDetail(id) {
    this.user_id=id;
    this.buyerOffset=0;
    this.moreBuyerBtn=true;
    this.buyerDetailReport=[];
    this.getAaReportDetail();
  }

  getAaReportDetail(){
    let data=this.commonActivityService.getFullFormDataWithDateFormatted(this.reportForm);
    data['id']=this.user_id;
    data['limit']=this.limit;
    data['offset']=this.buyerOffset;
   
    let url =apiUrl.aa_report_by_buyer_id;
    this.commonApplicationService.getSearch(url, { params: data })
    .subscribe(response => {
         if(response.status!='failed'){
           //this.buyerDetailReport=response.data;
            if(response.data.length>0){
              for(let i=0; response.data.length>i; i++){
                this.buyerDetailReport.push(response.data[i]);
              }    
            }
            if(response.data.length != this.limit){
              this.moreBuyerBtn=false;
            } 
            this.buyerOffset=(this.buyerOffset+response.data.length);
          
         }
         this.loadingBuyerContent=false;
      },
      error => {
        //this.loader=false;
        this.loadingBuyerContent=false;
      }
    );
  }

  viewMoreBuyer(){
    this.loadingBuyerContent=true;
    this.getAaReportDetail();
  }


  getSaleAaReport(){
    let data=this.commonActivityService.getFullFormDataWithDateFormatted(this.reportForm);
    data['limit']=this.limit;
    data['offset']=this.offset;
    let url =apiUrl.aa_sale_report;
    this.commonApplicationService.getSearch(url, { params: data })
    .subscribe(response => {
      console.log(response);
         if(response.status!='failed'){
            this.saleReport=response.data;
         }
         this.loadingContent=false;
      },
      error => {
        this.loadingContent=false;
      }
    );
  }

  saleTypeDetail(sale_type){
    
    this.sale_type_id=sale_type;
    this.saleOffset=0;
    this.moreSaleBtn=true;
    this.saleTypeDetailReport=[];
    this.getSaleTypeDetailInfo();
   
  }

  getSaleTypeDetailInfo(){
    let data=this.commonActivityService.getFullFormDataWithDateFormatted(this.reportForm);
    data['sale_type']=this.sale_type_id;
    data['limit']=this.limit;
    data['offset']=this.saleOffset;
    let url =apiUrl.aa_report_by_sale_type;
    this.commonApplicationService.getSearch(url, { params: data })
    .subscribe(response => {
         if(response.status!='failed'){
          // this.saleTypeDetailReport=response.data;
           if(response.data.length>0){
            for(let i=0; response.data.length>i; i++){
              this.saleTypeDetailReport.push(response.data[i]);
            }    
          }
            if(response.data.length != this.limit){
              this.moreSaleBtn=false;
            } 
            this.saleOffset=(this.saleOffset+response.data.length);
          
         }
         this.loadingSaleContent=false;
      },
      error => {
        this.loadingSaleContent=false;
      }
    );
  }

  viewMoreSaleType(){
    this.loadingSaleContent=true;
    this.getSaleTypeDetailInfo();
  }

  exportSaleTypeDetail(sale_type){
    let result=this.commonActivityService.getFullFormDataWithDateFormatted(this.reportForm);
    result['sale_type']=sale_type;
    let url =apiUrl.export_sale_type_aa_report;
    let options = {
      headers: { "Content-Type": "application/json", Accept: "application/pdf" },
      responseType: "blob"
    };
    this.commonApplicationService.postDownload(url,result, options)
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
            
            filename = "Es_"+new Date().getTime()+".csv";
          }
          saveAs(response, filename);
          console.log(options);
          console.log(response);
        
        },
        
        error => {
//          this.alertService.error(error.statusText);  
        }
    ); 
  }


  exportBuyItReport(id){
    let result=this.commonActivityService.getFullFormDataWithDateFormatted(this.reportForm);
    result['id']=id;
    let url =apiUrl.export_buyit_aa_report;
    let options = {
      headers: { "Content-Type": "application/json", Accept: "application/pdf" },
      responseType: "blob"
    };
    this.commonApplicationService.postDownload(url,result, options)
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
            
            filename = "Es_"+new Date().getTime()+".csv";
          }
          saveAs(response, filename);
        },
        error => { }
    ); 
  }

}
