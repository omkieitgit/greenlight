import { Component, ViewChild, AfterViewInit, Input,Output,EventEmitter } 		 from '@angular/core';
import { CommunicationService } from '../../shared/_services';

@Component({
  selector: 'panel',
  inputs: ['title', 'variant', 'noBody', 'noButton', 'bodyClass', 'footerClass', 'panelClass'],
  templateUrl: './panel.component.html'
})

export class PanelComponent implements AfterViewInit {
  @ViewChild('panelFooter') panelFooter;
  expand = false;
  reload = false;
  //collapse = true;
  remove = false;
  showFooter = false;
  @Input() collapse:boolean = true;
  @Input() removeBtn:boolean = false;
  @Output() valueChange = new EventEmitter();
  @Output() removePannel= new EventEmitter();
  constructor(private communicationService: CommunicationService ) {
       
     }

  ngAfterViewInit() {
    
    setTimeout(() => {
      this.showFooter = this.panelFooter.nativeElement && this.panelFooter.nativeElement.children.length > 0;
    });    

    this.communicationService.getExpand().subscribe(collapse => {
        this.collapse=collapse;
        this.panelCollapse();
    });
  }

  panelExpand() {
    this.expand = !this.expand;
  }
  panelReload() {
    this.reload = true;

    setTimeout(() => {
        this.reload = false;
    }, 1500);
  }
  panelCollapse() {
    this.valueChange.emit(this.collapse);
    this.collapse = !this.collapse;
  }
  
  
  panelRemove() {
    if(confirm("Are you sure want to delete record ?")){
      this.remove = !this.remove;
      this.removePannel.emit(true);
    }
  }
}
