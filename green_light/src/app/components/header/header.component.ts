import { Component, Input, Output, EventEmitter, Renderer2, OnDestroy,OnInit,NgZone } from '@angular/core';
import pageSettings from '../../config/page-settings';
import {AlertService} from '../../shared/_services';
import { CommonApplicationService } from '../../shared/_services';
import { apiUrl } from '../../config/api-url';
import {StorageService} from '../../shared/_services/storage.service';
import {ExtendedHttpClientService} from '../../shared/_services/extened-http-client.service';
import { CommunicationService } from '../../shared/_services';
import {BootController} from '../../../boot-control';
import { Router, ActivatedRoute } from '@angular/router';
import { FormGroup,FormBuilder, Validators } from '@angular/forms';
import { HttpClient } from '@angular/common/http';
import { environment } from 'environments/environment';
//import { CookieService } from 'ngx-cookie-service';


@Component({
  selector: 'header',
  templateUrl: './header.component.html'
})
export class HeaderComponent implements OnDestroy,OnInit{
  	@Input() pageSidebarTwo;
	@Output() toggleSidebarRightCollapsed = new EventEmitter<boolean>();
	@Output() toggleMobileSidebar = new EventEmitter<boolean>();
	@Output() toggleMobileRightSidebar = new EventEmitter<boolean>();
	pageSettings = pageSettings;
	first_name:string='';
	last_name:string='';
	property_config: any= {};
	error_message:boolean=false;
	message:string;

	searchProForm:FormGroup;
	minChar:number=3;
	filteredOptions:any;
	loading:boolean=false;
	noResult:boolean=false;
	searchResult:any;
	isSearch:boolean=false;
	user_info:any;


	mobileSidebarToggle() {
		this.toggleMobileSidebar.emit(true);
	}
	mobileRightSidebarToggle() {
		this.toggleMobileRightSidebar.emit(true);
	}
	toggleSidebarRight() {
		this.toggleSidebarRightCollapsed.emit(true);
	}

	mobileTopMenuToggle() {
		this.pageSettings.pageMobileTopMenuToggled = !this.pageSettings.pageMobileTopMenuToggled;
	}

	mobileMegaMenuToggle() {
		this.pageSettings.pageMobileMegaMenuToggled = !this.pageSettings.pageMobileMegaMenuToggled;
	}

	ngOnDestroy() {
		this.pageSettings.pageMobileTopMenuToggled = false;
		this.pageSettings.pageMobileMegaMenuToggled = false;
	}

	constructor(private renderer: Renderer2,
			private storageService:StorageService,
			private alertService:AlertService,
			private commonApplicationService:CommonApplicationService,
			private communicationService: CommunicationService,
			private ngZone: NgZone,
			private router: Router,
			private http:HttpClient,
			private fb:FormBuilder,
			//private cookieService:CookieService
			) {
		

	}

	ngOnInit() {
		this.communicationService.getUser().subscribe(user => {
			this.first_name=user['first_name'];
			this.last_name=user['last_name'];
			this.storageService.set('user_info',user);
		});

		this.user_info=this.storageService.get('user_info');
		if(this.user_info==""){
			this.getUserInfo();
		}else{
			this.first_name=this.user_info['first_name'];
			this.last_name=this.user_info['last_name'];
			console.log(this.first_name);
		}
		      
		this.property_config =  this.storageService.get("property_config");
	    	if(this.property_config == null || this.property_config == false){
	      		this.getPropertyConfig();
		}
			

		this.searchProForm=this.fb.group({
			search_property: ["", Validators.required],
		});

		this.searchProForm.get('search_property').valueChanges.subscribe(value => { 
			if(value && value.length>=this.minChar){
				this._filter(value);
			}else{
				this.filteredOptions=[];
			}
		});
  	}

  	getPropertyConfig(){
    // Get config details
		let url = apiUrl.property_config;
		this.commonApplicationService.get(url).subscribe(response => {
			// Store property config
			console.log("Property Config>>>>"+response);
			this.storageService.setHard("property_config", response.row);
			console.log("Property Config<<<<"+this.storageService.get("property_config"));
		},
		error => {
			if (error.status === 401) {
				this.error_message=true;
				this.message=error.error['message'];
				this.alertService.error(error.error['message']);
			}
		});  
	}
	
	logout() {
		// Removes auth token kept in local storage (not strictly relevant to this demo)
		this.removeAuthToken();
		// Triggers the reboot in main.ts        
		this.ngZone.runOutsideAngular(() => BootController.getbootControl().restart());
		// Navigate back to login
		//this.router.navigate(['login']);
		//this.cookieService.delete();
		window.location.href=environment.base_url+'/login';  

	}

	removeAuthToken(){
		this.storageService.set("token","");
		this.storageService.removeHardKey('token_detail'); 

	}


	private _filter(value: any):any{
		this.loading=true;
		this.noResult=false;
		this.filteredOptions=[];
		this.commonApplicationService.get(apiUrl.propertyAutoComplete+'?query='+value+'&limit=10').subscribe(response=>{
			this.filteredOptions= response;
			this.loading=false;
			if(this.filteredOptions.length==0){
				this.noResult=true;
			}
	
		});
	}

	displayFn(selectedVal:any): string {
    		return selectedVal && selectedVal.address ? selectedVal.address : '';
  	}

	searchProperty(){
    
		let searchVal=this.searchProForm.get('search_property').value;
		if(searchVal.house_id){
			this.loading=false;
			this.isSearch=true;
			let house_id=searchVal.house_id?searchVal.house_id:'';
			searchVal=searchVal.address?searchVal.address:searchVal;
			this.router.navigate(['home/showdetail/'+house_id+'/'+searchVal.split(' ').join('-')]);
		}
    
  	}

	getUserInfo(){
		let url =apiUrl.user_detail;
		this.commonApplicationService.get(url)
		.subscribe(
			data =>{
				this.storageService.set('user_info',data['row']);
				this.first_name=data['row']['first_name'];
				this.last_name=data['row']['last_name'];
			},
			error => {
				this.alertService.common(error);
			}
		);
	}

	get inviteSettingAccess(){
		let inviteAccess=['admin','home_buyer','wholesale_buyer'];
		if(inviteAccess.indexOf(this.user_info.current_role) !== -1){
			return true;
		}
		return false;
	}
}
