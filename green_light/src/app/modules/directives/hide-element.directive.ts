import { Directive, Input, TemplateRef, ViewContainerRef } from '@angular/core';
import { StorageService } from '../../shared/_services/storage.service';
import {CommunicationService} from '../../shared/_services';
@Directive({
  selector: '[noteBtnEnable],[hideElement],[hideBtnElement],[accounting],[accountingTab],[wholesale_buyer]'
})
export class HideElementDirective {
  private hasView = false;
  userRole: any;
  buyerRole:any =["wholesale_buyer","home_buyer","sub_to"];
  accountingTabAccessRole:any=["wholesale_buyer","home_buyer","sub_to","admin","accounting"];
  adminAcAccess=["admin","accounting"];
  constructor(private storageService:StorageService,
              private templateRef: TemplateRef<any>,
              private viewContainer: ViewContainerRef,
              private communicationService:CommunicationService) { 
                this.userRole = this.storageService.get("user_info")['current_role'];
              }
            
  ngOnInit(){
    
  }

  @Input() set noteBtnEnable(condition: any){

    
    if(!this.esMemberAccess()){
      if(condition=='buyer'){
        this.viewContainer.createEmbeddedView(this.templateRef);
        this.hasView = true;
      }else{
        this.viewContainer.clear();
        this.hasView = false;
      }
    }else{
      if(this.esMemberAccess()){
        this.viewContainer.createEmbeddedView(this.templateRef);
        this.hasView = true;
      }
    }
   

  }

  @Input() set hideBtnElement(condition: any){

    
    if(!this.esMemberAccess()){
      if(condition=='wholesale_buyer'){
        this.viewContainer.createEmbeddedView(this.templateRef);
        this.hasView = true;
      }else{
        this.viewContainer.clear();
        this.hasView = false;
      }
    }else{

    //}
    //if(this.userRole == "admin"){
        this.viewContainer.createEmbeddedView(this.templateRef);
        this.hasView = true;
    }

  }

  
  @Input() set hideElement(condition: boolean){

    if(!this.esMemberAccess()){
      if(!condition){
        this.viewContainer.createEmbeddedView(this.templateRef);
        this.hasView = true;
      }else{
        this.viewContainer.clear();
        this.hasView = false;
      }
    }else{
      this.viewContainer.createEmbeddedView(this.templateRef);
      this.hasView = true;
    }
    
  }


  @Input() set accounting(condition: boolean){
    if(this.adminAcAccess.indexOf(this.userRole)!==-1){
      this.viewContainer.createEmbeddedView(this.templateRef);
      this.hasView = true;
    }
  }

  @Input() set accountingTab(condition: boolean){
    if(this.accountingTabAccessRole.indexOf(this.userRole)!==-1){
      this.viewContainer.createEmbeddedView(this.templateRef);
      this.hasView = true;
    }
  }

  @Input() set wholesale_buyer(condition: boolean){

    if(!this.esMemberAccess()){
      this.viewContainer.createEmbeddedView(this.templateRef);
      this.hasView = true;
    }
  }

  esMemberAccess(){

    
    if(this.buyerRole.indexOf(this.userRole) === -1){
      return true;
    }else{
      return false;
    }
   
  }

}
