@extends('client.client_dashboard')
@section('client')

<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Client All Orders</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0"></ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <div class="card">  
                    <div class="card-body">

        <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
            <thead>
            <tr>
                <th>Sl</th>
                <th>Date</th>
                <th>Invoice</th>
                <th>Amount</th>
                <th>Payment</th> 
                <th>Status</th>
                <th>Action</th> 
            </tr>
            </thead>

            <tbody>
            @php $sl = 1; @endphp
            @foreach ($orderItemGroupData as $orderitem)
                @php
                    $firstItem = $orderitem->first();
                    $order = $firstItem->order;
                @endphp  
                <tr>
                    <td>{{ $sl++ }}</td>
                    <td>{{ $order->order_date }}</td>
                    <td>{{ $order->invoice_no }}</td>
                    <td>₹{{ $order->amount }}</td>
                    <td>{{ $order->payment_method }}</td>
                    <td>
                        @if (strtolower($order->status) == 'pending')
                            <span class="badge bg-info">Pending</span>
                        @elseif (strtolower($order->status) == 'confirm')
                            <span class="badge bg-warning text-dark">Processing</span>
                        @elseif (strtolower($order->status) == 'processing')
                            <span class="badge bg-primary">Out for Delivery</span>
                        @elseif (strtolower($order->status) == 'deliverd')
                            <span class="badge bg-success">Delivered</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('client.order.details', $order->id) }}" class="btn btn-info waves-effect waves-light">
                            <i class="fas fa-eye"></i>
                        </a>

                        @if (strtolower($order->status) === 'pending')
                            <a href="javascript:void(0)" class="btn btn-warning mt-1 mark-action"
                               data-url="{{ route('client.pending.to.confirm', $order->id) }}"
                               data-text="Mark this order as Confirm?">
                                Mark as Confirm
                            </a>
                        @elseif (strtolower($order->status) === 'confirm')
                            <a href="javascript:void(0)" class="btn btn-warning mt-1 mark-action"
                               data-url="{{ route('client.confirm.to.processing', $order->id) }}"
                               data-text="Mark this order as Out for Delivery?">
                                Mark as Out for Delivery
                            </a>
                        @elseif (strtolower($order->status) === 'processing')
                            <a href="javascript:void(0)" class="btn btn-success mt-1 mark-action"
                               data-url="{{ route('client.processing.to.delivered', $order->id) }}"
                               data-text="Mark this order as Delivered?">
                                Mark as Delivered
                            </a>
                        @endif
                    </td>
                </tr>
            @endforeach    
            </tbody>
        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Handle session alert
    document.addEventListener("DOMContentLoaded", function () {
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: '{{ session('success') }}',
                confirmButtonColor: '#28a745'
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Oops!',
                text: '{{ session('error') }}',
                confirmButtonColor: '#dc3545'
            });
        @endif

        // Handle confirm popup for each action
        document.querySelectorAll(".mark-action").forEach(function(btn) {
            btn.addEventListener("click", function(e) {
                e.preventDefault();
                const url = this.getAttribute("data-url");
                const message = this.getAttribute("data-text");

                Swal.fire({
                    title: 'Are you sure?',
                    text: message,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, proceed!',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            });
        });
    });
</script>

@endsection
