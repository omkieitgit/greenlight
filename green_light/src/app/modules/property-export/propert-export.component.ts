import { Component, OnInit,Input } from '@angular/core';
import {MatDialog, MatDialogRef, MAT_DIALOG_DATA} from '@angular/material/dialog';
import { CommonApplicationService,CommonActivityService,AlertService } from '../../shared/_services';
import {ExportDialogComponent} from './export-dialog/export-dialog.component';
import { apiUrl } from '../../config/api-url';
import {saveAs} from 'file-saver';
import { StorageService } from '@shared-service/_services/storage.service';

@Component({
  selector: 'app-propert-export',
  templateUrl: './propert-export.component.html',
  styleUrls: ['./propert-export.component.css']
})
export class PropertExportComponent implements OnInit {

  @Input() public house_ids:any;

  constructor(private dialog:MatDialog,
              private commonApplicationService:CommonApplicationService,
              private alertService:AlertService,
              private storageService:StorageService) { }

  ngOnInit() {
  }

  export(){
    if(this.house_ids.length>0){
      let dialogRef =this.dialog.open(ExportDialogComponent,{width: '600px',disableClose:true});
      dialogRef.afterClosed().subscribe(result => {

        if(result){
          result['house_ids']=this.house_ids; 
          let url = apiUrl.export+'export';
          result.responseType = "blob";
          result['search_fields']=this.storageService.getHard('search_fields');
  
          let options = {
            headers: { "Content-Type": "application/json", Accept: "application/pdf" },
            responseType: "blob"
          };
          // application/pdf
          // application/vnd.ms-excel, 
          // application/csv
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
                  
                  if(result.export_type == "csv")
                  {
                    filename = "GLPF_"+new Date().getTime()+".csv";
                  }
                  else  if(result.export_type == "xls")
                  {
                    filename = "GLPF_"+new Date().getTime()+".xls";
                  }
                  else{
                    filename = "GLPF_"+new Date().getTime()+".pdf";
                  }
                  
                }
                
                saveAs(response, filename);
                console.log(options);
                console.log(response);
              
              },
              
              error => {
                //Rativardhan: Only for PDF,image,CSV,XLS this situation we will take error from statusText
                this.alertService.error(error.statusText);  
              }
          ); 
        }
       


      });
    }else{
      this.alertService.error('Please select any property.');
    }
  }

  // Not In use but for future reference.
  downloadFIle(response){
    // It is necessary to create a new blob object with mime-type explicitly set
            // otherwise only Chrome works like it should
            var newBlob = new Blob([response], { type: "application/pdf" });

            // IE doesn't allow using a blob object directly as link href
            // instead it is necessary to use msSaveOrOpenBlob
            if (window.navigator && window.navigator.msSaveOrOpenBlob) {
                window.navigator.msSaveOrOpenBlob(newBlob);
                return;
            }

            // For other browsers: 
            // Create a link pointing to the ObjectURL containing the blob.
            const data = window.URL.createObjectURL(newBlob);

            var link = document.createElement('a');
            link.href = data;
            link.download = "Test.pdf";
            // this is necessary as link.click() does not work on the latest firefox
            link.dispatchEvent(new MouseEvent('click', { bubbles: true, cancelable: true, view: window }));

            setTimeout(function () {
                // For Firefox it is necessary to delay revoking the ObjectURL
                window.URL.revokeObjectURL(data);
                link.remove();
            }, 100);
            
  }

  // wholetail(){
    
  //   if(this.house_ids.length>0){
  //     let data={};
  //     let dialogRef =this.dialog.open(WholesaleRetailDialogComponent,{ width: '600px',data:data,disableClose:true} );
  //     dialogRef.afterClosed().subscribe(result => {
  //       result['house_ids']=this.house_ids; 
  //       let url = apiUrl.export+'wholesale_retail';
  //       this.commonApplicationService.post(url,result)
  //       .subscribe(
  //           response => {
  //             this.alertService.common(response);
  //           },
  //           error => {
  //             this.alertService.error(error);  
  //           }
  //       ); 
  //     });
  //   }else{
  //     this.alertService.error('Please select any property.');
  //   }
    
  // }
}
