
    @include('consumer.includes.header')
    <section class="ftco-section contact-section">
            <div class="row block-9 justify-content-center mb-5">
                <div class="col-md-6 mb-md-5">
                <div class="card">
                        <div class="card-body">
                        <h2 class="text-center">Case Input</h2>
                                        @if ($errors->any())
                                                <div class="alert alert-danger">
                                                        <ul>
                                                        @foreach ($errors->all() as $error)
                                                                <li>{{ $error }}</li>
                                                        @endforeach
                                                        </ul>
                                                </div>
                                        @endif
                                        <form  method="POST" action="{{ route('case-input') }}" class="bg-light p-5 contact-form">
                                        @csrf
                                                <div class="mb-3">
                                                        <label class="form-label">Property Address*</label>
                                                        <input class="form-control form-control-lg" type="text" name="address" placeholder="Enter Property address" required/>
                                                </div>
                                                <div class="mb-3">
                                                        <label class="form-label">City</label>
                                                        <input class="form-control form-control-lg" type="text" name="city" placeholder="Enter City" required/>
                                                </div>
                                                <div class="mb-3">
                                                        <label class="form-label">State</label>
                                                        <input class="form-control form-control-lg" type="text" name="state" placeholder="Enter state" required />
                                                </div>
                                                <div class="mb-3">
                                                        <label class="form-label">County</label>
                                                        <input class="form-control form-control-lg" type="text" name="state" placeholder="Enter County" />
                                                </div>
                                                <div class="mb-3">
                                                        <label class="form-label">Zip Code</label>
                                                        <input class="form-control form-control-lg" type="text" name="zip" placeholder="Enter Zip Code" />
                                                </div>
                                                <div class="mb-3">
                                                        <label class="form-label">Parcel ID 1</label>
                                                        <input class="form-control form-control-lg" type="text" name="parcel_id1" placeholder="Enter Parcel ID 1" />
                                                </div>
                                              
                                                <div class="text-center mt-3">
                                                        <button type="submit" class="btn btn-lg btn-primary">Save</button>
                                                </div>
                                        </form>
                        </div>
                </div>
                            
                           
                    </div>
            </div>
    </section>
@include('consumer.includes.footer')


