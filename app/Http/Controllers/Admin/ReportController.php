<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use App\Models\Product; 
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use DateTime;

class ReportController extends Controller
{
    ///////////////////// ADMIN REPORT METHODS //////////////////////

    // Show the main reports page for admin
    public function AminAllReports(){
        return view('admin.backend.report.all_report');
    }

    // Search admin reports by a specific date
    public function AminSearchByDate(Request $request){
        $date = new DateTime($request->date);
        $formatDate = $date->format('d F Y'); // Format: 03 June 2024

        // Get all orders that match the selected date
        $orderDate = Order::where('order_date', $formatDate)->latest()->get();
        return view('admin.backend.report.search_by_date', compact('orderDate', 'formatDate'));
    }

    // Search admin reports by month and year
    public function AminSearchByMonth(Request $request){
        $month = $request->month;
        $years = $request->year_name;

        // Get all orders that match the selected month and year
        $orderMonth = Order::where('order_month', $month)
            ->where('order_year', $years)
            ->latest()->get();

        return view('admin.backend.report.search_by_month', compact('orderMonth', 'month', 'years'));
    }

    // Search admin reports by year
    public function AminSearchByYear(Request $request){ 
        $years = $request->year;

        // Get all orders that match the selected year
        $orderYear = Order::where('order_year', $years)->latest()->get();
        return view('admin.backend.report.search_by_year', compact('orderYear', 'years'));
    }

    ///////////////////// CLIENT REPORT METHODS //////////////////////

    // Show the main reports page for the client
    public function ClientAllReports(){
        return view('client.backend.report.all_report');
    }

    // Search client-specific reports by date
    public function ClientSearchByDate(Request $request){
        $date = new DateTime($request->date);
        $formatDate = $date->format('d F Y');
        $cid = Auth::guard('client')->id();

        // Get all orders that belong to this client (indirectly via order items)
        $orders = Order::where('order_date', $formatDate)
            ->whereHas('OrderItems', function ($query) use ($cid) {
                $query->where('client_id', $cid);
            })
            ->latest()
            ->get();

        // Fetch and group all order items for those orders
        $orderItemGroupData = OrderItem::with(['order', 'product'])
            ->whereIn('order_id', $orders->pluck('id'))
            ->where('client_id', $cid)
            ->orderBy('order_id', 'desc')
            ->get()
            ->groupBy('order_id');

        return view('client.backend.report.search_by_date', compact('orderItemGroupData', 'formatDate'));
    }

    // Search client-specific reports by month and year
    public function ClientSearchByMonth(Request $request){
        $month = $request->month;
        $years = $request->year_name;
        $cid = Auth::guard('client')->id();

        // Filter orders by month/year where client is involved
        $orders = Order::where('order_month', $month)
            ->where('order_year', $years)
            ->whereHas('OrderItems', function ($query) use ($cid) {
                $query->where('client_id', $cid);
            })
            ->latest()
            ->get();

        // Group order items for display
        $orderItemGroupData = OrderItem::with(['order', 'product'])
            ->whereIn('order_id', $orders->pluck('id'))
            ->where('client_id', $cid)
            ->orderBy('order_id', 'desc')
            ->get()
            ->groupBy('order_id');

        return view('client.backend.report.search_by_month', compact('orderItemGroupData', 'month', 'years'));
    }

    // Search client-specific reports by year
    public function ClientSearchByYear(Request $request){
        $years = $request->year;
        $cid = Auth::guard('client')->id();

        // Get orders for the given year where the client is involved
        $orders = Order::where('order_year', $years)
            ->whereHas('OrderItems', function ($query) use ($cid) {
                $query->where('client_id', $cid);
            })
            ->latest()
            ->get();

        // Get all relevant order items for those orders
        $orderItemGroupData = OrderItem::with(['order', 'product'])
            ->whereIn('order_id', $orders->pluck('id'))
            ->where('client_id', $cid)
            ->orderBy('order_id', 'desc')
            ->get()
            ->groupBy('order_id');

        return view('client.backend.report.search_by_year', compact('orderItemGroupData', 'years'));
    }
}
