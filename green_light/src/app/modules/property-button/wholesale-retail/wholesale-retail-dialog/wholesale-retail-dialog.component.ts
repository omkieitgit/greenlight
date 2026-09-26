import { Component, OnInit } from '@angular/core';
import { MatDialogRef, MAT_DIALOG_DATA} from '@angular/material/dialog';
import { FormBuilder, FormGroup, Validators, FormArray } from '@angular/forms';
import { CommonActivityService } from '../../../../shared/_services';

@Component({
  selector: 'app-wholesale-retail-dialog',
  templateUrl: './wholesale-retail-dialog.component.html',
  styleUrls: ['./wholesale-retail-dialog.component.css']
})
export class WholesaleRetailDialogComponent implements OnInit {
  wholesaleRetailForm: FormGroup;
  invalidFields:any;
  submitted:boolean=false;

  constructor(private formBuilder: FormBuilder,
              private commonActivityService :CommonActivityService,
              public dialogRef: MatDialogRef<WholesaleRetailDialogComponent>) { }

  ngOnInit() {
    this.initialize();
  }

  initialize(){
    this.wholesaleRetailForm = this.formBuilder.group({
        arv: ['',Validators.required],     
        cma_arv:['',Validators.required],
        renovation_cost:['',Validators.required],
        page_show:[""]
   });
 }

 get f() { return this.wholesaleRetailForm.controls; }

  closeDialog() {
    let result = this.commonActivityService.getFullFormData(this.wholesaleRetailForm);
    console.log(result);
    this.invalidFields = this.commonActivityService.findInvalidControls(this.wholesaleRetailForm);
    this.submitted = true;
    if(this.wholesaleRetailForm.invalid) { 
      return;
    }  
    this.dialogRef.close(result);
  }
}
