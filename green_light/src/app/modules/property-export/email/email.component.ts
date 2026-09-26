import { Component, OnInit, Input } from '@angular/core';
import { CommonApplicationService,CommonActivityService,AlertService } from '../../../shared/_services';
import { apiUrl } from '../../../config/api-url';
import {MatDialog, MatDialogRef, MAT_DIALOG_DATA} from '@angular/material/dialog';
import {ExportTypeComponent} from '../export-type/export-type.component';


@Component({
  selector: 'app-email',
  templateUrl: './email.component.html',
  styleUrls: ['./email.component.css']
})
export class EmailComponent implements OnInit {

  emailFlag:boolean=false;
  @Input() house_ids:any;
  type:string='Email'
  
  constructor(private dialog:MatDialog,
              private commonActivityService :CommonActivityService,
              private commonApplicationService:CommonApplicationService,
              private alertService:AlertService) { }

  ngOnInit() {
  }

  email(){
    if(this.house_ids.length>0){
      let data={'export_type':this.type}
      let dialogRef =this.dialog.open(ExportTypeComponent,{width: '600px',data,disableClose:true});
      dialogRef.afterClosed().subscribe(result => {
        if(result){
          result['house_ids']=this.house_ids; 
          let url = apiUrl.export+'email';
          this.commonApplicationService.post(url,result)
          .subscribe(
              response => {
                if(response['status']=='success'){
                  this.alertService.success(response.message);  
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
    }else{
      this.alertService.error('Please select any property.');
    }


    // if(this.house_ids.length>0){

    //   this.emailFlag=true;
    //   let url = apiUrl.export+'email';
    //   this.commonApplicationService.post(url,{house_ids:this.house_ids})
    //   .subscribe(
    //       response => {
    //         if(response['status']=='success'){
    //           //this.alertService.success(response.message);  
    //           window.open(response.data.url,'_blank');
    //         }else{
    //           this.alertService.common(response); 
    //         }
    //       },
    //       error => {
    //         this.alertService.common(error);
    //         // this.alertService.error(error);  
    //       }
    //   ); 
    // }
    // else{
    //   this.alertService.error('Please select any property.');
    // }
  }

}
