<!doctype html>
<html lang="en">

    <head>

        <meta charset="utf-8" />
        <title>Client Register </title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
        <meta content="Themesbrand" name="author" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="{{ asset('backend/assets/images/favicon.ico') }}">

        <!-- preloader css -->
        <link rel="stylesheet" href="{{ asset('backend/assets/css/preloader.min.css') }}" type="text/css" />

        <!-- Bootstrap Css -->
        <link href="{{ asset('backend/assets/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
        <!-- Icons Css -->
        <link href="{{ asset('backend/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
        <!-- App Css-->
        <link href="{{ asset('backend/assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />

    </head>

    <body>

    <!-- <body data-layout="horizontal"> -->
<div class="auth-page">
    <div class="container-fluid p-0">
        <div class="row g-0">
            <div class="col-xxl-3 col-lg-4 col-md-5">
                <div class="auth-full-page-content d-flex p-sm-5 p-4">
                    <div class="w-100">
                        <div class="d-flex flex-column h-100">
                            
                            <div class="auth-content my-auto">
                                <div class="text-center">
                                    <h5 class="mb-0">Welcome Back !</h5>
                                    <p class="text-muted mt-2">Register to continue to Client.</p>
                                </div>

    @if ($errors->any())
    @foreach ($errors->all() as $error)
        <li>{{$error }}</li>
    @endforeach
@endif

@if (Session::has('error'))
    <li>{{ Session::get('error') }}</li>
@endif
@if (Session::has('success'))
    <li>{{ Session::get('success') }}</li>
@endif     

<form class="mt-4 pt-2" action="{{ route('client.register.submit') }}"  method="post">
    @csrf

    <div class="mb-3">
        <label class="form-label">Restaurant Name</label>
        <input type="text" name="name" class="form-control" id="name" placeholder="Enter Name">
    </div>
    <div class="mb-3">
        <label class="form-label">Phone</label>
        <input type="text" name="phone" class="form-control" id="phone" placeholder="Enter Phone">
    </div>
    <div class="mb-3">
        <label class="form-label">Address</label>
        <input type="text" name="address" class="form-control" id="address" placeholder="Enter Address">
    </div>

    <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" id="email" placeholder="Enter Email">
    </div>
    <div class="mb-3">
        <div class="d-flex align-items-start">
            <div class="flex-grow-1">
                <label class="form-label">Password</label>
            </div>
            
        </div>
        
        <div class="input-group auth-pass-inputgroup">
            <input type="password" name="password"  class="form-control" placeholder="Enter password" aria-label="Password" aria-describedby="password-addon">
            <button class="btn btn-light shadow-none ms-0" type="button" id="password-addon"><i class="mdi mdi-eye-outline"></i></button>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col">
            <div class="form-check">
                
            </div>  
        </div>
        
    </div>
    <div class="mb-3">
        <button class="btn btn-primary w-100 waves-effect waves-light" type="submit">Register</button>
    </div>
</form>

                                <div class="mt-4 pt-2 text-center">
                                    <div class="signin-other-title">
                                        <h5 class="font-size-14 mb-3 text-muted fw-medium">- Register with -</h5>
                                    </div>

                                    <ul class="list-inline mb-0">
                                        <li class="list-inline-item">
                                            <a href="javascript:void()"
                                                class="social-list-item bg-primary text-white border-primary">
                                                <i class="mdi mdi-facebook"></i>
                                            </a>
                                        </li>
                                        <li class="list-inline-item">
                                            <a href="javascript:void()"
                                                class="social-list-item bg-info text-white border-info">
                                                <i class="mdi mdi-twitter"></i>
                                            </a>
                                        </li>
                                        <li class="list-inline-item">
                                            <a href="javascript:void()"
                                                class="social-list-item bg-danger text-white border-danger">
                                                <i class="mdi mdi-google"></i>
                                            </a>
                                        </li>
                                    </ul>
                                </div>

                                <div class="mt-5 text-center">
                                    <p class="text-muted mb-0">Already have an account ? <a href="{{ route('client.login') }}" class="text-primary fw-semibold">Sign in</a>
 </p>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
                <!-- end auth full page content -->
            </div>
            <!-- end col -->
            <div class="col-xxl-9 col-lg-8 col-md-7">
                <div class="auth-bg pt-md-5 p-4 d-flex">
                    <div class="bg-overlay bg-primary"></div>
                    <ul class="bg-bubbles">
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                    </ul>
                    <!-- end bubble effect -->
