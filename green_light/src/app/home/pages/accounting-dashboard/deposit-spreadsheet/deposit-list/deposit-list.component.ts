import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup } from '@angular/forms';
import { apiUrl } from '@config/api-url';
import { CommonActivityService, CommonApplicationService } from '@shared-service/_services';

@Component({
  selector: 'app-deposit-list',
  templateUrl: './deposit-list.component.html',
  styleUrls: ['./deposit-list.component.css']
})
export class DepositListComponent implements OnInit {

  loading:boolean=false;
  depositSheetInfo:any=[];
  depositSearchForm:FormGroup;
  loader:boolean=false;
  limit:number=20;
  offset:number=0;
  moreBtn:boolean=true;
  depositLender:any;
  initUrl:string=apiUrl.allDepositSpreadSheet;
  deposit_lender_category:any;
  deposit_link_category:any;
  client_list:any;
  constructor(private commonApplicationService:CommonApplicationService,
    private commonActivityService:CommonActivityService,
              private formBuilder:FormBuilder) { }

  ngOnInit(): void {
    this.depositSearchForm = this.formBuilder.group({
      property_address: [''],
      lender: [''],
      client_id:['']
    });
    this.getDepositLender();
  
  }
  
  searchDepositSheet(){
    this.loader=true;
    let result = this.commonActivityService.getFullFormDataWithDateFormatted(this.depositSearchForm);
    result['limit']=this.limit;
    result['offset']=0;
    this.depositSheetInfo=[];
    result['lender']=result['lender']?result['lender']:'';
    this.getDepositSheet(result);
  }

  getDepositSheet(request){
    this.loading=true;
    
    this.commonApplicationService.getSearch(this.initUrl, { params: request }).subscribe(response => {

    //this.commonApplicationService.get(url).subscribe(response => {
      if(response['row']){
        let depositSheet=response['row']['deposit_sheet'];
     
        this.deposit_lender_category=response['row']['lender_category'];
        this.deposit_link_category=response['row']['link_category'];

        for(let i=0; depositSheet.length>i; i++){
          this.depositSheetInfo.push(depositSheet[i]);
        }
        if(depositSheet.length!=this.limit){
          this.moreBtn=false;
        }
        // this.calculateBlance();
        this.offset=(this.offset+depositSheet.length);

      }
      this.depositSearchForm.get('lender').setValue(request['lender']);
      this.loading = false;
      this.loader = false;
      
    },
    (err: any) => {
      this.loading = false; 
      this.loader = false;
    })
  }
  getDepositLender(){
    let url = apiUrl.getLenderDepositSheet;
    this.commonApplicationService.get(url).subscribe(response => {
      this.depositLender=response['row']['deposit_lender'];
      this.client_list=response['row']['client_list'];
      let request={'limit':this.limit,'offset':this.offset,'lender':this.depositLender[0].id?this.depositLender[0].id:''};
      this.getDepositSheet(request);
    },
    (err: any) => {
    })
  }

  onChangeClient($event){
    if($event.target.value){
      this.depositSearchForm.get('lender').reset();
    }
    
  }

  onChangeLender($event){
    if($event.target.value){
      this.depositSearchForm.get('client_id').reset();
    }
   
  }
  // getloadmorepages(){
  //   this.loader=true;
  //   let result = this.commonActivityService.getFullFormDataWithDateFormatted(this.depositSearchForm);
  //   result['limit']=this.limit;
  //   result['offset']=this.offset;
  //   this.getDepositSheet(this.initUrl,result);
  // }



}
