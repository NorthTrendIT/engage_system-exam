@extends('layouts.master')

@section('title','Dashboard')

@section('content')
<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <!--begin::Toolbar-->
  <div class="toolbar" id="kt_toolbar">
     <!--begin::Container-->
     <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
        <!--begin::Page title-->
        <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
           <!--begin::Title-->
           <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">Hi {{ @Auth::user()->first_name ?? "" }} {{ @Auth::user()->last_name ?? "" }},
           <!--end::Title-->
        </div>
        <!--end::Page title-->
     </div>
     <!--end::Container-->
  </div>
  <!--end::Toolbar-->
  <!--begin::Post-->
  <div class="post d-flex flex-column-fluid" id="kt_post">
     <!--begin::Container-->
     <div id="kt_content_container" class="container-xxl">
        <!--begin::Row-->
        <div class="row gy-5 g-xl-8">
            @if(Auth::user()->role_id != 1)
            <!--begin::Col-->
            <div class="col-xl-4">
                <!--begin::List Widget 6-->
                <div class="card card-xl-stretch mb-xl-8">
                    <!--begin::Header-->
                    <div class="card-header border-0 mt-5">
                        <h3 class="card-title fw-bolder text-dark">Notifications</h3>
                        <div class="card-toolbar">
                            @if(isset($notification) && count($notification) > 0)
                            <a href="{{ route('news-and-announcement.index') }}" class="btn btn-sm btn-light-primary font-weight-bold mr-2">
                                View All
                            </a>
                            @endif
                        </div>
                    </div>
                    <!--end::Header-->
                    <!--begin::Body-->
                    <div class="card-body pt-0">
                        @if(isset($notification) && count($notification) > 0)
                        @foreach($notification as $item)
                        <a href="{{ route('news-and-announcement.show',$item->id) }}" class="fw-bolder text-gray-800 text-hover-primary fs-6">
                        <div class="d-flex align-items-center @if($item->is_important) bg-light-danger @else bg-light-success @endif rounded p-5 mb-7">
                            <span class="svg-icon @if($item->is_important) svg-icon-danger @else svg-icon-success @endif me-5">
                                <!--begin::Svg Icon | path: icons/duotune/abstract/abs027.svg-->
                                <span class="svg-icon svg-icon-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path opacity="0.3" d="M21.25 18.525L13.05 21.825C12.35 22.125 11.65 22.125 10.95 21.825L2.75 18.525C1.75 18.125 1.75 16.725 2.75 16.325L4.04999 15.825L10.25 18.325C10.85 18.525 11.45 18.625 12.05 18.625C12.65 18.625 13.25 18.525 13.85 18.325L20.05 15.825L21.35 16.325C22.35 16.725 22.35 18.125 21.25 18.525ZM13.05 16.425L21.25 13.125C22.25 12.725 22.25 11.325 21.25 10.925L13.05 7.62502C12.35 7.32502 11.65 7.32502 10.95 7.62502L2.75 10.925C1.75 11.325 1.75 12.725 2.75 13.125L10.95 16.425C11.65 16.725 12.45 16.725 13.05 16.425Z" fill="black" />
                                        <path d="M11.05 11.025L2.84998 7.725C1.84998 7.325 1.84998 5.925 2.84998 5.525L11.05 2.225C11.75 1.925 12.45 1.925 13.15 2.225L21.35 5.525C22.35 5.925 22.35 7.325 21.35 7.725L13.05 11.025C12.45 11.325 11.65 11.325 11.05 11.025Z" fill="black" />
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                            </span>
                            <div class="flex-grow-1 me-2">
                                {{ $item->title }}
                                <span class="text-muted fw-bold d-block">{{ getNotificationType($item->type) }}</span>
                            </div>
                        </div>
                        </a>
                        @endforeach
                        @else
                        <div class="d-flex align-items-center p-5 mb-7">
                            <div class="flex-grow-1 me-2" style="text-align: center">
                                <span class="text-muted fw-bold d-block">No new Notification.</span>
                            </div>
                        </div>
                        @endif
                    </div>
                    <!--end::Body-->
                </div>
                <!--end::List Widget 6-->
            </div>

            @if(Auth::user()->role_id == 4)
            <div class="col-xl-8">
                <div class="card card-xl-stretch mb-xl-8">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-sm-5 mb-md-0 mb-lg-0">
                                <div class="bg-light-dark px-6 py-8 rounded-2 min-w-150 position-relative d-flex justify-content-between align-items-center mb-5">
                                    <a href="" class="text-muted fw-bold fs-6">Total Pending Orders </a>
                                    <span class="count text-muted fw-bold fs-1">
                                    <img src="{{ asset('assets/assets/media/loader-gray.gif') }}" style="width: 40px;display: none;" class="sales_order_to_invoice_lead_time_loader_img"> 
                                    <span class="sales_order_to_invoice_lead_time_count">{{@$dashboard['total_pending_order']}}</span>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-sm-5 mb-md-0 mb-lg-0">
                                <div class="bg-light-warning px-6 py-8 rounded-2 min-w-150 position-relative d-flex justify-content-between align-items-center mb-5">
                                    <a href="" class="text-warning fw-bold fs-6">Total On Process Orders</a>
                                    <span class="count text-warning fw-bold fs-1">
                                    <img src="{{ asset('assets/assets/media/loader-gray.gif') }}" style="width: 40px;display: none;" class="sales_order_to_invoice_lead_time_loader_img"> 
                                    <span class="sales_order_to_invoice_lead_time_count">{{@$dashboard['total_on_process_order']}}</span>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-sm-5 mb-md-0 mb-lg-0">
                                <div class="bg-light-primary px-6 py-8 rounded-2 min-w-150 position-relative d-flex justify-content-between align-items-center mb-5">
                                    <a href="" class="text-primary fw-bold fs-6">Total For Delivery Orders</a>
                                    <span class="count text-primary fw-bold fs-1">
                                    <img src="{{ asset('assets/assets/media/loader-gray.gif') }}" style="width: 40px;display: none;" class="sales_order_to_invoice_lead_time_loader_img"> 
                                    <span class="sales_order_to_invoice_lead_time_count">{{@$dashboard['total_for_delivery_order']}}</span>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-sm-5 mb-md-0 mb-lg-0">
                                <div class="bg-light-success px-6 py-8 rounded-2 min-w-150 position-relative d-flex justify-content-between align-items-center mb-5">
                                    <a href="" class="text-success fw-bold fs-6">Total Delivered Orders</a>
                                    <span class="count text-success fw-bold fs-1">
                                    <img src="{{ asset('assets/assets/media/loader-gray.gif') }}" style="width: 40px;display: none;" class="sales_order_to_invoice_lead_time_loader_img"> 
                                    <span class="sales_order_to_invoice_lead_time_count">{{@$dashboard['total_delivered_order']}}</span>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-sm-5 mb-md-0 mb-lg-0">
                                <div class="bg-light-warning px-6 py-8 rounded-2 min-w-150 position-relative d-flex justify-content-between align-items-center mb-5">
                                    <a href="" class="text-warning fw-bold fs-6">Total Completed Orders</a>
                                    <span class="count text-warning fw-bold fs-1">
                                    <img src="{{ asset('assets/assets/media/loader-gray.gif') }}" style="width: 40px;display: none;" class="sales_order_to_invoice_lead_time_loader_img"> 
                                    <span class="sales_order_to_invoice_lead_time_count">{{@$dashboard['total_completed_order']}}</span>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-sm-5 mb-md-0 mb-lg-0">
                                <div class="bg-light-danger px-6 py-8 rounded-2 min-w-150 position-relative d-flex justify-content-between align-items-center mb-5">
                                    <a href="" class="text-danger fw-bold fs-6">Total Back Order Products</a>
                                    <span class="count text-danger fw-bold fs-1">
                                    <img src="{{ asset('assets/assets/media/loader-gray.gif') }}" style="width: 40px;display: none;" class="sales_order_to_invoice_lead_time_loader_img"> 
                                    <span class="sales_order_to_invoice_lead_time_count">{{@$dashboard['total_back_order']}}</span>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-sm-5 mb-md-0 mb-lg-0">
                                <div class="bg-light-info px-6 py-8 rounded-2 min-w-150 position-relative d-flex justify-content-between align-items-center mb-5">
                                    <a href="" class="text-info fw-bold fs-6">Total Overdue Invoices</a>
                                    <span class="count text-info fw-bold fs-1">
                                    <img src="{{ asset('assets/assets/media/loader-gray.gif') }}" style="width: 40px;display: none;" class="sales_order_to_invoice_lead_time_loader_img"> 
                                    <span class="sales_order_to_invoice_lead_time_count">{{@$dashboard['total_overdue_invoice']}}</span>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-sm-5 mb-md-0 mb-lg-0">
                                <div class="bg-light-dark px-6 py-8 rounded-2 min-w-150 position-relative d-flex justify-content-between align-items-center mb-5">
                                    <a href="" class="text-dark fw-bold fs-6">Total Overdue Amount</a>
                                    <span class="count text-dark fw-bold fs-1">
                                    <img src="{{ asset('assets/assets/media/loader-gray.gif') }}" style="width: 40px;display: none;" class="sales_order_to_invoice_lead_time_loader_img"> 
                                    <span class="sales_order_to_invoice_lead_time_count">{{@$dashboard['total_amount_of_overdue_invoices']}}</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>                        
                </div>
            </div>
            @endif
            <!--end::Col-->
            @endif
           <!--begin::Col-->
           @if(Auth::user()->role_id == 1)
           <div class="col-xl-6">
                <!-- Pending Orders -->
                <div class="card card-custom gutter-b">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column mb-5">
                            @if(count($local_order) > 0)
                            <span class="card-label font-weight-bolder fw-bolder text-danger mb-1">Pending Orders ({{ count($local_order) }})</span>
                            @else
                            <span class="card-label font-weight-bolder fw-bolder text-primary mb-1">Pending Orders</span>
                            @endif
                        </h3>
                    </div>
                    <div class="card-body pt-2">
                        @if(isset($local_order) && count($local_order) > 0)
                            <div class="d-flex mb-8">
                                <div class="d-flex flex-column flex-grow-1 my-lg-0 my-2 pr-3">
                                    <div class="d-flex pt-2">
                                        @if(count($local_order) > 0)
                                        <a href="{{ route('orders.panding-orders') }}" class="btn btn-light-primary font-weight-bolder py-2 font-size-sm">View All</a>
                                        <a href="#" class="btn btn-light-primary font-weight-bolder py-2 font-size-sm mx-5 push-all-order">Push All</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @else
                        <div class="d-flex mb-8">
                            <div class="d-flex flex-column flex-grow-1 my-lg-0 my-2 pr-3">
                                <span class="text-dark-75 font-weight-bolder font-size-lg mb-2">No Pending Order to push.</span>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-xl-6">
                <!-- Pending Promotion -->
                <div class="card card-custom gutter-b">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column mb-5">
                            @if(count($promotion) > 0)
                            <span class="card-label fw-bolder text-danger mb-1">Pending Promotion ({{ count($promotion) }})</span>
                            @else
                            <span class="card-label fw-bolder text-primary mb-1">Pending Promotion</span>
                            @endif
                        </h3>
                    </div>
                    <div class="card-body pt-2">
                        @if(isset($promotion) && count($promotion) > 0)
                            <div class="d-flex mb-8">
                                <div class="d-flex flex-column flex-grow-1 my-lg-0 my-2 pr-3">
                                    <div class="d-flex pt-2">
                                        @if(count($promotion) > 0)
                                        <a href="{{ route('orders.pending-promotion') }}" class="btn btn-light-primary font-weight-bolder py-2 font-size-sm">View All</a>
                                        <a href="#" class="btn btn-light-primary font-weight-bolder py-2 font-size-sm mx-5 push-all-promotion">Push All</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @else
                        <div class="d-flex mb-8">
                            <div class="d-flex flex-column flex-grow-1 my-lg-0 my-2 pr-3">
                                <span class="text-dark-75 font-weight-bolder font-size-lg mb-2">No Pending Promotion to push.</span>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
                <div class="card mb-5 mb-xl-8">
                    <div class="card-body">
                        <div class="row mb-5 ">
                            <div class="col-md-12 d-flex justify-content-end">
                                <a href="javascript:" class="btn btn-icon btn-bg-light btn-active-color-success btn-sm sync-lead-time" title="Sync" ><i class="fa fa-sync"></i></a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-sm-5 mb-md-0 mb-lg-0">
                                <div class="bg-light-warning px-6 py-8 rounded-2 min-w-150 position-relative d-flex justify-content-between align-items-center">
                                    <a href="{{ route('reports.sales-order-to-invoice-lead-time-report.index') }}" class="text-warning fw-bold fs-6">Sales Order to Invoice Lead Time </a>
                                    <span class="count text-warning fw-bold fs-1">
                                    <img src="{{ asset('assets/assets/media/loader-gray.gif') }}" style="width: 40px;display: none;" class="sales_order_to_invoice_lead_time_loader_img"> 
                                    <span class="sales_order_to_invoice_lead_time_count">{{ @$sales_order_to_invoice_lead_time->value ? @$sales_order_to_invoice_lead_time->value." Day(s)" : "" }}</span>
                                    </span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="bg-light-success px-6 py-8 rounded-2 min-w-150 position-relative d-flex justify-content-between align-items-center">
                                    <a href="{{ route('reports.invoice-to-delivery-lead-time-report.index') }}" class="text-success fw-bold fs-6">Invoice to Delivery Lead Time </a>
                                    <span class="count text-success fw-bold fs-1">
                                    <img src="{{ asset('assets/assets/media/loader-gray.gif') }}" style="width: 40px;display: none;" class="invoice_to_delivery_lead_time_loader_img"> 
                                    <span class="invoice_to_delivery_lead_time_count">{{ @$invoice_to_delivery_lead_time->value ? @$invoice_to_delivery_lead_time->value." Day(s)" : "" }}</span>
                                    </span>
                              </div>
                            </div>
                        </div>                        
                    </div>
                </div>
            </div>
           @endif
        </div>

        @if(@Auth::user()->role_id == 1)
        <div class="row gy-5 g-xl-8">
            <!-- Promotion Report -->
            <div class="col-xl-6">
                <div class="card card-xl-stretch mb-xl-8">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <a href="{{ route('reports.promotion-report.index') }}" class="text-dark text-hover-primary fw-bolder fs-3">Promotion Reports</a>
                        </h3>
                        
                    </div>

                    <div class="card-body">
                        <!--begin::Chart-->
                        <div id="promotion_report_cart" style="height: 320px; min-height: 320px;">

                        </div>
                        <!--end::Chart-->
                    </div>
                    <!--end::Body-->
                </div>
                <!--end::Charts Widget 1-->
            </div>

            <!-- Product Report-->
            <div class="col-xl-6">
                <div class="card card-xl-stretch mb-xl-8">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <a href="{{ route('reports.product-report.index') }}" class="text-dark text-hover-primary fw-bolder fs-3">Product Reports</a>
                        </h3>
                        
                    </div>

                    <div class="card-body">
                        <!--begin::Chart-->
                        <div id="product_report_cart" style="height: 320px; min-height: 320px;">

                        </div>
                        <!--end::Chart-->
                    </div>
                    <!--end::Body-->
                </div>
                <!--end::Charts Widget 1-->
            </div>
        </div>

        <div class="row gy-5 g-xl-8">
            <!-- Back Order Report-->
            <div class="col-xl-12">
                <div class="card card-xl-stretch mb-xl-8">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <a href="{{ route('reports.back-order-report.index') }}" class="text-dark text-hover-primary fw-bolder fs-3">Back Order Report</a>
                        </h3>
                        
                    </div>

                    <div class="card-body">
                        <!--begin::Chart-->
                        <div id="back_order_report_cart" style="height: 320px; min-height: 320px;">

                        </div>
                        <!--end::Chart-->
                    </div>
                    <!--end::Body-->
                </div>
                <!--end::Charts Widget 1-->
            </div>
        </div>


        <div class="row gy-5 g-xl-8">
            <!-- Promotion Report -->
            <div class="col-xl-6">
                <div class="card card-xl-stretch mb-xl-8">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <a href="#" class="text-dark text-hover-primary fw-bolder fs-3">Customer Buying</a>
                        </h3>                        
                    </div>
                    <div class="card-body">
                      <div id="active_customer_graph" class="h-500px" style="height: 320px; min-height: 320px;"></div>
                    </div>
                    <!--end::Body-->
                </div>
                <!--end::Charts Widget 1-->
            </div>
        </div>

        <div class="row gy-5 g-xl-8">
            <!-- Back Order Report-->
            <div class="col-xl-12">
                <div class="card card-xl-stretch mb-xl-8">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <a href="#" class="text-dark text-hover-primary fw-bolder fs-3">Top Performing Products</a>
                        </h3>
                        <input type="button" name="this_month" id="this_month" value="This Month" class="btn btn-primary btn-sm">
                        <input type="button" name="this_week" id="this_week" value="This Week" class="btn btn-primary btn-sm">
                        <input type="button" name="all_time" id="all_time" value="All Time" class="btn btn-primary btn-sm">
                        <div class="">
                          <div class="input-icon">
                            <input type="text" class="form-control form-control-lg form-control-solid" placeholder="Select date range" name = "filter_date_range" id="kt_daterangepicker_1" readonly>
                            <span>
                            </span>
                          </div>
                        </div>
                        <select id="total_performing_type" class="">
                            <option value="Quantity">Quantity</option>
                            <option value="Liters">Liters</option>
                            <option value="Amount">Amount</option>
                        </select>
                        
                    </div>

                    <div class="card-body">
                        <!--begin::Chart-->
                        <div id="top_performing_products_graph" style="height: 320px; min-height: 320px;">

                        </div>
                        <!--end::Chart-->
                    </div>
                    <!--end::Body-->
                </div>
                <!--end::Charts Widget 1-->
            </div>
        </div>

        <div id="hover">
        
        </div>
        
        @endif

        @if(@Auth::user()->role_id == 4)
        <div class="row gy-5 g-xl-8">
            <!-- Back Order Report-->
            <div class="col-xl-12">
                <div class="card card-xl-stretch mb-xl-8">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <a href="#" class="text-dark text-hover-primary fw-bolder fs-3">Status Count</a>
                        </h3>
                        
                    </div>

                    <div class="card-body">
                        <!--begin::Chart-->
                        <div id="status_count_chart" style="height: 320px; min-height: 320px;">

                        </div>
                        <!--end::Chart-->
                    </div>
                    <!--end::Body-->
                </div>
                <!--end::Charts Widget 1-->
            </div>
        </div>

        <div class="row gy-5 g-xl-8">
            <!-- Promotion Report -->
            <div class="col-xl-6">
                <div class="card card-xl-stretch mb-xl-8">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <a href="#" class="text-dark text-hover-primary fw-bolder fs-3">Top Products</a>
                        </h3>
                        <select id="total_performing_type" class="">
                            <option value="Quantity">Quantity</option>
                            <option value="Liters">Liters</option>
                            <option value="Amount">Amount</option>
                        </select>
                        <select id="total_performing_orders" class="">
                            <option value="order">Order</option>
                            <option value="invoice">Invoice</option>
                            <option value="back_order">Back Order</option>
                        </select>
                        
                    </div>

                    <div class="card-body">
                        <!--begin::Chart-->
                        <div id="" style="height: 320px; min-height: 320px;">
                            <table id="top_products_per_quantity" class="table table-row-gray-300 align-middle gs-0 gy-4 table-bordered display nowrap">
                                <thead>
                                    <tr> 
                                        <td>Top</td>
                                        <td>Name</td>
                                        <td>Total Qty</td>
                                    </tr>
                                </thead>
                                <tbody id="top_products_per_quantity_tbody">
                                    
                                </tbody>
                            </table>
                        </div>
                        <!--end::Chart-->
                    </div>
                    <!--end::Body-->
                </div>
                <!--end::Charts Widget 1-->
            </div>
            <div class="col-xl-6">
                <div class="card card-xl-stretch mb-xl-8">
                    
                    <div class="card-body">
                        <!--begin::Chart-->
                        <div id="top_products_per_quantity_chart" class="h-500px" style="height: 320px; min-height: 320px;">

                        </div>
                        <!--end::Chart-->
                    </div>
                    <!--end::Body-->
                </div>
                <!--end::Charts Widget 1-->
            </div>            
        </div>        

        <div class="row gy-5 g-xl-8">
            <!-- Back Order Report-->
            <div class="col-xl-12">
                <div class="card card-xl-stretch mb-xl-8">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <a href="#" class="text-dark text-hover-primary fw-bolder fs-3">Recent Order Report</a>
                        </h3>
                        
                    </div>

                    <div class="card-body">
                        <!--begin::Chart-->
                        <div id="" style="height: 320px; min-height: 310px;">
                            <table id="recent_order_report" class="table table-row-gray-300 align-middle gs-0 gy-4 table-bordered display nowrap">
                                <thead>
                                    <tr>
                                        <td>No</td>
                                        <td>Order #</td>
                                        <td>Total Amount</td>
                                        <td>Date</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($orders))
                                    @foreach($orders as $k=>$val)
                                    <tr>
                                        <td>{{$k+1}}</td>
                                        <td>{{@$val->u_omsno}} </td>
                                        <td>₱ {{number_format_value(@$val->doc_total)}}</td>
                                        <td>{{date('m/d/Y', strtotime(@$val->created_at))}}</td>
                                    </tr>
                                    @endforeach
                                    @else
                                    <tr>
                                        <td colspan="4">No Data Available</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <!--end::Chart-->
                    </div>
                    <!--end::Body-->
                </div>
                <!--end::Charts Widget 1-->
            </div>
        </div>

        <div class="row gy-5 g-xl-8">
            <!-- Back Order Report-->
            <div class="col-xl-12">
                <div class="card card-xl-stretch mb-xl-8">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <a href="#" class="text-dark text-hover-primary fw-bolder fs-3">Order to invoice lead time</a>
                        </h3>
                        
                    </div>

                    <div class="card-body">
                        <!--begin::Chart-->
                        <div id="" style="height: 310px; min-height: 310px;">
                            <table id="order_to_invoice_lead_time" class="table table-row-gray-300 align-middle gs-0 gy-4 table-bordered display nowrap">
                                <thead>
                                    <tr>
                                        <td>No</td>
                                        <td>Order #</td>
                                        <td>Order Date</td>
                                        <td>Invoice #</td>
                                        <td>Invoice Date</td>
                                        <td>Lead Time(days)</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($invoice_lead))
                                    @foreach($invoice_lead as $k=>$val)
                                    <tr>
                                        <td>{{$k+1}}</td>
                                        <td>{{@$val->order->doc_num}}</td>
                                        <td>{{date('m/d/Y', strtotime(@$val->order->doc_date))}}</td>
                                        <td>{{@$val->doc_num}}</td>
                                        <td>{{date('m/d/Y', strtotime(@$val->doc_date))}}</td>
                                        <?php
                                            $endDate = $val->created_at;
                                            $startDate = $val->order->created_at;

                                            $days = (strtotime($endDate) - strtotime($startDate)) / (60 * 60 * 24);
                                        ?>
                                        <td>{{$days}}</td>
                                    </tr>
                                    @endforeach
                                    @else
                                    <tr>
                                        <td colspan="4">No Data Available</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <!--end::Chart-->
                    </div>
                    <!--end::Body-->
                </div>
                <!--end::Charts Widget 1-->
            </div>
        </div>

        <div class="row gy-5 g-xl-8">
            <!-- Back Order Report-->
            <div class="col-xl-12">
                <div class="card card-xl-stretch mb-xl-8">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <a href="#" class="text-dark text-hover-primary fw-bolder fs-3">Invoice to delivery lead time</a>
                        </h3>
                        
                    </div>

                    <div class="card-body">
                        <!--begin::Chart-->
                        <div id="" style="height: 310px; min-height: 310px;">
                            <table id="invoice_to_delivery_lead_time" class="table table-row-gray-300 align-middle gs-0 gy-4 table-bordered display nowrap">
                                <thead>
                                    <tr>
                                        <td>No</td>
                                        <td>Order #</td>
                                        <td>Invoice #</td>
                                        <td>Invoice Date</td>
                                        <td>Delivery Date</td>
                                        <td>Lead Time(days)</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($delivery_lead))
                                    @foreach($delivery_lead as $k=>$val)
                                    <tr>
                                        <td>{{$k+1}}</td>
                                        <td>{{@$val->order->doc_num}}</td>      
                                        <td>{{@$val->doc_num}}</td>
                                        <td>{{date('m/d/Y', strtotime(@$val->doc_date))}}</td>
                                        <td>{{date('m/d/Y', strtotime(@$val->u_delivery))}}</td>
                                        <?php
                                            $endDate = $val->created_at;
                                            $startDate = $val->u_delivery;

                                            $days = (strtotime($endDate) - strtotime($startDate)) / (60 * 60 * 24);
                                        ?>
                                        <td>{{$days}}</td>
                                    </tr>
                                    @endforeach
                                    @else
                                    <tr>
                                        <td colspan="4">No Data Available</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <!--end::Chart-->
                    </div>
                    <!--end::Body-->
                </div>
                <!--end::Charts Widget 1-->
            </div>
        </div>
        @endif
     </div>
     <!--end::Container-->
  </div>
  <!--end::Post-->
