import { Component, OnInit, Input } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { InfoModel } from '../info.model';
import { Router,ActivatedRoute } from '@angular/router';
import { CommonApplicationService,AlertService,CommonActivityService,CommunicationService } from '@shared-service/_services';
import { apiUrl } from '@config/api-url';
import { StorageService } from '@shared-service/_services/storage.service';


@Component({
  selector: 'school',
  templateUrl: './school.component.html'
})
export class SchoolComponent implements OnInit {
  shoolForm: FormGroup;
  private infoData : InfoModel;
  result: any;  
  @Input() schoolData: any;
  invalidFields: any; 
  submitted = false;
  loading = false;
  loadingMessage: any;
  propErr: boolean = false;
  propErrMsg: any = "";
  property_id: string;

  constructor(private formBuilder: FormBuilder,          
          private commonApplicationService: CommonApplicationService,
          private commonActivityService: CommonActivityService,
          private storageService:StorageService,private alertService: AlertService,
          private router: Router,
          private route: ActivatedRoute,
          private communicationService:CommunicationService) { }

  ngOnInit() {
    this.property_id = this.route.snapshot.paramMap.get('property_id');
    // Load info details
    if(this.property_id !== undefined){
      this.initialize();
      //this.propErr = true;
      //this.getSchoolDetails();    
    }
    this.communicationService.getScrapperData().subscribe(scrapper=>{
      this.update_scrapper_info(scrapper);
    });
  }


  initialize(){
     this.shoolForm = this.formBuilder.group({
      elementary_school: [this.schoolData.elementary_school, Validators.required],
      middle_school: [this.schoolData.middle_school, Validators.required],
      high_school: [this.schoolData.high_school, Validators.required],
      elementary_ranking: [this.schoolData.elementary_ranking, ''],
      middle_ranking: [this.schoolData.middle_ranking, ''],
      high_ranking: [this.schoolData.high_ranking, ''],
      elementary_distance: [this.schoolData.elementary_distance, ''],
      middle_distance: [this.schoolData.middle_distance, ''],
      high_distance: [this.schoolData.high_distance, '']
      
    });
    this.commonActivityService.isDisabled("PROPERTY_INFO", this.shoolForm);
  }

 

  update_scrapper_info(scrapper){
    this.schoolData.elementary_school=scrapper.elementary_school;
    this.schoolData.middle_school=scrapper.middle_school;
    this.schoolData.high_school=scrapper.high_school;

    this.schoolData.elementary_ranking=scrapper.elementary_ranking;
    this.schoolData.middle_ranking=scrapper.middle_ranking;
    this.schoolData.high_ranking=scrapper.high_ranking;

    this.schoolData.elementary_distance=scrapper.elementary_distance;
    this.schoolData.middle_distance=scrapper.middle_distance;
    this.schoolData.high_distance=scrapper.high_distance;
    this.initialize();
  }

/*----------------------------- Form validation ---------------------------------------*/
  validateForm(data: any) {     
    
    let result = this.commonActivityService.getFullFormData(data);
    this.invalidFields = this.commonActivityService.findInvalidControls(data);
    this.submitted = true;
    if(this.shoolForm.invalid) { 
      return;
    }    
    this.loading=true;
    // FORM SUBMITTED
    this.saveSchoolDetails(result); 

    
    this.loading=false;
  }

/*----------------------------- Form validation ---------------------------------------*/

/*----------------------------- Save Details ---------------------------------------*/
  saveSchoolDetails(data: any){
    data.house_id = this.property_id;
    //console.log("savingdata>"+this.propertyForm);
    console.log("savingdata>"+data);
    if(this.property_id){
        let url = apiUrl.update_school+"/"+this.property_id;
        this.commonApplicationService.put(url, data)
        .subscribe(
            data => {
              this.alertService.success(data.message);  
            },
            error => {
                this.alertService.error(data.message);  
            }
        );
      } 
        else{
          this.alertService.error("Please save property detail.");  
      }
  }

/*----------------------------- Save Details ---------------------------------------*/

 get f() { return this.shoolForm.controls; }
 
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
