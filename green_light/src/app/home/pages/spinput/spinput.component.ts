import { Component, Injectable, OnDestroy, OnInit, Renderer2 } from '@angular/core';
import { NgbDateAdapter, NgbDateStruct } from '@ng-bootstrap/ng-bootstrap';
import { FormControl, NgForm, FormBuilder, FormGroup, Validators, FormArray } from '@angular/forms';
import { Router }    from '@angular/router';
import { first } from 'rxjs/operators';
import {IMyDpOptions, IMyDateModel} from 'mydatepicker';
import { SpInputModel } from './spinput.model';
//import pageSettings from '../../../config/page-settings';
//import { AlertService, UserService } from '../../shared/_services';

// for ngb datepicker adapter
@Injectable()
export class NgbDateNativeAdapter extends NgbDateAdapter<Date> {

  fromModel(date: Date): NgbDateStruct {
    return (date && date.getFullYear) ? {year: date.getFullYear(), month: date.getMonth() + 1, day: date.getDate()} : null;
  }

  toModel(date: NgbDateStruct): Date {
    return date ? new Date(date.year, date.month - 1, date.day) : null;
  }
}

@Component({
 selector: 'spinput',
 templateUrl: './spinput.html',
 providers: [{provide: NgbDateAdapter, useClass: NgbDateNativeAdapter}]
 })


/*@Component({
  selector: 'owner',
  templateUrl: './owner.html',
  providers: [{provide: NgbDateAdapter, useClass: NgbDateNativeAdapter}]
})*/

export class SpinputComponent  implements OnInit{
  constructor(private formBuilder: FormBuilder,
        private router: Router) { }

  spForm: FormGroup;
  submitted = false;
  property_id: string;
  // ngbdatepicker
  model1: Date;
  model2: Date;
  get today() {
    return new Date();
  }

  // ngbtimepicker
  time2;
  ctrl = new FormControl('', (control: FormControl) => {
    const value = control.value;

    if (!value) {
      return null;
    }

    if (value.hour < 12) {
      return {tooEarly: true};
    }
    if (value.hour > 13) {
      return {tooLate: true};
    }

    return null;
  });

  time = {hour: 13, minute: 30};
  meridian = true;
  toggleMeridian() {
      this.meridian = !this.meridian;
  }

  public myDatePickerOptions: IMyDpOptions = { dateFormat: 'yyyy-mm-dd',firstDayOfWeek : 'su'};

  onDateChanged(event: IMyDateModel) {
        return event.formatted;
    }

ngOnInit() {
  this.initialize(); 
}

initialize(){
     this.spForm = this.formBuilder.group({
      house_id:[this.property_id],
      //trustee_deposit_returned_date:[(this.trusteeData.trustee_deposit_returned_date != null)? {jsdate: new Date(this.trusteeData.trustee_deposit_returned_date)}: null],
      state: [this.property_id],
      county: [this.property_id],
      range: [this.property_id],
      case_number: [this.property_id],
      mortgagor_grantor_1: [this.property_id],
      mortgagor_grantor_2: [this.property_id],
      sale_type: [this.property_id],
      instrument: [this.property_id],
      book: [this.property_id],
      page: [this.property_id],
      str: [this.property_id],
      sale_place: [this.property_id],
      sale_date: [this.property_id],
      sale_time: [this.property_id],
      opening_bid: [this.property_id],
      parcel_id: [this.property_id],
      propety_address: [this.property_id],
      city: [this.property_id],
      zipcode: [this.property_id],
      trustee_address: [this.property_id],
      trustee_url: [this.property_id],
      hoa_name: [this.property_id],
      account_number: [this.property_id],
      legal_notice: [this.property_id],
      legal_notice_url: [this.property_id],
      nos: [this.property_id],
      nos_date: [this.property_id],
    });
}

get f() { return this.spForm.controls; }

}