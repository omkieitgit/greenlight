import { Component, OnInit,Input } from '@angular/core';
import { CommonApplicationService,CommonActivityService,CommunicationService } from '@shared-service/_services';
import { apiUrl } from '@config/api-url';
@Component({
  selector: 'app-defective-notes-detail',
  templateUrl: './defective-notes-detail.component.html',
  styleUrls: ['./defective-notes-detail.component.css']
})
export class DefectiveNotesDetailComponent implements OnInit {

  @Input() property_id;
  @Input() lien_type;
  mortgage_notes:any=[];
  
  constructor(private commonApplicationService: CommonApplicationService,
              private commonActivityService: CommonActivityService,
              private communicationService:CommunicationService) { }

  ngOnInit() {
    this.get_mortgage_notes();
    this.communicationService.getMortgageNotes().subscribe(data => {
      if(this.lien_type==data.lien_type)
        this.mortgage_notes.push(data);
    });
  }

  get_mortgage_notes(){
    let url = apiUrl.mortgage_notes+this.property_id+'/'+this.lien_type;
    this.commonApplicationService.get(url).subscribe(response => {
      if(response !== undefined && response.data.length>0){             
          this.mortgage_notes=response.data;
      }
    });
  }

  get_date(date){
    if(date){
      var d = new Date((date*1000));
      var back_date = (d.getMonth() + 1) + '/' + d.getDate() + '/' + d.getFullYear()+' '+d.getHours()+':'+d.getMinutes();
      return back_date;
    }
  }

}
