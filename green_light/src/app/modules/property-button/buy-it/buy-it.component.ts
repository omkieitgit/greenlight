import { Component, Inject, OnInit } from '@angular/core';
import { NgbDateAdapter, NgbDateStruct } from '@ng-bootstrap/ng-bootstrap';
import { FormControl, NgForm,FormArray, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router,ActivatedRoute }    from '@angular/router';
import { first } from 'rxjs/operators';
import { apiUrl } from '../../../config/api-url';
import { CommonApplicationService } from '../../../shared/_services';
import { CommonActivityService,AlertService} from '../../../shared/_services';
import {MatDialog, MAT_DIALOG_DATA, MatDialogRef} from '@angular/material/dialog';

@Component({ 
  selector: 'buy-it',
  templateUrl: './buy-it.html',
})

export class BuyItComponent  implements OnInit{
  buyItForm: FormGroup;
  invalidFields: any;  
  submitted = false;
  loading = false;
  loadingMessage: any;
  propErr: boolean = false;
  propErrMsg: any = "";
  modifyBtn : boolean = false;
  property_id: string;
  designation: any = [
    {
      name: "Buyer",
      value: "buyer"
    },
    {
      name: "Lender",
      value: "lender",
      selected: true
    },
    {
      name: "SubTo",
      value: "subto",
      selected: true
    }
  ];
  constructor(private formBuilder: FormBuilder,
        private router: Router,
        private route: ActivatedRoute,
        private commonActivityService :CommonActivityService,
        private commonApplicationService:CommonApplicationService,
        private alertService: AlertService,
        private dialogRef:MatDialogRef<BuyItComponent>,
        @Inject(MAT_DIALOG_DATA) public data: any
       ) { 
         
   }
  

  ngOnInit() {
    this.buyItForm = this.formBuilder.group({
      did_you_buy: [ "", Validators.required],
      //did_you_picture: ["", Validators.required],
      //please_submit: ["", Validators.required],
      //buyit_repair_cost: ["", Validators.required],
      buyit_estimation_arv_value: ["", Validators.required],
      highest_offer_bid: ["", Validators.required],
      //designation: ["", Validators.required],
      designation: this.formBuilder.array([], [Validators.required]),
      notes: ["", Validators.required],
      request_type:['buyit']
    });
    console.log(this.buyItForm);
    this.property_id = this.data.property_id;
  }

  onCheckboxChange(e) {
    const checkArray: FormArray = this.buyItForm.get('designation') as FormArray;

    if (e.target.checked) {
      checkArray.push(new FormControl(e.target.value));
    } else {
      let i: number = 0;
      checkArray.controls.forEach((item: FormControl) => {
        if (item.value == e.target.value) {
          checkArray.removeAt(i);
          return;
        }
        i++;
      });
    }
  }

  get f() { return this.buyItForm.controls; }

  // Property Validation
  validateForm(data: any) {     
    let result = this.commonActivityService.getFullFormData(data);
    this.invalidFields = this.commonActivityService.findInvalidControls(data);
    this.submitted = true;

    if(this.buyItForm.invalid) { 
      return;
    }    
    
    if (this.buyItForm.controls.did_you_buy.value != 'yes'
      //|| this.buyItForm.controls.did_you_picture.value != 'yes'
      //|| this.buyItForm.controls.please_submit.value != 'yes'
      //|| this.buyItForm.controls.do_money_finance.value != 'yes'
    ) {
      this.alertService.error("You must be able to answer yes to all 4 or these questions for us to be able to pursue this property.");  
      return;
    }
    
    // FORM SUBMITTED
    this.saveInfoDetails(result);  
  }

  // Save info detail
  saveInfoDetails(data: any){
    this.loading = true;
    //console.log("savingdata>"+this.propertyForm);
    let url = apiUrl.save_buy_it+"/"+this.property_id;
    this.commonApplicationService.post(url, data)
        .subscribe(
            data => {
              if(data.status=='success'){
                this.alertService.success(data.message);  
                this.dialogRef.close();
              }else{
                this.alertService.error(data.message);  
              }
              this.loading = false;
            },
            error => {
                this.loading = false;
                this.alertService.common(error);
            }
        ); 
  }

  // "You must be able to answer yes to all 6 or these questions for us to be able to pursue this property.

  // "
}

