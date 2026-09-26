import { Component,OnInit,ViewChild,ComponentFactoryResolver, ViewContainerRef } from '@angular/core';
import { FormBuilder, FormGroup } from '@angular/forms';
import { Router, ActivatedRoute}    from '@angular/router';

import { SaleDateComponent, } from './sale-date/sale-date.component';
import { CommonApplicationService,AlertService,CommonActivityService } from '@shared-service/_services';
import { apiUrl } from '@config/api-url';
import { SaleModel } from './sale-date/sale.model';
import { StorageService } from '@shared-service/_services/storage.service';


@Component({
  selector: 'sale',
  templateUrl: './sale.html',
})

export class SaleComponent  implements OnInit{
  _ref:any; 
  sale_date_count:number=1;
  sale_date_data: any;
  nosImTrusteeList:any={'nos_by':'','im_by':'','trustee_caller':''};

  @ViewChild('salesParent', { read: ViewContainerRef }) container: ViewContainerRef;

  constructor(private formBuilder: FormBuilder,
        private router: Router,
        private route: ActivatedRoute,
        private _cfr: ComponentFactoryResolver,
        private commonApplicationService: CommonApplicationService,
        private commonActivityService: CommonActivityService,
        private alertService: AlertService,
        private storageService:StorageService) { }

        propertyForm: FormGroup;
        submitted = false;
        property_id: string;
        propErrMsg: any = "";
        saleResult: any; 
        saleDataLength: number =0;
        openPanel:boolean = false;
        loading:boolean=false;

        ngOnInit() {
         this.property_id = this.route.snapshot.paramMap.get('property_id');
          // Load info details
          if(this.property_id !== undefined){
            if(this.openPanel){
                this.getSaleDetails(); 
            }   
          }
        }

        panelExpand(flag){
            if(!this.openPanel){
              this.getSaleDetails();
              this.getNosImTrusteeList();
              this.openPanel = true;
            }
          }

        initialize(){         
          if(this.saleDataLength){            
            for(var i=0; i<this.saleDataLength; i++){             
              var comp = this._cfr.resolveComponentFactory(SaleDateComponent);
              var saleDateComponent = this.container.createComponent(comp);
              saleDateComponent.instance._ref = saleDateComponent;
              saleDateComponent.instance.sale_date_count  =  this.sale_date_count+i;
              saleDateComponent.instance.saleData  =  this.saleResult[i];       
              saleDateComponent.instance.nosImTrusteeList  =  this.nosImTrusteeList;     
            }
            this.sale_date_count = this.saleDataLength;
          }else{
              var comp = this._cfr.resolveComponentFactory(SaleDateComponent);
              var saleDateComponent = this.container.createComponent(comp);
              saleDateComponent.instance._ref = saleDateComponent;
              saleDateComponent.instance.sale_date_count  =  this.sale_date_count;
              saleDateComponent.instance.saleData = new SaleModel();
              saleDateComponent.instance.nosImTrusteeList  =  this.nosImTrusteeList;     

          }
           //saleDateComponent.instance._ref.destroy();
        }
        

//-------------------- GET SALE DATE ---------------------------
        getSaleDetails(){
            this.loading = true;
              let url = apiUrl.sale_detail+'/'+this.property_id;
              this.commonApplicationService.get(url).subscribe(response => {
                this.loading = false;
                this.saleResult = response['data']['sale'];
                if(this.saleResult !== undefined){
                        this.saleDataLength = this.saleResult.length;
                        console.log("GETING SALE DATA .....");
                }
                this.initialize();
              },
                (err: any) => {
                  this.propErrMsg = "Error occured, Please try again later!";
                  this.loading = false;
                })
            }
//-------------------- GET SALE DATE ---------------------------

          // Property Validation
          validateForm() { 
            this.submitted = true;
            if (this.propertyForm.invalid) { 
              this.propertyForm.get('propAddress').markAsTouched();      
              return;
            }
            // do something else
        }

        get f() { return this.propertyForm.controls; }

        removeObject(){
          this._ref.destroy();
        }   
        save(){
          alert('Saved Successfully!');
        }

        more_sale_date(){    
          ++this.sale_date_count;
          var comp = this._cfr.resolveComponentFactory(SaleDateComponent);
          var saleDateComponent = this.container.createComponent(comp);
          saleDateComponent.instance._ref = saleDateComponent;
          saleDateComponent.instance.sale_date_count=this.sale_date_count;
          saleDateComponent.instance.saleData = new SaleModel();
          saleDateComponent.instance.nosImTrusteeList  =  this.nosImTrusteeList;     
          //saleDateComponent.instance.sale_date_count=count+1;
          //this.count
          
        }

        getNosImTrusteeList(){
          if(this.storageService.get('user_info').current_role=='admin'){
              this.getUserList('nos_by');
              this.getUserList('im_by');
              this.getUserList('trustee_caller');
              this.getUserList('auction_by');
          }
        }

        getUserList(role){
          let url = apiUrl.user_list+'?role='+role;
          this.commonApplicationService.get(url).subscribe(response => {
              this.nosImTrusteeList[role]=response;
          });
      
        }
}
