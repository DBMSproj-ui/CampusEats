@extends('frontend.dashboard.dashboard')
@section('dashboard')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    @php
        // Fetch up to 3 products that belong to the current client
        $products = App\Models\Product::where('client_id', $client->id)->limit(3)->get();

        // Extract the names of the menus associated with these products
        $menuNames = $products
            ->map(function ($product) {
                return $product->menu->menu_name; // Access the related menu's name
    })
    ->toArray();

// Convert the array of menu names into a single string separated by dots
$menuNamesString = implode(' . ', $menuNames);

// Fetch the first active coupon for this client (if any)
$coupons = App\Models\Coupon::where('client_id', $client->id)->where('status', '1')->first();
    @endphp


    <section class="restaurant-detailed-banner">
        <div class="text-center">
            <img class="img-fluid cover" src="{{ asset('upload/client_images/' . $client->cover_photo) }}">
        </div>
        <div class="restaurant-detailed-header">
            <div class="container">
                <div class="row d-flex align-items-end">
                    <div class="col-md-8">
                        <div class="restaurant-detailed-header-left">
                            <img class="img-fluid mr-3 float-left" alt="osahan"
                                src="{{ asset('upload/client_images/' . $client->photo) }}">
                            <h2 class="text-white">{{ $client->name }}</h2>
                            <p class="text-white mb-1"><i class="icofont-location-pin"></i>{{ $client->address }} <span
                                    class="badge badge-success">OPEN</span>
                            </p>
                            <p class="text-white mb-0"><i class="icofont-food-cart"></i> {{ $menuNamesString }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="restaurant-detailed-header-right text-right">
                            <button class="btn btn-success" type="button"><i class="icofont-clock-time"></i> 25–35 min
                            </button>
                            <h6 class="text-white mb-0 restaurant-detailed-ratings"><span
                                    class="generator-bg rounded text-white"><i class="icofont-star"></i> 4</span> 1 Ratings
                                <i class="ml-3 icofont-speech-comments"></i> 1 reviews
                            </h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>
    <section class="offer-dedicated-nav bg-white border-top-0 shadow-sm">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <span class="restaurant-detailed-action-btn float-right">
                        <button class="btn btn-light btn-sm border-light-btn" type="button"><i
                                class="icofont-heart text-danger"></i> Mark as Favourite</button>
                    </span>
                    <ul class="nav" id="pills-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="pills-order-online-tab" data-toggle="pill"
                                href="#pills-order-online" role="tab" aria-controls="pills-order-online"
                                aria-selected="true">Order Online</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pills-gallery-tab" data-toggle="pill" href="#pills-gallery"
                                role="tab" aria-controls="pills-gallery" aria-selected="false">Gallery</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pills-restaurant-info-tab" data-toggle="pill"
                                href="#pills-restaurant-info" role="tab" aria-controls="pills-restaurant-info"
                                aria-selected="false">Restaurant Info</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pills-book-tab" data-toggle="pill" href="#pills-book" role="tab"
                                aria-controls="pills-book" aria-selected="false">Book A Table</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pills-reviews-tab" data-toggle="pill" href="#pills-reviews"
                                role="tab" aria-controls="pills-reviews" aria-selected="false">Ratings & Reviews</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <section class="offer-dedicated-body pt-2 pb-2 mt-4 mb-4">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <div class="offer-dedicated-body-left">
                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade show active" id="pills-order-online" role="tabpanel"
                                aria-labelledby="pills-order-online-tab">

                                @php
                                    $populers = App\Models\Product::where('status', 1)
                                        ->where('client_id', $client->id)
                                        ->where('most_populer', 1)
                                        ->orderBy('id', 'desc')
                                        ->limit(5)
                                        ->get();
                                @endphp
                                <div id="#menu" class="bg-white rounded shadow-sm p-4 mb-4 explore-outlets">
                                    <h6 class="mb-3">Most Popular <span class="badge badge-success"><i
                                                class="icofont-tags"></i> 10% Off All Items </span></h6>
                                    <div class="owl-carousel owl-theme owl-carousel-five offers-interested-carousel mb-3">

                                        @foreach ($populers as $populer)
                                            <div class="item">
                                                <div class="mall-category-item">
                                                    <a href="#">
                                                        <img class="img-fluid" src="{{ asset($populer->image) }}">
                                                        <h6>{{ $populer->name }}</h6>
                                                        @if ($populer->discount_price == null)
                                                            ₹{{ $populer->price }}
                                                        @else
                                                            ₹<del>{{ $populer->price }}</del>
                                                            ₹{{ $populer->discount_price }}
                                                        @endif
                                                        <span class="float-right">
                                                            <a href="javascript:void(0);" 
   class="btn btn-outline-secondary btn-sm add-to-cart-btn" 
   data-id="{{ $populer->id }}">
   ADD
</a>

                                                        </span>
                                                    </a>
                                                </div>
                                            </div>
                                        @endforeach


                                    </div>
                                </div>

                                @php
                                    $bestsellers = App\Models\Product::where('status', 1)
                                        ->where('client_id', $client->id)
                                        ->where('best_seller', 1)
                                        ->orderBy('id', 'desc')
                                        ->limit(3)
                                        ->get();
                                @endphp

                                <div class="row">
                                    <h5 class="mb-4 mt-3 col-md-12">Best Sellers</h5>
                                    @foreach ($bestsellers as $bestseller)
                                        <div class="col-md-4 col-sm-6 mb-4">
                                            <div
                                                class="list-card bg-white h-100 rounded overflow-hidden position-relative shadow-sm">
                                                <div class="list-card-image">
                                                    <div class="star position-absolute"><span
                                                            class="badge badge-success"><i class="icofont-star"></i> 3.1
                                                            (300+)
                                                        </span></div>
                                                    <div class="favourite-heart text-danger position-absolute"><a
                                                            href="#"><i class="icofont-heart"></i></a></div>
                                                    <div class="member-plan position-absolute"><span
                                                            class="badge badge-dark">Promoted</span></div>
                                                    <a href="#">
                                                        <img src="{{ asset($bestseller->image) }}"
                                                            class="img-fluid item-img">
                                                    </a>
                                                </div>
                                                <div class="p-3 position-relative">
                                                    <div class="list-card-body">
                                                        <h6 class="mb-1"><a href="#"
                                                                class="text-black">{{ $bestseller->name }}</a></h6>


                                                        <p class="text-gray time mb-0">
                                                            @if ($bestseller->discount_price == null)
                                                                <a class="btn btn-link btn-sm text-black"
                                                                    href="#">₹{{ $bestseller->price }} </a>
                                                            @else
                                                                ₹<del>{{ $bestseller->price }}</del>
                                                                <a class="btn btn-link btn-sm text-black"
                                                                    href="#">₹{{ $bestseller->discount_price }} </a>
                                                            @endif
                                                            <span class="float-right">
                                                                <a href="javascript:void(0);" 
   class="btn btn-outline-secondary btn-sm add-to-cart-btn" 
   data-id="{{ $bestseller->id }}">
   ADD
</a>

                                                            </span>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach


                                </div>


                                @foreach ($menus as $menu)
                                    <div class="row">
                                        <h5 class="mb-4 mt-3 col-md-12">{{ $menu->menu_name }} <small
                                                class="h6 text-black-50">{{ $menu->products->count() }} ITEMS</small></h5>
                                        <div class="col-md-12">
                                            <div class="bg-white rounded border shadow-sm mb-4">

                                                @foreach ($menu->products as $product)
                                                    <div class="menu-list p-3 border-bottom">
                                                        <a href="javascript:void(0);" 
   class="btn btn-outline-secondary btn-sm add-to-cart-btn" 
   data-id="{{ $product->id }}">
   ADD
</a>


                                                        <div class="media">
                                                            <img class="mr-3 rounded-pill"
                                                                src="{{ asset($product->image) }}"
                                                                alt="Generic placeholder image">
                                                            <div class="media-body">
                                                                <h6 class="mb-1">{{ $product->name }}</h6>
                                                                <p class="text-gray mb-0">₹{{ $product->price }}
                                                                    ({{ $product->size ?? '' }})
                                                                </p>

                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach

                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                            </div>

                            <div class="tab-pane fade" id="pills-gallery" role="tabpanel"
                                aria-labelledby="pills-gallery-tab">
                                <div id="gallery" class="bg-white rounded shadow-sm p-4 mb-4">
                                    <div class="restaurant-slider-main position-relative homepage-great-deals-carousel">
                                        <div class="owl-carousel owl-theme homepage-ad">

                                            @foreach ($gallerys as $index => $gallery)
                                                <div class="item">
                                                    <img class="img-fluid" src="{{ asset($gallery->gallery_img) }}">
                                                    <div
                                                        class="position-absolute restaurant-slider-pics bg-dark text-white">
                                                        {{ $index + 1 }} of {{ $gallerys->count() }} Photos</div>
                                                </div>
                                            @endforeach

                                        </div>


                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="pills-restaurant-info" role="tabpanel"
                                aria-labelledby="pills-restaurant-info-tab">
                                <div id="restaurant-info" class="bg-white rounded shadow-sm p-4 mb-4">
                                    <div class="address-map float-right ml-5">
                                        <div class="mapouter">
                                            <div class="gmap_canvas">
                                                <iframe
                                                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d4308.30217848582!2d92.82779779057326!3d26.700323082829325!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3744ebc8fd314411%3A0x28a60e3c5515613b!2sTezpur%20University!5e0!3m2!1sen!2sin!4v1748857137543!5m2!1sen!2sin"
                                                    width="300" height="180" style="border:0;" allowfullscreen=""
                                                    loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                                                </iframe>
                                            </div>
                                        </div>
                                    </div>

                                    <h5 class="mb-4">Restaurant Info</h5>
                                    <p class="mb-3">{{ $client->address }}

                                    </p>
                                    <p class="mb-2 text-black"><i class="icofont-phone-circle text-primary mr-2"></i>
                                        {{ $client->phone }}</p>
                                    <p class="mb-2 text-black"><i class="icofont-email text-primary mr-2"></i>
                                        {{ $client->email }}</p>
                                    <p class="mb-2 text-black"><i class="icofont-clock-time text-primary mr-2"></i>
                                        {{ $client->shop_info }}
                                        <span class="badge badge-success"> OPEN NOW </span>
                                    </p>
                                    <h5 class="mt-4 mb-4">More Info</h5>
                                    <p class="mb-3">Dal Makhani, Panneer Butter Masala, Kadhai Paneer, Raita, Veg Thali,
                                        Laccha Paratha, Butter Naan</p>
                                    <div class="border-btn-main mb-4">
                                        <a class="border-btn text-success mr-2" href="#"><i
                                                class="icofont-check-circled"></i> Breakfast</a>
                                        <a class="border-btn text-success mr-2" href="#"><i
                                                class="icofont-check-circled"></i> Lunch</a>
                                        <a class="border-btn text-success mr-2" href="#"><i
                                                class="icofont-check-circled"></i> Dinner</a>
                                        <a class="border-btn text-danger mr-2" href="#"><i
                                                class="icofont-close-circled"></i> No Alcohol Available</a>
                                        <a class="border-btn text-success mr-2" href="#"><i
                                                class="icofont-check-circled"></i> Vegetarian Available</a>
                                        <a class="border-btn text-success mr-2" href="#"><i
                                                class="icofont-check-circled"></i> Non-Veg Available</a>
                                        <a class="border-btn text-success mr-2" href="#"><i
                                                class="icofont-check-circled"></i> Indoor Seating</a>
                                        <a class="border-btn text-success mr-2" href="#"><i
                                                class="icofont-check-circled"></i> Outdoor Seating</a>
                                    </div>
                                </div>
                            </div>



                            <div class="tab-pane fade" id="pills-book" role="tabpanel" aria-labelledby="pills-book-tab">
                                <div id="book-a-table"
                                    class="bg-white rounded shadow-sm p-4 mb-5 rating-review-select-page">
                                    <h5 class="mb-4">Book A Table</h5>
                                    <form>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>Full Name</label>
                                                    <input class="form-control" type="text"
                                                        placeholder="Enter Full Name">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>Email Address</label>
                                                    <input class="form-control" type="text"
                                                        placeholder="Enter Email address">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>Mobile number</label>
                                                    <input class="form-control" type="text"
                                                        placeholder="Enter Mobile number">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>Date And Time</label>
                                                    <input class="form-control" type="text"
                                                        placeholder="Enter Date And Time">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group text-right">
                                            <button class="btn btn-primary" type="button"> Submit </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="pills-reviews" role="tabpanel"
                                aria-labelledby="pills-reviews-tab">
                                <div id="ratings-and-reviews"
                                    class="bg-white rounded shadow-sm p-4 mb-4 clearfix restaurant-detailed-star-rating">
                                    <span class="star-rating float-right">
                                        <a href="#"><i class="icofont-ui-rating icofont-2x active"></i></a>
                                        <a href="#"><i class="icofont-ui-rating icofont-2x active"></i></a>
                                        <a href="#"><i class="icofont-ui-rating icofont-2x active"></i></a>
                                        <a href="#"><i class="icofont-ui-rating icofont-2x active"></i></a>
                                        <a href="#"><i class="icofont-ui-rating icofont-2x"></i></a>
                                    </span>
                                    <h5 class="mb-0 pt-1">Rate this Place</h5>
                                </div>
                                <div class="bg-white rounded shadow-sm p-4 mb-4 clearfix graph-star-rating">
                                    <h5 class="mb-4">Ratings and Reviews</h5>
                                    <div class="graph-star-rating-header">
                                        <div class="star-rating">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <a href="#"><i
                                                        class="icofont-ui-rating {{ $i <= round($roundedAverageRating) ? 'active' : '' }}"></i></a>
                                            @endfor
                                            <b class="text-black ml-2">{{ $totalReviews }}</b>
                                        </div>
                                        <p class="text-black mb-4 mt-2">Rated {{ $roundedAverageRating }} out of 5</p>
                                    </div>

                                    <div class="graph-star-rating-body">

                                        @foreach ($ratingCounts as $star => $count)
                                            <div class="rating-list">
                                                <div class="rating-list-left text-black">
                                                    {{ $star }} Star
                                                </div>
                                                <div class="rating-list-center">
                                                    <div class="progress">
                                                        <div style="width: {{ $ratingPercentages[$star] }}%"
                                                            aria-valuemax="5" aria-valuemin="0" aria-valuenow="5"
                                                            role="progressbar" class="progress-bar bg-primary">
                                                            <span class="sr-only">{{ $ratingPercentages[$star] }}%
                                                                Complete (danger)</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="rating-list-right text-black">
                                                    {{ number_format($ratingPercentages[$star], 2) }}%</div>
                                            </div>
                                        @endforeach

                                    </div>

                                </div>
                                <div class="bg-white rounded shadow-sm p-4 mb-4 restaurant-detailed-ratings-and-reviews">
                                    <a href="#" class="btn btn-outline-primary btn-sm float-right">Top Rated</a>
                                    <h5 class="mb-1">All Ratings and Reviews</h5>
                                    <style>
                                        .icofont-ui-rating {
                                            color: #ccc;
                                        }

                                        .icofont-ui-rating.active {
                                            color: #dd646e;
                                        }
                                    </style>
                                    @php
                                        $reviews = App\Models\Review::where('client_id', $client->id)
                                            ->where('status', 1)
                                            ->latest()
                                            ->limit(5)
                                            ->get();
                                    @endphp

                                    @foreach ($reviews as $review)
                                        <div class="reviews-members pt-4 pb-4">
                                            <div class="media">
                                                <a href="#"><img alt="Generic placeholder image"
                                                        src="{{ !empty($review->user->photo) ? url('upload/user_images/' . $review->user->photo) : url('upload/no_image.jpg') }}"
                                                        class="mr-3 rounded-pill"></a>
                                                <div class="media-body">
                                                    <div class="reviews-members-header">
                                                        <span class="star-rating float-right">
                                                            @php
                                                                $rating = $review->rating ?? 0;
                                                            @endphp
                                                            @for ($i = 1; $i <= 5; $i++)
                                                                @if ($i <= $rating)
                                                                    <a href="#"><i
                                                                            class="icofont-ui-rating active"></i></a>
                                                                @else
                                                                    <a href="#"><i
                                                                            class="icofont-ui-rating"></i></a>
                                                                @endif
                                                            @endfor
                                                        </span>
                                                        <h6 class="mb-1"><a class="text-black"
                                                                href="#">{{ $review->user->name }}</a></h6>
                                                        <p class="text-gray">
                                                            {{ Carbon\Carbon::parse($review->created_at)->diffForHumans() }}
                                                        </p>
                                                    </div>
                                                    <div class="reviews-members-body">
                                                        <p> {{ $review->comment }} </p>
                                                    </div>
                                                    <div class="reviews-members-footer">
                                                        <a class="total-like" href="#"><i
                                                                class="icofont-thumbs-up"></i></a> <a class="total-like"
                                                            href="#"><i class="icofont-thumbs-down"></i></a>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                    <hr>

                                    <hr>
                                    <a class="text-center w-100 d-block mt-4 font-weight-bold" href="#">See All
                                        Reviews</a>
                                </div>


                                <div class="bg-white rounded shadow-sm p-4 mb-5 rating-review-select-page">
                                    @guest
                                        <p><b>For Add Resturant Review. You need to login first <a
                                                    href="{{ route('login') }}"> Login Here </a> </b></p>
                                    @else
                                        <style>
                                            .star-rating label {
                                                display: inline-flex;
                                                margin-right: 5px;
                                                cursor: pointer;
                                            }

                                            .star-rating input[type="radio"] {
                                                display: none;
                                            }

                                            .star-rating input[type="radio"]:checked+.star-icon {
                                                color: #dd646e;
                                            }
                                        </style>

                                        <h5 class="mb-4">Leave Comment</h5>
                                        <p class="mb-2">Rate the Place</p>
                                        <form method="post" action="{{ route('store.review') }}">
                                            @csrf
                                            <input type="hidden" name="client_id" value="{{ $client->id }}">

                                            <div class="mb-4">
                                                <span class="star-rating">
                                                    <label for="rating-1">
                                                        <input type="radio" name="rating" id="rating-1" value="1"
                                                            hidden><i
                                                            class="icofont-ui-rating icofont-2x star-icon"></i></label>

                                                    <label for="rating-2">
                                                        <input type="radio" name="rating" id="rating-2" value="2"
                                                            hidden><i
                                                            class="icofont-ui-rating icofont-2x star-icon"></i></label>
                                                    <label for="rating-3">
                                                        <input type="radio" name="rating" id="rating-3" value="3"
                                                            hidden><i
                                                            class="icofont-ui-rating icofont-2x star-icon"></i></label>

                                                    <label for="rating-4">
                                                        <input type="radio" name="rating" id="rating-4" value="4"
                                                            hidden><i
                                                            class="icofont-ui-rating icofont-2x star-icon"></i></label>

                                                    <label for="rating-5">
                                                        <input type="radio" name="rating" id="rating-5" value="5"
                                                            hidden><i
                                                            class="icofont-ui-rating icofont-2x star-icon"></i></label>


                                                </span>
                                            </div>

                                            <div class="form-group">
                                                <label>Your Comment</label>
                                                <textarea class="form-control" name="comment" id="comment"></textarea>
                                            </div>
                                            <div class="form-group">
                                                <button class="btn btn-primary btn-sm" type="submit"> Submit Comment
                                                </button>
                                            </div>
                                        </form>

                                    @endguest
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @php
                    use Carbon\Carbon;
                    $coupon = App\Models\Coupon::where('client_id', $client->id)
                        ->where('validity', '>=', Carbon::now()->format('Y-m-d'))
                        ->latest()
                        ->first();
                @endphp

                <div class="col-md-4">
                    <div class="pb-2">
                        <div
                            class="bg-white rounded shadow-sm text-white mb-4 p-4 clearfix restaurant-detailed-earn-pts card-icon-overlap">
                            <img class="img-fluid float-left mr-3" src="{{ asset('frontend/img/earn-score-icon.png') }}">
                            <h6 class="pt-0 text-primary mb-1 font-weight-bold">OFFER</h6>

                            {{-- <pre>{{ print_r(Session::get('coupon'), true) }}</pre> --}}

                            @if ($coupon == null)
                                <p class="mb-0">No Coupon is Available </p>
                            @else
                                <p class="mb-0">{{ $coupon->discount }}% off | Use coupon <span
                                        class="text-danger font-weight-bold">{{ $coupon->coupon_name }}</span></p>
                            @endif

                            <div class="icon-overlap">
                                <i class="icofont-sale-discount"></i>
                            </div>
                        </div>
                    </div>

                    <div class="generator-bg rounded shadow-sm mb-4 p-4 osahan-cart-item">
                        <h5 class="mb-1 text-white">Your Order</h5>
                        <p class="mb-4 text-white">{{ count((array) session('cart')) }} ITEMS</p>
                        <div class="bg-white rounded shadow-sm mb-2">

                            @php $total = 0 @endphp
                            @if (session('cart'))
                                @foreach (session('cart') as $id => $details)
                                    @php
                                        $total += $details['price'] * $details['quantity'];
                                    @endphp

                                    <div class="gold-members p-2 border-bottom">
                                        <p class="text-gray mb-0 float-right ml-2">
                                            ₹{{ $details['price'] * $details['quantity'] }}</p>
                                        <span class="count-number float-right">

                                            <button class="btn btn-outline-secondary  btn-sm left dec"
                                                data-id="{{ $id }}"> <i class="icofont-minus"></i> </button>

                                            <input class="count-number-input" type="text"
                                                value="{{ $details['quantity'] }}" readonly="">

                                            <button class="btn btn-outline-secondary btn-sm right inc"
                                                data-id="{{ $id }}"> <i class="icofont-plus"></i> </button>

                                            <button class="btn btn-outline-danger btn-sm right remove"
                                                data-id="{{ $id }}"> <i class="icofont-trash"></i> </button>
                                        </span>
                                        <div class="media">
                                            <div class="mr-2"><img src="{{ asset($details['image']) }}"
                                                    width="25px"></div>
                                            <div class="media-body">
                                                <p class="mt-1 mb-0 text-black">{{ $details['name'] }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif


                        </div>

                        @if (Session::has('coupon'))
                            <div class="mb-2 bg-white rounded p-2 clearfix">
                                <p class="mb-1">Item Total <span
                                        class="float-right text-dark">{{ count((array) session('cart')) }}</span></p>

                                <p class="mb-1">Coupon Name <span
                                        class="float-right text-dark">{{ session()->get('coupon')['coupon_name'] }} (
                                        {{ session()->get('coupon')['discount'] }} %) </span>
                                    <a type="submit" onclick="couponRemove()"><i class="icofont-ui-delete float-right"
                                            style="color: red;"></i></a>
                                </p>


                                <p class="mb-1 text-success">Total Discount
                                    <span class="float-right text-success">

                                        @if (Session::has('coupon'))
                                            ₹{{ $total - Session()->get('coupon')['discount_amount'] }}
                                        @else
                                            ₹{{ $total }}
                                        @endif

                                    </span>
                                </p>
                                <hr />
                                <h6 class="font-weight-bold mb-0">TO PAY <span class="float-right">
                                        @if (Session::has('coupon'))
                                            ₹{{ Session()->get('coupon')['discount_amount'] }}
                                        @else
                                            ₹{{ $total }}
                                        @endif
                                    </span></h6>
                            </div>
                        @else
                            <div class="mb-2 bg-white rounded p-2 clearfix">
                                <div class="input-group input-group-sm mb-2">
                                    <input type="text" class="form-control" placeholder="Enter promo code"
                                        id="coupon_name">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="submit" id="button-addon2"
                                            onclick="ApplyCoupon()"><i class="icofont-sale-discount"></i> APPLY</button>
                                    </div>
                                </div>
                            </div>
                        @endif



                        <div class="mb-2 bg-white rounded p-2 clearfix">
                            <img class="img-fluid float-left" src="{{ asset('frontend/img/wallet-icon.png') }}">
                            <h6 class="font-weight-bold text-right mb-2">Subtotal : <span class="text-danger">
                                    @if (Session::has('coupon'))
                                        ₹{{ Session()->get('coupon')['discount_amount'] }}
                                    @else
                                        ₹{{ $total }}
                                    @endif
                                </span></h6>
                            <p class="seven-color mb-1 text-right">Extra charges may apply</p>

                        </div>

                        <a href="{{ route('checkout') }}" class="btn btn-success btn-block btn-lg">Checkout <i
                                class="icofont-long-arrow-right"></i></a>
                    </div>

                    <div class="text-center pt-2 mb-4">

                    </div>
                    <div class="text-center pt-2">

                    </div>
                </div>
            </div>
        </div>
    </section>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function () {

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 1500,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        // Quantity Increase
        $('.inc').on('click', function () {
            var id = $(this).data('id');
            var input = $(this).closest('span').find('input');
            var newQuantity = parseInt(input.val()) + 1;
            updateQuantity(id, newQuantity);
        });

        // Quantity Decrease
        $('.dec').on('click', function () {
            var id = $(this).data('id');
            var input = $(this).closest('span').find('input');
            var newQuantity = parseInt(input.val()) - 1;
            if (newQuantity >= 1) {
                updateQuantity(id, newQuantity);
            }
        });

        // Remove from cart
        $('.remove').on('click', function () {
            var id = $(this).data('id');
            removeFromCart(id);
        });

        // Add to cart with restaurant check
        $('.add-to-cart-btn').on('click', function () {
            let productId = $(this).data('id');

            $.ajax({
                url: '/add_to_cart/' + productId,
                method: 'GET',
                success: function (response) {
                    if (response.error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Sorry!',
                            text: response.error,
                            confirmButtonColor: '#e3342f'
                        });
                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: 'Added to Cart',
                            text: response.message,
                            confirmButtonColor: '#38c172'
                        }).then(() => {
                            location.reload();
                        });
                    }
                },
                error: function () {
                    Swal.fire('Oops!', 'Something went wrong. Please try again.', 'error');
                }
            });
        });

        // Update quantity
        function updateQuantity(id, quantity) {
            $.ajax({
                url: '{{ route('cart.updateQuantity') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    quantity: quantity
                },
                success: function (response) {
                    Toast.fire({
                        icon: 'success',
                        title: 'Quantity Updated'
                    }).then(() => {
                        location.reload();
                    });
                }
            });
        }

        // Remove item
        function removeFromCart(id) {
            $.ajax({
                url: '{{ route('cart.remove') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id
                },
                success: function (response) {
                    Toast.fire({
                        icon: 'success',
                        title: 'Cart Item Removed'
                    }).then(() => {
                        location.reload();
                    });
                }
            });
        }

        // Apply coupon (optional)
        window.ApplyCoupon = function () {
            var coupon_name = $('#coupon_name').val();

            $.ajax({
                type: 'POST',
                url: "{{ route('coupon.apply') }}",
                data: {
                    _token: '{{ csrf_token() }}',
                    coupon_name: coupon_name
                },
                success: function (data) {
                    if (data.validity == true) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Coupon Applied!',
                            text: data.success,
                            confirmButtonColor: '#38c172'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Invalid!', data.error, 'error');
                    }
                }
            });
        }

        // Remove coupon (optional)
        window.couponRemove = function () {
            $.ajax({
                type: 'GET',
                url: "{{ route('coupon.remove') }}",
                success: function (data) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Coupon Removed',
                        text: data.success,
                        confirmButtonColor: '#3490dc'
                    }).then(() => {
                        location.reload();
                    });
                }
            });
        }

    });
</script>


@endsection
