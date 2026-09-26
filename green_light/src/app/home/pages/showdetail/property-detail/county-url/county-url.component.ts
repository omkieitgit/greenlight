import { Component, OnInit,Input,Output,EventEmitter } from '@angular/core';
import {MatDialog,MatDialogRef, MAT_DIALOG_DATA} from '@angular/material/dialog';
import {CountyUrlDialogComponent} from './county-url-dialog/county-url-dialog.component';
import { CommonApplicationService,AlertService } from '@shared-service/_services';
import { apiUrl } from '@config/api-url';
import {StorageService} from '@shared-service/_services/storage.service';

@Component({
  selector: 'app-county-url',
  templateUrl: './county-url.component.html',
  styleUrls: ['./county-url.component.css']
})
export class CountyUrlComponent implements OnInit {
  @Input() state;
  @Input() county;
  private countyResult:any;
  @Output() valueChange = new EventEmitter();

  constructor(private dialog:MatDialog,
              private alertService:AlertService,
              private commonApplicationService:CommonApplicationService,
              private storageService:StorageService,
              ) { }

  ngOnInit() {
    this.getCountyDetail();
  }

  getCountyDetail(){
    if(this.storageService.get('county_info')==""){
      let url = apiUrl.county_url;
      this.commonApplicationService.get(url).subscribe(response => {
        if(response !== undefined){      
          this.storageService.set("county_info",response.row);      
        }
      },
        (err: any) => {
          this.alertService.common(err);
        })
    }
  }

  get_county_url(){
    this.countyResult=this.storageService.get('county_info');
    let data={'state':this.state,'county':this.county,'result':this.countyResult};
    let dialogRef=this.dialog.open(CountyUrlDialogComponent,{ width: '900px',data:data,disableClose:true} );
    dialogRef.afterClosed().subscribe(info => {
      if(info){
        this.countyResult=this.storageService.get('county_info');
        this.valueChange.emit(this.countyResult[info.final_state][info.final_county]);
      }
    });
    
  }


}
