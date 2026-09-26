<div class="collapse navbar-collapse" id="ftco-nav">
        <ul class="navbar-nav ml-auto">
                <li class="nav-item active"><a href="/" class="nav-link">Home</a></li>
                <li class="nav-item"><a href="{{ url('about') }}" class="nav-link">About</a></li>
                <li class="nav-item"><a href="{{ url('services') }}" class="nav-link">Services</a></li>
                <!-- <li class="nav-item"><a href="{{ url('properties') }}" class="nav-link">Properties</a></li>
                <li class="nav-item"><a href="{{ url('blog') }}" class="nav-link">Blog</a></li> -->
                <li class="nav-item"><a href="{{ url('contact') }}" class="nav-link">Contact</a></li>

                @if(Auth::check())
                        <li class="nav-item"><a href="{{ url('setup') }}" class="nav-link">Setup</a></li>
                @else
                        <li class="nav-item"><a href="{{ url('login') }}" class="nav-link">Login</a></li>
                        <li class="nav-item"><a href="{{ url('register') }}" class="nav-link">Register</a></li>
                @endif
        </ul>
</div>