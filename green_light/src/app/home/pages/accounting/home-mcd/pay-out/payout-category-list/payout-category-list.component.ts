import { Component, Inject, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material/dialog';
import { AlertService, apiUrl,CommonActivityService, CommonApplicationService } from '@shared-service/_services';

@Component({
  selector: 'app-payout-category-list',
  templateUrl: './payout-category-list.component.html',
  styleUrls: ['./payout-category-list.component.css']
})
export class PayoutCategoryListComponent implements OnInit {

  categoryForm:FormGroup;
  submitted:boolean;
  loading:boolean;
  categoryList:any;
  // payoutCategory:any=[
  //   {'key':'a_to_b','title':'Costs paid out of closing Hud A to B:'},
  //   {'key':'settlement','title':'Credits received on settlement:'},
  //   {'key':'adjustment','title':'Cash Adjustments:'},
  //   {'key':'b_to_c','title':'Less:Costs paid out of closing Hud B to C:'},
  //   {'key':'btoc_settlement','title':'Credits received on settlement:'},
  //   {'key':'btoc_adjustment','title':'Income Statement Adjustments:'},
  // ];
  payoutCategory:any=[
    {'a_to_b':'Costs paid out of closing Hud A to B'},
    {'settlement':'Credits received on settlement'},
    {'adjustment':'Cash Adjustments'},
    {'b_to_c':'Costs paid out of closing Hud B to C'},
    {'btoc_settlement':'Credits received on settlement'},
    {'btoc_adjustment':'Income Statement Adjustments'},
    {'othersale':'Income other than Sale'}
  ];

  constructor(private fb: FormBuilder,
              private commonActivityService:CommonActivityService,
              private commonApplicationService:CommonApplicationService,
              private alertService:AlertService,
              private dialogRef:MatDialogRef<PayoutCategoryListComponent>,
              @Inject(MAT_DIALOG_DATA) public data) { 
                this.categoryList=data.categoryList;
              }

  ngOnInit(): void {
    this.categoryForm=this.fb.group({
      id:[''],
      category_name: ['', Validators.required],
    });
   
  }


   // Property Validation
  validateForm(data: any) {     
    let result = this.commonActivityService.getFullFormData(data);
    this.submitted = true;
    if(this.categoryForm.invalid) { 
      return;
    }    
    this.saveCategory(result);  
  }

  // Save info detail
  saveCategory(data: any){ // +"/"+this.property_id
    this.loading = true;
    let url = apiUrl.payoutCategory;
    this.commonApplicationService.put(url, data)
        .subscribe(
            response => {

              let itemIndex = this.categoryList?this.categoryList.findIndex(item => item.id == response.data.id):'-1';
              if(itemIndex >= 0){
                this.categoryList[itemIndex] = response.data;
              }

              this.categoryForm.reset();
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

  editPayout(category){
    this.categoryForm.get('id').setValue(category.id);
    this.categoryForm.get('category_name').setValue(category.category_name);
  }

  removePayout(id){

    if(confirm("Are you sure want to delete record ?")){
      let url = apiUrl.renovationCategory+'/'+id;
      this.commonApplicationService.delete(url).subscribe(response => {
        if(response.status=='success'){
          this.categoryList = this.categoryList.filter(item => item.id !== id);
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

  closeDialog(): void {
    this.dialogRef.close(this.categoryList);
  }

}
