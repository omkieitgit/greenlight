import { Component, Injectable, OnInit } from '@angular/core';
import { NgbDateAdapter, NgbDateStruct } from '@ng-bootstrap/ng-bootstrap';
import { CommonApplicationService, CommunicationService } from '@shared-service/_services';
import { apiUrl } from '@config/api-url';
import { Router,ActivatedRoute } from '@angular/router';
import { StorageService } from '@shared-service/_services/storage.service';


@Component({
 selector: 'accounting',
 templateUrl: './accounting.html',
 })


export class AccountingComponent implements OnInit{
  
  property_id:string;
  payoutDetail:any;
  nonHubResult:any;
  renoHomeBuyerCategory:any;

  loadPayout:boolean=false;
  loadNunHud:boolean=false;
  viewAccessOnly:boolean=false;
  isBuyerMcdAccess:boolean=true;
  propertyInfo:any;

  constructor(private commonApplicationService:CommonApplicationService,
              private route: ActivatedRoute,
              private storageService:StorageService,
              private communicationService:CommunicationService) { }

  openWindowCustomClass(e){

  }

  ngOnInit() {
    this.viewAccessOnly=this.storageService.getHard('ac_view_access');
    this.propertyInfo=this.storageService.getHard('property_info');
    this.property_id = this.route.parent.snapshot.parent.params.property_id;
    // Load info details
    if(this.property_id !== undefined){
     this.getCategory();
     this.getPayout();
     this.getRenoIncCarry();
     this.isMcdAccess();
    }

    this.communicationService.getUpdateRenovation().subscribe(response=>{
      if(response){
        this.getRenoIncCarry();
      }
    });
    
  }
  
  getCategory(){
    let url = apiUrl.renovationCategory;
    this.commonApplicationService.get(url).subscribe(response => {
      this.storageService.setHard('inovice_category_list',response.data);
      //this.categoryList=response.data;
    },
    (err: any) => {})
  }

  
  getPayout(){

    let url = apiUrl.payout+'/'+this.property_id;
    this.commonApplicationService.get(url).subscribe(response => {
      this.payoutDetail=response.data;
      this.loadPayout=true;
    },
    (err: any) => {})
  }
  
  getRenoIncCarry(){
    let url = apiUrl.inovice_non_hud+'/'+this.property_id;
    this.commonApplicationService.get(url)
      .subscribe(
          data => {
            this.renoHomeBuyerCategory=data.data;
            this.nonHubResult=data.data;
            this.loadNunHud=true;
            if(this.nonHubResult){ 
              for(let i=0; i<this.nonHubResult.length; i++){
                this.nonHubResult[i].amount=this.nonHubResult[i].total_amount;
              }
            }
            this.storageService.setHard('nun_hud_cat',this.nonHubResult);
          },
          error => {
          }
      ); 
  }
  isMcdAccess(){
    let url = apiUrl.isMcdAccess+'/'+this.property_id;
    this.commonApplicationService.get(url).subscribe(response => {
      this.isBuyerMcdAccess=response.data;
    },
    (err: any) => {})
  }
}

export class NgbdCollapseBasic {
  public isCollapsed = false;
}
