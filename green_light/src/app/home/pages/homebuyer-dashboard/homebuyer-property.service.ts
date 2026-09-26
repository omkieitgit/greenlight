import { Injectable } from '@angular/core';
import { CommonApplicationService,CommonActivityService,AlertService,CommunicationService} from '../../../shared/_services';
import { apiUrl } from '../../../config/api-url';
import { Subject } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class HomebuyerPropertyService {

  countResult=new Subject<any>();
  searchResult=new Subject<any>();

  
  constructor(private commonApplicationService:CommonApplicationService,
              private communicationService:CommunicationService) { }


  getWhPropertyCount(request){
    let url = apiUrl.wholesale_buyer_count;
    this.commonApplicationService.getSearch(url,{ params: request }).subscribe(response => {
      if(response.status=='success'){
       this.countResult.next(response);
      }
    },
    (err: any) => {
       "Error occured, Please try again later!";
    })
  }

  getSearchResult(){
    return this.countResult.asObservable();
  }


  get_wholesale_info(requestPrames){
    
    let url = apiUrl.wholesale_buyer_list;
    this.commonApplicationService.getSearch(url, { params: requestPrames }).subscribe(response => {
      if(response.status=='success'){
        this.searchResult.next(response.data);
      }
    },
    (err: any) => {
       "Error occured, Please try again later!";
    })
  }

  getSearchDetail(){
    return this.searchResult.asObservable();
  }
  
}
