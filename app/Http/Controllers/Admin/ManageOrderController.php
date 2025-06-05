<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use Illuminate\Support\Facades\Session;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ManageOrderController extends Controller
{
    // Show all orders with status "Pending"
    public function PendingOrder(){
        $allData = Order::where('status','Pending')->orderBy('id','desc')->get();
        return view('admin.backend.order.pending_order',compact('allData'));
    }

    // Show all orders with status "Confirm"
    public function ConfirmOrder(){
        $allData = Order::where('status','confirm')->orderBy('id','desc')->get();
        return view('admin.backend.order.confirm_order',compact('allData'));
    }

    // Show all orders with status "Processing"
    public function ProcessingOrder(){
        $allData = Order::where('status','processing')->orderBy('id','desc')->get();
        return view('admin.backend.order.processing_order',compact('allData'));
    }

    // Show all orders with status "Delivered"
    public function DeliverdOrder(){
        $allData = Order::where('status','deliverd')->orderBy('id','desc')->get();
        return view('admin.backend.order.deliverd_order',compact('allData'));
    }

    // Show admin order details including items and total price
    public function AdminOrderDetails($id){
        $order = Order::with('user')->where('id',$id)->first();
        $orderItem = OrderItem::with('product')->where('order_id',$id)->orderBy('id','desc')->get();

        $totalPrice = 0;
        foreach($orderItem as $item){
            $totalPrice += $item->price * $item->qty;
        }

        return view('admin.backend.order.admin_order_details',compact('order','orderItem','totalPrice'));
    }

    // Admin: Move order from "Pending" to "Confirm"
    public function PendingToConfirm($id){
        Order::find($id)->update(['status' => 'confirm']);
        return redirect()->route('confirm.order')->with([
            'message' => 'Order Confirm Successfully',
            'alert-type' => 'success'
        ]);
    }

    // Admin: Move order from "Confirm" to "Processing"
    public function ConfirmToProcessing($id){
        Order::find($id)->update(['status' => 'processing']);
        return redirect()->route('processing.order')->with([
            'message' => 'Order Processing Successfully',
            'alert-type' => 'success'
        ]);
    }

    // Admin: Move order from "Processing" to "Delivered"
    public function ProcessingToDiliverd($id){
        Order::find($id)->update(['status' => 'deliverd']);
        return redirect()->route('deliverd.order')->with([
            'message' => 'Order Processing Successfully',
            'alert-type' => 'success'
        ]);
    }

    // Client: View all their orders grouped by order ID
    public function AllClientOrders(){
        $clientId = Auth::guard('client')->id();

        $orderItemGroupData = OrderItem::with(['product','order'])
            ->where('client_id',$clientId)
            ->orderBy('order_id','desc')
            ->get()
            ->groupBy('order_id');

        return view('client.backend.order.all_orders',compact('orderItemGroupData'));
    }

    // Client: View details of a specific order
    public function ClientOrderDetails($id){
        $cid = Auth::guard('client')->id();

        $order = Order::with('user')->where('id',$id)->first();
        $orderItem = OrderItem::with('product')
            ->where('order_id',$id)
            ->where('client_id',$cid)
            ->orderBy('id','desc')->get();

        $totalPrice = 0;
        foreach($orderItem as $item){
            $totalPrice += $item->price * $item->qty;
        }

        return view('client.backend.order.client_order_details',compact('order','orderItem','totalPrice'));
    }

    // Client: Change order from "Pending" to "Processing"
    public function ClientPendingToProcessing($id){
        $order = Order::findOrFail($id);
        $clientId = Auth::guard('client')->id();

        // Verify client owns a part of the order
        $hasAccess = OrderItem::where('order_id', $order->id)->where('client_id', $clientId)->exists();
        if (!$hasAccess) return redirect()->back()->with('error', 'Unauthorized action.');

        if (strtolower($order->status) === 'pending') {
            $order->status = 'processing';
            $order->save();
            return redirect()->back()->with('success', 'Order status updated to Processing.');
        }

        return redirect()->back()->with('error', 'Only pending orders can be updated.');
    }

    // Client: Change order from "Pending" to "Confirm"
    public function ClientPendingToConfirm($id){
        $order = Order::findOrFail($id);
        $clientId = Auth::guard('client')->id();

        $hasAccess = OrderItem::where('order_id', $order->id)->where('client_id', $clientId)->exists();
        if (!$hasAccess) return redirect()->back()->with('error', 'Unauthorized action.');

        if (strtolower($order->status) === 'pending') {
            $order->status = 'confirm';
            $order->save();
            return redirect()->back()->with('success', 'Order marked as Confirm.');
        }

        return redirect()->back()->with('error', 'Only pending orders can be updated.');
    }

    // Client: Change order from "Confirm" to "Processing"
    public function ClientConfirmToProcessing($id){
        $order = Order::findOrFail($id);
        $clientId = Auth::guard('client')->id();

        $hasAccess = OrderItem::where('order_id', $order->id)->where('client_id', $clientId)->exists();
        if (!$hasAccess) return redirect()->back()->with('error', 'Unauthorized action.');

        if (strtolower($order->status) === 'confirm') {
            $order->status = 'processing';
            $order->save();
            return redirect()->back()->with('success', 'Order marked as Out for Delivery.');
        }

        return redirect()->back()->with('error', 'Only confirmed orders can be updated.');
    }

    // User: View list of all their orders
    public function UserOrderList(){
        $userId = Auth::user()->id;
        $allUserOrder = Order::where('user_id', $userId)->orderBy('id', 'desc')->get();
        return view('frontend.dashboard.order.order_list',compact('allUserOrder'));
    }

    // User: View detailed order page
    public function UserOrderDetails($id){
        $order = Order::with('user')->where('id',$id)->where('user_id',Auth::id())->first();
        $orderItem = OrderItem::with('product')->where('order_id',$id)->orderBy('id','desc')->get();

        $totalPrice = 0;
        foreach($orderItem as $item){
            $totalPrice += $item->price * $item->qty;
        }

        return view('frontend.dashboard.order.order_details',compact('order','orderItem','totalPrice'));
    }

    // User: Download invoice PDF for an order
    public function UserInvoiceDownload($id){
        $order = Order::with('user')->where('id',$id)->where('user_id',Auth::id())->first();
        $orderItem = OrderItem::with('product')->where('order_id',$id)->orderBy('id','desc')->get();

        $totalPrice = 0;
        foreach($orderItem as $item){
            $totalPrice += $item->price * $item->qty;
        }

        // Generate downloadable PDF using domPDF
        $pdf = Pdf::loadView('frontend.dashboard.order.invoice_download', compact('order','orderItem','totalPrice'))
            ->setPaper('a4')->setOption([
                'tempDir' => public_path(),
                'chroot' => public_path(),
            ]);

        return $pdf->download('invoice.pdf');
    }

    // Client: Mark order as delivered from processing
    public function ClientProcessingToDelivered($id){
        $order = Order::findOrFail($id);
        $clientId = Auth::guard('client')->id();

        $hasAccess = OrderItem::where('order_id', $order->id)->where('client_id', $clientId)->exists();
        if (!$hasAccess) return redirect()->back()->with('error', 'Unauthorized action.');

        if (strtolower($order->status) === 'processing') {
            $order->status = 'deliverd';
            $order->delivered_date = now();
            $order->save();
            return redirect()->back()->with('success', 'Order marked as Delivered.');
        }

        return redirect()->back()->with('error', 'Only processing orders can be marked as delivered.');
    }
}
