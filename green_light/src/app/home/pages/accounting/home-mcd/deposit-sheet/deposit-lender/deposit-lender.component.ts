import { Component, Input, OnInit, SimpleChanges } from '@angular/core';
import { FormArray, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { MatDialog } from '@angular/material/dialog';
import { apiUrl } from '@config/api-url';
import { AlertService, CommonApplicationService } from '@shared-service/_services';
import { Category, DepositLenderCatComponent } from './deposit-lender-cat/deposit-lender-cat.component';

@Component({
  selector: 'app-deposit-lender',
  templateUrl: './deposit-lender.component.html',
  styleUrls: ['./deposit-lender.component.css']
})
export class DepositLenderComponent implements OnInit {

 
  @Input() public depositSheetForm: FormGroup;
  @Input() deposit_lender_list;
  @Input() deposit_lender;

  loading:boolean=false;
  categoryList:Category[];

  constructor(private formBuilder:FormBuilder,
    private commonApplicationService:CommonApplicationService,
    private alertService:AlertService, private dailog:MatDialog) { }

  ngOnInit(): void {
    this.depositSheetForm.setControl('more_lender_data',this.formBuilder.array([this.linkFields()]));
   
  }
  ngOnChanges(changes: SimpleChanges): void{
    if(changes?.deposit_lender?.currentValue != changes?.deposit_lender?.previousValue){
      this.populateDataInForm();
    }
  }

  linkFields() : FormGroup
  {    
    return this.formBuilder.group({
      id:[],
      lender_id: [''],
      amount: [''],
      trans_type:[]
    });
  }

  addPriceInputField() : void
  {
   const control = <FormArray>this.depositSheetForm.controls.more_lender_data;
   control.push(this.linkFields());
  }
  
  removePriceInputField(i : number,id) : void
  {
      if(confirm("Are you sure want to delete record ?")){
           const control = <FormArray>this.depositSheetForm.controls.more_lender_data;
           control.removeAt(i);
           if(id){
         
              let url = apiUrl.removeDepositAccount+'/'+id;
              this.commonApplicationService.delete(url).subscribe(response => {
                if(response !== undefined){             
                    this.alertService.success(response.message); 
                }
              },
              (err: any) => {
                this.alertService.error("Error occured, Please try again later!");
              })
          }
      }
  }


  populateDataInForm(){
    if(this.deposit_lender && this.deposit_lender.length>0){
      for(var i=0; i<this.deposit_lender.length; i++ ){
          this.addPriceInputField();
          this.fillPriceForm(i);
        }
    }else{
      const control = <FormArray>this.depositSheetForm.controls.more_lender_data;
      control.clear();
      this.addPriceInputField();
    }
  }

  fillPriceForm(index:number){
    //this.priceHistoryForm.get('price_info_data')['controls'][index].controls.price_date.setValue(this.priceHistoryData[index].price_date);
    let lenderForm=this.depositSheetForm.get('more_lender_data')['controls'][index]?.controls;
    if(lenderForm){
      lenderForm.id.setValue(this.deposit_lender[index].id);
      lenderForm.lender_id.setValue(this.deposit_lender[index].lender);
      lenderForm.amount.setValue(this.deposit_lender[index].amount);
      lenderForm.trans_type.setValue(this.deposit_lender[index].trans_type);
    }
  }

  categoryDialog(){
    const dialogRef =this.dailog.open(DepositLenderCatComponent,{ width: '800px',data:{categoryList:this.deposit_lender_list}});
    dialogRef.afterClosed().subscribe(result => {
      if(result)
        this.deposit_lender_list=result;
    });
  }

  displayFn(lender?: any): string | undefined {
      return lender ? lender.lender_name : undefined;
  }

}
