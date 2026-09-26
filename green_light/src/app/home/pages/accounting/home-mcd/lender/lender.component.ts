import { Component, EventEmitter, Input, OnInit, Output } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { AlertService, apiUrl, CommonActivityService, CommonApplicationService } from '@shared-service/_services';

@Component({
  selector: 'app-lender',
  templateUrl: './lender.component.html',
  styleUrls: ['./lender.component.css']
})
export class LenderComponent implements OnInit {

  lenderForm:FormGroup;
  submitted: boolean;
  openPanel: any;
  @Input() property_id: any;
  @Input() mcdLenderResult:any;
  @Output() updateLender= new EventEmitter<boolean>();
  @Input() viewAccessOnly;
  loading: boolean;

  constructor(private formBuilder:FormBuilder,
              private commonActivityService:CommonActivityService,
              private commonApplicationService:CommonApplicationService,
              private alertService:AlertService) { }

  ngOnInit(): void {
    this.lenderForm = this.formBuilder.group({ 
      id:[],
      lender_name:['',[Validators.required]],
      percentage:['',[Validators.required]],
      agreement_org_name:[''],
      artical_org_name:[''],
      ein_org_name:[''],
      is_lender:[''],
      is_craig_per:['']
    });
  }

  validateForm(data){
    let result = this.commonActivityService.getFullFormDataWithDateFormatted(data);
    this.submitted = true;
    console.log('niform');
    if(this.lenderForm.invalid) { 
      return;
    }    
    // FORM SUBMITTED
    console.log('form submitted');
    this.saveUpdateMcd(result);  
  }

  panelExpand(flag){
    if(!this.openPanel && this.property_id !== undefined){
      this.openPanel = true;
      //this.getMcdInfo();
    }
  }

  get f() { return this.lenderForm.controls; }

  
  uploadHandler(event){
    let elem = event.target; 
    if(elem.files.length > 0){
    }
  }

  saveUpdateMcd(input: any){
    this.loading = true;
   // let input = new FormData();
    // input.append("lender_name", data['lender_name']);
    // input.append("percentage", data['percentage']?data['percentage']:0);
    // input.append("id", data['id']);

    // if(agreement != "" && agreement != undefined){
    //   input.append('document_agreement',agreement.files[0]);
    // }
    // if(artical != "" && artical != undefined){
    //   input.append('artical',artical.files[0]);
    // }
    // if(ein != "" && ein != undefined){
    //   input.append('ein',ein.files[0]);
    // }
    
    let url = apiUrl.mcd_lender+'/'+this.property_id;
    this.commonApplicationService.post(url, input)
        .subscribe(
            data => {
              if(data.status=='success'){

                let itemIndex = this.mcdLenderResult.findIndex(item => item.id == data.data.id);
                if(itemIndex >= 0){
                  this.mcdLenderResult[itemIndex] = data.data;
                }else{
                  this.mcdLenderResult.push(data.data);
                }
                this.alertService.success(data.message);  
                this.updateLender.emit(true);
                this.lenderForm.reset();
                this.submitted=false;
              }else{
                this.alertService.error(data.message); 
              }
              
              this.loading = false;
            },
            error => {
              this.loading = false;
              this.alertService.common(error); 
            }
        ); 
  }

  removeMcdLender(id){

      if(confirm("Are you sure want to delete record ?")){
        let url = apiUrl.mcd_lender+'/'+id;
        this.commonApplicationService.delete(url).subscribe(response => {
          if(response.status=='success'){
            this.mcdLenderResult = this.mcdLenderResult.filter(item => item.id !== id);
            this.updateLender.emit(true);
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

  editMcdLender(lender){
    this.lenderForm.get('id').setValue(lender.id);
    this.lenderForm.get('lender_name').setValue(lender.lender_name);
    this.lenderForm.get('percentage').setValue(lender.percentage);
    this.lenderForm.get('agreement_org_name').setValue(lender.agreement_org_name);
    this.lenderForm.get('artical_org_name').setValue(lender.artical_org_name);
    this.lenderForm.get('ein_org_name').setValue(lender.ein_org_name);
    this.lenderForm.get('is_lender').setValue(lender.is_lender);
    this.lenderForm.get('is_craig_per').setValue(lender.is_craig_per);

  }

  isValidURL(string) {
    var res = string.match(/(http(s)?:\/\/.)?(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)/g);
    return (res !== null)
  };
}
