import { Component, OnInit, Input } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router,ActivatedRoute } from '@angular/router';
import {MatDialog} from '@angular/material/dialog';

import {ScrapperComponent} from './scrapper/scrapper.component';

import { CommonApplicationService,AlertService,CommonActivityService,CommunicationService } from '@shared-service/_services';
import { apiUrl,scraperApiUrl,fixedUrl } from '@config/api-url';
import { StorageService } from '@shared-service/_services/storage.service';

@Component({
  selector: 'real-state',
  templateUrl: './real-state.component.html'
})
export class RealStateComponent implements OnInit {
  realStateForm: FormGroup;
  result: any;  
  @Input() realStateData: any;
  invalidFields: any; 
  submitted = false;
  loading = false;
  loadingMessage: any;
  propErr: boolean = false;
  propErrMsg: any = "";
  property_id: string;
  scrapper_info:any;
  zillow_loader:boolean=false;
  redfin_loader:boolean=false;
  realtor_loader:boolean=false;
  trulia_loader:boolean=false;
  har_loader:boolean=false;
  gui_loader:boolean=false;  
  movoto_loader:boolean=false;

  beenVerifiedUrl:string=fixedUrl.beenverified_url;

  constructor(private formBuilder: FormBuilder,          
          private commonApplicationService: CommonApplicationService,
          private commonActivityService: CommonActivityService,
          private storageService:StorageService,private alertService: AlertService,
          private router: Router,
          private route: ActivatedRoute,
          private dialog:MatDialog,
          private communicationService:CommunicationService) { }

  ngOnInit() {
    this.property_id = this.route.snapshot.paramMap.get('property_id');
    // Load info details
    if(this.property_id !== undefined){
      //this.propErr = true;
     this.initialize();
    }
  }

  initialize(){
    
    if(this.realStateData.beenverified_url && this.realStateData.beenverified_url!==undefined){
      this.beenVerifiedUrl=this.realStateData.beenverified_url;
    }

     this.realStateForm = this.formBuilder.group({
      zillow_url: [this.realStateData?this.realStateData.zillow_url:'', Validators.required],
      zestimate: [this.realStateData?this.realStateData.zestimate:''],
      redfin_url: [this.realStateData?this.realStateData.redfin_url:'', Validators.required],
      redfin_est: [this.realStateData?this.realStateData.redfin_est:''],
      realtor_url: [this.realStateData?this.realStateData.realtor_url:'', Validators.required],
      realtor_est: [this.realStateData?this.realStateData.realtor_est:''],
      truila_url: [this.realStateData?this.realStateData.truila_url:'', Validators.required],
      truila_est: [this.realStateData?this.realStateData.truila_est:''],
      har_url: [this.realStateData?this.realStateData.har_url:'', Validators.required],
      har_est: [this.realStateData?this.realStateData.har_est:''],
      gui_url: [this.realStateData?this.realStateData.gui_url:'', Validators.required],
      gui_est: [this.realStateData?this.realStateData.gui_est:''],
      movoto_url: [this.realStateData?this.realStateData.movoto_url:'', Validators.required],
      movoto_est: [this.realStateData?this.realStateData.movoto_est:''],
      beenverified_url: [this.beenVerifiedUrl],
      save:['Save'],
    });
    this.sendMessage();
    //this.commonActivityService.isDisabled("PROPERTY_INFO", this.realStateForm);
  }

  sendMessage(): void {
    let data = {
      "zillow_url" : this.realStateData.zillow_url, 
      "redfin_url": this.realStateData.redfin_url,
      "har_url": this.realStateData.har_url, 
    };
    this.storageService.setHard('real_info',data);
}

/*----------------------------- Form validation ---------------------------------------*/
  validateForm(data: any) {     
    console.log(this.realStateForm);
    let result = this.commonActivityService.getFullFormData(data);
    this.invalidFields = this.commonActivityService.findInvalidControls(data);
    console.log("Form Invalid Filleds = ",this.invalidFields);
    this.submitted = true;
    console.log('niform');
    if(this.realStateForm.invalid) { 
      console.log('Form is invalid, Fill all fields.');
      this.alertService.error('Form is invalid, Fill all fields.');  
      //this.propertyForm.get('prop_address').markAsTouched();      
      return;
    }    
    // FORM SUBMITTED
    console.log('form submitted');
    this.saveRealStatedetails(result);  
  }

/*----------------------------- Form validation ---------------------------------------*/

/*----------------------------- Save Details ---------------------------------------*/
  saveRealStatedetails(data: any){
    this.loading = true;
    //console.log("savingdata>"+this.propertyForm);
    console.log("savingdata>"+data);
    if(this.property_id){
      let url = apiUrl.save_prop_real_state+this.property_id;
      this.commonApplicationService.put(url, data)
        .subscribe(
            data => {
              this.alertService.success(data.message);  
              this.loading = false;
            },
            error => {
                this.alertService.common(error);  
                this.loading = false;
            }
        ); 
    }else{
        this.loading = false;
        this.alertService.error("Please save property detail.");  
    }
    
  }

