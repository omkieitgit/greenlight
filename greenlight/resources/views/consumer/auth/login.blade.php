
    @include('consumer.includes.header')
    

        <section class="ftco-section contact-section">
      
                <div class="row block-9 justify-content-center mb-5">
                        <div class="col-md-6 mb-md-5">
                                <h2 class="text-center">Login</h2>
                                @if ($errors->any())
                                        <div class="alert alert-danger">
                                                <ul>
                                                @foreach ($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                @endforeach
                                                </ul>
                                        </div>
                                @endif
                                <form  method="POST" action="{{ route('auth.login') }}" class="bg-light p-5 contact-form">
                                        @csrf
                                        <div class="form-group">
                                                <input type="text" class="form-control" placeholder="Email Id" name="email">
                                        </div>
                                        <div class="form-group position-relative">
                                                <input type="password" class="form-control" placeholder="Password" name="password">
                                                <i class="icon-eye-slash  color-danger show_password"></i>
                                        </div>
                                        <div class="form-group">
                                                <input type="submit" value="Login" class="btn btn-primary py-3 px-5">
                                        </div>
                                </form>
                        
                        </div>
                </div>
        </section>
@include('consumer.includes.footer')

<script>
        $(document).ready(function(){
                $('.show_password').click(function(){
                        $(this).parent('div').find('input').attr('type', function(index, attr){
                                return attr == 'text'? 'password' : 'text';
                        });
                        if($(this).parent('div').find('input').attr("type") == "text"){
                                $(this).addClass("icon-eye").removeClass('icon-eye-slash');
                        }else{
                                $(this).addClass("icon-eye-slash").removeClass('icon-eye');
                        }
                })
        })

</script>