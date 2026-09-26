import { Component, Inject,OnInit } from '@angular/core';
import { FormControl, FormArray,FormBuilder, FormGroup, Validators } from '@angular/forms';
import { apiUrl } from '../../../config/api-url';
import { CommonApplicationService,CommonActivityService,AlertService } from '../../../shared/_services';
import {MatDialogRef, MAT_DIALOG_DATA} from '@angular/material/dialog';
declare var braintree: any;

@Component({
  selector: 'app-get-picture',
  templateUrl: './get-picture.component.html',
  styleUrls: ['./get-picture.component.css']
})
export class GetPictureComponent implements OnInit {

  getPictureForm: FormGroup;
  invalidFields: any;  
  submitted = false;
  loading = false;
  loadingMessage: any;
  propErr: boolean = false;
  propErrMsg: any = "";
  modifyBtn : boolean = false;
  property_id: string;
  result:any;
  
  amount:any=1;
  visiblePayment:boolean=false;
  showBraintreeButton = false;
  showBraintreeButtonText = "Pay Now";
  braintreeClientToken: any;
  integration: any
  braintreeLocal:any;
  crawlSpaceFlag:boolean=false;
  numberOfHouse:number=0;

  
  constructor(private formBuilder: FormBuilder,
    private commonActivityService :CommonActivityService,
    private commonApplicationService:CommonApplicationService,
    private alertService: AlertService,
    private dialogRef: MatDialogRef<GetPictureComponent>,
    @Inject(MAT_DIALOG_DATA) public data: any) { }

  ngOnInit() {
    this.property_id = this.data.property_id;
    this.braintreeLocal = braintree;

    this.getPictureForm = this.formBuilder.group({
      number_home: [ "", Validators.required],
      notes: ["", Validators.required], 
    });

    this.optionB.valueChanges.subscribe(checked => {
      if (checked=='crawl_space') {
        this.crawlSpaceFlag=true;
        const validators = [ Validators.required];
        this.getPictureForm.addControl('number_of_home', new FormControl('', validators));
      } else {
        this.crawlSpaceFlag=false;
        this.getPictureForm.removeControl('number_of_home');
      }
      this.getPictureForm.updateValueAndValidity();
    });
   
  }
  
  get optionB() {
    return this.getPictureForm.get('number_home') as FormControl;
  }

  crawlSpaceAmount(event){
    this.numberOfHouse=event.target.value*75;
  }


  get f() { return this.getPictureForm.controls; }

  // Property Validation
  validateForm(data: any) {  
    
    this.result = this.commonActivityService.getFullFormData(data);
    this.invalidFields = this.commonActivityService.findInvalidControls(data);
    this.submitted = true;
    if(this.getPictureForm.invalid) { 
      return;
    }    
    this.visiblePayment=true;
    
    if(this.getPictureForm.get('number_home').value=='crawl_space'){
      this.amount=75*this.getPictureForm.get('number_of_home').value;
    }else{
      this.amount=75+50*(this.getPictureForm.get('number_home').value-1);
    }
    
    
    this.getBraintreeClientToken();
   // this.saveInfoDetails(result);  
  }

 
  getBraintreeClientToken(){
    this.loading=true;
    let url = apiUrl.get_picture_payment;
    this.commonApplicationService.get(url)
      .subscribe(
        response => {
            if(response.status=='success'){
               this.braintreeClientToken = response.data.token;
               this.intializeBraintree();
            }
            if(response.status=='failed'){
              this.alertService.success(response.message);  
            }
          },
          error => {
            this.alertService.common(error); 
          }
      ); 
  }
  
  postBraintreeClientToken(payObj: Object) {
    console.log('BraintreeComponent: postBraintreeClientToken');
    payObj['buyer_notes']=this.result.notes;
    payObj['number_home']=this.result.number_home;
    payObj['number_of_home']=this.result.number_of_home;
    
    const url = apiUrl.get_picture;
    this.commonApplicationService.post(url, payObj).subscribe(
      data => {
        console.log(data)
        console.log(data && data.status && data.status == "failed")
        if(data && data.status && data.status == "failed")
        {
          this.alertService.error(data.message);
        }
        else{
          
          console.log('BraintreeComponent : postBraintreeClientToken: data');
          this.dialogRef.close();
          this.alertService.success(data.message);
          //console.log(this.callback)
          //this.callback.next(data);
        }

      },
      error => {
        this.alertService.common(error); 
      }
    );
  }

  intializeBraintree(){

    console.log('BraintreeComponent: intializeBraintree');
    const temp = this;
    this.showBraintreeButton = true;
    this.loading = false;
    this.braintreeLocal.setup(this.braintreeClientToken, 'dropin', {
        container: 'braintree-payment-form',
        paypal: {
            singleUse: false,
            amount: this.amount,
            currency: 'USD'
        },
        onReady: function(integration) {
          temp.integration = integration
          console.log('BraintreeComponent: intializeBraintree onReady');
         
        },
        onPaymentMethodReceived: function(obj) {
          console.log('BraintreeComponent: intializeBraintree onPaymentMethodReceived');
          //obj.user_id = temp.user_id;
          temp.postBraintreeClientToken(obj);
     
        }
    });
     
  }

  processing = () => {
    this.showBraintreeButtonText = 'Processing...';
  }


  _payment_callback(event)
  {

    console.log(event)
    // this.showPaymentForm = false;
  }

}
