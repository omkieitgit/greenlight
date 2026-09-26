import { Component, OnInit, Inject } from '@angular/core';
import {MatDialog, MatDialogRef, MAT_DIALOG_DATA} from '@angular/material/dialog'

@Component({
  selector: 'app-es-guide-detail',
  templateUrl: './es-guide-detail.component.html',
  styleUrls: ['./es-guide-detail.component.css']
})
export class EsGuideDetailComponent implements OnInit {

  constructor(@Inject(MAT_DIALOG_DATA) public data) { }

  ngOnInit(): void {
    
  }

}
