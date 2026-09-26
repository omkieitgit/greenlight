import { Component, EventEmitter, Input, OnInit, Output } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { AlertService, apiUrl, CommonActivityService, CommonApplicationService } from '@shared-service/_services';
import { IMyDateModel, IMyDpOptions } from 'mydatepicker';
import { bookingModel, ShortTermRentalModel } from './short-term-rental.model';

@Component({
  selector: 'app-short-term-rental',
  templateUrl: './short-term-rental.component.html',
  styleUrls: ['./short-term-rental.component.css']
})
export class ShortTermRentalComponent implements OnInit {

  ShortTermRentalForm:FormGroup;
  submitted: boolean;
  openPanel: boolean;
  shortTermRental:ShortTermRentalModel[];
  bookingInfo:bookingModel[];

  bookingType:string='booking';
  rentalType:string='rental';

  @Input() property_id: any;
  @Output() updateShartTermRental= new EventEmitter<number>();

  loading: boolean;

  constructor(private formBuilder:FormBuilder,
              private commonActivityService:CommonActivityService,
              private commonApplicationService:CommonApplicationService,
              private alertService:AlertService) { }

  ngOnInit(): void {
    this.ShortTermRentalForm = this.formBuilder.group({ 
      id:[],
      property_number:[''],
      guest_name:[''],
      check_in_date:['',[Validators.required]],
      check_out_date:[''],
      property_description:[],
      amount_received:['',[Validators.required]],
      account_deposited:[],
      link:[],
      remark:[],
      rental_type:['rental',[Validators.required]],
      deposite_link:[],
      amount_deposit:[]
    });
  }
  public myDatePickerOptions: IMyDpOptions = { dateFormat: 'mm/dd/yyyy',firstDayOfWeek : 'su'};
  onDateChanged(event: IMyDateModel) {
      return event.formatted;
  }
  validateForm(data){
    let result = this.commonActivityService.getFullFormDataWithDateFormatted(data);
    this.submitted = true;
    console.log('niform');
    if(this.ShortTermRentalForm.invalid) { 
      return;
    }    
    // FORM SUBMITTED
    console.log('form submitted');
    this.saveShortTermRental(result);  
  }

  panelExpand(flag){
    if(!this.openPanel && this.property_id !== undefined){
      this.openPanel = true;
      this.getShortTermRental();
    }
  }

  getShortTermRental(){
    this.loading=true;
    let url = apiUrl.shortTermRental+'/'+this.property_id;
    this.commonApplicationService.get(url).subscribe(response => {
      if(response['row']){
        //this.shortTermRental = response['row'];
        this.shortTermRental=response['row'].filter(res=>res.rental_type==this.rentalType);
        this.bookingInfo=response['row'].filter(res=>res.rental_type==this.bookingType);

      }
      this.loading = false;
    },
    (err: any) => {
      this.loading = false;
    })
  }

  get f() { return this.ShortTermRentalForm.controls; }

  
  uploadHandler(event){
    let elem = event.target; 
    if(elem.files.length > 0){
    }
  }

  saveShortTermRental(data: any){

    this.loading = true;
    let url = apiUrl.shortTermRental+'/'+this.property_id;
    this.commonApplicationService.post(url, data)
        .subscribe(
            data => {
              if(data.status=='success'){

                if(data.data.rental_type==this.rentalType){
                  let itemIndex = this.shortTermRental.findIndex(item => item.id == data.data.id);
                  if(itemIndex >= 0){
                    this.shortTermRental[itemIndex] = data.data;
                  }else{
                    this.shortTermRental.push(data.data);
                  }
                  this.emitShortTermRental();
                }

                if(data.data.rental_type==this.bookingType){
                  let itemIndex = this.bookingInfo.findIndex(item => item.id == data.data.id);
                  if(itemIndex >= 0){
                    this.bookingInfo[itemIndex] = data.data;
                  }else{
                    this.bookingInfo.push(data.data);
                  }
                }
                
                this.alertService.success(data.message);  
                this.ShortTermRentalForm.reset();
                this.submitted=false;
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

  removeShortTermRental(id){

      if(confirm("Are you sure want to delete record ?")){
        let url = apiUrl.shortTermRental+'/'+id;
        this.commonApplicationService.delete(url).subscribe(response => {
          if(response.status=='success'){
            this.shortTermRental = this.shortTermRental.filter(item => item.id !== id);
            this.emitShortTermRental();
            this.alertService.success(response.message);
          }else{
            this.alertService.error(response.message); 
          }
        },
        (err: any) => {
          this.loading = false;
          this.alertService.common(err); 
        })
      }
  }

  editShortTermRental(rental){

    this.ShortTermRentalForm.get('id').setValue(rental.id);
    this.ShortTermRentalForm.get('property_number').setValue(rental.property_number);
    this.ShortTermRentalForm.get('guest_name').setValue(rental.guest_name);
    this.ShortTermRentalForm.get('check_in_date').setValue(rental.check_in_date?{jsdate: new Date(rental.check_in_date)}:null);
    this.ShortTermRentalForm.get('check_out_date').setValue(rental.check_out_date?{jsdate: new Date(rental.check_out_date)}:null);
    this.ShortTermRentalForm.get('property_description').setValue(rental.property_description);
    this.ShortTermRentalForm.get('amount_deposit').setValue(rental.amount_deposit);
    this.ShortTermRentalForm.get('account_deposited').setValue(rental.account_deposited);
    this.ShortTermRentalForm.get('remark').setValue(rental.remark);
    this.ShortTermRentalForm.get('link').setValue(rental.link);
    this.ShortTermRentalForm.get('rental_type').setValue(rental.rental_type);
    this.ShortTermRentalForm.get('amount_received').setValue(rental.amount_received);


  }

  calTotalAmount(key){
    let totalAmount:any=0;
    if(this.shortTermRental){
      for(var i=0; i<this.shortTermRental.length; i++){
        totalAmount=parseFloat(totalAmount)+parseFloat(this.shortTermRental[i][key]?this.shortTermRental[i][key]:0); 
      }
    }
    return totalAmount.toFixed(2);
  }

  calTotalBookingAmount(key){
    let totalAmount:any=0;
    if(this.shortTermRental){
      for(var i=0; i<this.bookingInfo.length; i++){
        totalAmount=parseFloat(totalAmount)+parseFloat(this.bookingInfo[i][key]?this.bookingInfo[i][key]:0); 
      }
    }
    return totalAmount.toFixed(2);
  }

  isValidURL(string) {
    var res = string.match(/(http(s)?:\/\/.)?(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)/g);
    return (res !== null)
  };

  emitShortTermRental(){
    const totalAirbnb = this.shortTermRental.reduce((sum, item) => (sum) + parseFloat(item.amount_received), 0);
    this.updateShartTermRental.emit(totalAirbnb);

  }
  
}
