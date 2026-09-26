import { Component, Input, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { apiUrl } from '@config/api-url';
import { AlertService, CommonActivityService, CommonApplicationService } from '@shared-service/_services';
import { IMyDateModel, IMyDpOptions } from 'mydatepicker';

@Component({
  selector: 'app-vehicle-nos',
  templateUrl: './vehicle-nos.component.html',
  styleUrls: ['./vehicle-nos.component.css']
})
export class VehicleNosComponent implements OnInit {

  vehicleNosInfoForm:FormGroup;
  submitted: boolean;
  loading: boolean;
  @Input() vehicleNosInfo;
  @Input() vehicleId;
  
  nosUserList:any;

  constructor(private formBuilder:FormBuilder,
              private commonActivityService:CommonActivityService,
              private commonApplicationService:CommonApplicationService,
              private alertService:AlertService) { }

  ngOnInit(): void {
    this.getUserList();
    this.vehicleNosInfoForm = this.formBuilder.group({
      id:[''],
      nos_date: ['', Validators.required],
      nos_by: ['', Validators.required],
      nos_by_date: ['', Validators.required],
      vehicleId:[this.vehicleId]
    });
  }
  public myDatePickerOptions: IMyDpOptions = { dateFormat: 'mm/dd/yyyy',firstDayOfWeek : 'su'};

  onDateChanged(event: IMyDateModel) {
    return event.formatted;
  }

  validateForm(data,fileUploading){
    let result = this.commonActivityService.getFullFormDataWithDateFormatted(data);
    this.submitted = true;
    console.log('niform');
    if(this.vehicleNosInfoForm.invalid) { 
      return;
    }    
    // FORM SUBMITTED
    console.log('form submitted');
    this.saveUpdateVehicleInfo(result,fileUploading);  
  }

  uploadHandler(event){
    let elem = event.target; 
    if(elem.files.length > 0){
    }
  }

  saveUpdateVehicleInfo(data,document){
      this.loading = true;
      
    let input = new FormData();
    input.append("nos_date", data['nos_date']);
    input.append("nos_by", data['nos_by']); 
    input.append("nos_by_date", data['nos_by_date']);
    input.append("id", data['id']);
    input.append("vehicleId", data['vehicleId']);
    if(document != "" && document != undefined){
      input.append('vehicle_nos_document',document.files[0]);
    }
    
    
    let url = apiUrl.vehicleNos;
    this.commonApplicationService.post(url, input).subscribe(
        response => {
          if(response.status=='success'){
            let itemIndex = this.vehicleNosInfo.findIndex(item => item.id == response.data.id);
            if(itemIndex >= 0){
              this.vehicleNosInfo[itemIndex] = response.data;
            }else{
              this.vehicleNosInfo.push(response.data);
            }
            this.vehicleNosInfoForm.reset();
            this.alertService.success(response.message);  
            }else{
              this.alertService.error(response.message); 
            }
            this.submitted=false;
            this.loading = false;
        },
        error => {
          this.loading = false;
          this.alertService.common(error); 
        }
    ); 
  }

  panelExpand($event){

  }
  
  editVehicleInfo(vehicle){
    this.vehicleNosInfoForm.get("id").setValue(vehicle?.id);
    let nosDate = null;
    if(vehicle?.nos_date !== undefined && vehicle?.nos_date != null){
      let dDate = new Date(vehicle?.nos_date);
      nosDate = {date: {year: dDate.getFullYear(),month: dDate.getMonth() + 1,day: dDate.getDate()}}
    }

    let nosByDate = null;
    if(vehicle?.nos_by_date !== undefined && vehicle?.nos_by_date != null){
      let dDate = new Date(vehicle?.nos_by_date);
      nosByDate = {date: {year: dDate.getFullYear(),month: dDate.getMonth() + 1,day: dDate.getDate()}}
    }

    this.vehicleNosInfoForm.get("nos_date").setValue(nosDate);
    this.vehicleNosInfoForm.get("nos_by").setValue(vehicle?.nos_by);
    this.vehicleNosInfoForm.get("nos_by_date").setValue(nosByDate);
    this.vehicleNosInfoForm.get('vehicleId').setValue(vehicle?.vehicleId);
  }

  getUserList(){
    let url = apiUrl.user_list+'?role=nos_by';
    this.commonApplicationService.get(url).subscribe(response => {
        this.nosUserList=response;
    });
  }

}
