<nav class="navbar fixed_top navbar-expand-md navbar-light shadow">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}"> <span class="block"><img
                    src="{{ asset('storage/images/logo.png') }}" height="28"
                    alt="TeamWorx LT"></span> <span class="small_nav_text">We worx like
                a team <i class="fa-solid fa-people-group"></i>
		</span>

        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse"
                data-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>


        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav mr-auto">
                <li class="nav-item"><a class="nav-link" href="{{route('welcome')}}#about_us">{{__('nav.about_us')}}</a>
                </li>
                <li class="nav-item"><a class="nav-link" href="{{route('welcome')}}#contacts">{{__('nav.contacts')}}</a>
                </li>
            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ml-auto">

                <li class="nav-item dropdown">@foreach($languages as $language)
                        @if($language->tag == app()->getLocale())
                            <a
                                class="nav-link dropdown-toggle" href="#" id="dropdown09"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img
                                    src="{{ asset('storage/images/flags/'. $language->tag .'.png') }}"
                                alt="{{$language->alt}}"> {{$language->language}}</a> @endif
                    @endforeach
                    <div class="dropdown-menu" aria-labelledby="dropdown09">
                        @foreach($languages as $language) <a class="dropdown-item"
                                                             href="{{route('set_language', ['language'=>$language->tag])}}"><img
                                src="{{ asset('storage/images/flags/'. $language->tag .'.png') }}"
                                alt="{{$language->alt}}"> {{$language->language}}</a> @endforeach
                    </div>
                </li>


                <!-- Authentication Links -->
                @guest @if (Route::has('login'))
                    <li class="nav-item"><a class="nav-link" href="#"
                                            data-toggle="modal" data-target="#sideNav">{{__('nav.login')}}
                        </a></li> @endif @if (Route::has('register'))
                    <li class="nav-item"><a class="nav-link"
                                            href="{{ route('register') }}">{{ __('nav.register') }}</a></li>
                @endif @else
                    <li class="nav-item">
                        <a class="nav-link" href="#" role="button" data-toggle="modal"
                           data-target="#sideNav"> {{ Auth::user()->name }} </a>
                    </li> @endguest
            </ul>
        </div>
    </div>
</nav>
