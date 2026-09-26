import { Component, Input,OnInit } from '@angular/core';
import {MatDialog, MatDialogRef, MAT_DIALOG_DATA} from '@angular/material/dialog';
import {AddMeComponent} from './add-me/add-me.component';

@Component({
  selector: 'app-add-me-btn',
  templateUrl: './add-me-btn.component.html',
  styleUrls: ['./add-me-btn.component.css']
})
export class AddMeBtnComponent implements OnInit {
  
  @Input() property_id;

  constructor(private dialog:MatDialog) { }

  ngOnInit() {
  }

  add_me(e){
    e.preventDefault();
    e.stopPropagation();
    let data={property_id:this.property_id};
    this.dialog.open(AddMeComponent,{ width: '500px',data:data,disableClose:true} );
  }

}
