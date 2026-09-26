import { Component,Input,Injectable, OnDestroy, OnInit, Renderer2 ,ViewChild, ElementRef} from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { NgbDateAdapter, NgbDateStruct } from '@ng-bootstrap/ng-bootstrap';
import { FormControl, NgForm, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router, ActivatedRoute }    from '@angular/router';
import { PicModel } from './pic.model';
import { Lightbox } from 'ngx-lightbox';
import { EmbedVideoService } from 'ngx-embed-video';
import { CommonApplicationService,AlertService,
         CommonActivityService,CommunicationService,
         MessageService } from '@shared-service/_services';
         
import { apiUrl,map_api } from '@config/api-url';
import { StorageService } from '@shared-service/_services/storage.service';


//import pageSettings from '../../../config/page-settings';
//import { AlertService, UserService } from '../../shared/_services';
declare var H: any;

// for ngb datepicker adapter
@Injectable()
export class NgbDateNativeAdapter extends NgbDateAdapter<Date> {

  fromModel(date: Date): NgbDateStruct {
    return (date && date.getFullYear) ? {year: date.getFullYear(), month: date.getMonth() + 1, day: date.getDate()} : null;
  }

  toModel(date: NgbDateStruct): Date {
    return date ? new Date(date.year, date.month - 1, date.day) : null;
  }
}

@Component({
 selector: 'picvideo',
 templateUrl: './picvideo.html',
 providers: [{provide: NgbDateAdapter, useClass: NgbDateNativeAdapter}]
 })


/*@Component({
  selector: 'owner',
  templateUrl: './owner.html',
  providers: [{provide: NgbDateAdapter, useClass: NgbDateNativeAdapter}]
})*/

export class PicvideoComponent  implements OnInit{
  private picData : PicModel;
  private picDataOrg: any;
  private _albums: any = [];
  private _albumsImg: any = [];
  propertyPicForm: FormGroup;
  submitted = false;
  property_pic_category_list: string[];
  property_config: any= {};
  property_id: string;
  propErr: boolean = false;
  propErrMsg: any = "";
  loadingMessage: any;
  result: any;   
  uploadUrl: string;
  pic_list: any[];  
  iframe_video_html: any;
  p_video_url: string = null;
  p_map_url: string = null;
  p_image_url: string = null;
  pic_list_count: number =0;
  // ngbdatepicker
  model1: Date;
  model2: Date;
  loadMap:boolean=false;
  openPanel:boolean = false;
  expand:boolean=false;
  latLongForm: FormGroup;
  invalidFields:any;
  map_data:any;
  loading:boolean=false;
  loading_lat:boolean=false;
  
  geoResult:any;
  geoFlag:boolean=false;
  lat:any;
  lng:any;
  map_info:any;
  load_map:boolean=false;

  pictureIds=[];
  selectedText:string="SELECT ALL";
  selectedAll:boolean=false;

  constructor(private formBuilder: FormBuilder,
        private router: Router,
        private route: ActivatedRoute,
        private storageService:StorageService,
        private commonApplicationService: CommonApplicationService,
        private alertService: AlertService,
        private _lightbox: Lightbox,
        private commonActivityService: CommonActivityService,
        private communicationService: CommunicationService,
        private embedService: EmbedVideoService,
        private http:HttpClient,
        private messageService:MessageService
        ) {
            this.property_config =  this.storageService.get("property_config");
            this.property_pic_category_list = this.property_config.picture_type;
           
         }

 
  get today() {
    return new Date();
  }
  

  // ngbtimepicker
  time2;
  ctrl = new FormControl('', (control: FormControl) => {
    const value = control.value;

    if (!value) {
      return null;
    }

    if (value.hour < 12) {
      return {tooEarly: true};
    }
    if (value.hour > 13) {
      return {tooLate: true};
    }

    return null;
  });

