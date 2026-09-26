import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup } from '@angular/forms';
import { AlertService, apiUrl, CommonActivityService, CommonApplicationService } from '@shared-service/_services';

@Component({
  selector: 'app-msc-form-list',
  templateUrl: './msc-form-list.component.html',
  styleUrls: ['./msc-form-list.component.css']
})
export class MscFormListComponent implements OnInit {
  
  loading: boolean;
  mscFormList:any;
  recipientList:any; 
  payersList:any;
  loader: boolean;
  limit: number=20;
  mscForm:FormGroup;
  offset:number=0;
  moreBtn:boolean=true;

  constructor(private commonApplicationService:CommonApplicationService,
              private formBuilder:FormBuilder,
              private alertService:AlertService,
              private commonActivityService:CommonActivityService) { }

  ngOnInit(): void {

    this.mscForm = this.formBuilder.group({
      payers_id: ['', ''],
      recipient_id: ['', ''],
      form_year:['']
    });
    this.getRecipient();
    this.getPayers();
    this.searchMscFormList();
  }

  searchMscFormList(){
    this.loader=true;
    let result = this.commonActivityService.getFullFormDataWithDateFormatted(this.mscForm);
    result['limit']=this.limit;
    result['offset']=0;
    this.mscFormList=[];
    this.getMscFormList(result);
  }

  getMscFormList(request){
    this.loading=true;
    let url=apiUrl.misc_1099;
    this.commonApplicationService.getSearch(url, { params: request })
    .subscribe(response => {

      let mscData=response['data'];
      
      for(let i=0; mscData.length>i; i++){
        this.mscFormList.push(mscData[i]);
      }
      if(mscData.length!=this.limit){
        this.moreBtn=false;
      }
      // this.calculateBlance();
      this.offset=(this.offset+mscData.length);

      this.loader = false;
      this.loading = false;
    },
    (err: any) => {
      this.loading = false; 
      this.loader = false;

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

  getRecipient(){
    let url=apiUrl.recipient;
    this.commonApplicationService.get(url)
    .subscribe(response => {
      if(response['data']){
        this.recipientList=response['data'];
      }
      this.loading = false;
    },
    (err: any) => {
      this.loading = false; 
    })
  }

  getPayers(){
    let url=apiUrl.payers;
    this.commonApplicationService.get(url)
    .subscribe(response => {
      if(response['data']){
        this.payersList=response['data'];
      }
      this.loading = false;
    },
    (err: any) => {
      this.loading = false; 
    })
  }

  getloadmorepages(){
    this.loader=true;
    let result = this.commonActivityService.getFullFormDataWithDateFormatted(this.mscForm);
    result['limit']=this.limit;
    result['offset']=this.offset;
    this.getMscFormList(result);
  }
}
