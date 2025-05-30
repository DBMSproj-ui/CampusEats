@extends('admin.admin_dashboard')
@section('admin') 

<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
        </div>
        <!-- end page title -->

        <div class="row row-cols-1 row-cols-md-1 row-cols-lg-2 row-cols-xl-2">

            <div class="col">
                <div class="card h-100">
                    <div class="card-header">
                        <h4>Delivery Details</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered border-primary mb-0">
                                <tbody>
                                    <tr><th width="50%">Delivery Name:</th><td>{{ $order->name }}</td></tr>
                                    <tr><th>Delivery Phone:</th><td>{{ $order->phone }}</td></tr>
                                    <tr><th>Delivery Email:</th><td>{{ $order->email }}</td></tr>
                                    <tr><th>Delivery Address:</th><td>{{ $order->address }}</td></tr>
                                    <tr><th>Order Date:</th><td>{{ $order->order_date }}</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card h-100">
                    <div class="card-header">
                        <h4>Order Details</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered border-primary mb-0">
                                <tbody>
                                    <tr><th width="50%">Name:</th><td>{{ $order->user->name }}</td></tr>
                                    <tr><th>Phone:</th><td>{{ $order->user->phone }}</td></tr>
                                    <tr><th>Email:</th><td>{{ $order->user->email }}</td></tr>
                                    <tr><th>Payment Type:</th><td>{{ $order->payment_method }}</td></tr>
                                    <tr><th>Transx Id:</th><td>{{ $order->transaction_id }}</td></tr>
                                    <tr><th>Invoice:</th><td class="text-danger">{{ $order->invoice_no }}</td></tr>
                                    <tr><th>Order Amount:</th><td>₹{{ $order->total_amount }}</td></tr>
                                    <tr><th>Order Status:</th><td><span class="badge bg-success">{{ $order->status }}</span></td></tr>
                                    <tr> 
                                        <th></th>
                                        <td> 
                                            @if($order->status == 'Pending')
                                            <a href="{{ route('pening_to_confirm',$order->id) }}" class="btn btn-block btn-success" id="confirmOrder">Confirm Order</a>
                                            @elseif ($order->status == 'confirm')
                                            <a href="{{ route('confirm_to_processing',$order->id) }}" class="btn btn-block btn-success" id="processingOrder">Processing Order</a>
                                            @elseif ($order->status == 'processing')
                                            <a href="{{ route('processing_to_deliverd',$order->id) }}" class="btn btn-block btn-success" id="deliverdOrder">Delivered Order</a>
                                            @endif
                                        </td> 
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Product table -->
        <div class="row row-cols-1 row-cols-md-1 row-cols-lg-2 row-cols-xl-1 mt-3">
            <div class="col">
                <div class="card">
                    <div class="table-responsive">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td><label>Image</label></td>
                                    <td><label>Product Name</label></td>
                                    <td><label>Restaurant Name</label></td>
                                    <td><label>Product Code</label></td>
                                    <td><label>Quantity</label></td>
                                    <td><label>Price</label></td>
                                </tr>

                                @foreach ($orderItem as $item)
                                <tr>
                                    <td><img src="{{ asset($item->product->image) }}" style="width:50px; height:50px"></td>
                                    <td>{{ $item->product->name }}</td>
                                    <td>{{ $item->client_id == NULL ? 'Owner' : $item->product->client->name }}</td>
                                    <td>{{ $item->product->code }}</td>
                                    <td>{{ $item->qty }}</td>
                                    <td>
                                        ₹{{ $item->price }} <br> Total = ₹{{ $item->price * $item->qty }}
                                    </td>
                                </tr>
                                @endforeach

                                <tr>
                                    <td colspan="5" class="text-end"><strong>Total Price:</strong></td>
                                    <td><strong>₹{{ $totalPrice }}</strong></td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-end"><strong>Final Price (after coupon):</strong></td>
                                    <td><strong>₹{{ $order->total_amount }}</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div> <!-- container-fluid -->
</div>

@endsection