  time = {hour: 13, minute: 30};
  meridian = true;
  toggleMeridian() {
      this.meridian = !this.meridian;
  }
  ngOnInit() {
    this.property_id = this.route.snapshot.paramMap.get('property_id');
    
      // Load info details
      if(this.property_id !== undefined){
        this.propErr = true;
        if(this.openPanel){
          this.getInfoDetails(); 
          this.getPropertyImages(); 
          this.getGeoInfo();

        }
      }

      this.messageService.getArrData().subscribe(result=>{
        this.map_data=result;
      });
    
      this.communicationService.getExpand().subscribe(collapse => {
          this.panelExpand(!collapse); 
        });

  }

  initialize(){

    this.propertyPicForm = this.formBuilder.group({
      image_url: [(this.picData.image_url != null)? this.picData.image_url: ""],
      google_map_url: [ (this.picData.google_map_url != null)?this.picData.google_map_url : "" ],
      video_url: [(this.picData.video_url != null)?this.picData.video_url: ""],
      picture_type: [ (this.picData.picture_type != null)?this.picData.picture_type: "" ],
      video_type:[(this.picData.video_type!= null)?this.picData.video_type: ""],
      image_file:[''],
      save:['Save'],
      upload:['Upload']
    });
       
    // If vidoe and img urls are set then pass them.
    if(this.picData && this.picData.video_url && this.picData.video_url != null){
      this.p_video_url = this.picData.video_url;
      this.iframe_video_html = this.embedService.embed(this.p_video_url,{attr: { width: '100%', height: 500 }});
    }

    if(this.picData.image_url != null){
      this.p_image_url = this.picData.image_url;    
    }
    
    //this.commonActivityService.isDisabled("PROPETY_MAP", this.propertyPicForm);
   
  }

  geoInitialize(){
    console.log(this.geoResult);
    this.latLongForm = this.formBuilder.group({
      latitude: [( this.geoResult != null)? this.geoResult.latitude: ""],
      longitude: [ ( this.geoResult!= null)?this.geoResult.longitude : "" ],
    });
  }

  panelExpand(flag){
    this.loadMap=flag;
    if(!this.openPanel){
        this.getInfoDetails();
        this.getPropertyImages();
        this.getGeoInfo();
        this.openPanel = true;
    }
  }

 // Get property Info Details 
  getInfoDetails(){
    this.loadingMessage = true;
    //this.commonApplicationService.get(url,data,sucess_message,error_message);
      let url = apiUrl.map_video+'/'+this.property_id;
      this.commonApplicationService.get(url).subscribe(response => {
        this.loadingMessage = false;
        this.result = response.data;
        if(this.result !== undefined){
                this.picData = this.result;
                this.picDataOrg = Object.assign({}, this.picData);
                this.propErr = false;
                this.loadingMessage = false;
                this.initialize();       
        }
      },
        (err: any) => {
          this.propErrMsg = "Error occured, Please try again later!";
          this.propErr = true;
          this.loadingMessage = false;
        })
  }

/******************************* SAVE MAP/PIC/VID URLs*********************/
 saveUrls(fileUploading, event):void{  
  if(this.property_id==null || this.property_id===undefined){
    this.alertService.error("Please save property detail.");  
    return;
  }
    console.log('My File upload',event,fileUploading);
    let uploadErr = 0;

    // Form validation
    if(this.propertyPicForm['controls']['image_url'].value == "" && this.propertyPicForm['controls']['google_map_url'].value == "" 
      && this.propertyPicForm['controls']['video_url'].value == ""){

      this.propertyPicForm.setControl('image_url', new FormControl('', Validators.required));
      this.propertyPicForm['controls']['image_url'].setErrors({'required': true});

      this.propertyPicForm.setControl('google_map_url', new FormControl('', Validators.required));
      this.propertyPicForm['controls']['google_map_url'].setErrors({'required': true});

      this.propertyPicForm.setControl('video_url', new FormControl('', Validators.required));
      this.propertyPicForm['controls']['video_url'].setErrors({'required': true});
      uploadErr = 1;
      console.log('FAIL');
    }else if(this.propertyPicForm['controls']['video_url'].value != "" && this.propertyPicForm['controls']['video_type'].value == ""){
      this.propertyPicForm.setControl('video_type', new FormControl('', Validators.required));
      this.propertyPicForm['controls']['video_type'].setErrors({'required': true});
      uploadErr = 1;
      console.log('FAIL-1');
    }else{
      this.propertyPicForm['controls']['image_url'].setErrors({'required': false});
      this.propertyPicForm['controls']['google_map_url'].setErrors({'required': false});
      this.propertyPicForm['controls']['video_url'].setErrors({'required': false});
      console.log('PASS');
    }

     if(uploadErr) return;
     this.loading = true;
    let postData = {
      "house_id" : this.property_id,
      "google_map_url": this.propertyPicForm['controls']['google_map_url'].value,
      "image_url":  this.propertyPicForm['controls']['image_url'].value,
      "video_url":  this.propertyPicForm['controls']['video_url'].value,
      "video_type": (this.propertyPicForm['controls']['video_url'].value != "" )? this.propertyPicForm['controls']['video_type'].value : ""
    };

    this.uploadUrl = apiUrl.map_video+'/'+this.property_id;
    this.commonApplicationService.put(this.uploadUrl, postData)
    .subscribe(res => {
              console.log(res);
              this.populateDetails();
              this.alertService.success(res.message); 
              this.communicationService.setGoogleMapUrlUpdate(postData.google_map_url);
              this.ngOnInit();
              this.loading=false;
            },
            error => {
              this.loading=false;
              this.alertService.common(error);
            }
      );
  }

