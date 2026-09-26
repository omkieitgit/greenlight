import { Component, Input, OnInit, SimpleChanges } from '@angular/core';
import { FormArray, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { apiUrl } from '@config/api-url';
import { AlertService, CommonApplicationService } from '@shared-service/_services';

@Component({
  selector: 'app-deposit-link',
  templateUrl: './deposit-link.component.html',
  styleUrls: ['./deposit-link.component.css']
})
export class DepositLinkComponent implements OnInit {

  @Input() public depositSheetForm: FormGroup;
  @Input() deposit_link;

  loading:boolean=false;
  constructor(private formBuilder:FormBuilder,
              private commonApplicationService:CommonApplicationService,
              private alertService:AlertService) { }

  ngOnInit(): void {
    
    this.depositSheetForm.setControl('more_link_data',this.formBuilder.array([this.linkFields()]));

  }

  ngOnChanges(changes: SimpleChanges): void{
    if(changes?.deposit_link?.currentValue != changes?.deposit_link?.previousValue){
      this.populateDataInForm();
    }
  }

  linkFields() : FormGroup
  {    
    return this.formBuilder.group({
      id:[],
      link_name: [''],
      link: [''],
    });
  }

  addPriceInputField() : void
  {
   const control = <FormArray>this.depositSheetForm.controls.more_link_data;
   control.push(this.linkFields());
  }
  
  removePriceInputField(i : number,id) : void
  {
      if(confirm("Are you sure want to delete record ?")){
           const control = <FormArray>this.depositSheetForm.controls.more_link_data;
           control.removeAt(i);
           if(id){
         
              let url = apiUrl.removeDepositLink+'/'+id;
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
    if(this.deposit_link && this.deposit_link.length>0){
      for(var i=0; i<this.deposit_link.length; i++ ){
          this.addPriceInputField();
          this.fillPriceForm(i);
        }
    }else{
      const control = <FormArray>this.depositSheetForm.controls.more_link_data;
      control.clear();
      this.addPriceInputField();
    }
  }

  fillPriceForm(index:number){
    //this.priceHistoryForm.get('price_info_data')['controls'][index].controls.price_date.setValue(this.priceHistoryData[index].price_date);
    let linkForm=this.depositSheetForm.get('more_link_data')['controls'][index]?.controls;
    if(linkForm){
      linkForm.id.setValue(this.deposit_link[index].id);
      linkForm.link_name.setValue(this.deposit_link[index].link_name);
      linkForm.link.setValue(this.deposit_link[index].link);
    }
  }

}
