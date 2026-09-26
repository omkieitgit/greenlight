import { DOCUMENT } from '@angular/common';
import { Component, Inject, OnInit } from '@angular/core';
import { FormArray, FormBuilder, FormGroup } from '@angular/forms';
import { MAT_DIALOG_DATA } from '@angular/material/dialog';
import { apiUrl } from '@config/api-url';
import { AlertService, CommonActivityService, CommonApplicationService } from '@shared-service/_services';

@Component({
  selector: 'app-sqft-calculation',
  templateUrl: './sqft-calculation.component.html',
  styleUrls: ['./sqft-calculation.component.css']
})
export class SqftCalculationComponent implements OnInit {
  loading:boolean=false;
  sqftCalForm:FormGroup;
  cma_type: any;
  property_id: any;
  sqft_data_result:any;
  constructor(private formBuilder:FormBuilder,             
              @Inject(DOCUMENT) private document: any,
              private commonActivityService:CommonActivityService,
              private commonApplicationService:CommonApplicationService,
              private alertService:AlertService,
              @Inject(MAT_DIALOG_DATA) public data,
  ) {
    this.property_id=this.data.property_id;
    this.cma_type=this.data.cma_arv_type;
    if(this.property_id !== undefined){
      this.getCmaArvInfo();
    }
  }

  ngOnInit(): void {
    
    this.sqftCalForm = this.formBuilder.group({
      sqft_info_data: this.formBuilder.array([
        this.formBuilder.group({
          price_sqft_amount: [''],
          lot_acres_to_sqft: ['']
        })
      ]),
      cma_type:[]
    });
  }

  calSqlft(i){
    let sqftrow=this.sqftCalForm.controls.sqft_info_data["controls"][i];

    let acr=sqftrow.get('lot_acres_to_sqft').value;
    if(acr){
      let sqftVal:any=(acr*43560).toFixed(2);
      sqftrow.get('lot_acres_to_sqft').setValue(sqftVal);
      let priceperSq=sqftrow.get('price_sqft_amount').value;

      let totalCalDay=this.document.querySelector('.cal_days_'+i);
      if(totalCalDay){
        totalCalDay.innerHTML=(priceperSq/sqftVal).toFixed(2);   
      }
    }
  }

  


  addMoreField() : void
  {
   const control = <FormArray>this.sqftCalForm.controls.sqft_info_data;
   control.push(this.formBuilder.group({
      price_sqft_amount: [''],
      lot_acres_to_sqft: ['']
    }));
  }

  saveInfo(){

    let adomForm=this.sqftCalForm.get('sqft_info_data')['controls'];

    let adom_date=[];
    for(let i=0; i<adomForm.length; i++){
      let adom = this.commonActivityService.getFullFormDataWithDateFormatted(adomForm[i]);
      if(adom['price_sqft_amount'] && adom['lot_acres_to_sqft'])
        adom_date.push(adom);
    }
    let input = new FormData();
    input.append("cma_type", this.cma_type);
    input.append("json", JSON.stringify(adom_date));

    let url = apiUrl.cma_arv_sqft+'/'+this.property_id;
    this.commonApplicationService.post(url, input)
    .subscribe(
        response => {
          this.alertService.success(response.message);  
          this.loading = false;
        },
        error => {
          this.loading = false;
          this.alertService.common(error);  
        }
    ); 
    
  }

  getCmaArvInfo(){
    this.loading = true;
    let url = apiUrl.cma_arv_sqft+'/'+this.property_id;
    this.commonApplicationService.get(url).subscribe(response =>{
      this.loading = false;
      if(response['data'].length>0){
        this.sqft_data_result = response['data'];
        this.sqft_data_result=this.sqft_data_result.filter(adom_type=>(adom_type.cma_type==this.cma_type));
        if(this.sqft_data_result.length>0){
          this.populateDataInAdom();
        }
      }
    })
  }


  populateDataInAdom(){
    let adom_data=JSON.parse(this.sqft_data_result[0].sqft_json_data);
    if(adom_data.length){
        if(adom_data.length == 1){
             this.fillAdomForm(0,adom_data);
        }else{
            for(var i=0; i<adom_data.length; i++ ){
              this.addMoreField(); 
              this.fillAdomForm(i,adom_data);
            }
        }
    }     
  }

  fillAdomForm(index:number,adom_data){
    let adomInfo=this.sqftCalForm.get('sqft_info_data')['controls'];
    adomInfo[index].controls.price_sqft_amount.setValue(adom_data[index].price_sqft_amount);
    adomInfo[index].controls.lot_acres_to_sqft.setValue(adom_data[index].lot_acres_to_sqft);
    setTimeout(()=>{
      let totalCalDay=this.document.querySelector('.cal_days_'+index);
      if(totalCalDay){
        totalCalDay.innerHTML=(adom_data[index]?.price_sqft_amount/adom_data[index]?.lot_acres_to_sqft).toFixed(2);   
      }
    },10)
  }

}