  populateDetails(){

    if(this.propertyPicForm['controls']['video_url'].value !=""){
      this.iframe_video_html = this.embedService.embed(this.propertyPicForm['controls']['video_url'].value,{attr: { width: '100%', height: 300 }});
    }else{
      this.iframe_video_html='';
    }
    if(this.propertyPicForm['controls']['image_url'].value!=""){
      this.p_image_url = this.propertyPicForm['controls']['image_url'].value;
    }else{
      this.p_image_url='';
    } 
    
  }

  getPropertyImages(){
    //this.commonApplicationService.get(url,data,sucess_message,error_message);
      let url = apiUrl.picture+'/'+this.property_id;
      this.commonApplicationService.get(url).subscribe(response => {
        if(response !== undefined){             
              this.pic_list =  response.data;
              this.prepareImg();
              //this.showPropertyList = true;
        }
      },
        (err: any) => {
          this.propErrMsg = "Error occured, Please try again later!";
          this.propErr = true;
          this.loadingMessage = false;
        })
  }
/******************************* SAVE MAP/PIC/VID URLs*********************/


/******************** FILE UPLOAD FUNCTIONALITY *******************/
  uploadHandler(event){
     let elem = event.target;  //line 2 
    if(elem.files.length > 0){
      this.propertyPicForm['controls']['image_file'].setErrors({'required': false});
    }
  }

  fileUploader(fileUploading, event):void{  
    console.log('My File upload',event,fileUploading);
    let uploadErr = 0;
    if(!this.propertyPicForm['controls']['picture_type'].value){
      this.propertyPicForm.setControl('picture_type', new FormControl('', Validators.required));
      this.propertyPicForm['controls']['picture_type'].setErrors({'required': true});
      uploadErr = 1;
    }
    
    if(fileUploading.files.length == 0){
      this.propertyPicForm.setControl('image_file', new FormControl('', Validators.required));
      this.propertyPicForm['controls']['image_file'].setErrors({'required': true});
      uploadErr = 1;
    }

    if(uploadErr) return;
    var fileToUpload = fileUploading.files;
    for(var i=0; i<fileToUpload.length; i++){
      let input = new FormData();
      input.append("picture_type",  this.propertyPicForm['controls']['picture_type'].value);
      input.append("house_id", this.property_id);
      input.append("document", fileToUpload[i]);
      this.uploadUrl = apiUrl.picture;
      this.commonApplicationService.post(this.uploadUrl, input)
        .subscribe(res => {
                  //console.log(fileToUpload.webkitRelativePath());
                  this.pic_list.push({url: res.data.url});
                  this._albums.push({src: res.data.url});
                  this.alertService.success(res.message); 
                  this.ngOnInit();
                  //this.getUploadedDocuments();
                },
                error => {
                  this.alertService.common(error);  
                }
          );
      }
    }
    

/******************** FILE UPLOAD FUNCTIONALITY *******************/
  