</div>
<!--end::Content-->
@endsection

@push('js')
<script src="{{ asset('assets') }}/assets/plugins/custom/flotcharts/flotcharts.bundle.js"></script>
<script src="http://www.flotcharts.org/flot/source/jquery.flot.legend.js"></script>
@if(@Auth::user()->role_id == 1)
<script>
    getData();

    var data = [];
    var category = [];

    var options = {
        series: data,
        chart: {
            type: 'bar',
            height: 350,
            toolbar: {
                show: false
            }
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '35%',
                endingShape: 'rounded'
            },
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        xaxis: {
            categories: category,
        },
        yaxis: {
            title: {
                text: ''
            }
        },
        fill: {
            opacity: 1
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return  val
                }
            }
        },
        colors:['#F33A6A']
    };

    var topPerformingProduct = new ApexCharts(document.querySelector("#top_performing_products_graph"), options);
    topPerformingProduct.render();

    $(document).on('click', '.push-all-order', function(event) {
        event.preventDefault();

        Swal.fire({
            title: 'Are you sure want to push all pending orders?',
            //text: "Once deleted, you will not be able to recover this record!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, do it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route('orders.push-all-order') }}',
                    method: "POST",
                    data: {
                            _token:'{{ csrf_token() }}',
                        }
                })
                .done(function(result) {
                    if(result.status == false){
                    toast_error(result.message);
                    }else{
                    toast_success(result.message);
                    setTimeout(function(){
                        window.location.reload();
                    },500)
                    }
                })
                .fail(function() {
                    toast_error("error");
                });
            }
        })
    });

    $(document).on('click', '.push-all-promotion', function(event) {
      event.preventDefault();
      var id = $(this).data('id');
      Swal.fire({
        title: 'Are you sure want to push all promotion?',
        //text: "Once deleted, you will not be able to recover this record!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, do it!'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: '{{ route('orders.push-all-promotion') }}',
            method: "POST",
            data: {
                    _token:'{{ csrf_token() }}',
                }
          })
          .done(function(result) {
            if(result.status == false){
              toast_error(result.message);
            }else{
              toast_success(result.message);
              setTimeout(function(){
                window.location.reload();
              },500)
            }
          })
          .fail(function() {
            toast_error("error");
          });
        }
      })
    });

    function getData(){
        // Get Promotion Report Chart Data
        $.ajax({
            url: '{{ route('reports.promotion-report.get-chart-data') }}',
            method: "POST",
            data: {
                    _token:'{{ csrf_token() }}',
                }
        })
        .done(function(result) {
            if(result.status == false){
                toast_error(result.message);
            }else{
                render_promotion_graph(result.data, result.category)
            }
        })
        .fail(function() {
            toast_error("error");
        });

        // Get Product Report Chart Data
        $.ajax({
            url: '{{ route('reports.product-report.get-chart-data') }}',
            method: "POST",
            data: {
                    _token:'{{ csrf_token() }}',
                }
        })
        .done(function(result) {
            if(result.status == false){
                toast_error(result.message);
            }else{
                render_product_graph(result.data, result.category)
            }
        })
        .fail(function() {
            toast_error("error");
        });


        // Get Back Order Report Chart Data
        $.ajax({
            url: '{{ route('reports.back-order-report.get-chart-data') }}',
            method: "POST",
            data: {
                    _token:'{{ csrf_token() }}',
                }
        })
        .done(function(result) {
            if(result.status == false){
                toast_error(result.message);
            }else{
                render_back_order_graph(result.data, result.category)
            }
        })
        .fail(function() {
            toast_error("error");
        }); 

        // Get Cutomer Buying chart Data
        $.ajax({
            url: '{{ route('reports.customer-buying.get-chart-data') }}',
            method: "POST",
            data: {
                    _token:'{{ csrf_token() }}',
                }
        })
        .done(function(result) {
            if(result.status == false){
                toast_error(result.message);
            }else{
                render_customer_graph(result.data)
            }
        })
        .fail(function() {
            toast_error("error");
        });

        // Get Top performing Product Report Chart Data
        top_perform_product_data();
    }

    $(document).on("click","#this_month",function(){
        var range = 'this_month';
        top_perform_product_data(range);
    });

    $(document).on("click","#this_week",function(){
        var range = 'this_week';
        top_perform_product_data(range);
    });

    $(document).on("click","#all_time",function(){
        var range = 'null';
        top_perform_product_data(range);
    });

    $('#kt_daterangepicker_1').on('apply.daterangepicker', function(ev, picker){
        var range = 'custom_date';
        top_perform_product_data(range);
    });

    $(document).on("change","#total_performing_type",function(){
        var range = 'null';
        top_perform_product_data(range);
    });


    function top_perform_product_data(range){
        var type = $("#total_performing_type").val();
        if($("#kt_daterangepicker_1").val() == ""){
            var custom_date = '';
        }else{
            var custom_date = $("#kt_daterangepicker_1").val();
        }
        // Get Top performing Product Report Chart Data
        $.ajax({
            url: "{{ route('reports.top-performing-graph.get-chart-data') }}",
            method: "POST",
            data: {
                    _token:'{{ csrf_token() }}',
                    'type':type,
                    'range':range,
                    'custom':custom_date,
                }
        })
        .done(function(result) {
            if(result.status == false){
                toast_error(result.message);
            }else{
                category = result.category;
                topPerformingProduct.updateOptions({                
                    xaxis: { categories: category },
                });
                topPerformingProduct.updateSeries([
                    {
                      name: result.data[0].name,  
                      data: result.data[0].data
                    }
                ]);
            }
        })
        .fail(function() {
            toast_error("error");
        });
    }

    function labelFormatter(label, series) {
      return "<div class='default_label' style='font-size:8pt; text-align:center; padding:2px; color:white;'>" + label + "<br/>" + Math.round(series.percent) + "%</div>";
    }

    $('#active_customer_graph').bind("plothover", function(event, pos, obj) {
      if(obj){
        var percent = Math.round(obj.series.percent);
        $("#hover").html("<span style='font-weight:bold; color:" + obj.series.color + "'>" + obj.series.label + " (" + percent + "%)</span>");
        $('#hover').css({'position':'absolute','display':'block','left':pos.pageX,'top':pos.pageY}); 
      }
      else {
        $('#hover').css('display','none');
      }
    });

    $('#top_products_per_quantity_chart').bind("plothover", function(event, pos, obj) {
      if(obj){
        var percent = Math.round(obj.series.percent);
        $("#hover").html("<span style='font-weight:bold; color:" + obj.series.color + "'>" + obj.series.label + " (" + percent + "%)</span>");
        $('#hover').css({'position':'absolute','display':'block','left':pos.pageX,'top':pos.pageY}); 
      }
      else {
        $('#hover').css('display','none');
      }
    });

    $('#top_product_per_amount_chart').bind("plothover", function(event, pos, obj) {
      if(obj){
        var percent = Math.round(obj.series.percent);
        $("#hover").html("<span style='font-weight:bold; color:" + obj.series.color + "'>" + obj.series.label + " (" + percent + "%)</span>");
        $('#hover').css({'position':'absolute','display':'block','left':pos.pageX,'top':pos.pageY}); 
      }
      else {
        $('#hover').css('display','none');
      }
    });

    function render_customer_graph(result){
      var data = [

            { label: "Active", data: result.activeCustomers, color: '#FAA0A0' },
            { label: "Inactive", data: result.inactiveCustomers, color: '#F33A6A' }, 
            { label: "Active with Orders", data: result.customerWithOrder, color: '#FFF5EE' },            
          ];
      $.plot('#active_customer_graph', data, {
        series: {
          pie: {
            show: true,
            innerRadius:0.5,
            radius: 1,

            label: {
              show: true,
              radius: 3/4,
              formatter: labelFormatter,
              threshold: 0.1,
            }
          }
        },
        legend: {
          show: false
        },
        grid: {
          hoverable: true,
          clickable: true
        },
      });

      if(result.inactiveCustomers == 0 && result.activeCustomers == 0){
        $('#active_customer_graph').removeClass('h-500px');
      }
    }

    function render_promotion_graph(data, category){

        var options = {
            series: data,
            chart: {
                type: 'bar',
                height: 350,
                toolbar: {
                    show: false
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '35%',
                    endingShape: 'rounded'
                },
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: category,
            },
            yaxis: {
                title: {
                    text: ''
                }
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return  val
                    }
                }
            },
            colors:['#A1A5B7', '#009EF7', '#dc3545']
        };

        var promotionChart = new ApexCharts(document.querySelector("#promotion_report_cart"), options);
        if (promotionChart.ohYeahThisChartHasBeenRendered) {
            promotionChart.destroy();
        }
        promotionChart.render();
    }

    function render_product_graph(data, category){

        var options = {
            series: data,
            chart: {
                type: 'bar',
                height: 350,
                toolbar: {
                    show: false
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '35%',
                    endingShape: 'rounded'
                },
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: category,
            },
            yaxis: {
                title: {
                    text: ''
                }
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return  val
                    }
                }
            },
            colors:['#A1A5B7', '#009EF7', '#dc3545']
        };

        var productChart = new ApexCharts(document.querySelector("#product_report_cart"), options);
        if (productChart.ohYeahThisChartHasBeenRendered) {
            productChart.destroy();
        }
        productChart.render();
    }

    function render_back_order_graph(data, category){

        var options = {
            series: data,
            chart: {
                type: 'bar',
                height: 350,
                toolbar: {
                    show: false
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '35%',
                    endingShape: 'rounded'
                },
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: category,
            },
            yaxis: {
                title: {
                    text: ''
                }
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return  val
                    }
                }
            },
            colors:['#A1A5B7', '#009EF7', '#dc3545']
        };

        var backOrderChart = new ApexCharts(document.querySelector("#back_order_report_cart"), options);
        if (backOrderChart.ohYeahThisChartHasBeenRendered) {
            backOrderChart.destroy();
        }
        backOrderChart.render();
    }

    function render_top_performing_product_graph(data, category){

        var options = {
            series: data,
            chart: {
                type: 'bar',
                height: 350,
                toolbar: {
                    show: false
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '35%',
                    endingShape: 'rounded'
                },
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: category,
            },
            yaxis: {
                title: {
                    text: ''
                }
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return  val
                    }
                }
            },
            colors:['#F33A6A']
        };

        var topPerformingProduct = new ApexCharts(document.querySelector("#top_performing_products_graph"), options);
        topPerformingProduct.render();
    }

    $('[name="filter_company"]').select2({
        ajax: {
            url: "{{ route('common.getBusinessUnits') }}",
            type: "post",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    _token: "{{ csrf_token() }}",
                    search: params.term,
                    filter_company: $('[name="filter_company"]').find('option:selected').val(),
                };
            },
            processResults: function (response) {
                return {
                    results: response
                };
            },
            cache: true
        },
        placeholder: 'Businnes Unit',
        // minimumInputLength: 1,
        multiple: false,
    });


    @if(userrole() == 1)

        @if(is_null(@$sales_order_to_invoice_lead_time->value) || is_null(@$invoice_to_delivery_lead_time->value))
            render_report_data();
        @endif

        function render_report_data(){
            $('.sales_order_to_invoice_lead_time_loader_img, .invoice_to_delivery_lead_time_loader_img').show();
            $('.sales_order_to_invoice_lead_time_count, .invoice_to_delivery_lead_time_count').text("");
            $.ajax({
                url: '{{ route('home.get-report-data') }}',
                method: "POST",
                data: {
                        _token:'{{ csrf_token() }}',
                    }
            })
            .done(function(result) {
                if(result.status){
                    // toast_success(result.message);

                    $('.sales_order_to_invoice_lead_time_count').text(result.data.sales_order_to_invoice_lead_time + " Day(s)");
                    $('.invoice_to_delivery_lead_time_count').text(result.data.invoice_to_delivery_lead_time + " Day(s)");
                }else{
                    toast_error(result.message);
                }
                $('.sales_order_to_invoice_lead_time_loader_img, .invoice_to_delivery_lead_time_loader_img').hide();
            })
            .fail(function() {
                toast_error("error");
                $('.sales_order_to_invoice_lead_time_loader_img, .invoice_to_delivery_lead_time_loader_img').hide();
            });  
        }


        $(document).on('click', '.sync-lead-time', function(event) {
            event.preventDefault();

            Swal.fire({
                title: 'Are you sure want to sync details?',
                text: "It may take some time to sync details.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, do it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    render_report_data();
                }
            })
        });
    @endif  

      
