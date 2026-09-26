import { Component, Inject, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material/dialog';
import { AlertService, apiUrl, CommonActivityService, CommonApplicationService } from '@shared-service/_services';

@Component({
  selector: 'app-trandesman-user',
  templateUrl: './trandesman-user.component.html',
  styleUrls: ['./trandesman-user.component.css']
})
export class TrandesmanUserComponent implements OnInit {
  
  tradesmanUserForm:FormGroup;
  submitted:boolean=false;
  loading:boolean=false;
  property_id;
  tradesmanUserList:any;


  constructor(private formBuilder:FormBuilder,
              private commonActivityService:CommonActivityService,
              private commonApplicationService:CommonApplicationService,
              private alertService:AlertService,
              private dialogRef:MatDialogRef<TrandesmanUserComponent>,
              @Inject(MAT_DIALOG_DATA) public data) { }

  ngOnInit(): void {
    this.tradesmanUserList=this.data.tradesmanUserList;
    this.tradesmanUserForm = this.formBuilder.group({ 
      id:[],
      name:['',[Validators.required]],
    });
  }

  validateForm(data){
    let result = this.commonActivityService.getFullFormDataWithDateFormatted(data);
    this.submitted = true;
    console.log('niform');
    if(this.tradesmanUserForm.invalid) { 
      return;
    }    
    // FORM SUBMITTED
    console.log('form submitted');
    if(!this.loading){
      this.loading = true;
      this.saveUpdateTradesmanUser(result);  
    }
     
  }

  saveUpdateTradesmanUser(data:any){
   
    let url = apiUrl.tradesmanUser;
    this.commonApplicationService.post(url, data)
        .subscribe(
            response => {
              if(response.status=='success'){
                
                let tradesman_user=response?.data;
                let itemIndex = this.tradesmanUserList.findIndex(item => item.id == tradesman_user.id);
                if(itemIndex >= 0){
                  this.tradesmanUserList[itemIndex] =tradesman_user;
                }else{
                  this.tradesmanUserList.push(tradesman_user);
                }
                this.alertService.success(response.message);  
                this.tradesmanUserForm.reset();
                this.submitted=false;
              }else{
                this.alertService.error(response.message); 
              }
              this.loading = false;
            },
            error => {
              this.loading = false;
              this.alertService.common(error); 
            }
        ); 
  }

  editTradesmanUser(user){
    this.tradesmanUserForm.get('id').setValue(user.id);
    this.tradesmanUserForm.get('name').setValue(user.username);
  }
  closeDialog(): void {
    this.dialogRef.close(this.tradesmanUserList);
  }
}