<div class="row justify-content-center align-items-center">
<div class="col-xl-7">
    <div class="p-0 p-sm-4 px-xl-0">
        <div id="reviewcarouselIndicators" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-indicators carousel-indicators-rounded justify-content-start ms-0 mb-0">
                <button type="button" data-bs-target="#reviewcarouselIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#reviewcarouselIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
                <button type="button" data-bs-target="#reviewcarouselIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
            </div>
            <!-- end carouselIndicators -->
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <div class="testi-contain text-white">
                        <i class="bx bxs-quote-alt-left text-success display-6"></i>

                        <h4 class="mt-4 fw-medium lh-base text-white">“Running a campus restaurant has never been this smooth. Orders, customers, promotions — all in one place.”</h4>

                        <div class="mt-4 pt-3 pb-5">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0">
                                    <img src="{{ asset('backend/assets/images/users/avatar-1.jpg') }}" class="avatar-md img-fluid rounded-circle" alt="...">
                                </div>
                                <div class="flex-grow-1 ms-3 mb-4">
                                    <h5 class="font-size-18 text-white">Sanya Mehta
                                    </h5>
                                    <p class="mb-0 text-white-50">Student</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="carousel-item">
                    <div class="testi-contain text-white">
                        <i class="bx bxs-quote-alt-left text-success display-6"></i>

                        <h4 class="mt-4 fw-medium lh-base text-white">“Great food delivered fast. CampusEats made it easier for us to serve students on time, every time.”</h4>

                        <div class="mt-4 pt-3 pb-5">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0">
                                    <img src="{{ asset('backend/assets/images/users/avatar-1.jpg') }}" class="avatar-md img-fluid rounded-circle" alt="...">
                                </div>
                                <div class="flex-grow-1 ms-3 mb-4">
                                    <h5 class="font-size-18 text-white">Amit Verma 
                                    </h5>
                                    <p class="mb-0 text-white-50">Vendor</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="carousel-item">
                    <div class="testi-contain text-white">
                        <i class="bx bxs-quote-alt-left text-success display-6"></i>

                        <h4 class="mt-4 fw-medium lh-base text-white">“CampusEats helped us grow from a small stall to one of the most loved delivery services on campus.”</h4>

                        <div class="mt-4 pt-3 pb-5">
                            <div class="d-flex align-items-start">
                                <img src="{{ asset('backend/assets/images/users/avatar-1.jpg') }}" class="avatar-md img-fluid rounded-circle" alt="...">
                                <div class="flex-1 ms-3 mb-4">
                                    <h5 class="font-size-18 text-white">P. Chetry</h5>
                                    <p class="mb-0 text-white-50">Restaurant Manager
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end carousel-inner -->
        </div>
        <!-- end review carousel -->
    </div>
</div>
</div>
                </div>
            </div>
            <!-- end col -->
        </div>
        <!-- end row -->
    </div>
    <!-- end container fluid -->
</div>


        <!-- JAVASCRIPT -->
        <script src="{{ asset('backend/assets/libs/jquery/jquery.min.js') }}"></script>
        <script src="{{ asset('backend/assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('backend/assets/libs/metismenu/metisMenu.min.js') }}"></script>
        <script src="{{ asset('backend/assets/libs/simplebar/simplebar.min.js') }}"></script>
        <script src="{{ asset('backend/assets/libs/node-waves/waves.min.js') }}"></script>
        <script src="{{ asset('backend/assets/libs/feather-icons/feather.min.js') }}"></script>
        <!-- pace js -->
        <script src="{{ asset('backend/assets/libs/pace-js/pace.min.js') }}"></script>
        <!-- password addon init -->
        <script src="{{ asset('backend/assets/js/pages/pass-addon.init.js') }}"></script>

    </body>

</html>