@include('setup.includes.header')
<main class="content gl-content">
        <div class="container-fluid p-0">
                <h1 class="h3 mb-3"><strong>Property Detail</strong></h1>
                <div class="row">
                        <div class="col-md-3 col-xl-3">
                                <div class="card mb-3">
                                        <div class="card-header">
                                                <h5 class="card-title mb-0">Profile Details</h5>
                                        </div>
                                        <!-- <div class="card-body">
                                                <ul class="list-unstyled mb-0">
                                                        <li class="mb-1"><span data-feather="home" class="feather-sm me-1"></span> Lives in <a href="#">San </a></li>
                                                        <li class="mb-1"><span data-feather="briefcase" class="feather-sm me-1"></span> Works at <a href="#">GitHub</a></li>
                                                        <li class="mb-1"><span data-feather="map-pin" class="feather-sm me-1"></span> From <a href="#">Boston</a></li>
                                                </ul>
                                        </div> -->
                                        
                                </div>
                        </div>

                        <div class="col-md-9 col-xl-9">
                                <div class="card">
                                        <div class="card-header">
                                                @if ($errors->any())
                                                        <div class="alert alert-danger">
                                                                <ul>
                                                                @foreach ($errors->all() as $error)
                                                                        <li>{{ $error }}</li>
                                                                @endforeach
                                                                </ul>
                                                        </div>
                                                @endif
                                        </div>
                                        
                                        <div class="card-body">
                                                <form  method="POST" id="case_input_form" class="row g-3">
                                                        @csrf
                                                        <div class="form-group row mb-1">
                                                                <div class="col-md-3">
                                                                        <label for="validationCustom01" class="col-form-label">Property Address*</label>
                                                                        <input type="text" class="form-control" id="validationCustom01" name="address" >
                                                                </div>
                                                                <div class="col-md-3">
                                                                        <label for="validationCustom02" class="col-form-label">City</label>
                                                                        <input type="text" class="form-control" id="validationCustom02" required="" name="city">
                                                                </div>
                                                                <div class="col-md-3">
                                                                        <label for="validationCustom04" class="col-form-label">State</label>
                                                                        <select class="form-select" id="validationCustom04" required=""  name="state">
                                                                                <option selected="" disabled="" value="">Choose...</option>
                                                                                @foreach($states as $key=>$value)
                                                                                        <option value="{{ $key }}">{{ $value }}</option>
                                                                                @endforeach
                                                                        </select>
                                                                        <!-- <div class="invalid-feedback">
                                                                                Please select a valid state.
                                                                        </div> -->
                                                                </div>

                                                                <div class="col-md-3">
                                                                        <label for="validationCustom03" class="col-form-label">County</label>
                                                                        <input type="text" class="form-control" id="validationCustom03" required="" name="county">
                                                                        <!-- <div class="invalid-feedback">
                                                                                Please provide a valid city.
                                                                        </div> -->
                                                                </div>
                                                        </div>
                                                        <div class="form-group row mb-1">
                                                                <div class="col-md-3">
                                                                        <label for="validationCustom05" class="col-form-label">Zip Code</label>
                                                                        <input type="text" class="form-control" id="validationCustom05" required=""  name="zip">
                                                                </div>
                                                                <div class="col-md-3">
                                                                        <label for="validationCustom05" class="col-form-label">Parcel ID 1</label>
                                                                        <input type="text" class="form-control" id="validationCustom05" required="" name="parcel_id1">
                                                                </div>
                                                                <div class="col-md-3">
                                                                        <label class="col-form-label">Lot Acres/Sqft <span class="pull-right" ></span> </label>
                                                                        <input type="text" formControlName="lot_acreage_sf" [ngClass]="{ 'is-invalid': submitted && f.lot_acreage_sf.errors }" class="form-control m-b-0" appOnBlurSave (updatedValue)="autoSave($event)"/>
                                                                        <div *ngIf="f.lot_acreage_sf.errors" class="invalid-feedback">
                                                                                <div *ngIf="f.lot_acreage_sf.errors.required">Lot / Acreage is required</div>
                                                                        </div>
                                                                
                                                                </div>  
                                                                <div class="col-md-3" *ngIf="fieldHide">
                                                                        <label class="col-form-label">Total SQFT</label>
                                                                        <input type="text" formControlName="total_sqft" [ngClass]="{ 'is-invalid': submitted && f.total_sqft.errors }" class="form-control m-b-0" appOnBlurSave (updatedValue)="autoSave($event)" />            
                                                                        <div *ngIf="f.total_sqft.errors" class="invalid-feedback">
                                                                                <div *ngIf="f.total_sqft.errors.required">Total SQFT is required</div>
                                                                        </div>
                                                                </div> 
                                                        </div>
                                                        <div class="form-group row mb-1">
                                                                <div class="col-md-3">
                                                                        <label class="col-form-label">Cost per SQFT</label>
                                                                        <input type="text" formControlName="cost_per_sqft" [ngClass]="{ 'is-invalid': submitted && f.cost_per_sqft.errors }"  class="form-control m-b-0" appOnBlurSave (updatedValue)="autoSave($event)" />
                                                                        <div *ngIf="f.cost_per_sqft_pr.errors" class="invalid-feedback">
                                                                                <div *ngIf="f.cost_per_sqft_pr.errors.required">Cost per Sqft is required</div>
                                                                        </div>
                                                                </div>
                                                                <div class="col-md-3" *ngIf="fieldHide">
                                                                        <label class="col-form-label">Total Living SQFT</label>
                                                                        <input type="text" formControlName="total_living_sqft" [ngClass]="{ 'is-invalid': submitted && f.total_living_sqft.errors }" class="form-control m-b-0"   appOnBlurSave (updatedValue)="autoSave($event)"       />            
                                                                        <div *ngIf="f.total_living_sqft.errors" class="invalid-feedback">
                                                                                <div *ngIf="f.total_living_sqft.errors.required">Total Living SQFT is required</div>
                                                                        </div>
                                                                </div>

                                                                <div class="col-md-3" *ngIf="fieldHide">
                                                                        <label class="col-form-label">Main Floor</label>
                                                                        <input type="text" formControlName="main_floor_area" [ngClass]="{ 'is-invalid': submitted && f.main_floor_area.errors }" class="form-control m-b-0" appOnBlurSave (updatedValue)="autoSave($event)" />            
                                                                        <div *ngIf="f.main_floor_area.errors" class="invalid-feedback">
                                                                                <div *ngIf="f.main_floor_area.errors.required">Main Floor is required</div>
                                                                        </div>
                                                                </div>

                                                                <div class="col-md-3" *ngIf="fieldHide">
                                                                        <label class="col-form-label">Second Floor</label>
                                                                        <input type="text" formControlName="second_floor_area" [ngClass]="{ 'is-invalid': submitted && f.second_floor_area.errors }" class="form-control m-b-0" appOnBlurSave (updatedValue)="autoSave($event)" />            
                                                                        <div *ngIf="f.second_floor_area.errors" class="invalid-feedback">
                                                                                <div *ngIf="f.second_floor_area.errors.required">Second Floor is required</div>
                                                                        </div>
                                                                </div>
                                                        </div>
                                                        
                                                        
                                                        

                                                        <div class="qform-group row m-b-1" *ngIf="fieldHide">

                                                        <div class="col-md-3" *ngIf="fieldHide">
                                                                <label class="col-form-label">Third Floor</label>
                                                                <input type="text" formControlName="third_floor_area" [ngClass]="{ 'is-invalid': submitted && f.third_floor_area.errors }" class="form-control m-b-0" appOnBlurSave (updatedValue)="autoSave($event)"/>            
                                                                <div *ngIf="f.third_floor_area.errors" class="invalid-feedback">
                                                                        <div *ngIf="f.third_floor_area.errors.required">Third Floor is required</div>
                                                                </div>     
                                                        </div>  
                                                        <div class="col-md-3">
                                                                <label class="col-form-label">Year Built</label>
                                                                <input type="text" formControlName="year_built" [ngClass]="{ 'is-invalid': submitted && f.year_built.errors }" class="form-control m-b-0" appOnBlurSave (updatedValue)="autoSave($event)"/>            
                                                                <div *ngIf="f.year_built.errors" class="invalid-feedback">
                                                                <div *ngIf="f.year_built.errors.required">Year Built is required</div>
                                                                </div>           
                                                                </div>
                                                                <div class="col-md-3">
                                                                <label class="col-form-label">Bed</label>
                                                                <select formControlName="bed" [ngClass]="{ 'is-invalid': submitted && f.bed.errors }" class="form-control m-b-0" appOnBlurSave (updatedValue)="autoSave($event)" >
                                                                        <option value="">Select Bed</option>
                                                                        @for($i=0; $i<=5; $i++)
                                                                                <option value="{{ $i }}">{{ $i }}</option>
                                                                        @endfor
                                                                </select>         
                                                                <div *ngIf="f.bed.errors" class="invalid-feedback">
                                                                <div *ngIf="f.bed.errors.required">Bed is required</div>
                                                                </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                <label class="col-form-label">Bath</label>
                                                                <select formControlName="bath" [ngClass]="{ 'is-invalid': submitted && f.bath.errors }" class="form-control m-b-0" appOnBlurSave (updatedValue)="autoSave($event)">
                                                                <option value="">Select Bath</option>
                                                                        @foreach($counter_float_5 as $floatCount)
                                                                                <option value="{{ $floatCount }}">{{ $floatCount }}</option>
                                                                        @endforeach
                                                                </select>           
                                                                <div *ngIf="f.bath.errors" class="invalid-feedback">
                                                                <div *ngIf="f.bath.errors.required">Bath is required</div>
                                                                </div>
                                                                </div> 
                                                        
                                                        
                                                        </div>
                                                        <!-- one row end -->
                                                        <div class="form-group row m-b-1"  *ngIf="fieldHide">
                                                        <div class="col-md-3">
                                                                <label class="col-form-label">Full Bath</label>
                                                                <select formControlName="full_bath" [ngClass]="{ 'is-invalid': submitted && f.full_bath.errors }" class="form-control m-b-0" appOnBlurSave (updatedValue)="autoSave($event)">
                                                                        <option value="">Select Full Bath</option>
                                                                        @for($i=0; $i<=5; $i++)
                                                                                <option value="{{ $i }}">{{ $i }}</option>
                                                                        @endfor
                                                                </select>
                                                                <div *ngIf="f.full_bath.errors" class="invalid-feedback">
                                                                <div *ngIf="f.full_bath.errors.required">Full Bath is required</div>
                                                                </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                <label class="col-form-label">1/2 Bath</label>            
                                                                <select formControlName="half_bath" [ngClass]="{ 'is-invalid': submitted && f.half_bath.errors }" class="form-control m-b-0" appOnBlurSave (updatedValue)="autoSave($event)">
                                                                        <option value="">Select 1/2 Bath</option>
                                                                        @for($i=0; $i<=5; $i++)
                                                                                <option value="{{ $i }}">{{ $i }}</option>
                                                                        @endfor
                                                                </select>
                                                                <div *ngIf="f.half_bath.errors" class="invalid-feedback">
                                                                <div *ngIf="f.half_bath.errors.required">1/2 Bath is required</div>
                                                                </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                <label class="col-form-label">3/4 Bath</label>
                                                                <select  formControlName="three_quarter_bath" [ngClass]="{ 'is-invalid': submitted && f.three_quarter_bath.errors }" class="form-control m-b-0" appOnBlurSave (updatedValue)="autoSave($event)">
                                                                        <option value="">Select 3/4 Bath</option>
                                                                        @for($i=0; $i<=5; $i++)
                                                                                <option value="{{ $i }}">{{ $i }}</option>
                                                                        @endfor
                                                                </select>
                                                                <div *ngIf="f.three_quarter_bath.errors" class="invalid-feedback">
                                                                        <div *ngIf="f.three_quarter_bath.errors.required">3/4 Bath is required</div>
                                                                </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                <label class="col-form-label">Basement</label>
                                                                <input type="text" formControlName="basement_area" [ngClass]="{ 'is-invalid': submitted && f.basement_area.errors }" class="form-control m-b-0" appOnBlurSave (updatedValue)="autoSave($event)"/>            
                                                                <div *ngIf="f.basement_area.errors" class="invalid-feedback">
                                                                <div *ngIf="f.basement_area.errors.required">Basement is required</div>
                                                                </div>
                                                                </div>
                                                        </div>

                                                        <!-- one row start-->
                                                        <div class="form-group row m-b-1"  *ngIf="fieldHide">
                                                        
                                                                <div class="col-md-3">
                                                                <label class="col-form-label">Finished Basement</label>
                                                                <input type="text" formControlName="finished_basement_area" [ngClass]="{ 'is-invalid': submitted && f.finished_basement_area.errors }" class="form-control m-b-0" appOnBlurSave (updatedValue)="autoSave($event)" />            
                                                                <div *ngIf="f.finished_basement_area.errors" class="invalid-feedback">
                                                                <div *ngIf="f.finished_basement_area.errors.required">Finished Basement is required</div>
                                                                </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                <label class="col-form-label">Finished Attic</label>
                                                                <input type="text" formControlName="finished_attic" [ngClass]="{ 'is-invalid': submitted && f.finished_attic.errors }" class="form-control m-b-0" appOnBlurSave (updatedValue)="autoSave($event)"/>            
                                                                <div *ngIf="f.finished_attic.errors" class="invalid-feedback">
                                                                <div *ngIf="f.finished_attic.errors.required">Finished Attic is required</div>
                                                                </div> 
                                                                </div>
                                                                <div class="col-md-3">
                                                                <label class="col-form-label">Garages</label>
                                                                <select formControlName="garages" [ngClass]="{ 'is-invalid': submitted && f.stories.errors }" class="form-control m-b-0" appOnBlurSave (updatedValue)="autoSave($event)">
                                                                        <option value="">Select Garages</option> 
                                                                        @for($i=0; $i<=5; $i++)
                                                                                <option value="{{ $i }}">{{ $i }}</option>
                                                                        @endfor
                                                                </select>
                                                                <div *ngIf="f.garages.errors" class="invalid-feedback">
                                                                <div *ngIf="f.garages.errors.required">Garages is required</div>
                                                                </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                <label class="col-form-label">Garage Type</label>
                                                                <select formControlName="garage_types" [ngClass]="{ 'is-invalid': submitted && f.garage_types.errors }" class="form-control m-b-0" appOnBlurSave (updatedValue)="autoSave($event)">
                                                                        <option value="">Select Garages Type</option>
                                                                        @foreach($property_info['garage_types'] as $key=>$garage)
                                                                                <option value="{{ $key }}">{{ $garage }}</option>
                                                                        @endforeach
                                                                </select>
                                                                <div *ngIf="f.garage_types.errors" class="invalid-feedback">
                                                                <div *ngIf="f.garage_types.errors.required">Garage Type is required</div>
                                                                </div>
                                                                </div>
                                                                
                                                        </div>
                                                        <!-- one row end -->        

                                                        <!-- one row start-->
                                                        <div class="form-group row m-b-1"  *ngIf="fieldHide">
                                                        <div class="col-md-3">
                                                                <label class="col-form-label">Garage Sqft</label>
                                                                <input type="text" formControlName="garage_sf" [ngClass]="{ 'is-invalid': submitted && f.garage_sf.errors }" class="form-control m-b-0"  appOnBlurSave (updatedValue)="autoSave($event)"/>
                                                                <div *ngIf="f.garage_sf.errors" class="invalid-feedback">
                                                                <div *ngIf="f.garage_sf.errors.required">Garage Sqft is required</div>
                                                                </div>
                                                                </div>
                                                        <div class="col-md-3">
                                                                <label class="col-form-label">Enclosed Porch</label>
                                                                <input type="text" formControlName="enclosed_porch" [ngClass]="{ 'is-invalid': submitted && f.enclosed_porch.errors }" class="form-control m-b-0" appOnBlurSave (updatedValue)="autoSave($event)"/>            
                                                                <div *ngIf="f.enclosed_porch.errors" class="invalid-feedback">
                                                                <div *ngIf="f.enclosed_porch.errors.required">Enclosed Porch is required</div>
                                                                </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                        <label class="col-form-label">Bonus Room</label>
                                                        <input type="text" formControlName="bonus_room" [ngClass]="{ 'is-invalid': submitted && f.bonus_room.errors }" class="form-control m-b-0" appOnBlurSave (updatedValue)="autoSave($event)"/>            
                                                        <div *ngIf="f.bonus_room.errors" class="invalid-feedback">
                                                                <div *ngIf="f.bonus_room.errors.required">Bonus Room is required</div>
                                                        </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                        <label class="col-form-label"># of Families</label>
                                                        <select  formControlName="of_families" [ngClass]="{ 'is-invalid': submitted && f.of_families.errors }" class="form-control m-b-0" appOnBlurSave (updatedValue)="autoSave($event)">
                                                                <option value="">Select # of Families</option>
                                                                {{-- <option [value]="count" *ngFor="let count of counter_5">{{count}}</option> --}}
                                                        </select>
                                                        <div *ngIf="f.of_families.errors" class="invalid-feedback">
                                                                <div *ngIf="f.of_families.errors.required"># of Families is required</div>
                                                        </div>        
                                                        </div>
                                                                
                                                        </div>
                                                        <!-- one row end -->
                                                        
                                                        <!-- <div class="alert alert-dark fade show m-b-0 semi-bold text-black">Legal Description</div>-->

                                                        <!-- one row start-->
                                                        

                                                        <div class="form-group row m-b-1 m-t-5">
                                                        <div class="col-md-3">
                                                        <label class="col-form-label">Property Description</label>
                                                        </div>
                                                        <div class="col-md-10">
                                                        
                                                        <textarea autosize [minRows]="4" formControlName="property_description" [ngClass]="{ 'is-invalid': submitted && f.property_description.errors }" class="form-control m-b-0" appOnBlurSave (updatedValue)="autoSave($event)"></textarea>
                                                        <div *ngIf="f.property_description.errors" class="invalid-feedback">
                                                                <div *ngIf="f.property_description.errors.required">Property Description is required</div>
                                                        </div>
                                                        </div>
                                                        </div>
                                                        <!-- one row end-->
                                                        <!-- one row start-->
                                                        <div class="form-group row m-b-1 m-t-5">
                                                        <div class="col-md-3">
                                                                <label class="col-form-label">Legal Description</label>
                                                        </div>
                                                        <div class="col-md-10">
                                                                <textarea autosize [minRows]="4" formControlName="legal_description" [ngClass]="{ 'is-invalid': submitted && f.legal_description.errors }" class="form-control m-b-0" appOnBlurSave (updatedValue)="autoSave($event)"></textarea>
                                                        <div *ngIf="f.legal_description.errors" class="invalid-feedback">
                                                                <div *ngIf="f.legal_description.errors.required">Legal Description is required</div>
                                                        </div>
                                                        </div>
                                                        </div>
                                                        <!-- one row end-->
                                                        <div class="form-group row m-b-1"  *ngIf="fieldHide">
                                                        <div class="col-md-3">
                                                        <label class="col-form-label"># of Kitchens</label>
                                                        <select  formControlName="of_kitchen" [ngClass]="{ 'is-invalid': submitted && f.of_kitchen.errors }" class="form-control m-b-0" appOnBlurSave (updatedValue)="autoSave($event)">
                                                                <option value="">Select # of Kitchens</option>
                                                                {{-- <option [value]="count" *ngFor="let count of counter_5">{{count}}</option> --}}
                                                        </select>
                                                        <div *ngIf="f.of_kitchen.errors" class="invalid-feedback">
                                                                <div *ngIf="f.of_kitchen.errors.required"># of Kitchens is required</div>
                                                        </div> 
                                                        </div>
                                                        <div class="col-md-3">
                                                        <label class="col-form-label">Fireplaces</label>
                                                        <select  formControlName="fireplaces" [ngClass]="{ 'is-invalid': submitted && f.fireplaces.errors }" class="form-control m-b-0" appOnBlurSave (updatedValue)="autoSave($event)">
                                                                <option value="">Select Fireplace</option>
                                                                {{--  <option [value]="fireplace.key" *ngFor="let fireplace of fire_place_option | keyvalue">{{fireplace.value}}</option> --}}
                                                        </select>          
                                                        <div *ngIf="f.fireplaces.errors" class="invalid-feedback">
                                                                <div *ngIf="f.fireplaces.errors.required">Fireplaces is required</div>
                                                        </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                        <label class="col-form-label">Subdivision</label>
                                                        <input type="text" formControlName="subdivision" [ngClass]="{ 'is-invalid': submitted && f.subdivision.errors }" class="form-control m-b-0" appOnBlurSave (updatedValue)="autoSave($event)"/>            
                                                        <div *ngIf="f.subdivision.errors" class="invalid-feedback">
                                                                <div *ngIf="f.subdivision.errors.required">Subdivision is required</div>
                                                        </div>
                                                        </div>    
                                                        <div class="col-md-3">
                                                        <label class="col-form-label">Ext Wall</label>
                                                        <select formControlName="ext_wall_type" [ngClass]="{ 'is-invalid': submitted && f.ext_wall_type.errors }" class="form-control m-b-0" appOnBlurSave (updatedValue)="autoSave($event)">
                                                                <option value="">Select Ext Wall</option>
                                                                {{-- <option [value]="ext_wall.key" *ngFor="let ext_wall of ext_walls | keyvalue">{{ext_wall.value}}</option> --}}
                                                        </select>
                                                        <div *ngIf="f.ext_wall_type.errors" class="invalid-feedback">
                                                                <div *ngIf="f.ext_wall_type.errors.required">Ext Wall is required</div>
                                                        </div>
                                                        </div>
                                                        </div>
                                                        <!-- one row start-->
                                                        <div class="form-group row m-b-1"  *ngIf="fieldHide">
                                                                
                                                        
                                                        <div class="col-md-3">
                                                        <label class="col-form-label">Roofing</label>
                                                        <select formControlName="roofing" [ngClass]="{ 'is-invalid': submitted && f.roofing.errors }" class="form-control m-b-0" appOnBlurSave (updatedValue)="autoSave($event)">
                                                                <option value="">Select Roofing</option>
                                                                {{-- <option [value]="roof.key" *ngFor="let roof of roofing | keyvalue">{{roof.value}}</option> --}}
                                                        </select> 
                                                        <div *ngIf="f.roofing.errors" class="invalid-feedback">
                                                                <div *ngIf="f.roofing.errors.required">Rooging is required</div>
                                                        </div>    
                                                        </div>
                                                        <div class="col-md-3">
                                                        <label class="col-form-label">AC</label>
                                                        <select formControlName="ac" [ngClass]="{ 'is-invalid': submitted && f.ac.errors }" class="form-control m-b-0" appOnBlurSave (updatedValue)="autoSave($event)">
                                                                <option value="">Select AC</option>
                                                                {{--  <option [value]="ac.key" *ngFor="let ac of ac_option | keyvalue">{{ac.value}}</option> --}}
                                                        </select> 
                                                        <div *ngIf="f.ac.errors" class="invalid-feedback">
                                                                <div *ngIf="f.ac.errors.required">AC is required</div>
                                                        </div> 
                                                        </div>
                                                        <div class="col-md-3">
                                                        <label class="col-form-label">Heating</label>
                                                        <select formControlName="heating" [ngClass]="{ 'is-invalid': submitted && f.heating.errors }" class="form-control m-b-0" appOnBlurSave (updatedValue)="autoSave($event)">
                                                                <option value="">Select Heating</option>              
                                                                {{-- <option [value]="heating.key" *ngFor="let heating of heating_option | keyvalue">{{heating.value}}</option> --}}
                                                        </select>
                                                        <div *ngIf="f.heating.errors" class="invalid-feedback">
                                                                <div *ngIf="f.heating.errors.required">Heating is required</div>
                                                        </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                        <label class="col-form-label">Pool/SPA</label>
                                                        <select formControlName="pool" [ngClass]="{ 'is-invalid': submitted && f.pool.errors }" class="form-control m-b-0" appOnBlurSave (updatedValue)="autoSave($event)">
                                                                <option value="">Select Pool/SPA</option>    
                                                                {{--  <option [value]="pool.key" *ngFor="let pool of poll_option | keyvalue">{{pool.value}}</option>    --}}        
                                                        </select>
                                                        <div *ngIf="f.pool.errors" class="invalid-feedback">
                                                                <div *ngIf="f.pool.errors.required">Pool/SPA is required</div>
                                                        </div>
                                                        </div>
                                                        
                                                        </div>
                                                        <!-- one row end -->

                                                        <!-- one row start-->
                                                        <div class="form-group row m-b-1">
                                                                <div class="col-md-3">
                                                                <label class="col-form-label">Stories</label>
                                                                <select formControlName="stories" [ngClass]="{ 'is-invalid': submitted && f.stories.errors }" class="form-control m-b-0" appOnBlurSave (updatedValue)="autoSave($event)">
                                                                <option value="">Select Stories</option> 
                                                                {{--  <option [value]="count" *ngFor="let count of counter_10">{{count}}</option> --}}
                                                                </select>
                                                                <div *ngIf="f.stories.errors" class="invalid-feedback">
                                                                <div *ngIf="f.stories.errors.required">Stories is required</div>
                                                                </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                        <label class="col-form-label">Property Type</label>
                                                                <select formControlName="property_type" (change)="changeSpecificProp($event.target.value)" [ngClass]="{ 'is-invalid': submitted && f.property_type.errors }" class="form-control m-b-0" appOnBlurSave (updatedValue)="autoSave($event)">
                                                                <option value="">Select Property Type</option> 
                                                                {{--  <option [value]="property.key" *ngFor="let property of property_type | keyvalue">{{property.value}}</option> --}}
                                                                </select>            
                                                                <div *ngIf="f.property_type.errors" class="invalid-feedback">
                                                                <div *ngIf="f.property_type.errors.required">Property Type is required</div>
                                                        </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                        <label class="col-form-label">Specific Property Type</label>
                                                        <select formControlName="specific_property_type" [ngClass]="{ 'is-invalid': submitted && f.specific_property_type.errors }" class="form-control m-b-0" (change)="checkPropertyType($event.target.value)" appOnBlurSave (updatedValue)="autoSave($event)">
                                                                <option value="">Select Specific Property Type</option> 
                                                                {{--  <option [value]="specific_property.key" *ngFor="let specific_property of specific_property_type | keyvalue">{{specific_property.value}}</option> --}}
                                                        </select>
                                                        <div *ngIf="f.specific_property_type.errors" class="invalid-feedback">
                                                                <div *ngIf="f.specific_property_type.errors.required">Specific Property Type is required</div>
                                                        </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                                <label class="col-form-label">Building Style</label>
                                                                <select formControlName="building_style"  [ngClass]="{ 'is-invalid': submitted && f.building_style.errors }" class="form-control m-b-0" appOnBlurSave (updatedValue)="autoSave($event)">
                                                                <option value="">Select Building Style</option>
                                                                {{-- <option [value]="building.key" *ngFor="let building of building_style | keyvalue">{{building.value}}</option> --}}
                                                                </select>
                                                                <div *ngIf="f.building_style.errors" class="invalid-feedback">
                                                                <div *ngIf="f.building_style.errors.required">Building Style is required</div>
                                                                </div>
                                                        </div>
                                                        
                                                        </div>
                                                        <!-- one row end -->
                                                        <div class="form-group row m-b-1">
                                                        <div class="col-md-3">
                                                                <label class="col-form-label">Parcel ID 1</label>
                                                                <input type="text" formControlName="parcel_id1" [ngClass]="{ 'is-invalid': submitted && f.parcel_id1.errors }" class="form-control m-b-0" appOnBlurSave (updatedValue)="autoSave($event)"/>        
                                                                <div *ngIf="f.parcel_id1.errors" class="invalid-feedback">
                                                                <div *ngIf="f.parcel_id1.errors.required">Parcel ID 1 is required</div>
                                                                </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                                <label class="col-form-label">County Value</label>
                                                                <input type="text" formControlName="county_value" [ngClass]="{ 'is-invalid': submitted && f.county_value.errors }" class="form-control m-b-0" appOnBlurSave (updatedValue)="autoSave($event)"/>
                                                                <div *ngIf="f.county_value.errors" class="invalid-feedback">
                                                                <div *ngIf="f.county_value.errors.required">County Value is required</div>
                                                                </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                                <label class="col-form-label">PRC URL</label>
                                                                <span [appLink]="infoData.prc_url"></span>
                                                                <input type="text" formControlName="prc_url" [ngClass]="{ 'is-invalid': submitted && f.prc_url.errors }" class="form-control m-b-0" appOnBlurSave (updatedValue)="autoSave($event)"/>
                                                                <div *ngIf="f.prc_url.errors" class="invalid-feedback">
                                                                <div *ngIf="f.prc_url.errors.required">PRC URL is required</div>
                                                                </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                        <label class="col-form-label">County Assessor URL</label>
                                                        <span [appLink]="infoData.country_assessor_url"></span>
                                                        <input type="text" formControlName="country_assessor_url" [ngClass]="{ 'is-invalid': submitted && f.country_assessor_url.errors }" class="form-control m-b-0" appOnBlurSave (updatedValue)="autoSave($event)"/>
                                                        <div *ngIf="f.country_assessor_url.errors" class="invalid-feedback">
                                                                <div *ngIf="f.country_assessor_url.errors.required">County Assessor URL is required</div>
                                                        </div>
                                                        </div>
                                                        </div>
                                                        <!-- one row start-->
                                                        <div class="form-group row m-b-1">
                                                        
                                                        <div class="col-md-3">
                                                        <label class="col-form-label">GIS URL </label>
                                                        <span [appLink]="infoData.gis_url"></span>
                                                        <input type="text" formControlName="gis_url" [ngClass]="{ 'is-invalid': submitted && f.gis_url.errors }" class="form-control m-b-0" appOnBlurSave (updatedValue)="autoSave($event)" />
                                                        <div *ngIf="f.gis_url.errors" class="invalid-feedback">
                                                                <div *ngIf="f.gis_url.errors.required">GIS URL is required</div>
                                                        </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                        <label class="col-form-label">Treasurer URL </label>
                                                        <span [appLink]="infoData.treasurer_url"></span>
                                                        <input type="text" formControlName="treasurer_url" [ngClass]="{ 'is-invalid': submitted && f.treasurer_url.errors }" class="form-control m-b-0" appOnBlurSave (updatedValue)="autoSave($event)"/>
                                                        <div *ngIf="f.treasurer_url.errors" class="invalid-feedback">
                                                                <div *ngIf="f.treasurer_url.errors.required">Treasurer_url URL is required</div>
                                                        </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                        <label class="col-form-label">Tax Bill URL</label>
                                                        <span [appLink]="infoData.tax_bill_url"></span>
                                                        <input type="text" formControlName="tax_bill_url" [ngClass]="{ 'is-invalid': submitted && f.tax_bill_url.errors }" class="form-control m-b-0" appOnBlurSave (updatedValue)="autoSave($event)"/>
                                                        <div *ngIf="f.tax_bill_url.errors" class="invalid-feedback">
                                                                <div *ngIf="f.tax_bill_url.errors.required">Tax Bill is required</div>
                                                        </div>
                                                        </div>
                                                        <div class="col-md-3 m-t-35">
                                                        <app-county-url *hideElement="true" [state]="f.state.value" [county]="f.county.value" (valueChange)='populateCounty($event)'></app-county-url>
                                                        </div>
                                                        <div class="col-12">
                                                                <button class="btn btn-primary" type="button" onclick="save_property()">Save</button>
                                                        </div>
                                                </form>
                                        </div>
                                </div>
                        </div>
                </div>

        </div>
</main>
@include('setup.includes.footer')