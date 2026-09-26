import { Component, OnInit } from '@angular/core';
import { apiUrl } from '../../../../config/api-url';
import { FormControl, NgForm, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { AlertService,CommonActivityService,CommunicationService,CommonApplicationService } from '../../../../shared/_services';
import { Router,ActivatedRoute } from '@angular/router';
import { StorageService } from '../../../../shared/_services/storage.service';


@Component({
  selector: 'app-workprofile',
  templateUrl: './workprofile.component.html',
  styleUrls: ['./workprofile.component.css']
})
export class WorkprofileComponent implements OnInit {
  WorkprofileForm: FormGroup;
  property_id:string;
  loading =false;
  workProfileResponse:any;
  userRoles:any;
  WPuserList:any;
  submitted:boolean=false;

  constructor(private formBuilder: FormBuilder,
              private commonApplicationService: CommonApplicationService,
              private commonActivityService:CommonActivityService,
              private route: ActivatedRoute,
              private router:Router,
              private alertService:AlertService,
              private storageService:StorageService) { }

  ngOnInit(): void {
    console.log(this.storageService.get('user_info')['current_role']);
    if(this.storageService.get('user_info')['current_role']!='admin'){
      this.router.navigate(['home/dashboard']);  
    }
      

    this.getUserWorkProfile();
    this.getWPUserList();
    this.initialize();
  }
 
  initialize(){
    this.WorkprofileForm = this.formBuilder.group({
      work_profile_team: ['',[Validators.required]],
     user_id: ['',[Validators.required]],
    });
  }
  

  getUserWorkProfile(){
    let url=apiUrl.work_profile;

    this.commonApplicationService.get(url).subscribe(response =>{
      this.workProfileResponse=response;
      //this.userRoles=response['']
    })
  }

  getWPUserList(){
    let url=apiUrl.work_profile_user_list;
    this.commonApplicationService.get(url).subscribe(response =>{
      this.WPuserList=response;
      //this.userRoles=response['']
    })
  }

  teamDetail(wpTeam)
  {
    this.userRoles=this.workProfileResponse.pay_rates[wpTeam];
  }

  userDetail(event){
    let wp_team=this.WPuserList.find(val => val.id == event.target.value).work_profile_team;
    if(wp_team){
      this.f.work_profile_team.setValue(wp_team);
      this.teamDetail(wp_team);
    }else{
      this.userRoles=[];
      this.f.work_profile_team.setValue('');
    }
  }

  get f() { return this.WorkprofileForm.controls; }

  validateForm(data: any) {     
    let result = this.commonActivityService.getFullFormDataWithDateFormatted(data);
    this.submitted = true;
    if(this.WorkprofileForm.invalid)
      return;
    this.saveWorkProfile(result);  
  }


  saveWorkProfile(data: any){
    this.loading = true;
    let url = apiUrl.user_detail+'/'+data['user_id']+'/work_profile';
    this.commonApplicationService.post(url, data)
        .subscribe(
            response => {
              this.alertService.success(response.message);  
              this.loading = false;
              let updateItem = this.WPuserList.find(val => val.id == response.data.id);
              let index = this.WPuserList.indexOf(updateItem);
              this.WPuserList[index].work_profile_team=response.data.work_profile_team;
              this.submitted = false;
            },
            error => {
              this.loading = false;
              this.alertService.common(error);  
            }
        ); 
  }
}