</script>
@endif
@if(@Auth::user()->role_id == 4)
<script type="text/javascript">
    // Get Status Counting
    $.ajax({
        url: '{{ route('reports.status-count-chart.get-chart-data') }}',
        method: "POST",
        data: {
                _token:'{{ csrf_token() }}',
            }
    })
    .done(function(result) {
        if(result.status == false){
            toast_error(result.message);
        }else{
            render_status_chart_graph(result.data, result.category)
        }
    })
    .fail(function() {
        toast_error("error");
    });

    function render_status_chart_graph(data, category){

        var options = {
            series: data,
            chart: {
                type: 'bar',
                height: 350,
                toolbar: {
                    show: false
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '35%',
                    endingShape: 'rounded'
                },
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: category,
            },
            yaxis: {
                title: {
                    text: ''
                }
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return  val
                    }
                }
            },
            colors:['#0000FF']
        };

        var backOrderChart = new ApexCharts(document.querySelector("#status_count_chart"), options);
        if (backOrderChart.ohYeahThisChartHasBeenRendered) {
            backOrderChart.destroy();
        }
        backOrderChart.render();
    }

    getProductData();

    $(document).on("change","#total_performing_type",function(){
        getProductData();
    });

    $(document).on("change","#total_performing_orders",function(){
        getProductData();
    });

    function getProductData(){
        var type = $("#total_performing_type").val();
        var order = $("#total_performing_orders").val();
        // Get Top Product Data
        $.ajax({
            url: "{{ route('reports.back-order-report.get-product-data') }}",
            method: "POST",
            data: {
                    _token:'{{ csrf_token() }}',
                    type:type,
                    order:order,
                }
        })
        .done(function(result) {    
            if(result.status == false){
                toast_error(result.message);
            }else{
                var html = '';
                render_top_product_quantity_graph(result.data1);
                if(result.data != ""){
                    if(order == 'order'){
                        $.each(result.data, function( index, value ) {
                            if(type == 'Quantity' || type == 'Amount'){
                                html += '<tr><td>'+(index+1)+'</td><td>'+value.item_description+'</td>';
                            }
                            if(type == 'Quantity'){
                                html += '<td>'+(value.quantity).toLocaleString()+'</td>';
                            }else if(type == 'Liters'){
                                html += '<tr><td>'+(index+1)+'</td><td>'+value.product.item_name+'</td>';
                                html += '<td>'+(value.product.sales_unit_weight).toLocaleString()+'</td>';
                            }else if(type == 'Amount'){
                                html += '<td>'+(value.gross_total).toLocaleString()+'</td>';
                            }
                          html += '</tr>';
                        });
                    }else if(order == 'invoice'){
                        $.each(result.data, function( index, value ) {
                            if(type == 'Quantity' || type == 'Amount'){
                                html += '<tr><td>'+(index+1)+'</td><td>'+value.item_description+'</td>';
                            }
                            if(type == 'Quantity'){
                                html += '<td>'+(value.quantity).toLocaleString()+'</td>';
                            }else if(type == 'Liters'){
                                html += '<tr><td>'+(index+1)+'</td><td>'+value.product.item_name+'</td>';
                                html += '<td>'+(value.product.sales_unit_weight).toLocaleString()+'</td>';
                            }else if(type == 'Amount'){
                                html += '<td>'+(value.gross_total).toLocaleString()+'</td>';
                            }
                          html += '</tr>';
                        });
                    }else if(order == 'back_order'){
                        $.each(result.data, function( index, value ) {
                            if(type == 'Quantity' || type == 'Amount'){
                                html += '<tr><td>'+(index+1)+'</td><td>'+value.item_description+'</td>';
                            }
                            if(type == 'Quantity'){
                                html += '<td>'+(value.quantity).toLocaleString()+'</td>';
                            }else if(type == 'Liters'){
                                html += '<tr><td>'+(index+1)+'</td><td>'+value.product.item_name+'</td>';
                                html += '<td>'+(value.product.sales_unit_weight).toLocaleString()+'</td>';
                            }else if(type == 'Amount'){
                                html += '<td>'+(value.gross_total).toLocaleString()+'</td>';
                            }
                          html += '</tr>';
                        });
                    }
                }
                $('#top_products_per_quantity_tbody').html(html);
            }
        })
        .fail(function() {
            toast_error("error");
        });
    }

    function render_top_product_quantity_graph(result){ 
      var data = [
            { label: result[0].name, data: result[0].key, color: '#FFC300' },
            { label: result[1].name, data: result[1].key, color: '#33BBFF' }, 
            { label: result[2].name, data: result[2].key, color: '#FFA533' },
            { label: result[3].name, data: result[3].key, color: '#848BA5' },
            { label: result[4].name, data: result[4].key, color: '#DA80D5' },
            //{ label: "Others", data: result.others, color: '#90EE90' },
          ];
        console.log(result);
      $.plot('#top_products_per_quantity_chart', data, {
        series: {
          pie: {
            show: true,
            innerRadius:0.5,            
            radius: 1,

            label: {
              show: true,
              radius: 3/4,
              //formatter: labelFormatter,
              threshold: 0.1,
            }
          }
        },
        legend: {
          show: false
        },
        grid: {
          hoverable: true,
          clickable: true
        },
      });

    }

    

    // Get Top Product per Quantity Chart
    // $.ajax({
    //     url: "{{ route('reports.top-product-per-quantity-chart.get-chart-data') }}",
    //     method: "POST",
    //     data: {
    //             _token:'{{ csrf_token() }}',
    //         }
    // })
    // .done(function(result) {    
    //     if(result.status == false){
    //         toast_error(result.message);
    //     }else{
    //         render_top_product_quantity_graph(result.data1)
    //         render_top_product_amount_graph(result.data)
    //     }
    // })
    // .fail(function() {
    //     toast_error("error");
    // });    

</script>
 @endif
@endpush
