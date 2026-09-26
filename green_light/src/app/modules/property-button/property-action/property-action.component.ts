import { Component, OnInit,Input } from '@angular/core';
import { CommonApplicationService,CommonActivityService,CommunicationService,AlertService} from '../../../shared/_services';
import { apiUrl } from '../../../config/api-url';
import { Router,ActivatedRoute }    from '@angular/router';
import {MatDialog, MatDialogRef, MAT_DIALOG_DATA} from '@angular/material/dialog';
import {StorageService} from '../../../shared/_services/storage.service';
import {SubToComponent} from '../sub-to/sub-to.component';
@Component({
  selector: 'app-property-action',
  templateUrl: './property-action.component.html',
  styleUrls: ['./property-action.component.css']
})
export class PropertyActionComponent implements OnInit {

  property_id:string;
  collapse:boolean=false;
  loader:boolean=false;
  address:string;
  @Input() quickInput;
  @Input() slug_address;
  
  constructor(private route:ActivatedRoute,
              private router: Router,
              private alertService:AlertService,
              private commonActivityService :CommonActivityService,
              private commonApplicationService:CommonApplicationService,
              private dialog:MatDialog,
              private storageService:StorageService,
              private communicationService:CommunicationService) { }

  ngOnInit() {
    this.property_id = this.route.snapshot.paramMap.get('property_id');
    if(this.property_id !== undefined){
      this.address= this.slug_address;
    }
  }
  
  

 
  expand_all($event){
    this.collapse = !this.collapse;    
    $event.target.innerHTML=!this.collapse?'Expand All':'Hide All';
    this.communicationService.expandAll(this.collapse);
  }

  getSubToPropety(){
    this.dialog.open(SubToComponent,{ width: '800px',disableClose:true,data:{property_id:this.property_id}} );
  }


  
}
