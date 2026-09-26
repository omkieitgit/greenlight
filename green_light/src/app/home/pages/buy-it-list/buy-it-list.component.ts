import { Component, OnInit } from '@angular/core';

import { CommonApplicationService,AlertService } from '../../../shared/_services';
import { apiUrl } from '../../../config/api-url';
import { StorageService } from '../../../shared/_services/storage.service';
import { TableData } from './data';
import { Router, ActivatedRoute } from '@angular/router';
import { DatePipe } from '@angular/common';
import { CommonHelper } from '../../../shared/_utils/CommonHelper';
import { DomSanitizer } from '@angular/platform-browser';
import {saveAs} from 'file-saver';

@Component({
  selector: 'app-buy-it-list',
  templateUrl: './buy-it-list.component.html',
  styleUrls: ['./buy-it-list.component.css']
})
export class BuyItListComponent implements OnInit {

  my_favourite_property:any;
  offset: number = 1;
  totalRecord:number;
  searchField:any;
  limit:number=1;
  loading:boolean=true;
  private data:Array<any>;
  buyItResult:any;
  showBuyItList:boolean=false;
  sale_type:any;
  selectedHouseId:any=[];

  // ----------- Document upload config --------------------//
  public columns:Array<any> = [
    {title: '',   name: 'checkbox'},
    {title: 'Address',    name: 'address', sort: '' },
    {title: 'City',       name: 'city', sort: '' },
    {title: 'State',      name: 'state', sort: '' },
    {title: 'County',     name: 'county',className: ['office-header', 'text-success'], sort: 'asc'},
    {title: 'Zip',        name: 'zip', sort: '' },
    {title: 'Name',       name: 'name', sort: ''},
    {title: 'Sale Date',       name: 'sale_date', sort: ''},
    {title: 'Sale Type',       name: 'sale_type', sort: ''},
    {title: 'Redemption Date', name: 'redemption_expires', sort: ''},
    {title: 'Position',   name: 'position',className: 'text-warning'},
    {title: 'Date',       name: 'created_at'},
    {title: 'View',       name:'view'}
  ];

  public page:number = 1;
  public itemsPerPage:number = 10;
  public maxSize:number = 5;
  public numPages:number = 1;
  public length:number = 0;
  public rows:Array<any> = [];

  public config:any = {
    paging: true,
    sorting: {columns: this.columns},
    filtering: {filterString: ''},
    className: ['table-striped', 'table-bordered', 'm-b-0']
  };

  scrollBarHorizontal = (window.innerWidth < 1200);

  constructor(private storageService:StorageService,
    private alertService:AlertService,
    private router: Router,
    private datePipe:DatePipe,
    private commonApplicationService:CommonApplicationService,
    private sanitizer: DomSanitizer) { 
    }

  ngOnInit() {
    window.onresize = () => {
      this.scrollBarHorizontal = (window.innerWidth < 1200);
    };
    
    let property_config =  this.storageService.get("property_config");
    if(property_config !== null){
      this.sale_type= property_config.sale_type;
    }
    this.get_buyit();
    
  }

  get_buyit(){
    this.loading=false;
    let url = apiUrl.buyit;
    this.commonApplicationService.get(url).subscribe(response => {
      if(response !== undefined){             
       this.buyItResult=response.data;        
       this.loading=true;
       this.totalRecord=response.total; 
       this.getBuyItInfo();
      }
    },
      (err: any) => {
        this.alertService.common(err);    
      })
  }


  getBuyItInfo(){
    let pDoc =  this.buyItResult;
    if(pDoc && pDoc.length>0){
      for(var i =0; i<pDoc.length; i++){
        var dt = new Date(pDoc[i].created_at * 1000);
        pDoc[i].id=pDoc.house_id;
        pDoc[i].created_at =this.datePipe.transform(dt,'MM/dd/yyyy');
        pDoc[i].address = pDoc[i].address;
        pDoc[i].city = pDoc[i].city;
        pDoc[i].state = pDoc[i].state;
        pDoc[i].county = pDoc[i].county;
        pDoc[i].zipcode = pDoc[i].zip;
        pDoc[i].position = pDoc[i].position;
        pDoc[i].sale_date =(pDoc[i].last_sale_details && pDoc[i].last_sale_details.sale_date)?this.datePipe.transform(pDoc[i].last_sale_details.sale_date,'MM/dd/yyyy'):'';
        pDoc[i].sale_type =(pDoc[i].last_sale_details && pDoc[i].last_sale_details.sale_type)?this.sale_type[pDoc[i].last_sale_details.sale_type]:'';

        pDoc[i].redemption_expires =(pDoc[i].last_sale_details && pDoc[i].last_sale_details.redemption_expires)?this.datePipe.transform(pDoc[i].last_sale_details.redemption_expires,'MM/dd/yyyy'):'';
        pDoc[i].name = CommonHelper.filterUserName(pDoc[i]); 
        pDoc[i].view = '<a  href="/home/showdetail/'+pDoc[i].house_id+'" target="_blank">View</a>';
        pDoc[i].checkbox=this.sanitizer.bypassSecurityTrustHtml('<input type="checkbox" id="check_'+pDoc[i].house_id+'"/>');
      }
     // this.document_property = pDoc;
      this.data = pDoc;
      this.showBuyItList=true;
      this.onChangeTable(this.config);

    }  
  }
  
  

