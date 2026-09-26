import { Component, OnInit,HostListener} from '@angular/core';
import {MatDialog, MatDialogRef, MAT_DIALOG_DATA} from '@angular/material/dialog';
import { Router,ActivatedRoute }    from '@angular/router';

import { apiUrl } from '../../config/api-url';
import { CommonApplicationService,CommonActivityService,AlertService } from '../../shared/_services';


import { BuyItComponent } from './buy-it/buy-it.component';
import {AreaInviteComponent } from './area-invite/area-invite.component';
import {PassOnItComponent } from './pass-on-it/pass-on-it.component';
import {InstitutionalLenderComponent} from './institutional-lender/institutional-lender.component';

import {DepositeLenderComponent} from './deposite-lender/deposite-lender.component';
import {GetPictureComponent} from './get-picture/get-picture.component';
import {TitleSearchComponent} from './title-search/title-search.component';

@Component({
  selector: 'app-property-button',
  templateUrl: './property-button.component.html',
  styleUrls: ['./property-button.component.css']
})
export class PropertyButtonComponent implements OnInit {
  
  property_id: string;
  scrollFlag:boolean;
  loading:boolean=false;
  loading_quete:boolean=false;
  lenderRequestLoader:boolean=false;
  contactOwnerLoader:boolean=false;

  constructor(private route: ActivatedRoute,
              private dialog:MatDialog,
              private commonActivityService :CommonActivityService,
              private commonApplicationService:CommonApplicationService,
              private alertService: AlertService) { }

  ngOnInit() {
    this.property_id = this.route.snapshot.paramMap.get('property_id');

  }

  @HostListener("window:scroll", [])
  onWindowScroll() {
    var scrollHeight=document.body.scrollHeight;
    var scrollPosition = window.outerHeight +window.scrollY;
    var footerContentHeight=(document.querySelector('.property_queue') as HTMLElement).clientHeight+114;
    if ((scrollHeight - scrollPosition)< footerContentHeight) {
      this.scrollFlag=false;
    }
    else{
      this.scrollFlag=true;
    }
  }

  openDialog(type){
    switch (type) {
      case "buyIt":
          this.dialog.open(BuyItComponent,{ width: '800px',disableClose:true,data:{property_id:this.property_id}} );
      break;
      case "areaInvite":
          this.dialog.open(AreaInviteComponent,{ width: '600px',disableClose:true,data:{property_id:this.property_id}} );
      break;
      case "passOnIt":
          this.dialog.open(PassOnItComponent,{ width: '600px',disableClose:true,data:{property_id:this.property_id}} );
      break;
      case "institutionalLender":
        this.dialog.open(InstitutionalLenderComponent,{ width: '600px',disableClose:true,data:{property_id:this.property_id}} );
      break; 
      case "getPicture":
        this.dialog.open(GetPictureComponent,{ width: '600px',disableClose:true,data:{property_id:this.property_id}} );
      break; 
      case "depositLender":
        this.dialog.open(DepositeLenderComponent,{ width: '600px',disableClose:true,data:{property_id:this.property_id}} );
      break; 
      case "titleSearch":
        this.dialog.open(TitleSearchComponent,{ width: '600px',disableClose:true,data:{property_id:this.property_id}} );
      break; 
      default:
        // code...
      break;
    }
  }


  get_insurance_quete(){
    // Save info detail
    this.loading_quete = true;
    let url = apiUrl.insurance_quote+"/"+this.property_id;
    this.commonApplicationService.post(url)
      .subscribe(
          data => {
            if(data.status=='success'){
              this.alertService.success(data.message);  
            }
            if(data.status=='failed'){
              this.alertService.success(data.message);  
            }
            this.loading_quete=false;
          },
          error => {
            this.alertService.common(error); 
          }
      ); 
  }

  print_page(){
    window.print();
  }

  contactRequest(requestType='lender'){

    if(requestType=='lender'){
      this.lenderRequestLoader=true;
    } 
    if(requestType=='subto'){
      this.contactOwnerLoader=true;
    } 
    let url = apiUrl.contactRequst;
    let requestData={'house_id':this.property_id,'request_type':requestType};
    this.commonApplicationService.post(url,requestData)
      .subscribe(
          data => {
            if(data.status=='success'){
              this.alertService.success(data.message);  
            }
            if(data.status=='failed'){
              this.alertService.success(data.message);  
            }
            this.lenderRequestLoader=false;
            this.contactOwnerLoader=false;
          },
          error => {
            this.alertService.common(error); 
          }
      ); 
  }

  
}
