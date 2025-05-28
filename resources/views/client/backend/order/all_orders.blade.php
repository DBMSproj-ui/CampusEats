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
                        <ol class="breadcrumb m-0">
                           
                        </ol>
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
                <th>Action </th> 
            </tr>
            </thead>


            <tbody>
           @foreach ($orderItemGroupData as $key=> $orderitem)
           @php
               $firstItem = $orderitem->first();
               $order = $firstItem->order;
           @endphp  
            <tr>
                <td>{{ $key+1 }}</td>
                <td>{{ $order->order_date }}</td>
                <td>{{ $order->invoice_no }}</td>
                <td>${{ $order->amount }}</td>
                <td>{{ $order->payment_method }}</td>
                <td>
                    @if (strtolower($order->status) == 'pending')
                    <span class="badge bg-info">Pending</span>
                    @elseif ($order->status == 'confirm')
                    <span class="badge bg-primary">Confirm</span>
                    @elseif ($order->status == 'processing')
                    <span class="badge bg-warning">Processing</span>
                    @elseif ($order->status == 'deliverd')
                    <span class="badge bg-success">Deliverd</span>
                    @endif
                   </td>                
               
                
        <td>
    <a href="{{ route('client.order.details', $order->id) }}" class="btn btn-info waves-effect waves-light">
        <i class="fas fa-eye"></i>
    </a>

    @if ($order->status === 'Pending')
        <a href="{{ route('client.pending.to.processing', $order->id) }}"
           class="btn btn-warning waves-effect waves-light mt-1"
           onclick="return confirm('Change status to Processing?')">
            Mark as Processing
        </a>
    @endif
    @if (strtolower($order->status) === 'processing')
    <a href="{{ route('client.processing.to.delivered', $order->id) }}"
       class="btn btn-success btn-sm mt-1"
       onclick="return confirm('Mark this order as Delivered?')">
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
            </div> <!-- end col -->
        </div> <!-- end row --> 

         
    </div> <!-- container-fluid -->
</div>

 
   


@endsection