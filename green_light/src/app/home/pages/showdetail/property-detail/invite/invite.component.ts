import { Component, OnInit,Input } from '@angular/core';
import { Router,ActivatedRoute }    from '@angular/router';
import {MatDialog, MatDialogRef, MAT_DIALOG_DATA} from '@angular/material/dialog';
import { SendInviteComponent } from './send-invite/send-invite.component';
import { InviteListComponent } from './invite-list/invite-list.component';
import { InviteAllComponent } from './invite-all/invite-all.component';
import {InvitedDataComponent} from './invited-data/invited-data.component';
@Component({
  selector: 'app-invite',
  templateUrl: './invite.component.html',
  styleUrls: ['./invite.component.css']
})
export class InviteComponent implements OnInit {
  
  @Input() property_id: string;
  @Input() isFavourite;

  constructor(private route: ActivatedRoute,
              public dialog: MatDialog) { }

  ngOnInit() {
    this.property_id = this.route.snapshot.paramMap.get('property_id');
  }

  openDialog(type){
    switch (type) {
      case "sendInvite":
        const dialogRef =  this.dialog.open(SendInviteComponent,{ width: '600px',disableClose:true,data:{property_id:this.property_id}} );
        dialogRef.afterClosed().subscribe(result => {
            if(result['data']){
              this.dialog.open(InvitedDataComponent,{ width: '600px',disableClose:true,data:result['data']});
            }
        });
       
      break;
      case "inviteList":
          this.dialog.open(InviteListComponent,{ width: '1000px',disableClose:true,data:{property_id:this.property_id}} );
      break;
      case "inviteAll":
          this.dialog.open(InviteAllComponent,{ width: '1000px',disableClose:true,data:{property_id:this.property_id}} );
      break;
      default:
        // code...
      break;
    }
  }
}
