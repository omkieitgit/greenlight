import { Component, OnInit } from '@angular/core';
import { Router,ActivatedRoute } from '@angular/router';
import { CommonApplicationService,AlertService } from '@shared-service/_services';
import { apiUrl } from '@config/api-url';
import { StorageService } from '@shared-service/_services/storage.service';


@Component({
  selector: 'app-owner-borrow',
  templateUrl: './owner-borrow.component.html',
  styleUrls: ['./owner-borrow.component.css']
})
export class OwnerBorrowComponent implements OnInit {
  
  property_id:any;
  openPanel:boolean=false;
  loading:boolean=false;  
  ownerBorrowLoading:boolean=false;

  owner_data:any;
  
  owner_info:any;
  borrower_info:any;
  document_owner:any;
  document_borrower:any;
  owner_borrower_info:any;
  owner_document_type_list:any;
  owner:'owner';

  constructor(private route:ActivatedRoute,
              private commonApplicationService:CommonApplicationService,
              private alertService:AlertService,
              private storageService:StorageService) { }

  ngOnInit() {
    this.property_id = this.route.snapshot.paramMap.get('property_id');
    let property_config =  this.storageService.get("property_config");
    this.owner_document_type_list = property_config.owner_document_type;

  }

  panelExpand(flag){
    if(!this.openPanel){
      this.openPanel = true;
      this.getOwnerBorrowInfo();
    }
  }

  OwnerChangeInfo($event){
    this.owner_data=$event;
  }

  getOwnerBorrowInfo(){
    this.loading = true;
    let url = apiUrl.owner_borrow_all_info+'/'+this.property_id+'/all';
    this.commonApplicationService.get(url).subscribe(response => {
      if(response['data']){
        this.owner_info=response['data']['owner_info'];
        this.borrower_info=response['data']['borrower_info'];;
        this.document_owner=response['data']['document_owner'];;
        this.document_borrower=response['data']['document_borrower'];;
        this.owner_borrower_info=response['data']['owner_borrower_info'];
        this.owner_data=this.owner_info;
      }
      this.ownerBorrowLoading=true;
      this.loading= false;
    },
    (err: any) => {
      this.alertService.common("There are no posts pulled from the server!"); 
      this.loading = false;
      this.ownerBorrowLoading=false;

    })
}

}
