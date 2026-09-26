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
import { formConstants } from '@config/forms-constants';

Drilldown(Highcharts);

declare var require: any;

@Component({
  selector: 'app-county-report',
  templateUrl: './county-report.component.html',
  styleUrls: ['./county-report.component.css']
})
export class CountyReportComponent implements OnInit {
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
  states:any;
  selected_state:string;

  constructor(private formBuilder:FormBuilder,
              private commonApplicationService:CommonApplicationService,
              private storageService:StorageService,
              private router: Router,
              private commonActivityService:CommonActivityService,
             ) { }

  ngOnInit(): void {

    this.states = formConstants.states;
    
    let currentRole=this.storageService.get('user_info')['current_role'];
    let aaReportAccess=['admin','third_dca','chief_dca']
    if(!aaReportAccess.includes(currentRole))
      this.router.navigate(['home/dashboard']);  
      
    this.sale_type=this.storageService.getHard('property_config')['sale_type'];
    var date = new Date();
    var firstDay = new Date(date.getFullYear(), date.getMonth(), 1);
    var lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0);


    this.reportForm = this.formBuilder.group({
      // from: [{jsdate: firstDay}],
      // to: [{jsdate: lastDay}],
      sale_date_from:[{jsdate: firstDay}],
      sale_date_to:[{jsdate: lastDay}],
      state:['TX'],
      
    });


    this.getCountyReport();
    //this.getBuyerAaReport();
    //this.getSaleAaReport();
    
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


    this.getCountyReport();
    //this.getBuyerAaReport();
    //this.getSaleAaReport();
  }

 

  getCountyReport(){
   
    let data=this.commonActivityService.getFullFormDataWithDateFormatted(this.reportForm);
    let url =apiUrl.county_by_report;
    this.commonApplicationService.getSearch(url, { params: data })
    .subscribe(response => {
         if(response.status!='failed'){
            this.resultData=response.data;
            this.mapChartOption(data['state']);
            this.loadingContent=false;
         }
      },
      error => {
        // this.loader=false;
        this.loadingContent=false;
      }
    );
  }

  

  public myDatePickerOptions: IMyDpOptions = { dateFormat: 'mm/dd/yyyy',firstDayOfWeek : 'su'};

  onDateChanged(event: IMyDateModel) {
    return event.formatted;
  }

  mapChartOption(state){
    const caMapData = require("@highcharts/map-collection/countries/us/us-"+state.toLowerCase()+"-all.geo.json");
    const caMap = Highcharts.geojson(caMapData);
    this.selected_state=state;
    
    // Set a random value on map
    caMap.forEach((el: any, i) => {
      let row=this.resultData.find(row=>{
        if(row.county==el.properties['name'])
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
        height: (12 / 24) * 100 + "%",
      },
      title: {
        text: state+" County Report"
      },
      colorAxis: {
        min: 0,
        minColor: '#00acac',
        maxColor: '#009a42'
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
              color: "#035c5c"
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
      drilldown: {},
      onclick: function () { console.log('dddd')}
    };

  
    this.mapChart = new MapChart(this.chartOptions);
  }



  exportReport(countyName){

    let result=this.commonActivityService.getFullFormDataWithDateFormatted(this.reportForm);
    result['county']=countyName;
    let url =apiUrl.export_county_by_report;
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



}