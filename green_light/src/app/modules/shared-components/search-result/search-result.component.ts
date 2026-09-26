import { Component, OnInit ,Input,Output,EventEmitter, SimpleChanges} from '@angular/core';

import { CommonApplicationService,CommonActivityService, AlertService,CommunicationService} from '../../../shared/_services';
import { apiUrl } from '../../../config/api-url';

import * as jspdf from 'jspdf'; 
import html2canvas from 'html2canvas';
import { StorageService } from '@shared-service/_services/storage.service';
import { MatDialog } from '@angular/material/dialog';
import { MergeComponentComponent } from '../merge-component/merge-component.component';

@Component({
  selector: 'app-search-result',
  templateUrl: './search-result.component.html',
  styleUrls: ['./search-result.component.css']
})
export class SearchResultComponent implements OnInit {

  @Input() searchResult:any;
  @Input() loading:boolean;
  @Input() totalRecord:number;
  @Input() searchField:any;
  @Input() getPagination:boolean=false;
  @Input() isFilterShow:boolean=true;
  @Input() removedAlarm:boolean=false;

  house_ids:any=[];
  offset: number = 1;
  @Input() limit:number;
  emailFlag:boolean=false;
  selectedAll: any;
  selectPropertFlag:boolean=false;
  userRole:string;
  @Output() emitPagination  = new EventEmitter<any>();
  @Output() removeAlarmMe  = new EventEmitter<any>();

  primaryPropertyId:number;
  isMerge:boolean=false;

  constructor(private commonActivityService :CommonActivityService,
    private commonApplicationService:CommonApplicationService,
    private communicationService:CommunicationService,
    private alertService:AlertService,
    private storageService:StorageService,
    private dialog:MatDialog) { }

  ngOnInit() {
    this.userRole=this.storageService.get('user_info')['current_role'];
   //console.log(this.searchResult);
  }


  pageChanged(data){
    this.loading=false;
    if(this.getPagination){
      this.emitPagination.emit(data);
      return;
    }
    this.offset=data;
    this.un_select_all();
    this.searchField.offset= (data-1)*(this.searchField.limit?this.searchField.limit:10);
    let url = apiUrl.search;
    this.commonApplicationService.getSearch(url, { params: this.searchField })
        .subscribe(
            data => {
              this.loading=true;
              this.searchResult=data.data;
              //this.totalRecord=data.total;
            },
            error => {
                //this.error_message=true;
                //this.message=error['message'];
                //this.alertService.error(error);
                //this.loading = false;
            }
        ); 
  }

  changeLimit($event){
    this.limit=$event.target.value;
  }

  displayCounter($event){
    if(this.house_ids.indexOf($event)==-1){
      this.house_ids.push($event);
      console.log(this.house_ids);
    }
  }

  removeHouseIds($event){
    this.house_ids.pop($event);
  }

  select_all(){
    this.selectPropertFlag=true;
    var searchPorpertyInfo=document.getElementsByClassName('search_property_info');
    for (var i = 0; i < searchPorpertyInfo.length; i++) {
       if (searchPorpertyInfo[i].querySelector('input').type=='checkbox'){
          searchPorpertyInfo[i].querySelector('input').checked= true;
          this.house_ids.push(searchPorpertyInfo[i].querySelector('input').value);
       }
          
    }
  }

  un_select_all(){
    this.selectPropertFlag=false;
    var searchPorpertyInfo=document.getElementsByClassName('search_property_info');
    for (var i = 0; i < searchPorpertyInfo.length; i++) {
       if (searchPorpertyInfo[i].querySelector('input').type=='checkbox'){
          searchPorpertyInfo[i].querySelector('input').checked= false;
          this.house_ids.pop(searchPorpertyInfo[i].querySelector('input').value);
       }
          
    }
  }

  deleteSelectPorperty(){
    if(this.house_ids.length>0){
      if(confirm("Are you sure want to delete selected records?")){
        this.emailFlag=true;
        let url = apiUrl.delete_property;
        this.commonApplicationService.post(url,{house_ids:this.house_ids})
        .subscribe(
            response => {
              if(response['status']=='success'){
                this.alertService.success(response.message); 
                for(let i=0; i<this.house_ids.length; i++){
                  this.searchResult = this.searchResult.filter(item => item.house_id != this.house_ids[i]);
                }
                this.house_ids=[];
              }else{
                this.alertService.error(response.message[0]);  
              }
            },
            error => {
              this.alertService.common(error.error);
            }
        ); 
    }
  }
    else{
      this.alertService.error('Please select any property.');
    }
  }

  setPrimaryProperty($event){
    this.primaryPropertyId=$event;
  }

  mergeProperty(){
    this.isMerge=true;
  }
  cancleMerge(){
    this.isMerge=false;
  }

  mergeRecords(){
    if(this.house_ids.length>0){
        this.emailFlag=true;
        let url = apiUrl.mergeProperty;
        this.commonApplicationService.post(url,{house_ids:this.house_ids,primaryPropertyId:this.primaryPropertyId})
        .subscribe(
            response => {
              if(response['status']=='success'){
                  let dialogRef=this.dialog.open(MergeComponentComponent,{ width: '600px',data:response.data,disableClose:true} );
                  dialogRef.afterClosed().subscribe(result => {
                    if(result){
                      for(let i=0; i<result.length; i++){
                        this.searchResult = this.searchResult.filter(item => item.house_id != result[i].merge_house_id);
                      }
                      this.house_ids=[];
                    }
                  });
              }else{
                this.alertService.error(response.message);  
              }
            },
            error => {
              this.alertService.common(error.error);
            }
        ); 
    }
    else{
      this.alertService.error('Please select any property.');
    }
  }

  removeAlarm($event){
    this.removeAlarmMe.emit($event);
  }
}
