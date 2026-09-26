import { Component, OnInit,Input,Output,EventEmitter } from '@angular/core';
import { StorageService } from '../../../shared/_services/storage.service';
import { CommunicationService } from '../../../shared/_services';

@Component({
  selector: 'app-property-detail',
  templateUrl: './property-detail.component.html',
  styleUrls: ['./property-detail.component.css']
})
export class PropertyDetailComponent implements OnInit {

  search:any;
  @Input() public property:any;
  @Input() public isMerge:boolean;
  @Input() isFilterShow:boolean=true;
  @Input() removedAlarm:boolean=false;

  propertyImg:string='/assets/img/no-image.gif';
  overFlag:boolean=true;
  house_ids:any=[];
  @Output() public valueChange = new EventEmitter();
  @Output() public removeValueChange = new EventEmitter();
  @Output() public mergePrimaryValue = new EventEmitter();
  @Output() public removeAlarm = new EventEmitter();

  math = Math;
  count:number;

  constructor(private storageService:StorageService,
              private communicationService: CommunicationService) { }

  property_config: any= {};
  sale_type_option: string [];
  sale_status_option: string [];
  userRole:string;
  
  ngOnInit() {
    this.property_config =  this.storageService.get("property_config");
    if(this.property_config !== null){
        this.sale_type_option = this.property_config.sale_type;
        this.sale_status_option= this.property_config.sale_status;
        this.userRole = this.storageService.get("user_info")['current_role'];

        //this.property_document_type_list = this.property_config.property_document_type;
    }
    
  }

  cma_arv_user(cma_arv_info,key_name){

    if(cma_arv_info){
      for(let i=0; i<cma_arv_info.length;i++){
        if(cma_arv_info[i].info_added_by==key_name && cma_arv_info[i].user_id)
        {
          return  cma_arv_info[i].user.first_name+' '+cma_arv_info[i].user.last_name;
        }
      }
    }else{
      return  '';
    }
  }

  cma_arv_date(cma_arv_info,key_name){
    
    if(cma_arv_info){
      for(let i=0; i<cma_arv_info.length;i++){
        if(cma_arv_info[i].info_added_by==key_name && cma_arv_info[i].date)
        {
          return  cma_arv_info[i].date;
        }
      }
    }else{
      return  '';
    }
  }

  showPorpertyImg(image){
    this.overFlag=false;
    if(image){
      this.propertyImg=image.url;
    }else{
      this.propertyImg='/assets/img/no-image.gif';
    }
  }
  
  checkedProperty(e){
    e.preventDefault();
    e.stopPropagation();
    console.log(e);
    if(e.target.checked){
      this.valueChange.emit(e.target.value);
    }
     else{
      this.removeValueChange.emit(e.target.value);
     }     
  }

  checkedPrimaryProperty(e){
    e.preventDefault();
    e.stopPropagation();
    console.log(e);
    if(e.target.checked){
      this.mergePrimaryValue.emit(e.target.value);
    }
  }

  removeAlarmMe(e,alarm_id){
    e.preventDefault();
    e.stopPropagation();
    this.removeAlarm.emit(alarm_id);
  }
  
}
