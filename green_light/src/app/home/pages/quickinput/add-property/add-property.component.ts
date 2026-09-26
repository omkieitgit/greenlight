import { Component, OnInit, Input } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { CommonApplicationService,CommonActivityService,AlertService } from '../../../../shared/_services';
import { Router,ActivatedRoute } from '@angular/router';
import {apiUrl} from '../../../../config/api-url';
import { formConstants } from '@config/forms-constants';
import jsonData from '@config/state_county';
import { SlugifyPipe } from '../../../../modules/directives/slugify.pipe';

@Component({
  selector: 'app-add-property',
  templateUrl: './add-property.component.html',
  styleUrls: ['./add-property.component.css']
})
export class AddPropertyComponent implements OnInit {
  
  @Input() infoData:any;

  propertyForm:FormGroup;
  submitted:boolean=false;
  loading:boolean=false;
  countries:any;
  states:any;
  minChar:number=3;
  filteredOptions:any;

  noResult:boolean=false;
  searchResult:any;
  isSearch:boolean=false;
  property_id:any;
  propertyUrl:any;

  constructor(private formBuilder: FormBuilder,
              private router: Router,
              private route: ActivatedRoute,
              private alertService:AlertService,
              private commonApplicationService: CommonApplicationService,
              private commonActivityService: CommonActivityService,
              private fb:FormBuilder,
              private slug:SlugifyPipe) { }

  ngOnInit() {
   // this.countries         = formConstants.countries;
    this.states             = formConstants.states;
    this.property_id = this.route.snapshot.paramMap.get('property_id');

    this.propertyForm = this.formBuilder.group({
      address: [this.infoData.address, Validators.required],
      city: [this.infoData.city, Validators.required],
      county: [this.infoData.county, Validators.required],
      zip: [this.infoData.zip, Validators.required],
      state: [this.infoData.state, Validators.required],
      parcel_id1: [this.infoData.parcel_id1],
    });

    if(this.infoData.county){
      this.getCountyList(this.infoData.state);
    }
		
    this.propertyForm.get('address').valueChanges.subscribe(value => { 
        if(value && value.length>=this.minChar){
          this._filter(value);
        }else{
          this.filteredOptions=[];
        }
    });

    if(this.property_id){
      this.propertyUrl='/home/showdetail/'+this.property_id+'/'+this.getPropertySlug(this.infoData);
    }

  }

  get f() { return this.propertyForm.controls; }

 // Property Validation
 validateForm(data: any) {     
  let result = this.commonActivityService.getFullFormData(data);
  this.submitted = true;
  if(this.propertyForm.invalid) { 
    this.alertService.error('Form is invalid, Fill all fields.');  
    return;
  }   
  if(this.property_id){
    this.updateInfoDetail(result);
  } else{
    this.saveInfoDetails(result); 
  }
  // FORM SUBMITTED
   
}


updateInfoDetail(data: any){
  let url = apiUrl.property_info+'/'+this.property_id;
  this.commonApplicationService.put(url, data)
      .subscribe(
          data => {
            this.alertService.success(data.row);  
            this.loading = false;
          },
          error => {
              this.loading = false;
              this.alertService.common(error); 
          }
      );
}


// Save info detail
  saveInfoDetails(data: any){
    this.loading = true;
    let url = apiUrl.property_info;
    this.commonApplicationService.post(url, data)
      .subscribe(
          response => {
            this.alertService.common(response);  
            this.loading = false;
            this.router.navigate(['home/quickinput/'+response.row.house_id+'/'+this.getPropertySlug(data)]);  
          },
          error => {
            this.loading = false;
            this.alertService.common(error);  
          }
      ); 
  }
   
  getCountyList(state){
    this.countries = jsonData[state];
  }
  private _filter(value: any):any{
    this.loading=true;
    this.noResult=false;
    this.filteredOptions=[];
    this.commonApplicationService.get(apiUrl.propertyAutoComplete+'?query='+value+'&limit=10').subscribe(response=>{
      this.filteredOptions= response;
      this.loading=false;
      if(this.filteredOptions.length==0){
        this.noResult=true;
      }
      
		});
  }
  searchProperty(){
    
    let searchVal=this.propertyForm.get('address').value;
    if(searchVal.house_id){
      this.loading=false;
      this.isSearch=true;
			let house_id=searchVal.house_id?searchVal.house_id:'';
			searchVal=searchVal.address?searchVal.address:searchVal;
      let url= searchVal.split(' ').join('-');
      this.router.navigate(['home/quickinput/',house_id,url])

    }

  }

  displayFn(selectedVal:any): string {
    if(selectedVal && selectedVal.hasOwnProperty('address'))
      return selectedVal && selectedVal.address ? selectedVal.address : '';
    else
      return selectedVal;
  }

  getPropertySlug(infoData){
    let slugAddress=infoData.address+' '+infoData.city+' '+infoData.state+' '+infoData.zip;
    return this.slug.transform(slugAddress);
  }

}
