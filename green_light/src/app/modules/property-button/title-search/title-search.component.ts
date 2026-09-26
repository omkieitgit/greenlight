import { Component, OnInit,Inject } from '@angular/core';
import { apiUrl } from '../../../config/api-url';
import { CommonApplicationService,AlertService } from '../../../shared/_services';
import {MatDialogRef, MAT_DIALOG_DATA} from '@angular/material/dialog';

@Component({
  selector: 'app-title-search',
  templateUrl: './title-search.component.html',
  styleUrls: ['./title-search.component.css']
})
export class TitleSearchComponent implements OnInit {

  loading:boolean=false;
  property_id: string;

  constructor(private commonApplicationService:CommonApplicationService,
              private alertService: AlertService,
              private dialogRef: MatDialogRef<TitleSearchComponent>,
              @Inject(MAT_DIALOG_DATA) public data: any) { }

  ngOnInit() {
    this.property_id = this.data.property_id;
  }

  titleSearch(){
    this.loading = true;
    let url = apiUrl.title_search+"/"+this.property_id;
    this.commonApplicationService.post(url)
        .subscribe(
            data => {
              if(data.status=='success'){
                this.dialogRef.close();
                this.alertService.success(data.message);  
              }
              if(data.status=='failed'){
                this.alertService.success(data.message);  
              }
              this.loading=false;
            },
            error => {
                this.loading=false;
                this.alertService.common(error);
            }
        ); 
  }
}
