import { Component, OnInit,Inject } from '@angular/core';
import { FormControl, FormArray,FormBuilder, FormGroup, Validators } from '@angular/forms';
import { apiUrl } from '../../../config/api-url';
import { CommonApplicationService,CommonActivityService,AlertService } from '../../../shared/_services';
import {MatDialog, MAT_DIALOG_DATA} from '@angular/material/dialog';

@Component({
  selector: 'app-institutional-lender',
  templateUrl: './institutional-lender.component.html',
  styleUrls: ['./institutional-lender.component.css']
})
export class InstitutionalLenderComponent implements OnInit {

  institutionalLenderForm: FormGroup;
  invalidFields: any;  
  submitted = false;
  loading = false;
  loadingMessage: any;
  propErr: boolean = false;
  propErrMsg: any = "";
  modifyBtn : boolean = false;
  property_id: string;
  whatTypeLenderItem = [
    {text: 'Hard Money', key: 'Hard Money'},
    {text: 'Private money', key: 'Private money'}, 
    {text: 'Bank Loan', key: 'Bank Loan'}, 
    {text: 'Soft Money', key: 'Soft Money'}, 
    {text: 'Credit Union Loan', key: 'Credit Union Loan'}, 
    {text: 'Institutional Lender', key: 'Institutional Lender'}, 
  ];
  constructor(private formBuilder: FormBuilder,
              private commonActivityService :CommonActivityService,
              private commonApplicationService:CommonApplicationService,
              private alertService: AlertService,
              @Inject(MAT_DIALOG_DATA) public data: any) { }

  ngOnInit() {
    this.property_id = this.data.property_id;
    
    let checkboxGroup = new FormArray(this.whatTypeLenderItem.map(item => new FormGroup({
      id: new FormControl(item.key),
      text: new FormControl(item.text),
      checkbox: new FormControl(false)
    })));

    // create a hidden reuired formControl to keep status of checkbox group
    let hiddenControl = new FormControl(this.mapItems(checkboxGroup.value), Validators.required);
    // update checkbox group's value to hidden formcontrol
    checkboxGroup.valueChanges.subscribe((v) => {
      hiddenControl.setValue(this.mapItems(v));
    });
    
    this.institutionalLenderForm = this.formBuilder.group({
      contact_number: [ "", Validators.required],
      items: checkboxGroup, 
      notes: ["", Validators.required], 
      credit_score: ["", Validators.required],  
      hown_many_fix_n_flip: [""],  
      retal_currently_own: [""], 
      location_acquiring: [""],  
      cash_in_hand: [""],   
      gal_401k: [""],   
      bankruptcies: [""],   
      rehab_currently: [""], 
      gal_real_estate_12: [""], 
      what_type_lender: hiddenControl
    });
     
    
  }

  mapItems(items) {
    let selectedItems = items.filter((item) => item.checkbox).map((item) => item.id);
    return selectedItems.length ? selectedItems : null;
  }

  get f() { return this.institutionalLenderForm.controls; }

  // Property Validation
  validateForm(data: any) {  
    
    let result = this.commonActivityService.getFullFormData(data);
    this.invalidFields = this.commonActivityService.findInvalidControls(data);
    this.submitted = true;
    if(this.institutionalLenderForm.invalid) { 
      return;
    }    
    this.saveInfoDetails(result);  
  }

  // Save info detail
  saveInfoDetails(data: any){
    this.loading = true;
    let url = apiUrl.lender_it+"/"+this.property_id;
    this.commonApplicationService.post(url, data)
        .subscribe(
            data => {
              if(data.status=='success'){
                this.alertService.success(data.message);  
              }
              if(data.status=='failed'){
                this.alertService.success(data.message);  
              }
              this.loading = false;

            },
            error => {
              this.loading = false;
              this.alertService.common(error); 
            }
        ); 
  }

}
