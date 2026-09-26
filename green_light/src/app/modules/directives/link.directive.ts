import { Directive, ElementRef, HostListener, Input } from '@angular/core';

@Directive({
  selector: '[appLink]'
})
export class LinkDirective {

  @Input('appLink') link_url: any;
  @Input() align:string = 'pull-right';

  constructor(private el: ElementRef) {
   }
   ngOnInit() {   
    if(this.link_url){
      this.el.nativeElement.className=this.align+" col-form-label p-r-5";
      this.el.nativeElement.innerHTML = '<a href="'+this.link_url+'" target="_blank">Link</a>';;
    }
   }

   ngOnChanges(){
    if(this.link_url){
      this.el.nativeElement.className=this.align+" col-form-label p-r-5";
      this.el.nativeElement.innerHTML = '<a href="'+this.link_url+'" target="_blank">Link</a>';;
    }
   }

}
