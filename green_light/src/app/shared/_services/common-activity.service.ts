import { Injectable, Renderer2, RendererFactory2 } from '@angular/core';
import { HttpClient } from '@angular/common/http';
//import { base_url, property_info } from '../../config/api-url';
import { apiUrl } from '../../config/api-url';
import { StorageService } from './storage.service';
import * as roles from '../../config/role-based-forms';

@Injectable()
export class CommonActivityService {
    userRole: any;
    configRole: any;
    public renderer: Renderer2;
    constructor(private http: HttpClient,
                private storageService:StorageService,
                rendererFactory: RendererFactory2
                ) {
                  this.renderer = rendererFactory.createRenderer(null, null);
        this.userRole = this.storageService.get("user_info")['current_role'];
        if(this.userRole == "wholesale_buyer"){
            this.configRole  = roles.homeBuuyer();
        }else if(this.userRole == "lender_funder"){
             this.configRole = roles.landerFunder();
        }
     }
    
  isDisabled(tabName, tabForm){
           // Check full is form is enable/disabled or perticular fields
           if(this.userRole != "is_dtc" && this.configRole){
               var passedTab = this.configRole[tabName];
               if(Array.isArray(passedTab) && passedTab.length > 0){ // && passedTab.indexOf(key) != -1
                 var total = passedTab.length;
                 for(var i =0; i< total; i++){
                   tabForm.controls[passedTab[i]].disable();
                 }
               }else if(passedTab == "ON"){
                 return false;
               }else if(passedTab == "OFF"){
                 tabForm.disable();
               }else{
                 return false
               }
           }           
  }

  getModifiedOnly(form: any) {
        let dirtyValues = {};

        Object.keys(form.controls)
            .forEach(key => {
                let currentControl = form.controls[key];

                if (currentControl.dirty) {
                    if (currentControl.controls)
                        dirtyValues[key] = this.getModifiedOnly(currentControl);
                    else
                        dirtyValues[key] = currentControl.value;
                }
            });
      	//console.log("dirtyValues"+dirtyValues);
        return dirtyValues;
  }

  getFullFormData(form: any) {
        let formValues = {};
        Object.keys(form.controls)
            .forEach(key => {
                let currentControl = form.controls[key];
                formValues[key] = currentControl.value;
            });
          //console.log("dirtyValues"+dirtyValues);
        return formValues;
  }

  public findInvalidControls(form: any) {
    const invalid = [];
    const controls = form.controls;
    for (const name in controls) {
        if (controls[name].invalid) {
            invalid.push(name);
        }
    }
    return invalid;
  }


  public getFullFormDataWithDateFormatted(form:any) {

        let formValues = {};
        Object.keys(form.controls)
            .forEach(key => {
                let currentControl = form.controls[key];
                // Checking if date is object then converting to string and saving it.
                if(currentControl.value !== undefined && currentControl.value !== null 
                        &&  currentControl.value.date !== undefined ){
                        console.log("Yes its date");
                        var dt = currentControl.value.date;
                        let dayStr = (dt.day <=9)? "0"+dt.day : dt.day;
                        let monStr = (dt.month <=9)? "0"+dt.month : dt.month;
                        formValues[key] = dt.year+"-"+monStr+"-"+dayStr;
                }else if(currentControl.value !== undefined && currentControl.value !== null 
                        &&  currentControl.value.jsdate !== undefined ){
                        // Js Date
                        var dt = currentControl.value.jsdate;
                        let dayStr = (dt.getDate() <=9)? "0"+dt.getDate() : dt.getDate();
                        let month = dt.getMonth()+1;
                        let monStr = (month <=9)? "0"+month : month;
                        formValues[key] = dt.getFullYear()+"-"+monStr+"-"+dayStr;
                }else{
                    formValues[key] = currentControl.value;
                }
            });
          //console.log("dirtyValues"+dirtyValues);
        return formValues;
  }

  addElement(el) {
    const p: HTMLParagraphElement = this.renderer.createElement('i');
    p.classList.add('saved-icon','fas' ,'fa-sm', 'fa-check');
    this.renderer.appendChild(el.parentElement, p)
  }

  addErrorElement(el) {
    const p: HTMLParagraphElement = this.renderer.createElement('i');
    p.classList.add('error-icon','fas' ,'fa-sm', 'fa-times');
    this.renderer.appendChild(el.parentElement, p)
  }

  addLoader(el){
    const p: HTMLParagraphElement = this.renderer.createElement('i');
    p.classList.add('loader-icon','fa-spin','fas' ,'fa-sm', 'fa-spinner');
    this.renderer.appendChild(el.parentElement, p)
  }

  removeElement(el,className)
  {
    if(el.parentElement.querySelector('.'+className))
      el.parentElement.querySelector('.'+className).remove();
  }

}
