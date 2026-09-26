import { Component, OnInit,Input } from '@angular/core';
import { CommonApplicationService,CommonActivityService,AlertService } from '../../../shared/_services';
import { apiUrl } from '../../../config/api-url';
import {MatDialog, MatDialogRef, MAT_DIALOG_DATA} from '@angular/material/dialog';
import {ExportTypeComponent} from '../export-type/export-type.component';

@Component({
  selector: 'app-print',
  templateUrl: './print.component.html',
  styleUrls: ['./print.component.css']
})
export class PrintComponent implements OnInit {

  emailFlag:boolean=false;
  @Input() house_ids:any;
  type:string='Print'

  constructor(private dialog:MatDialog,
              private commonApplicationService:CommonApplicationService,
              private alertService:AlertService) { }

  ngOnInit() {
  }

  print(){
    if(this.house_ids.length>0){
      this.emailFlag=true;
      let data={'export_type':this.type}
      let dialogRef =this.dialog.open(ExportTypeComponent,{width: '600px',data,disableClose:true});
      dialogRef.afterClosed().subscribe(result => {
        if(result){
          result['house_ids']=this.house_ids; 
          let url = apiUrl.export+'print';
          this.commonApplicationService.post(url,result)
          .subscribe(
              response => {
                if(response['status']=='success'){
                  this.alertService.success(response.message);
                  window.open(response.data.url, "_blank");
                }else{
                  this.alertService.error(response.message);  
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

}
