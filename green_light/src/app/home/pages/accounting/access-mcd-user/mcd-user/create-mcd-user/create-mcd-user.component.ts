import { Component, EventEmitter, Input, OnInit, Output } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { AlertService, apiUrl, CommonActivityService, CommonApplicationService } from '@shared-service/_services';
import { Observable } from 'rxjs';
import { map, startWith } from 'rxjs/operators';

@Component({
  selector: 'app-create-mcd-user',
  templateUrl: './create-mcd-user.component.html',
  styleUrls: ['./create-mcd-user.component.css']
})
export class CreateMcdUserComponent implements OnInit {

  @Input() property_id:any;
  @Input() buyerList:any;

  mcdUserForm:FormGroup;
  loading:boolean=false;
  submitted:boolean=false;
  filteredOptions: Observable<any[]>;
  @Output() mcdUserDetail = new EventEmitter<any>();

  constructor(private formBuilder:FormBuilder,
              private commonActivityService:CommonActivityService,
              private commonApplicationService:CommonApplicationService,
              private alertService:AlertService) { }

  ngOnInit(): void {
    this.mcdUserForm = this.formBuilder.group({
      user_id:['',[Validators.required]],
    });

    this.filteredOptions = this.mcdUserForm.get('user_id').valueChanges.pipe(startWith(''),map(value => this._filter(value)) ); 

  }

  // Property Validation
  validateForm(data: any) {     
    let result = this.commonActivityService.getFullFormData(data);
    this.submitted = true;
    if(this.mcdUserForm.invalid) { 
      return;
    }    
    // FORM SUBMITTED
    this.saveInfoDetails(result);  
  }

  // Save info detail
  saveInfoDetails(data: any){ // +"/"+this.property_id
    this.loading = true;
    data['house_id']=this.property_id;
    data['user_id']=data['user_id'].id;
    let url = apiUrl.mcd_user;
    this.commonApplicationService.post(url, data)
        .subscribe(
            response => {

              // let itemIndex = this.payoutList?this.payoutList.findIndex(item => item.id == response.data.id):'-1';
              // if(itemIndex >= 0){
              //   this.payoutList[itemIndex] = response.data;
              // }else{
              //   this.payoutList.push(response.data);
              // }
              // this.calTotalAmount('amount');
              // this.payOutForm.reset();
              // if(data['category_id']=='new'){
              //   //this.getPayoutCat();
              //   this.category_list.push(response.data.category)
              // }
              this.mcdUserDetail.emit(response.data);
              this.mcdUserForm.reset();
              this.filteredOptions = this.mcdUserForm.get('user_id').valueChanges.pipe(startWith(''),map(value => this._filter(value)) ); 
              this.alertService.success(response.message);  
              this.loading = false;
              this.submitted = false;
            },
            error => {
              this.loading = false;
              this.alertService.common(error);
            }
        ); 
  }

  get f() { return this.mcdUserForm.controls; }

  onChangeCategory($event){}

  displayFn(user?: any): string | undefined {
      return user ? user.first_name+' '+user.last_name : undefined;
  }

  private _filter(name: string) {
    if(typeof name !== 'object'){
      const filterValue = name.toLowerCase();
      return this.buyerList.filter(option => (option.first_name+' '+option.last_name).toLowerCase().indexOf(filterValue) === 0);

    }
  }

}