  prepareImg(){
    this.pic_list_count = this.pic_list.length;
    for (let i = 0; i < this.pic_list.length; i++) {
        const src = this.pic_list[i].url;
        const caption = '';
        const thumb = this.pic_list[i].url;
        const album = {
           id: this.pic_list[i].id,
           src: src,
           caption: caption,
           thumb: thumb
        };
   
      this._albums.push(album);
    }
  }

  open(index: number): void {
    // open lightbox
    this._lightbox.open(this._albums, index);
  }

  openImageUrl(): void {
    // open lightbox
    const src = this.p_image_url;
        const caption = '';
        const thumb = this.p_image_url;
        const album = {
           src: src,
           caption: caption,
           thumb: thumb
    };
    this._albumsImg.push(album);   
    this._lightbox.open(this._albumsImg , 0);
  }
  
 
  close(): void {
    // close lightbox programmatically
    this._lightbox.close();
  }

  removeImage(picObj){
     
     if(confirm("Are you sure want to delete record ?")){
           const removeAlbum = {
               id: picObj.id,
               src: picObj.url,
               caption: "",
               thumb: picObj.url
        };    
        let url = apiUrl.picture+'/'+picObj.id;
      this.commonApplicationService.delete(url).subscribe(response => {
        if(response !== undefined){             
             this.alertService.success(response.message); 
              //var index =this._albums.indexOf(removeAlbum);
              var index =this.getIndexOfK(this.pic_list, removeAlbum.id);
              if (index !== -1) {
                 this._albums.splice(index, 1);
                 this.pic_list.splice(index, 1);
                 this.pic_list_count = this._albums.length;
              }
        }
      },
        (err: any) => {
          this.alertService.common(err);  
        })
     }
  }

  getIndexOfK(arr, k) {
  for (var i = 0; i < arr.length; i++) {
    if(arr[i]['id'] == k){
      return i;
    }
  }
  return -1;
  }
  

  get f() { return this.propertyPicForm.controls; }


  getGeoInfo(){
      let url = apiUrl.geo+'/'+this.property_id;
      this.commonApplicationService.get(url).subscribe(response =>{
        if(response !== undefined){
          this.geoFlag = true;
          var response=response['row'];
          if(response.latitude && response.longitude){
            this.geoResult = response;
            this.lat=this.geoResult.latitude;
            this.lng=this.geoResult.longitude;
            this.load_map=true;
          }
          else{
            this.getGeoCode(response.address);
          }
          this.geoInitialize();
        }
      },
        (err: any) => {
          this.propErrMsg = "Error occured, Please try again later!";
          this.geoFlag = true;
        })
  }

  // Property Validation
  validateForm(data: any) {     
    if(this.property_id==null || this.property_id===undefined){
      this.alertService.error("Please save property detail.");  
      return;
    }
    let result = this.commonActivityService.getFullFormData(data);
    this.invalidFields = this.commonActivityService.findInvalidControls(data);
    console.log("Form Invalid Filleds = ",this.invalidFields);
    this.submitted = true;
    console.log('niform');
    if(this.latLongForm.invalid) { 
      console.log('Form is invalid, Fill all fields.');
      //this.propertyForm.get('prop_address').markAsTouched();      
      return;
    }    
    // FORM SUBMITTED
    console.log('form submitted');
    result['address']=this.map_data.address;
    this.saveInfoDetails(result);  
  }

