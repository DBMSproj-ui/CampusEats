@extends('admin.admin_dashboard')
@section('admin')

<div class="page-content">
    <div class="container-fluid">

        <h4 class="mb-4">Welcome, Admin 👋</h4>

        <!-- Stat Cards -->
        <div class="row">
            @php
                $stats = [
                    ['label' => 'Total Users', 'value' => $totalUsers, 'color' => 'primary', 'icon' => 'mdi-account-group'],
                    ['label' => 'Total Clients', 'value' => $totalClients, 'color' => 'info', 'icon' => 'mdi-account-tie'],
                    ['label' => 'Total Orders', 'value' => $totalOrders, 'color' => 'success', 'icon' => 'mdi-clipboard-check'],
                    ['label' => 'Total Revenue', 'value' => '₹' . $totalRevenue, 'color' => 'warning', 'icon' => 'mdi-cash-multiple'],
                    ['label' => 'Pending Clients', 'value' => $pendingClients, 'color' => 'danger', 'icon' => 'mdi-account-clock'],
                    ['label' => 'Pending Orders', 'value' => $pendingOrders, 'color' => 'warning', 'icon' => 'mdi-truck-delivery-outline'],
                    ['label' => 'Delivered Orders', 'value' => $deliveredOrders, 'color' => 'success', 'icon' => 'mdi-truck-check'],
                    ['label' => 'Total Products', 'value' => $totalProducts, 'color' => 'secondary', 'icon' => 'mdi-package-variant'],
                    ['label' => 'Active Coupons', 'value' => $activeCoupons, 'color' => 'success', 'icon' => 'mdi-ticket-percent'],
                ];
            @endphp

            @foreach($stats as $stat)
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="card card-h-100">
                    <div class="card-body py-3 px-3">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h6 class="text-muted mb-1" style="font-size: 0.9rem;">{{ $stat['label'] }}</h6>
                                <h4 class="mb-0 text-{{ $stat['color'] }}">{{ $stat['value'] }}</h4>
                            </div>
                            <div class="col-4 text-end">
                                <i class="mdi {{ $stat['icon'] }} display-6 text-{{ $stat['color'] }}"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Quick Access Cards -->
        <div class="row mt-4">
            @foreach([
                ['label' => 'Approve Clients', 'route' => route('pending.restaurant'), 'icon' => 'mdi-account-check'],
                ['label' => 'Manage Products', 'route' => route('admin.all.product'), 'icon' => 'mdi-food'],
                ['label' => 'View Orders', 'route' => route('pending.order'), 'icon' => 'mdi-cart-outline'],
                ['label' => 'Reports', 'route' => route('admin.all.reports'), 'icon' => 'mdi-chart-line'],
                ['label' => 'Reviews', 'route' => route('admin.pending.review'), 'icon' => 'mdi-comment-check'],
            ] as $link)
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="card card-h-100">
                    <a href="{{ $link['route'] }}" class="text-decoration-none">
                        <div class="card-body text-center py-3">
                            <i class="mdi {{ $link['icon'] }} display-6 text-primary"></i>
                            <h6 class="mt-2 text-dark" style="font-size: 0.95rem;">{{ $link['label'] }}</h6>
                        </div>
                    </a>
                </div>
            </div>
            @endforeach
        </div>

    </div>
</div>

@endsection
