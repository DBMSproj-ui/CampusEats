@extends('frontend.dashboard.dashboard')
@section('dashboard')
 
@php
    $id = Auth::user()->id;
    $profileData = App\Models\User::find($id);
@endphp

<section class="section pt-4 pb-4 osahan-account-page">
    <div class="container">
       <div class="row">
          
        @include('frontend.dashboard.sidebar')


<div class="col-md-9">
    <div class="osahan-account-page-right rounded shadow-sm bg-white p-4 h-100">
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="orders" role="tabpanel" aria-labelledby="orders-tab">
            <h4 class="font-weight-bold mt-0 mb-4">Order List   </h4>
            
            
    <div class="bg-white card mb-4 order-list shadow-sm">
        <div class="gold-members p-4">
           
            <table class="table table-bordered dt-responsive  nowrap w-100">
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
               @foreach ($allUserOrder as $key=> $item)  
                <tr>
                    <td>{{ $key+1 }}</td>
                    <td>{{ $item->order_date }}</td>
                    <td>{{ $item->invoice_no }}</td>
                    <td>${{ $item->amount }}</td>
                    <td>{{ $item->payment_method }}</td>
                    <td>
                    @if ($item->status == 'Pending')
                    <span class="badge bg-info">Pending</span>
                    @elseif ($item->status == 'confirm')
                    <span class="badge bg-primary">Confirm</span>
                    @elseif ($item->status == 'processing')
                    <span class="badge bg-warning">Processing</span>
                    @elseif ($item->status == 'deliverd')
                    <span class="badge bg-success">Deliverd</span>
                    @endif
                    </td>                
                   
                    
            <td class="d-flex flex-column gap-1">
    <a href="{{ route('user.order.details', $item->id) }}" class="btn btn-sm btn-outline-primary mb-1">
        <i class="fas fa-eye"></i> View
    </a>

    <a href="{{ route('user.invoice.download', $item->id) }}" class="btn btn-sm btn-outline-danger mb-1">
        <i class="fa fa-download"></i> Invoice
    </a>

    @if (strtolower($item->status) === 'processing')
        <a href="{{ route('user.order.mark.delivered', $item->id) }}"
           class="btn btn-sm btn-outline-success"
           onclick="return confirm('Are you sure you want to mark this order as delivered?');">
           <i class="fas fa-check-circle"></i> Mark as Delivered
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
       </div>
    </div>
 </section> 

@endsection