import { Component, Inject, OnInit } from '@angular/core';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material/dialog';
import { apiUrl } from '@config/api-url';
import { AlertService, CommonApplicationService } from '@shared-service/_services';

@Component({
  selector: 'app-merge-component',
  templateUrl: './merge-component.component.html',
  styleUrls: ['./merge-component.component.css']
})
export class MergeComponentComponent implements OnInit {
  mergePropertyList:any;
  loading:boolean=false;

  constructor(private dialogRef:MatDialogRef<MergeComponentComponent>,
             private commonApplicationService:CommonApplicationService,
             private alertService:AlertService,
             @Inject(MAT_DIALOG_DATA) public data) { }

  ngOnInit(): void {
    this.mergePropertyList=this.data;
  }

  validateForm(){
    if(confirm("Are you sure you want to merge these records?, Once you click Ok the record cannot be undone.")){
      this.loading=true;
      let url = apiUrl.mergeRecords;
      this.commonApplicationService.post(url,{merge_property_data:this.mergePropertyList}).subscribe(response => {
        if(response.status=='success'){
          //this.dialogRef.close();
          this.dialogRef.close(this.mergePropertyList);
          //this.alertService.success(response.message);
        }else{
          this.alertService.error(response.message); 
        }
        this.loading=false;
      },
      (err: any) => {
        this.loading=false;
        this.alertService.common(err); 
      })
    }
  }
}
