import { Component, OnInit,Input,ViewChild, ElementRef } from '@angular/core';

import {MatDialog, MatDialogRef, MAT_DIALOG_DATA} from '@angular/material/dialog';
import {WholesaleRetailDialogComponent} from './wholesale-retail-dialog/wholesale-retail-dialog.component';
import { CommonApplicationService,CommonActivityService,AlertService } from '../../../shared/_services';
import { apiUrl } from '../../../config/api-url';


@Component({
  selector: 'app-wholesale-retail',
  templateUrl: './wholesale-retail.component.html',
  styleUrls: ['./wholesale-retail.component.css']
})
export class WholesaleRetailComponent implements OnInit {

  @Input() property_id;

  constructor(private dialog:MatDialog,
              private commonApplicationService:CommonApplicationService,
              private alertService:AlertService) { }

    @ViewChild('content') content: ElementRef;

    ngOnInit() {
    }

    wholesale_retail(e){
      e.preventDefault();
      e.stopPropagation();
      let data={property_id:this.property_id};
      let dialogRef =this.dialog.open(WholesaleRetailDialogComponent,{ width: '600px',data:data,disableClose:true} );
      dialogRef.afterClosed().subscribe(result => {
        if(result){
          result['house_id']=this.property_id; 
          let url = apiUrl.export+'wholesale_retail';
          this.commonApplicationService.post(url,result)
          .subscribe(
              response => {
                if(response['status']=='success'){
                  window.open(response.data.url, "_blank");
                  //this.alertService.success(response.message);  
                }else{
                  this.alertService.error(response.message[0]);  
                }
              },
              error => {
                this.alertService.common(error);
              }
          ); 
        }
      });
    }

}
