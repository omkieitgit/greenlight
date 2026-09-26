import { Component,Inject, OnInit, ElementRef, ViewChild} from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { apiUrl } from '@config/api-url';
import { CommonApplicationService,CommonActivityService,AlertService } from '@shared-service/_services';
import { MatDialogRef, MAT_DIALOG_DATA} from '@angular/material/dialog';
import  { HttpClient } from '@angular/common/http';
import {COMMA, ENTER} from '@angular/cdk/keycodes';
import {MatAutocompleteSelectedEvent, MatAutocomplete} from '@angular/material/autocomplete';
import {MatChipInputEvent} from '@angular/material/chips';


@Component({ 
  selector: 'send-invite',
  templateUrl: './send-invite.html',
})

export class SendInviteComponent  implements OnInit{
  sendInviteForm: FormGroup;
  invalidFields: any;  
  submitted = false;
  loading = false;
  searchLoading = false;
  loadingMessage: any;
  propErr: boolean = false;
  propErrMsg: any = "";
  modifyBtn : boolean = false;
  property_id: string;


  minChar:number=1;
  filteredOptions:any;
  noResult:boolean=false;
  sendInviteFor:boolean=false;

  visible = true;
  selectable = true;
  removable = true;
  separatorKeysCodes: number[] = [ENTER, COMMA];
  //filteredFruits: Observable<string[]>;
  user_list: string[] = [];

  @ViewChild('userInput') userInput: ElementRef<HTMLInputElement>;
  @ViewChild('auto') matAutocomplete: MatAutocomplete;

  constructor(private formBuilder: FormBuilder,
              private commonActivityService :CommonActivityService,
              private commonApplicationService:CommonApplicationService,
              private alertService: AlertService,
              private dialogRef: MatDialogRef<SendInviteComponent>,
              @Inject(MAT_DIALOG_DATA) public data: any,
              private http:HttpClient
       ) { 
        
   }
  

  ngOnInit() {
    this.sendInviteForm = this.formBuilder.group({
                          user_ids:["",[Validators.required]],
                         //emails: [ "", [Validators.required]], // this.commaSepEmail
                          subject: ["", [Validators.required]],
                          message: [""],
                          is_all_invite: [""],
                        });
    if(this.data.property_id){
      this.property_id = this.data.property_id;
    }
    
    this.sendInviteForm.get('user_ids').valueChanges.subscribe(value => { 
			if(value && value.length>=this.minChar){
				this._filter(value);
			}else{
				this.filteredOptions=[];
			}
		});
    this.sendInviteFor=this.data.is_all_invite?this.data.is_all_invite:0;


  }

  get f() { return this.sendInviteForm.controls; }

  // Property Validation
  validateForm(data: any) {  
    console.log(this.sendInviteForm);   
    let result = this.commonActivityService.getFullFormData(data);
    this.invalidFields = this.commonActivityService.findInvalidControls(data);
    this.submitted = true;
    if(this.sendInviteForm.invalid) { 
      return;
    }    
    // FORM SUBMITTED
    this.saveInfoDetails(result);  
  }
  // Save info detail
  saveInfoDetails(data: any){
    this.loading = true;
    if(!this.property_id || this.property_id == ''){
      this.alertService.error("Something went wrong, Please refresh page and try again."); 
      this.loading = false;
      return false;
    }
    data.user_ids=this.user_list;

    if(data.is_all_invite){
      data.is_all_invite=1;
    }else{
      data.is_all_invite=0;
    }

    let url = apiUrl.send_invite+"/"+this.property_id;
    this.commonApplicationService.post(url, data)
      .subscribe(
          data => {
            this.modifyBtn = false;
            this.alertService.common(data);  
            this.loading = false;
            this.dialogRef.close({ data: data['messages'] });
          },
          error => {
            this.loading = false;
            this.alertService.common(error);
            this.dialogRef.close();
          }
      ); 
  }


  add(event: MatChipInputEvent): void {
    const input = event.input;
    const value = event.value;
    // Add our fruit
    if ((value || '').trim()) {
      this.user_list.push(value.trim());
    }
    // Reset the input value
    if (input) {
      input.value = '';
    }
    //this.sendInviteForm.get('user_ids').setValue(null);
  }

  remove(fruit: string): void {
    const index = this.user_list.indexOf(fruit);
    if (index >= 0) {
      this.user_list.splice(index, 1);
    }
    if(this.user_list.length==0){
      this.sendInviteForm.get('user_ids').setValue(null);
    }
  }

  selected(event: MatAutocompleteSelectedEvent): void {
    this.user_list.push(event.option.value);
    this.userInput.nativeElement.value = '';
    //this.sendInviteForm.get('user_ids').setValue(null);
  }


  private _filter(value: any):any{
    this.searchLoading=true;
    this.noResult=false;
    this.filteredOptions=[];
    let url = apiUrl.user_list+'?keywords='+value;
    this.commonApplicationService.get(url).subscribe(response=>{
      this.filteredOptions= response;
      this.searchLoading=false;
      if(this.filteredOptions.length==0){
        this.noResult=true;
      }
  
    });
  }

   // commaSepEmail = (control: AbstractControl): { [key: string]: any } | null => {
  //   const emails = control.value.split(',').map(e=>e.trim());
  //   const forbidden = emails.some(email => Validators.email(new FormControl(email)));
  //   return forbidden ? { 'emails': { value: control.value } } : null;
  // };

}
