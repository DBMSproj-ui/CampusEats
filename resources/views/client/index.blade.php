@extends('client.client_dashboard')
@section('client')

@php
    $id = Auth::guard('client')->id();
    $client = App\Models\Client::find($id);
    $status = $client->status;
@endphp

<div class="page-content">
    <div class="container-fluid">

        @if ($status === '1')
        <h4>Restaurant Account is <span class="text-success">Active</span></h4>
        @else
        <h4>Restaurant Account is <span class="text-danger">Inactive</span></h4>
        <p class="text-danger"><b>Please wait for admin approval.</b></p>
        @endif

        <!-- Page Title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Dashboard</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">Dashboard</li>
                            <li class="breadcrumb-item active">Client Panel</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stat Cards -->
        <div class="row">
            @foreach ([
                ['label' => 'Total Orders', 'value' => $totalOrders, 'color' => 'success'],
                ['label' => 'Transactions', 'value' => $totalTransactions, 'color' => 'info'],
                ['label' => 'Total Revenue', 'value' => '₹' . $totalRevenue, 'color' => 'primary'],
                ['label' => 'Total Products', 'value' => $totalProducts, 'color' => 'warning'],
                ['label' => 'Total Menus', 'value' => $totalMenus, 'color' => 'info'],
                ['label' => 'Active Coupons', 'value' => $activeCoupons, 'color' => 'success'],
            ] as $stat)
            <div class="col-xl-4 col-md-6">
                <div class="card card-h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h6 class="text-muted mb-2">{{ $stat['label'] }}</h6>
                                <h3 class="mb-0 text-{{ $stat['color'] }}">{{ $stat['value'] }}</h3>
                            </div>
                            <div class="col-4 text-end">
                                <i class="mdi mdi-food-fork-drink display-4 text-{{ $stat['color'] }}"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Quick Access Cards -->
        <div class="row">
            @foreach ([
                ['label' => 'Manage Products', 'route' => route('all.product'), 'icon' => 'mdi mdi-food'],
                ['label' => 'Manage Menus', 'route' => route('all.menu'), 'icon' => 'mdi mdi-book-open-page-variant'],
                ['label' => 'Manage Coupons', 'route' => route('all.coupon'), 'icon' => 'mdi mdi-ticket-percent'],
                ['label' => 'Manage Reviews', 'route' => route('client.all.reviews'), 'icon' => 'mdi mdi-comment-text-multiple']
            ] as $quick)
            <div class="col-xl-3 col-md-6">
                <div class="card card-h-100">
                    <a href="{{ $quick['route'] }}" class="text-decoration-none">
                        <div class="card-body text-center">
                            <i class="{{ $quick['icon'] }} display-4 text-primary"></i>
                            <h6 class="mt-3 text-dark">{{ $quick['label'] }}</h6>
                        </div>
                    </a>
                </div>
            </div>
            @endforeach
        </div>

    </div> <!-- container-fluid -->
</div>

@endsection
