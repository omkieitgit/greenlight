import { Component, EventEmitter, Input, OnInit, Output } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { SumPipe } from '@shared-modules/directives/sum.pipe';
import { AlertService, apiUrl, CommonActivityService, CommonApplicationService } from '@shared-service/_services';
import { StorageService } from '@shared-service/_services/storage.service';
import { Observable } from 'rxjs';
import { map, startWith } from 'rxjs/operators';
import { PayoutCategory } from '../payout-category.model';

@Component({
  selector: 'app-payout-category',
  templateUrl: './payout-category.component.html',
  styleUrls: ['./payout-category.component.css']
})
export class PayoutCategoryComponent implements OnInit {
  payOutForm: FormGroup;
	@Input() property_id:string;
  @Input() openPanel:boolean;
  @Input() payoutInfo;
  @Input() categoryList;
  @Input() title:string;
  @Input() totalTitle:string;
  @Input() categoryType:string;
  @Input() totalCostsBtoC:number=0;
  @Input() showTotal:boolean=false;

	submitted = false;
	loading = false;
  loadingMessage: boolean;
  propErr: boolean = false;
  payoutList:any=[];
  showCatName:boolean;
  category_list:PayoutCategory[];
  filteredOptions: Observable<any[]>;
  viewAccessOnly:boolean=false;

  @Output() totalAmount = new EventEmitter<number>();

 constructor(private formBuilder: FormBuilder,
              private commonApplicationService: CommonApplicationService,
              private commonActivityService: CommonActivityService,
              private alertService: AlertService,
              public sumPipe:SumPipe,
              private storageService:StorageService,

              ) { }

	ngOnInit() {
    this.viewAccessOnly=this.storageService.getHard('ac_view_access');
		this.payOutForm = this.formBuilder.group({
      id:[],
      category_id:['',[Validators.required]],
      amount:['',[Validators.required]],
      house_id:[this.property_id],
      category_name:[],
      payout_type:[]
    });
    this.filteredOptions = this.payOutForm.get('category_id').valueChanges.pipe(startWith(''),map(value => this._filter(value)) ); 
    
  }  

  ngOnChanges() {
    if(this.openPanel && this.property_id){
      //this.category_list=[];
      this.category_list=this.categoryList.filter(res=>res.category_type==this.categoryType);
      this.payoutList=this.payoutInfo.payout_detail.filter(items => items.payout_type ===this.categoryType);
      this.calTotalAmount('amount');
    }
  }
  private _filter(name: string) {
    if(typeof name !== 'object'){
      const filterValue = name.toLowerCase();
      return this.category_list.filter(option => option.category_name.toLowerCase().indexOf(filterValue) === 0);

    }
  }
  
  panelExpand(flag){
    if(!this.openPanel){
      this.propErr = true;
      this.openPanel=true;
    }
  }

  editPayout(payout){
    this.payOutForm.get('id').setValue(payout.id);
    this.payOutForm.get('category_id').setValue(payout.category);
    this.payOutForm.get('amount').setValue(payout.amount);
    this.payOutForm.get('house_id').setValue(this.property_id);
    this.showCatName=false;

  }

  removePayout(id){

    if(confirm("Are you sure want to delete record ?")){
      let url = apiUrl.payout+'/'+id;
      this.commonApplicationService.delete(url).subscribe(response => {
        if(response.status=='success'){
          this.payoutList = this.payoutList.filter(item => item.id !== id);
          this.alertService.success(response.message);
          this.calTotalAmount('amount');
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
	 
  calTotalAmount(key){
    let totalAmount:any=0;
    if(this.payoutList){
      for(var i=0; i<this.payoutList.length; i++){
        totalAmount=parseFloat(totalAmount)+parseFloat(this.payoutList[i][key]?this.payoutList[i][key]:0); 
      }
    }
    this.totalAmount.emit(totalAmount.toFixed(2));
    return totalAmount.toFixed(2);
  }
   
  // Property Validation
  validateForm(data: any) {     
    let result = this.commonActivityService.getFullFormData(data);
    this.submitted = true;
    if(this.payOutForm.invalid) { 
      return;
    }    
    // FORM SUBMITTED
    this.saveInfoDetails(result);  
  }

  // Save info detail
  saveInfoDetails(data: any){ // +"/"+this.property_id
    this.loading = true;
    data['house_id']=this.property_id;
    data['payout_type']=this.categoryType;
    data['category_id']=data['category_id']=='new'?data['category_id']:data['category_id'].id;
    let url = apiUrl.payout_detail;
    this.commonApplicationService.post(url, data)
        .subscribe(
            response => {

              let itemIndex = this.payoutList?this.payoutList.findIndex(item => item.id == response.data.id):'-1';
              if(itemIndex >= 0){
                this.payoutList[itemIndex] = response.data;
              }else{
                this.payoutList.push(response.data);
              }
              this.calTotalAmount('amount');
              this.payOutForm.reset();
              if(data['category_id']=='new'){
                //this.getPayoutCat();
                this.category_list.push(response.data.category)
              }
              this.filteredOptions = this.payOutForm.get('category_id').valueChanges.pipe(startWith(''),map(value => this._filter(value)) ); 
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

  get f() { return this.payOutForm.controls; }

  onChangeCategory($event){
    if($event.option.value=='new'){
        this.showCatName=true;
        this.payOutForm.controls['category_name'].setValidators([Validators.required]);
        this.payOutForm.controls['category_name'].updateValueAndValidity();
    }else{
      this.showCatName=false;
      this.payOutForm.controls['category_name'].clearValidators();
        this.payOutForm.controls['category_name'].updateValueAndValidity();
    }
  }

  getPayoutCat(){
    let url = apiUrl.payoutCat;
      this.commonApplicationService.get(url).subscribe(response => {
        this.category_list=response.data.filter(res=>res.category_type==this.categoryType);
        
      },
      (err: any) => {
        this.loadingMessage = false;
        this.propErr = true;
      })
  }
  displayFn(category?: any): string | undefined {
    if(category=='new')
      return category;
    else
      return category ? category.category_name : undefined;
  }
}
