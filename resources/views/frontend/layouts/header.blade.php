<nav class="navbar navbar-expand-lg navbar-dark osahan-nav">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="{{ route('index') }}" style="text-decoration: none;">
            <div
                style="
      width: 36px; 
      height: 36px; 
      background-color: #ff4d4f; 
      border-radius: 50%; 
      display: flex; 
      align-items: center; 
      justify-content: center; 
      margin-right: 10px; 
      border: 2px solid #ffffff;">
                <i class="fas fa-utensils text-white" style="font-size: 18px;"></i>
            </div>
            <span
                style="
      font-size: 20px; 
      font-weight: 700; 
      color: #ffffff; 
      letter-spacing: 1px;
      border: 1px solid rgba(255,255,255,0.2); 
      padding: 2px 8px; 
      border-radius: 4px;">
                CAMPUSEATS
            </span>
        </a>


        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown"
            aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="/">
                        <i class="icofont-ui-home"></i> Home
                        <span class="sr-only">(current)</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('list.restaurant') }}">
                        <i class="icofont-restaurant"></i> Restaurants
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('user.order.list') }}">
                        <i class="icofont-listing-box"></i> My Orders
                    </a>
                </li>


                @auth
                    @php
                        $id = Auth::user()->id;
                        $profileData = App\Models\User::find($id);
                    @endphp
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                            <img alt="Generic placeholder image"
                                src="{{ !empty($profileData->photo) ? url('upload/user_images/' . $profileData->photo) : url('upload/no_image.jpg') }}"
                                class="nav-osahan-pic rounded-pill"> My Account
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow-sm border-0">
                            <a class="dropdown-item" href="{{ route('dashboard') }}"><i class="icofont-food-cart"></i>
                                Dashboard</a>
                            <a class="dropdown-item" href="{{ route('user.logout') }}">
                                <i class="icofont-logout"></i> Logout
                            </a>


                        </div>
                    </li>
                @else
                    <li class="nav-item dropdown">
                        <a class="nav-link" href="{{ route('login') }}" role="button" aria-haspopup="true"
                            aria-expanded="false">
                            Login
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link" href="{{ route('register') }}" role="button" aria-haspopup="true"
                            aria-expanded="false">
                            Register
                        </a>
                    </li>
                @endauth



                @php
                    $total = 0;
                    $cart = session()->get('cart', []);
                    $groupedCart = [];

                    foreach ($cart as $item) {
                        $groupedCart[$item['client_id']][] = $item;
                    }

                    $clients = App\Models\Client::whereIn('id', array_keys($groupedCart))->get()->keyBy('id');

                @endphp

                <li class="nav-item dropdown dropdown-cart">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-shopping-basket"></i> Cart
                        <span class="badge badge-success">{{ count((array) session('cart')) }}</span>
                    </a>
                    <div class="dropdown-menu dropdown-cart-top p-0 dropdown-menu-right shadow-sm border-0">

                        @foreach ($groupedCart as $clientId => $items)
                            @if (isset($clients[$clientId]))
                                @php
                                    $client = $clients[$clientId];
                                @endphp
                                <div class="dropdown-cart-top-header p-4">
                                    <img class="img-fluid mr-3" alt="osahan"
                                        src="{{ asset('upload/client_images/' . $client->photo) }}">
                                    <h6 class="mb-0">{{ $client->name }}</h6>
                                    <p class="text-secondary mb-0">{{ $client->address }}</p>
                                </div>
                            @endif
                        @endforeach


                        <div class="dropdown-cart-top-body border-top p-4">
                            @php $total = 0 @endphp
                            @if (session('cart'))
                                @foreach (session('cart') as $id => $details)
                                    @php
                                        $total += $details['price'] * $details['quantity'];
                                    @endphp

                                    <p class="mb-2"><i
                                            class="icofont-ui-press text-danger food-item"></i>{{ $details['name'] }} x
                                        {{ $details['quantity'] }} <span
                                            class="float-right text-secondary">₹{{ $details['price'] * $details['quantity'] }}</span>
                                    </p>
                                @endforeach
                            @endif

                        </div>
                        <div class="dropdown-cart-top-footer border-top p-4">
                            <p class="mb-0 font-weight-bold text-secondary">Sub Total <span
                                    class="float-right text-dark">
                                    @if (Session::has('coupon'))
                                        ₹{{ Session()->get('coupon')['discount_amount'] }}
                                    @else
                                        ₹{{ $total }}
                                    @endif
                                </span></p>

                        </div>
                        <div class="dropdown-cart-top-footer border-top p-2">
                            <a class="btn btn-success btn-block btn-lg" href="{{ route('checkout') }}"> Checkout</a>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>
