
    @include('consumer.includes.header')
    

        <section class="ftco-section contact-section">
      
                <div class="row block-9 justify-content-center mb-5">
                        <div class="col-md-6 mb-md-5">
                                <h2 class="text-center">Register</h2>
                                @if(Session::has('success'))
                                        <div class="alert alert-success">
                                                {{Session::get('success')}}
                                        </div>
                                @endif
                                
                                @if ($errors->any())
                                        <div class="alert alert-danger">
                                                <ul>
                                                @foreach ($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                @endforeach
                                                </ul>
                                        </div>
                                @endif
                                <form  method="POST" action="{{ route('register') }}" class="bg-light p-5 contact-form">
                                        @csrf
                                        <div class="form-container  mt-3" id="form_div">
                                                <div class="mb-3 text-start">
                                                <label for="name" class="form-label">First Name*</label>
                                                <input id="register_name" type="text" class="form-control  inputbg" name="first_name" value="" required="" autofocus="">
                                        </div>
                                        <div class="form-container  mt-3" id="form_div">
                                                <div class="mb-3 text-start">
                                                <label for="name" class="form-label">Last Name</label>
                                                <input id="register_name" type="text" class="form-control  inputbg" name="last_name">
                                        </div>
                                        <div class="form-container  mt-3" id="form_div">
                                                <div class="mb-3 text-start">
                                                <label for="name" class="form-label">Username</label>
                                                <input id="username" type="text" class="form-control  inputbg" name="username">
                                        </div>
                                        <div class="mb-3 text-start">
                                                <label for="email" class="form-label">Email address*</label>
                                                <input id="register_email" type="email" class="form-control inputbg" name="email" value="" required="">
                                        </div>

                                        <div class="mb-3 text-start Validate_Number_container position-relative">
                                                <label for="phone" class="form-label">Phone Number*</label>
                                                <input id="register_mobile" type="text" placeholder="+91" class="form-control inputbg" name="mobile" value="" required="" autofocus="">
                                        </div>

                                        <div class="mb-3 text-start position-relative">
                                                <label for="password" class="form-label">Password*</label>
                                                <input id="register_password" type="password" class="form-control inputbg" name="password" required="">

                                        </div>
                                        <div class="mb-3 text-start position-relative">
                                                <label for="password" class="form-label">Confirm Password*</label>
                                                <input id="register_password_confirm" type="password" class="form-control inputbg" name="password_confirmation" required="">
                                        </div>

                                        <div class="form-container  mt-3" id="form_div">
                                                <div class="mb-3 text-start">
                                                <label for="name" class="form-label">Address</label>
                                                <input id="register_name" type="text" class="form-control  inputbg" name="address">
                                        </div>
                                        <div class="form-container  mt-3" id="form_div">
                                                <div class="mb-3 text-start">
                                                <label for="name" class="form-label">City</label>
                                                <input id="city" type="text" class="form-control  inputbg" name="city">
                                        </div>
                                        <div class="form-container  mt-3" id="form_div">
                                                <div class="mb-3 text-start">
                                                <label for="name" class="form-label">State</label>
                                                <select name="state" class="form-control  inputbg">
                                                        @foreach($data['states'] as $key=>$value)
                                                                <option value="{{$key}}">{{$value}}</option>
                                                        @endforeach
                                                </select>
                                        </div>
                                        <div class="form-container  mt-3" id="form_div">
                                                <div class="mb-3 text-start">
                                                <label for="name" class="form-label">Role</label>
                                                <select name="role" class="form-control  inputbg">
                                                        @foreach($data['roles_es'] as $key=>$value)
                                                                <option value="{{$key}}">{{$value}}</option>
                                                        @endforeach
                                                </select>
                                        </div>
                                        
                                        <div class="mb-3 mt-4 text-center">
                                                <button type="submit"  class="btn btn-primary py-3 px-5">
                                                        Register
                                                </button>
                                        </div>
                                </form>
                        </div>
                </div>
        </section>
@include('consumer.includes.footer')


