import { Component, OnInit } from '@angular/core';
import { FormGroup, FormBuilder, FormArray, Validators } from '@angular/forms';

@Component({
  selector: 'app-listing',
  templateUrl: './listing.component.html',
  styleUrls: ['./listing.component.css']
})
export class ListingComponent implements OnInit {

  listingForm:FormGroup;

  constructor(private formBuilder:FormBuilder) { }

  ngOnInit(): void {
    
    this.listingForm = this.formBuilder.group({
    
      mls_1:[],
      mls_url:[],
      mls:[],
      listing_price:[],
      listing_date:[],
      zillow_url:[],
      realtor_url:[],
      date_listed:[],
      price_change:[],
      date:[],
      google_doc_url:[],
      add_showings:[],
      Add_Feedbackc:[],
      Add_Offers:[],
      documents:[],
      airBNB_url:[],
      airBNB_rental_rate:[],
      vrbo_url:[],
      vrbo_rental_rate:[],
      sold_price:[],
      sold_date:[],
      days_on_market:[],
      seller_grantor:[],
      buyer_grantee:[],
      deed_book_page:[],
      deed_recorded_date:[],
      property_closed_date:[],
    });

  }

  // addPriceInputField() : void
  // {
  //  const control = <FormArray>this.priceHistoryForm.controls.price_info_data;
  //  control.push(this.priceFields());
  // }
  // priceFields() : FormGroup
  // {    
  //   return this.formBuilder.group({
  //     broker_name: [],
  //     broker_phone:[],
  //     broker_email:[],
  //     broker_license:[],
  //     broker_address:[],
  //   });
  // }

}
