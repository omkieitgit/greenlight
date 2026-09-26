import { Component, Input, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { AlertService, apiUrl, CommonActivityService, CommonApplicationService } from '@shared-service/_services';

@Component({
  selector: 'app-mcd-other-info',
  templateUrl: './mcd-other-info.component.html',
  styleUrls: ['./mcd-other-info.component.css']
})
export class McdOtherInfoComponent implements OnInit {

  homeMcdOtherInfoForm:FormGroup
  submitted:boolean=false;
  showClientBox:boolean=false;
  showPayerBox:boolean=false;
  invalidFields:any;
  loading:boolean=false;
  payers:any;
  @Input() property_id;
  @Input() client_list;
  @Input() payers_list;
  @Input() other_mcd_info;
  @Input() property_info;

  constructor(private formBuilder:FormBuilder,
              private commonActivityService:CommonActivityService,
              private commonApplicationService:CommonApplicationService,
              private alertService:AlertService
              ) { }

  ngOnInit(): void {
    
    if(this.other_mcd_info?.payers_id){
      this.payers=this.payers_list.find(x=>x.id==this.other_mcd_info.payers_id);
    }

    this.homeMcdOtherInfoForm = this.formBuilder.group({ 
      id:[],
      client_id:[this.other_mcd_info?.client_id,[Validators.required]],
      client_name:[''],
      payers_id:[this.other_mcd_info?.payers_id,[Validators.required]],
      payers_name:[this.payers?.payers_name],
      payers_address:[this.payers?.payers_address],
      payers_tin:[this.payers?.payers_tin],
      is_sold:[this.other_mcd_info?.is_sold]
    });
    
  }

  get f() { return this.homeMcdOtherInfoForm.controls; }

  onChangeClient($event){
    this.showClientBox=false;
    if($event.target.value=='new'){
      this.showClientBox=true;
    }
  }

  onChangePayer($event){
    if($event.target.value=='new'){
      this.showPayerBox=true;
    }
    else{
      this.showPayerBox=false;
      this.payers=this.payers_list.find(x=>x.id==$event.target.value);
      this.homeMcdOtherInfoForm.get('payers_name').setValue(this.payers?.payers_name);
      this.homeMcdOtherInfoForm.get('payers_address').setValue(this.payers?.payers_address);
      this.homeMcdOtherInfoForm.get('payers_tin').setValue(this.payers?.payers_tin);
    }
  }

  validateForm(data){
    let result = this.commonActivityService.getFullFormDataWithDateFormatted(data);
    this.invalidFields = this.commonActivityService.findInvalidControls(data);
    console.log("Form Invalid Filleds = ",this.invalidFields);
    this.submitted = true;
    console.log('niform');
    if(this.homeMcdOtherInfoForm.invalid) { 
      console.log('Form is invalid, Fill all fields.');
      //this.propertyForm.get('prop_address').markAsTouched();      
      return;
    }
    this.saveOtherMcdInfo(result);    
  }

  saveOtherMcdInfo(data: any){
    this.loading = true;
    let url = apiUrl.mcd_other_info+'/'+this.property_id;
    this.commonApplicationService.post(url, data)
        .subscribe(
            response => {
              let itemIndex = this.client_list?this.client_list.findIndex(item => item.id == response.data?.client_info?.id):'-1';
              if(itemIndex < 0){
                this.client_list.push(response.data?.client_info);
              }

              let index = this.payers_list?this.payers_list.findIndex(item => item.id == response.data?.payers_info?.id):'-1';
              if(index < 0){
                this.payers_list.push(response.data?.payers_info);
              }

              this.homeMcdOtherInfoForm.get('client_id').setValue(response.data.client_id);
              this.homeMcdOtherInfoForm.get('payers_id').setValue(response.data.payers_id);
              this.alertService.success(response.message);  
              this.loading = false;
            },
            error => {
              this.loading = false;
              this.alertService.common(error); 
            }
        ); 
  }

  
}
