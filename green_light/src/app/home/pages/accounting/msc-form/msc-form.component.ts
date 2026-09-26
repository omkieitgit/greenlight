import { Component, Input, OnInit } from '@angular/core';
import { apiUrl } from '@config/api-url';
import { AlertService, CommonApplicationService, CommunicationService } from '@shared-service/_services';

@Component({
  selector: 'app-msc-form',
  templateUrl: './msc-form.component.html',
  styleUrls: ['./msc-form.component.css']
})
export class MscFormComponent implements OnInit {

  loading:boolean=false;
  @Input() property_id;
  mscFormList:any;
  panelOpen:boolean=false;

  constructor(private commonApplicationService:CommonApplicationService,
              private alertService:AlertService,
              private communicationService:CommunicationService) { }

  ngOnInit(): void {
    this.communicationService.getUpdateRenovation().subscribe(result=>{
        if(this.panelOpen && result){
          this.get1099MscForm();
        }
    })
  }

  panelExpand(flag){
    this.panelOpen=true;
    this.get1099MscForm();
  }
  
  get1099MscForm(){
    this.loading=true;
    let url = apiUrl.mscForm+'/'+this.property_id;
    this.commonApplicationService.get(url).subscribe(response => {
      if(response['data']){
       this.mscFormList=response['data'];
      }
      this.loading = false;
    },
    (err: any) => {
      this.loading = false;
    })
  }

  clientMscForm(selectedMsc){
    this.loading=true;

    let url = apiUrl.msc1099FormPdf;
    let options = {
      headers: { "Content-Type": "application/json", Accept: "application/pdf" },
      responseType: "blob"
    };
    let data={
      payers_id:selectedMsc.payers_id,
      recipients_id:selectedMsc.recipients_id,
      amount:selectedMsc.amount
    };

    this.commonApplicationService.postDownload(url,data, options).subscribe(response => {
        const fileURL = URL.createObjectURL(response);
        window.open(fileURL, '_blank');
        this.loading = false;
    },
    (err: any) => {
      this.alertService.common(err);
      this.loading = false;
    });
  }

}
