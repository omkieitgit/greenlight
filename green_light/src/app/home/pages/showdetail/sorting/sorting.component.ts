import { Component, ViewEncapsulation, OnInit, Input, EventEmitter, Output } from '@angular/core';
//import { TableData } from './data';
// import { SortingdataService } from '../../../../shared/_services';

@Component({
    selector: 'sort',
    templateUrl: './sorting.html',
    encapsulation: ViewEncapsulation.None,
})



export class SortingComponent implements OnInit {

  @Input() config:any = {};
  @Input() data:any = {};
  @Input() columns:Array<any> = [];
  @Input() docLength: number = 0;
  @Output() public rowDelete:EventEmitter<any> = new EventEmitter();

  listen() {
    this.onChangeTable(this.config);
  }

  public rows:Array<any> = [];
  public page:number = 1;
  public itemsPerPage:number = 10;
  public maxSize:number = 5;
  public numPages:number = 1;
  public length:number = 0;
  public reload:boolean= false;
  scrollBarHorizontal = (window.innerWidth < 1200);


  //private data:Array<any> = TableData;

  public constructor() {
    this.length = this.data.length;
  }

   

  public ngOnInit():void {
    window.onresize = () => {
      this.scrollBarHorizontal = (window.innerWidth < 1200);
    };
    // this.sortingdataService.change.subscribe(reload => {
    //   this.reload = reload;
    //   console.log("Listing to service");
    // });
    this.onChangeTable(this.config);
  }

  public onCellClick(data: any): any {
    if(data.column == 'delete'){
      if(confirm("Are you sure want to delete document ?")){
        this.rowDelete.emit(data.row);
      }      
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
  }

  onActivate(data: any){
    if(data.column && data.column.prop && data.column.prop == 'delete'){
      if(confirm("Are you sure want to delete document ?")){
        this.rowDelete.emit(data.row);
      }      
    }
  }

  colWidth(col){
    let tableWidth=document.getElementsByClassName('table-responsive') as HTMLCollectionOf<HTMLElement>;
    let colWidth=(tableWidth[0].offsetWidth/this.columns.length)-10;
    if(!this.scrollBarHorizontal){
      if(col=='org_name')
        return colWidth*2;
      else
        return colWidth;
    }
   
  }
 
}