  // Save info detail
  saveInfoDetails(data: any){
    this.loading_lat = true;
    //console.log("savingdata>"+this.propertyForm);
    console.log("savingdata>"+data);
    
    let url = apiUrl.geo+"/"+this.property_id;
    this.commonApplicationService.post(url, data)
        .subscribe(
            data => {
              this.alertService.success(data.message); 
              this.ngOnInit();
              this.loading_lat = false;
              
            },
            error => {
              this.alertService.common(error);  
                this.loading_lat = false;
            }
        ); 
  }

  getGeoCode(address){
    var url=map_api.get_geocode+'?';
    var mapUrl=url+'q='+address+'&apiKey='+map_api.api_key;
    this.http.get(mapUrl).subscribe(response=> {
      if(response !== undefined){
        this.lat=response['Response'].View[0].Result[0].Location.DisplayPosition.Latitude;
        this.lng=response['Response'].View[0].Result[0].Location.DisplayPosition.Longitude;
        this.saveInfoDetails({'latitude':this.lat,'longitude':this.lng});
      }
    },
      (err: any) => {
        this.propErrMsg = "Error occured, Please try again later!";
        this.geoFlag = true;
      });
    
    // this.commonApplicationService.get(mapUrl).subscribe(response =>{
   
  }

  removeUrlImg(img){
    this.p_image_url="";
    this.propertyPicForm['controls']['image_url'].setValue('');
    let postData = {
      "house_id" : this.property_id,
      "image_url":  this.propertyPicForm['controls']['image_url'].value,
    };

    this.uploadUrl = apiUrl.map_video+'/'+this.property_id;
    this.commonApplicationService.put(this.uploadUrl, postData)
    .subscribe(res => {  },error => { this.alertService.common(error);});
  }

  selectAllPictures(){
    
    var searchPorpertyInfo=document.getElementsByClassName('_card_picture');
    if(!this.selectedAll){
      for (var i = 0; i < searchPorpertyInfo.length; i++) {
        if (searchPorpertyInfo[i].querySelector('input').type=='checkbox'){
              this.selectedAll=true;
             this.selectedText="UNSELECT ALL";
             searchPorpertyInfo[i].querySelector('input').checked= true;
             this.pictureIds.push(searchPorpertyInfo[i].querySelector('input').value);
         }
      }
    }else{
      for (var i = 0; i < searchPorpertyInfo.length; i++) {
        if (searchPorpertyInfo[i].querySelector('input').type=='checkbox'){
          this.selectedAll=false;
          searchPorpertyInfo[i].querySelector('input').checked= false;
          let id=searchPorpertyInfo[i].querySelector('input').value;
          const index: number = this.pictureIds.indexOf(id);
          if (index !== -1) {
              this.pictureIds.splice(index, 1);
          }  
          this.selectedText="SELECT ALL";  
        }
      }
    }
  }

  removeMultipImages(){
      let postData = {
        "house_id" : this.property_id,
        "pictureIds": this.pictureIds,
      };
      if(confirm("Are you sure want to delete record ?")){
      
        let url = apiUrl.removeAllPicture+'/'+this.property_id;
        this.commonApplicationService.post(url,postData).subscribe(response => {
          if(response !== undefined){             
                this.alertService.success(response.message); 
                //var index =this._albums.indexOf(removeAlbum);
                this.pictureIds.forEach(element => {
                  var index =this.getIndexOfK(this.pic_list,element);
                  if (index !== -1) {
                      //this._albums.splice(index, 1);
                      this.pic_list.splice(index, 1);
                      this.pic_list_count = this._albums.length;
                  }
                });
                if(this.pic_list.length==0){
                  this.pic_list_count=0;
                }
          }
        },
        (err: any) => {
          this.alertService.common(err);  
        })
      }
  }


}