  /*----------------------------- Save Details ---------------------------------------*/

  get f() { return this.realStateForm.controls; }

  search_property(scraper_url,type){
    if(!this.trulia_loader){
      this.trulia_loader=true;
      let url = scraperApiUrl.trulia;
      this.scrapper_property(scraper_url,type,ScrapperComponent,url);
    }
  }

  zillow_search_property(scraper_url,type){
    if(!this.zillow_loader){
      this.zillow_loader=true;
      let url = scraperApiUrl.zillow;
      this.scrapper_property(scraper_url,type,ScrapperComponent,url);
    }
   
  }

  har_search_property(scraper_url,type){
    if(!this.har_loader){
      this.har_loader=true;
      let url = scraperApiUrl.har;
      this.scrapper_property(scraper_url,type,ScrapperComponent,url);
    }
  }

  redfin_search_property(scraper_url,type){
    if(!this.redfin_loader){
      this.redfin_loader=true;
      let url = scraperApiUrl.redfin;
      this.scrapper_property(scraper_url,type,ScrapperComponent,url);
    }
  }

  realtor_search_property(scraper_url,type){
    if(!this.realtor_loader){
      this.realtor_loader=true;
      let url = scraperApiUrl.realtor;
      this.scrapper_property(scraper_url,type,ScrapperComponent,url);
    }
  }

  gui_search_property(scraper_url,type){
    if(!this.gui_loader){
      this.gui_loader=true;
      let url = scraperApiUrl.gui;
      this.scrapper_property(scraper_url,type,ScrapperComponent,url);
    }
  }

  movoto_search_property(scraper_url,type){
    if(!this.movoto_loader){
      this.movoto_loader=true;
      let url = scraperApiUrl.movoto;
      this.scrapper_property(scraper_url,type,ScrapperComponent,url);
    }
  }

  scrapper_property(scraper_url,type,component,url){
    
    let input = new FormData();
    input.append('scraper_url', scraper_url);
    input.append('scraper_name', type);
    input.append('end_url', url);
    this.commonApplicationService.post(apiUrl.scraper, input)
      .subscribe(
          data => {
            this.scrapper_info=data;
            data.property_id=this.property_id;
            this.zillow_loader=this.trulia_loader=this.redfin_loader=false;
            this.har_loader=this.realtor_loader=this.gui_loader=this.movoto_loader=false;
            let dialogRef=this.dialog.open(component,{ width: '800px',data:data,disableClose:true} );
            dialogRef.afterClosed().subscribe(result => {
              this.communicationService.setScrapperData(result);
            });
          },
          error => {
            this.zillow_loader=this.trulia_loader=this.redfin_loader=false;
            this.har_loader=this.realtor_loader=false;
            this.alertService.common(error);
          }
      ); 
  }

  updateScrapperData(){
    
    //home/scrapper/{house_id}
    let data=this.scrapper_info;
    let url = apiUrl.update_scrapper_data+this.property_id;
    this.commonApplicationService.put(url, data)
        .subscribe(
            data => {
              this.alertService.success(data.message);  
              this.loading = false;
            },
            error => {
              this.loading = false;
              this.alertService.common(error); 
            }
        ); 

  }

  autoSave(data){
    this.commonActivityService.addLoader(data['el']);
    this.commonActivityService.removeElement(data['el'],'saved-icon');
    
    let saveInfo:any={'name':data['name'],'value':data['value']}
    let url = apiUrl.auto_save_property+'/'+this.property_id;
    this.commonApplicationService.put(url, saveInfo)
        .subscribe(
            response => {
              if(response['status']=='failed'){
                this.commonActivityService.removeElement(data['el'],'loader-icon');
                this.alertService.error(response['message']);
              }else{
                  this.commonActivityService.addElement(data['el']);
                  this.commonActivityService.removeElement(data['el'],'loader-icon');
              }
            },
            error => {
              this.loading = false;
              this.alertService.common(error);
            }
        ); 
  }


}


