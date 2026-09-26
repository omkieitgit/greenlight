import { Component, Input, OnInit } from '@angular/core';
import { apiUrl } from '@config/api-url';
import { AlertService, CommonApplicationService } from '@shared-service/_services';

@Component({
  selector: 'app-mcd-user',
  templateUrl: './mcd-user.component.html',
  styleUrls: ['./mcd-user.component.css']
})
export class McdUserComponent implements OnInit {
  openPanel: boolean=false;
  loaded:boolean=false;
  @Input() property_id:any;
  mcdUserList: any;
  buyerList: any;
  loading:boolean=false;

  constructor(private commonApplicationService:CommonApplicationService,
              private alertService:AlertService) { }

  ngOnInit(): void {
  }
  panelExpand(flag){
    if(!this.openPanel){
      this.openPanel=true;
      this.getMcdUserList();
    }
  }

  getMcdUserList(){
    
    let url = apiUrl.mcd_user+'/'+this.property_id;
    this.commonApplicationService.get(url)
      .subscribe(
          data => {
            this.mcdUserList=data.data['mcd_users'];
            this.buyerList=data.data['buyer_list'];
            this.loaded = true;
          },
          error => {
              this.loaded = true;
          }
      ); 
  }

  removeUser(id){
    if(confirm("Are you sure want to delete record ?")){
      this.loading=true;
      let url = apiUrl.mcd_user+'/'+id;
      this.commonApplicationService.delete(url).subscribe(response => {
        if(response.status=='success'){
          this.mcdUserList = this.mcdUserList.filter(item => item.id !== id);
          this.alertService.success(response.message);
        }else{
          this.alertService.error(response.message); 
        }
        this.loading = false;
      },
      (err: any) => {
        this.loading = false;
        this.alertService.common(err); 
      })
    }
  }

  addMcdUserInfo(response){
      let itemIndex = this.mcdUserList?this.mcdUserList.findIndex(item => item.id == response.id):'-1';
      if(itemIndex >= 0){
        this.mcdUserList[itemIndex] =response;
      }else{
        this.mcdUserList.push(response);
      }
  }

}
