import { Component, Injectable, OnDestroy,ViewChild, OnInit, Renderer2, ComponentFactoryResolver, Input, } from '@angular/core';
import { FormControl, NgForm, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { SortingComponent } from '../../showdetail/sorting/sorting.component';
import { CommonApplicationService } from '../../../../shared/_services';
import { apiUrl,scraperApiUrl } from '../../../../config/api-url';
import { AlertService } from '../../../../shared/_services';
import { CommonActivityService } from '../../../../shared/_services';
import { StorageService } from '../../../../shared/_services/storage.service';
import {IMyDpOptions, IMyDateModel} from 'mydatepicker';
import { Router,ActivatedRoute }    from '@angular/router';

@Component({
  selector: 'app-client-document-view',
  templateUrl: './client-document-view.component.html',
  styleUrls: ['./client-document-view.component.css']
})

export class ClientDocumentViewComponent implements OnInit {
    openPanel:boolean = false;
	  property_document_type_list: string[];
    document_property: any[];
    property_id: string;
    property_config:any;
    showPropertyList: boolean = false;
    documentResult: any;
    loading:boolean=false;
    slug_address:any;
    


    field_option:any={'other_name':false,'case_no':false};


	@ViewChild('documentListChanged') documentListChanged: SortingComponent; 
	constructor(private formBuilder: FormBuilder,
		private commonApplicationService: CommonApplicationService,
		private commonActivityService: CommonActivityService,
		private alertService: AlertService,
		private storageService:StorageService,
    private router: Router,
    private route: ActivatedRoute,
		) {
        this.property_config =  this.storageService.get("property_config");
	        if(this.property_config !== null && this.property_config !== false){
	          this.property_document_type_list = this.property_config.client_document_type;
	        }
        }

	documentChange() {
	this.documentListChanged.listen();
	}

	public myDatePickerOptions: IMyDpOptions = { dateFormat: 'yyyy-mm-dd',firstDayOfWeek : 'su'};

  	onDateChanged(event: IMyDateModel) {
        return event.formatted;
    }

    ngOnInit() {
      this.property_id = this.route.parent.snapshot.parent.params.property_id;
      this.slug_address = this.route.snapshot.paramMap.get('slug_address');
      if(this.property_id !== undefined){
        if(this.openPanel){
            this.getDocumentInfo();
            
        }
      }
    }

  panelExpand(flag){
    if(!this.openPanel){
      this.getDocumentInfo();
      this.openPanel = true;
    }
  }

   // ----------------------------------- Get details Owner ---------------------------------------// 
  getDocumentInfo(){
      this.loading = true;
      //this.commonApplicationService.get(url,data,sucess_message,error_message);
      let url = apiUrl.client_document+"/"+this.property_id;
      this.commonApplicationService.get(url).subscribe(response => {
        this.loading = false;
        this.documentResult = response['data'].client_document;
        this.showPropertyList=true;
        //this.formatDocuments();
      },
      (err: any) => {
        this.loading = false;
        this.alertService.common("There are no posts pulled from the server!");
      })
  }

}