  public changePage(page:any, data:Array<any> = this.data):Array<any> {
    this.numPages = (page.itemsPerPage) ? page.itemsPerPage : this.numPages;
    page = (page.page) ? page.page : page;
    let start = (page - 1) * this.itemsPerPage;
    let end = this.itemsPerPage > -1 ? (start + this.itemsPerPage) : data.length;
    return data.slice(start, end);
  }

  

  public changeSort(data:any, config:any):any {
    if (!config.sorting) {
      return data;
    }

    let columns = this.config.sorting.columns || [];
    let columnName:string = void 0;
    let sort:string = void 0;

    for (let i = 0; i < columns.length; i++) {
      if (columns[i].sort !== '' && columns[i].sort !== false) {
        columnName = columns[i].name;
        sort = columns[i].sort;
      }
    }

    if (!columnName) {
      return data;
    }

    // simple sorting
    return data.sort((previous:any, current:any) => {
      if (previous[columnName] > current[columnName]) {
        return sort === 'desc' ? -1 : 1;
      } else if (previous[columnName] < current[columnName]) {
        return sort === 'asc' ? -1 : 1;
      }
      return 0;
    });
  }

  public changeFilter(data:any, config:any):any {
    let filteredData:Array<any> = data;
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
    filteredData.forEach((item:any) => {
      let flag = false;
      this.columns.forEach((column:any) => {
        if(item[column.name] !== undefined && item[column.name] !== null){
            if (item[column.name].toString().match(this.config.filtering.filterString)) {
            flag = true;
          }
        }
      });
      if (flag) {
        tempArray.push(item);
      }
    });
    filteredData = tempArray;

    return filteredData;
  }

  public onChangeTable(config:any, page:any = {page: this.page, itemsPerPage: this.itemsPerPage}):any {
    if (config.filtering) {
      Object.assign(this.config.filtering, config.filtering);
    }

    if (config.sorting) {
      Object.assign(this.config.sorting, config.sorting);
    }

    let filteredData = this.changeFilter(this.data, this.config);
    let sortedData = this.changeSort(filteredData, this.config);
    this.rows = page && config.paging ? this.changePage(page, sortedData) : sortedData;
    this.length = sortedData.length;

    this.rows.forEach(element=>{
      if(this.selectedHouseId.includes(element.house_id)){
        element.checkbox=this.sanitizer.bypassSecurityTrustHtml('<input type="checkbox" checked id="check_'+element.house_id+'"/>');
      }
    });

  }

  public onCellClick(data: any): any {
    if(data.type=='click'){
      if(data.column=='houseId'){
        this.router.navigate(['/home/showdetail/'+data.row.house_id]);  
      }
      if(data.column.prop=='checkbox'){
        if(document.getElementById('check_'+data.row.house_id)['checked'] && !this.selectedHouseId.includes(data.row.house_id)){
          this.selectedHouseId.push(data.row.house_id);
        }else{
          const index: number = this.selectedHouseId.indexOf(data.row.house_id);
          if (index !== -1) {
              this.selectedHouseId.splice(index, 1);
          }     
        }
        
      }
    }
   
  }

  exportSelectedBuyIt(){
    if(this.selectedHouseId.length>0){
      let result:any={house_ids:this.selectedHouseId};
      this.exportBuyIt(result);
    } 
  }

  exportAllBuyIt(){
    let result:any={house_ids:'all'};
      this.exportBuyIt(result);
  }

  exportBuyIt(result){
          
          let url = apiUrl.buyitexport;
          result.responseType = "blob";
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
                }catch(e)
                {
                  filename = "Es_"+new Date().getTime()+".csv";

                  // if(result.export_type == "csv")
                  // {
                  //   filename = "Es_"+new Date().getTime()+".csv";
                  // }
                  // else  if(result.export_type == "xls")
                  // {
                  //   filename = "Es_"+new Date().getTime()+".xls";
                  // }
                  // else{
                  //   filename = "Es_"+new Date().getTime()+".pdf";
                  // }
                }
                saveAs(response, filename);
                console.log(options);
                console.log(response);
              
              },
              error => {
                this.alertService.error(error.statusText);  
              }); 
  }

}
