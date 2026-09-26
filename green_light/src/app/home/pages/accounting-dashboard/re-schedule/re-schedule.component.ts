import { Component, HostListener, OnInit } from '@angular/core';
import { FormBuilder, FormGroup } from '@angular/forms';
import { apiUrl } from '@config/api-url';
import { AlertService, CommonActivityService, CommonApplicationService } from '@shared-service/_services';
import { StorageService } from '@shared-service/_services/storage.service';

@Component({
  selector: 'app-re-schedule',
  templateUrl: './re-schedule.component.html',
  styleUrls: ['./re-schedule.component.css']
})
export class ReScheduleComponent implements OnInit {
  loading: boolean;
  reschedule_list:any;
  total_craig_profit:number;
  rescheduleForm:FormGroup;

  loader: boolean;
  limit: number=20;
  
  offset:number=0;
  moreBtn:boolean=true;
  scrollFlag: boolean;
  property_config:any;
  loan_type:any;

  constructor(private commonApplicationService:CommonApplicationService,
    private formBuilder:FormBuilder,
    private alertService:AlertService,
    private storageService:StorageService,
    private commonActivityService:CommonActivityService) { }

  ngOnInit(): void {

    this.property_config =  this.storageService.get("property_config");
    if(this.property_config !== null){
      this.loan_type          = this.property_config.loan_type;
    }

    this.rescheduleForm = this.formBuilder.group({
      is_sold: ['', ''],
      lender_name: ['', ''],
      property_address:['']
    });
    this.getReschedule('');
  }

 
  
  getReschedule(result){
      this.loading=true;
      let url=apiUrl.reSchedule;
      this.commonApplicationService.getSearch(url, { params: result })
      .subscribe(response => {
        if(response['data']){
          this.reschedule_list=response?.data?.reschedule_info;
          this.total_craig_profit=response?.data?.total_craig_profit;
        }
        this.loading = false;
      },
      (err: any) => {
        this.loading = false; 
      })
  }

  searchMscFormList(){
      this.loader=true;
      let result = this.commonActivityService.getFullFormDataWithDateFormatted(this.rescheduleForm);
      this.getReschedule(result);
  }
}
