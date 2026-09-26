import { Directive,Input,Renderer2,ElementRef} from '@angular/core';
import { StorageService } from '../../shared/_services/storage.service';
import {FormControl} from '@angular/forms';

@Directive({
  selector: '[appDisableElement]'
})
export class HasAccessDirective {
  
  userRole: any;

  @Input('appDisableElement') tabForm: any;
  @Input() disableBtn:any;
  @Input() hideElement:any;
  @Input() cma_arv_type:any;

  buyerRole:any =["wholesale_buyer","home_buyer","sub_to"];

  constructor(private storageService:StorageService,
              private renderer: Renderer2,
              private el: ElementRef) { }

  ngOnInit(){
    this.userRole = this.storageService.get("user_info")['current_role'];
    if(this.buyerRole.indexOf(this.userRole) !== -1 && this.cma_arv_type!="wholesale_buyer"){

    //if((this.userRole == "wholesale_buyer" || this.userRole == "home_buyer") && this.cma_arv_type!="wholesale_buyer"){
      if(this.tabForm){
        if(!this.isEmptyObject(this.tabForm)){
          Object.keys(this.tabForm.controls).forEach(field => {
            const control = this.tabForm.get(field);
            if (control instanceof FormControl) {
              control.disable();
            }
          }); 
        }else{
          this.tabForm.disable();
        }
      }
      
      

      if(this.disableBtn==true){
        this.renderer.setAttribute(this.el.nativeElement, 'disabled', this.disableBtn);
      }
      
    }
    
    // else if(this.userRole == "admin"){
    //   for(var i=0; i<this.tabForm.controls; i++){
    //     this.tabForm.controls[i].enable()
    //   }
    // }
  }

  isEmptyObject(obj) {
    return (obj && (Object.keys(obj).length === 0));
  }

}
