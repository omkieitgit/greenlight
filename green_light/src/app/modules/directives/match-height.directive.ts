import { Directive, ElementRef, HostListener, Input } from '@angular/core';

@Directive({
  selector: '[appMatchHeight]'
})
export class MatchHeightDirective {

  @Input() appMatchHeight: any;

  constructor(private el: ElementRef) {
   }
   ngAfterViewChecked() {    
        this.matchHeight(this.el.nativeElement, this.appMatchHeight);  
   }

   @HostListener('window:resize') onResize() {    
          // call our matchHeight function here later    
        this.matchHeight(this.el.nativeElement, this.appMatchHeight);  
   }
    matchHeight(parent: HTMLElement, className: string) {
      // match height logic here
      if (!parent) return;     
        const children= parent.getElementsByClassName(className);     
        if (!children) return;

      // reset all children height     
         let i = true;      
        Array.from(children).forEach((x: HTMLElement) => {    
              //x.style.height = 'initial';     
             const readmore = parent.getElementsByClassName('readmore');  
                Array.from(readmore).forEach((y: HTMLElement) => {  
                  if(x.scrollHeight-1 > x.offsetHeight){    
                  y.style.display = 'block';      
                i = false;       
             }else if(i==true){           
           y.style.display = 'none';     
               }      
            });    
          }) 
    }
}
