import { Component, OnInit } from '@angular/core';
import { StorageService } from '@shared-service/_services/storage.service';

@Component({
  selector: 'app-property-detail',
  templateUrl: './property-detail.component.html',
  styleUrls: ['./property-detail.component.css']
})
export class PropertyDetailComponent implements OnInit {

  propertyInfo:any;
  constructor(private sessionService:StorageService) { }

  ngOnInit(): void {
    this.propertyInfo =this.sessionService.getHard('property_info');
    
  }

}
