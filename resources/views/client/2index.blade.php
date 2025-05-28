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

        <!-- Stat + Graph Cards -->
        <div class="row">
            <!-- Monthly Revenue -->
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card card-h-100">
                    <div class="card-body pb-0">
                        <h6 class="text-muted mb-2">Monthly Revenue</h6>
                        <div id="monthlyRevenueChart"></div>
                    </div>
                </div>
            </div>

            <!-- Order Status Pie -->
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card card-h-100">
                    <div class="card-body pb-0">
                        <h6 class="text-muted mb-2">Order Status</h6>
                        <div id="orderStatusChart"></div>
                    </div>
                </div>
            </div>

            <!-- Top Selling Products -->
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card card-h-100">
                    <div class="card-body pb-0">
                        <h6 class="text-muted mb-2">Top Selling Products</h6>
                        <div id="topProductsChart" style="height: 220px;"></div>
                    </div>
                </div>
            </div>

            <!-- Stat Cards -->
            @foreach ([
                ['label' => 'Total Products', 'value' => $totalProducts, 'color' => 'warning', 'icon' => 'mdi mdi-package-variant'],
                ['label' => 'Total Menus', 'value' => $totalMenus, 'color' => 'info', 'icon' => 'mdi mdi-book-open-variant'],
                ['label' => 'Total Orders', 'value' => $totalOrders, 'color' => 'success', 'icon' => 'mdi mdi-cart-check']
            ] as $stat)
                <div class="col-xl-3 col-md-4 col-sm-6">
                    <div class="card card-h-100">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h6 class="text-muted mb-2">{{ $stat['label'] }}</h6>
                                    <h3 class="mb-0 text-{{ $stat['color'] }}">{{ $stat['value'] }}</h3>
                                </div>
                                <div class="col-4 text-end">
                                    <i class="{{ $stat['icon'] }} display-4 text-{{ $stat['color'] }}"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Quick Access -->
        <div class="row mt-4">
            @foreach ([
                ['label' => 'Manage Products', 'route' => route('all.product'), 'icon' => 'mdi mdi-package'],
                ['label' => 'Manage Menus', 'route' => route('all.menu'), 'icon' => 'mdi mdi-view-list'],
                ['label' => 'Manage Coupons', 'route' => route('all.coupon'), 'icon' => 'mdi mdi-sale'],
                ['label' => 'Manage Orders', 'route' => route('all.client.orders'), 'icon' => 'mdi mdi-truck-fast-outline'],
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

    </div>
</div>

<!-- Charts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {

    // Revenue Chart
    new ApexCharts(document.querySelector("#monthlyRevenueChart"), {
        chart: { type: 'area', height: 180, toolbar: { show: false } },
        series: [{ name: 'Revenue', data: @json($monthlyRevenue) }],
        xaxis: { categories: @json($months), labels: { rotate: -45 } },
        yaxis: { labels: { formatter: val => '₹' + val.toFixed(0) } },
        dataLabels: { enabled: true, formatter: val => '₹' + val.toFixed(0) },
        stroke: { curve: 'smooth', width: 2 },
        colors: ['#28a745'],
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0, stops: [0, 90, 100]
            }
        }
    }).render();

    // Order Status Pie
    new ApexCharts(document.querySelector("#orderStatusChart"), {
        chart: { type: 'pie', height: 220 },
        series: [{{ $deliveredOrders }}, {{ $pendingOrders }}, {{ $processingOrders }}],
        labels: ['Delivered', 'Pending', 'Processing'],
        colors: ['#34c38f', '#f1b44c', '#556ee6'],
        dataLabels: {
            enabled: true,
            formatter: val => val.toFixed(1) + '%',
            style: { fontSize: '13px', fontWeight: 'bold' }
        },
        legend: { position: 'bottom' }
    }).render();

    // Top Selling Products
    new ApexCharts(document.querySelector("#topProductsChart"), {
        chart: { type: 'bar', height: 220, toolbar: { show: false } },
        series: [{ name: 'Sold', data: @json($topProductSales) }],
        xaxis: { categories: @json($topProductNames) },
        dataLabels: { enabled: true },
        colors: ['#4099ff']
    }).render();

});
</script>

@endsection
