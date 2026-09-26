import { Component, OnInit,Inject } from '@angular/core';
import jsonData from '@config/state_county';
import { formConstants } from '@config/forms-constants';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { CommonApplicationService,AlertService } from '@shared-service/_services';
import { apiUrl } from '@config/api-url';
import {MatDialog, MatDialogRef, MAT_DIALOG_DATA} from '@angular/material/dialog';
import {StorageService} from '@shared-service/_services/storage.service';

@Component({
  selector: 'app-county-url-dialog',
  templateUrl: './county-url-dialog.component.html',
  styleUrls: ['./county-url-dialog.component.css']
})
export class CountyUrlDialogComponent implements OnInit {

  countries: any [];
  states: any;
  countyUrlForm:FormGroup;
  data:any;
  totalRecord:number;
  submitted:boolean=false;
  scrollBarHorizontal = (window.innerWidth < 1200);
  loadTable:boolean=false;

  constructor(private formBuilder:FormBuilder,
    private alertService:AlertService,
    private commonApplicationService:CommonApplicationService,
    private dialogRef: MatDialogRef<CountyUrlDialogComponent>,
    private storageService:StorageService,
    @Inject(MAT_DIALOG_DATA) private mat_data: any
    ) { }

  ngOnInit() {
    window.onresize = () => {
      this.scrollBarHorizontal = (window.innerWidth < 1200);
    };
    this.states     = formConstants.states;
    this.countries  = formConstants.countries;
    
    this.countyUrlForm = this.formBuilder.group({
      state: [this.mat_data.state, Validators.required],
      county: [this.mat_data.county, ''],
    });
    this.getCountyList(this.mat_data.state);
      this.onChangeTable(this.config);
    setTimeout(()=>{ 
      this.loadTable=true;
    }, 200);

    
    //this.getCountyDetail();
  }



  public rows:Array<any> = [];

  public columns:Array<any> = [
    {title: 'County Name',   name:'county', sort: false},
    {title: 'ROD URL',      name: 'rod_url', sort: false,className: 'text-warning' },
    {title: 'GIS URL',     name: 'gis_url', sort: false,className: ['office-header','text-warning', 'text-success']},
    {title: 'County Assessor URL', sort: false,  name: 'county_assessor_url',className: 'text-warning w-25'},
    {title: 'Treasurer URL',   name: 'treasurer_url', sort: false,className: 'text-warning'},
    {title: 'Action',       name: 'action'},
    
  ];
  public page:number = 1;
  public itemsPerPage:number = 10;
  public maxSize:number = 5;
  public numPages:number = 1;
  public length:number = 0;

  public config:any = {
    paging: true,
    sorting: {columns: this.columns},
    filtering: {filterString: ''},
    className: ['table-striped', 'table-bordered', 'm-b-0','county_url_table'],
  };


  changePage(page:any, data:Array<any> = this.data):Array<any> {
    this.numPages = (page.itemsPerPage) ? page.itemsPerPage : this.numPages;
    page = (page.page) ? page.page : page;
    let start = (page - 1) * this.itemsPerPage;
    let end = this.itemsPerPage > -1 ? (start + this.itemsPerPage) : data.length;
    return data.slice(start, end);
  }

  changeSort(data:any, config:any):any { 
      return data;
  }

  changeFilter(data:any, config:any):any {
    
    let filteredData:Array<any> = data;

    let countyVal=this.countyUrlForm.controls.county.value;
    if(countyVal){
      for (var item in filteredData) { //filteredData.forEach((item:any) => {
        if(item==countyVal){
          filteredData[item]['county']=item;
          filteredData[item]['action']='<div style="cursor: pointer;">Choose</div>';
          let tempArray:Array<any> = [];
          tempArray.push(filteredData[item]);
          filteredData = tempArray;
          return filteredData;
        }
      };
    }

    this.columns.forEach((column:any) => {
      if (column.filtering) {
        filteredData = filteredData.filter((item:any) => {
          return item[column.name].match(column.filtering.filterString);
        });
      }
    });

    if (!config.filtering) {
      return filteredData;
    }

    if (config.filtering.columnName) {
      return filteredData.filter((item:any) =>
        item[config.filtering.columnName].match(this.config.filtering.filterString));
    }

    let tempArray:Array<any> = [];
    for (var item in filteredData) { //filteredData.forEach((item:any) => {
      let flag = false;    
      this.columns.forEach((column:any) => {
        if(filteredData[item][column.name]){
          if (filteredData[item][column.name].toString().match(this.config.filtering.filterString)) {
            flag = true;
          }
        }
        filteredData[item]['county']=item;

      });
      if (flag) {
        tempArray.push(filteredData[item]);
      }
    };
    filteredData = tempArray;

    return filteredData;
  }

  onChangeTable(config:any, page:any = {page: this.page, itemsPerPage: this.itemsPerPage}):any {
    
    this.submitted=true;
    if(this.countyUrlForm.controls.state.value==""){
      this.length = 0;
      return this.rows = [];
    }
    this.data=this.storageService.get('county_info')[this.countyUrlForm.controls.state.value];        
    if(this.data===undefined){
      this.length = 0;
      return this.rows = [];
    }
    this.totalRecord=this.data.length; 
    
    if (config.filtering) {
      Object.assign(this.config.filtering, config.filtering);
    }
    if (config.sorting) {
      Object.assign(this.config.sorting, config.sorting);
    }
      
    let filteredData = this.changeFilter(this.data, this.config);
    let sortedData = this.changeSort(filteredData, this.config);

    for(var i=0; i<sortedData.length; i++){
      sortedData[i].rod_url = '<a href="'+sortedData[i].rod_url+'" target="_blank">'+sortedData[i].rod_url+'</a>';
      sortedData[i].gis_url = '<a href="'+sortedData[i].gis_url+'" target="_blank">'+sortedData[i].gis_url+'</a>';
      sortedData[i].county_assessor_url = '<a href="'+sortedData[i].county_assessor_url+'" target="_blank">'+sortedData[i].county_assessor_url+'</a>';
      sortedData[i].treasurer_url = '<a href="'+sortedData[i].treasurer_url+'" target="_blank">'+sortedData[i].treasurer_url+'</a>';
      sortedData[i].action='<div class="btn btn-sm btn-primary">Choose</div>';
    }

   
    this.rows = page && config.paging ? this.changePage(page, sortedData) : sortedData;
    this.length = sortedData.length;
    //this.rows=filteredData;
  }

  onCellClick(data: any): any {
    
    if(data.type=="click" && data.column && data.column.prop=='action'){
      this.dialogRef.close({'final_state':this.countyUrlForm.controls.state.value,'final_county':data.row.county});
    }

  }

  get f() { return this.countyUrlForm.controls; }

  getCountyList(state){
    this.countries = jsonData[state];
  }
}
