import { Component, Inject, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material/dialog';
import { apiUrl } from '@config/api-url';
import { AlertService, CommonActivityService, CommonApplicationService } from '@shared-service/_services';

@Component({
  selector: 'app-deposit-lender-cat',
  templateUrl: './deposit-lender-cat.component.html',
  styleUrls: ['./deposit-lender-cat.component.css']
})
export class DepositLenderCatComponent implements OnInit {

  categoryForm:FormGroup;
  submitted:boolean;
  loading:boolean;
  categoryList:Category[];

  constructor(private fb: FormBuilder,
              private commonActivityService:CommonActivityService,
              private commonApplicationService:CommonApplicationService,
              private alertService:AlertService,
              private dialogRef:MatDialogRef<DepositLenderCatComponent>,
              @Inject(MAT_DIALOG_DATA) public data) { 
                this.categoryList=data.categoryList;
              }

  ngOnInit(): void {
    this.categoryForm=this.fb.group({
      id:[''],
      lender_name: ['', Validators.required],
    });

    setTimeout(()=>{ 
      
    }, 10);
   
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
    let url = apiUrl.depositLender;
    this.commonApplicationService.post(url, data)
        .subscribe(
            response => {

              let itemIndex = this.categoryList?this.categoryList.findIndex(item => item.id == response.data.id):'-1';
              if(itemIndex >= 0){
                this.categoryList[itemIndex] = response.data;
              }else{
                this.categoryList.push(response.data);
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
    this.categoryForm.get('lender_name').setValue(category.lender_name);
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
export interface Category{
  id:number,
  lender_name:string;   
}