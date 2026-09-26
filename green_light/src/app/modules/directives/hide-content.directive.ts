import { Directive,Input,Renderer2,ElementRef,EventEmitter,Output} from '@angular/core';
import { StorageService } from '../../shared/_services/storage.service';
import {FormControl} from '@angular/forms';
@Directive({
  selector: '[appHideContent]'
})
export class HideContentDirective {
  @Input('appHideContent') tabForm: any;
  content_data:boolean;
  @Output() checkContentEvent = new EventEmitter();

  constructor(private storageService:StorageService,
              private renderer: Renderer2,
              private el: ElementRef){}
  
               
 ngOnInit(){
    setTimeout(()=>{
      this.hidePannel();
    }, 300);
 }
 
 hidePannel(){
  if(this.tabForm){
    this.content_data=false;
    if(!this.isEmptyObject(this.tabForm)){
      Object.keys(this.tabForm.controls).forEach(field => {
        const control = this.tabForm.get(field).value;
        if (control && this.isNotCheckField().indexOf(field) === -1) {
          //console.log(field,control);
          this.content_data=true;
        }
      }); 
      if(this.content_data){
        this.checkContentEvent.emit(false);
      }else{
        this.checkContentEvent.emit(true);
      }

    }
  }
 }

 isNotCheckField(){
   return ['mortgage_id','cma_arv_value','trustee_fees','sub_lien_position','date','rental_rate','es_excess_funds'];
 }

 isEmptyObject(obj) {
  return (obj && (Object.keys(obj).length === 0));
 }
}



