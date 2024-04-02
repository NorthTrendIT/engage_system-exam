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
            <div class="col-xl-3 @if(Auth::user()->role_id == 4) d-none @endif">
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
            {{-- <div class="col-xl-8">
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
            </div> --}}
            @endif
            <!--end::Col-->
            @endif
           <!--begin::Col-->
           @if( in_array(Auth::user()->role_id, [1,4,14]) )
           <div class="col-xl-2">
                <!-- Error Orders -->
                <div class="card" style="width: 166px; height: 150px;">
                    <div class="card-header border-0">
                        <h3 class="card-title align-items-start flex-column">   
                        </h3>
                    </div>
                    <div class="card-body text-center">
                        <span class="card-label font-weight-bolder fw-bolder text-danger">Error Orders</span>
                        <span class="card-label font-weight-bolder fw-bolder d-block fs-2">{{ (count($local_order) > 0) ? count($local_order) : 0 }}</span>
                        
                        <a href="{{ route('orders.panding-orders') }}" class="btn btn-primary btn-sm font-weight-bolder py-2 font-size-sm {{ (isset($local_order) && count($local_order) == 0) ? 'disabled' : ''}}">Push Orders</a>
                        <a href="#" class="btn btn-light-primary font-weight-bolder py-2 font-size-sm mx-5 push-all-order d-none">Push All</a>
                    </div>
                </div>
            </div>
            @if( in_array(Auth::user()->role_id, [4,14]) ) <!-- for agent and customer only -->
                <div class="col-xl-7 d-none">
                    <div class="card" style="height: 150px;">
                        {{-- <div class="card-header border-0">
                            <h3 class="card-title align-items-start flex-column">   
                            </h3>
                        </div> --}}
                        <div class="card-body text-center">
                            @if( in_array(Auth::user()->role_id, [14]) ) <!-- agent only -->
                                <div class="row">
                                    <select class="form-control form-control-sm form-control-solid" data-control="select2" data-hide-search="false" name="filter_customer_balance"  data-placeholder="Select Customer">
                                        <option value="{{@$default_customer_top_products['id']}}">{{ @$default_customer_top_products['card_name'].' (Code: '.@$default_customer_top_products->card_code.' -'.@$default_customer_top_products->sap_connection->db_name.')' }}</option>
                                    </select>
                                </div>
                            @endif
                            <div class="d-flex">
                                <div class="flex-column p-3">
                                    <div class="row g-0">
                                        <div class="col-md-5">
                                        <img src="data:image/png;base64,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" class="img-fluid rounded-start" alt="...">
                                        </div>
                                        <div class="col-md-7">
                                            <p class="card-text total_due_balance">{{ @$due_invoices['total_due'] }}</p>
                                            <h5 class="card-title">Balance</h5>
                                            {{-- <p class="card-text"><small class="text-muted">Last updated 3 mins ago</small></p> --}}
                                        </div>
                                    </div>
                                </div>

                                <div class="flex-column p-3">
                                    <div class="row g-0">
                                        <div class="col-md-5">
                                        <img src="data:image/png;base64,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" class="img-fluid rounded-start" alt="...">
                                        </div>
                                        <div class="col-md-7">
                                            <p class="card-text within_due_balance">{{ @$due_invoices['within_due'] }}</p>
                                            <h5 class="card-title">Within Due</h5>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex-column p-3">
                                    <div class="row g-0">
                                        <div class="col-md-5">
                                        <img src="data:image/png;base64,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" class="img-fluid rounded-start" alt="...">
                                        </div>
                                        <div class="col-md-7">
                                            <p class="card-text over_due_balance">{{ @$due_invoices['over_due'] }}</p>
                                            <h5 class="card-title">Over Due</h5>
                                            {{-- <p class="card-text"><small class="text-muted">Last updated 3 mins ago</small></p> --}}
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>
            @endif
            {{-- <div class="col-xl-6">
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
            </div> --}}

            <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12 d-none">
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
        <div class="row gy-5 g-xl-8 d-none">
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

        <div class="row gy-5 g-xl-8 d-none">
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


        {{-- <div class="row gy-5 g-xl-8 d-none">
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

        <div class="row gy-5 g-xl-8 d-none">
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
                            /**<input type="text" class="form-control form-control-lg form-control-solid" placeholder="Select date range" name = "filter_date_range" id="kt_daterangepicker_1" readonly> **/
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
        </div> --}}

        <div id="hover">
        
        </div>
        
        @endif

        @if(in_array(@Auth::user()->role_id, [1,4,14]))
        <div class="row gy-5 g-xl-8 mt-1" id="business_share_dashboard_div">
            <div class="col-xl-6" id="top-products-div2">
                <div class="card card-xl-stretch mb-xl-8">
                    
                    <div class="card-body">
                        <!--begin::Chart-->
                        <div class="text-center">
                            <i><h6>Top Products vs Total Sales</h6></i>
                            <button class="btn btn-primary " type="button" id="business-share-loader-canvas" disabled>
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                Loading...
                            </button>
                        </div>
                        <div id="bussiness_share_chart" class="h-350px" style="height: 320px; min-height: 250px;">
                        </div>
                        <!--end::Chart-->
                    </div>
                    <!--end::Body-->
                </div>
                <!--end::Charts Widget 1-->
            </div> 
        </div>

        <div class="row gy-5 g-xl-8" id="common_three_user_dasboard">
            <!-- Promotion Report -->
            <div class="col-xl-6">
                <div class="card card-xl-stretch mb-xl-8">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <a href="#" class="text-dark text-hover-primary fw-bolder fs-3"> TOP 20 PRODUCTS </a>
                        </h3>
                        <div class="d-flex">
                            <select id="total_performing_type" class="form-select form-select-sm d-inline-block">
                                <option value="Quantity">Quantity</option>
                                <option value="Liters">Liters</option>
                                <option value="Amount">Amount</option>
                            </select>
                            <select id="total_performing_orders" class="form-select form-select-sm">
                                <option value="order">Order</option>
                                <option value="invoice">Invoice</option>
                                <option value="back_order">Back Order</option>
                                {{-- <option value="over_served">Over Served</option> --}}
                            </select> 
                        </div>                       
                    </div>

                    <div class="card-body">
                        <div class="d-flex justify-content-end mb-2">
                            <div class="col-2 @if(in_array(Auth::user()->role_id, [4, 14])) d-none @endif">
                                <select id="total_performing_db" class="form-select form-select-sm">
                                    @foreach($company as $c)
                                        <option value="{{$c->id}}" {{($c->id === @$default_customer_top_products->real_sap_connection_id) ? 'selected' : ''}}>{{$c->company_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-3">
                                <select class="form-control form-control-sm form-control-solid" name="filter_customer_top_prod" data-control="select2" data-hide-search="false" data-placeholder="Select customer" data-allow-clear="true">
                                    <option value=""></option>
                                  </select>
                            </div>
                            <div class="col-3">
                                <div class="input-icon">
                                    <input type="text" class="form-control form-control-sm" placeholder="Select date range" name = "filter_date_range" id="kt_daterangepicker_1">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col">
                            <div class="d-block text-center">
                                <button class="btn btn-primary " type="button" id="top-products-loader" disabled>
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    Loading...
                                </button>
                            </div>
                            <!--begin::Chart-->
                            <div id="top-products-table-wrapper"  class="container table-responsive d-none">
                                <table id="top_products_per_quantity" class="table table-bordered table-striped display nowrap">
                                    <thead class="bg-dark text-white">
                                        <tr> 
                                            <td>Top</td>
                                            <td>Customer</td>
                                            <td>Code</td>
                                            <td>Product</td>
                                            <td>Total</td>
                                        </tr>
                                    </thead>
                                    <tbody id="top_products_per_quantity_tbody">
                                        
                                    </tbody>
                                </table>
                            </div>
                            <!--end::Chart-->
                            </div>
                        </div>
                    </div>
                    <!--end::Body-->
                </div>
                <!--end::Charts Widget 1-->
            </div>
            <div class="col-xl-6" id="top-products-div">
                <div class="card card-xl-stretch mb-xl-8">
                    
                    <div class="card-body">
                        <!--begin::Chart-->
                        <div class="text-center">
                            <i class="d-none"><h6>TOP CUSTOMER (Products)</h6></i>
                            <button class="btn btn-primary " type="button" id="top-products-loader-canvas" disabled>
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                Loading...
                            </button>
                        </div>
                        <div id="top_products_per_quantity_chart" class="h-350px" style="height: 320px; min-height: 250px;">
                        </div>
                        <!--end::Chart-->
                    </div>
                    <!--end::Body-->
                </div>
                <!--end::Charts Widget 1-->
            </div>            
        </div>

        <div class="row gy-5 g-xl-8" >
            <div class="col-xl-12" >
                <div class="card card-xl-stretch mb-xl-8">  
                    <div class="row ">
                        <div class="col-sm-12 text-center mt-2">
                            <h5 class="brandHeadTitle">Sales vs Target</h5>
                            <h6>(Brand)</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <!--begin::Chart-->
                            @php
                                $year1 = date("Y");
                                $endyear = $year1-10;
                            @endphp
                            <div class="row">
                                <label class="col-auto col-form-label col-form-label-sm" for="">Customer</label>
                                <div class="col-sm-3">
                                    <select class="form-control form-control-sm form-control-solid" data-control="select2" data-hide-search="false" name="filter_customer_brand" data-placeholder="Select">
                                        {{-- <option value=""></option> --}}
                                    </select>
                                </div>
                                <label class="col-auto col-form-label col-form-label-sm" for="">Brand</label>
                                <div class="col-sm-2">
                                    <select class="form-control form-control-sm form-control-solid select_brand" data-control="select2" data-hide-search="false" name="filter_brand" data-placeholder="Select">
                                        <option value=""></option>
                                    </select>
                                </div>
                                <label class="col-auto col-form-label col-form-label-sm" for="">Year</label>
                                <div class="col-sm-2">
                                    <select class="form-control form-control-sm form-control-solid" data-control="select2" data-hide-search="false" name="year_brand"  data-placeholder="Select">
                                        {{-- <option value=""></option> --}}
                                        @for ($year = $year1; $year >= $endyear; $year--)
                                            <option value="{{$year}}">{{ $year }}</option>
                                        @endfor  
                                    </select>
                                </div>
                                <div class="col-sm-1">
                                    <div class="d-flex justify-content-end ">
                                        <button class="btn btn-sm btn-primary" id="resync_brandchart-data">search</button>
                                        {{-- <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="targetBrandOptions" id="brandQtyOptions"  value="Quantity" checked>
                                            <label class="form-check-label" for="brandQtyOptions">Qty</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="targetBrandOptions" id="brandPercentageOptions"  value="Percentage">
                                            <label class="form-check-label" for="brandPercentageOptions">Percentage</label>
                                        </div> --}}
                                    </div>
                                </div>
                                <div class="col-sm-1 d-flex">
                                    <div class="align-self-center">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="brandRadio" id="quarterBrand" isCheck="no">
                                            <label class="form-check-label" for="quarterBrand">
                                              Quarter
                                            </label>
                                        </div>
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="radio" name="brandRadioYearComparison" id="yearComparisonBrand" isCheck="no">
                                            <label class="form-check-label" for="yearComparisonBrand" style="min-width: 115px !important;">
                                              Monthly Comparison
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="d-block text-center mt-4 d-none" id="brand-chart-loader">
                                    <button class="btn btn-primary mt-2" type="button"  disabled>
                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                        Loading...
                                    </button>
                                </div>
                                <div class="col">
                                    <div id="bdp_target_brand_column_chart" ></div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-12">
                                    <div class="container table-responsive">
                                        <table  class="table table-bordered table-striped display nowrap">
                                            <thead class="bg-dark text-white">
                                                <tr> 
                                                    <td>Month</td>
                                                    <td>Brand</td>
                                                    <td>Target</td>
                                                    <td>Actual</td>
                                                    <td>Short</td>
                                                    <td>Over</td>
                                                    <td>Percentage</td>
                                                    <td>Result</td>
                                                </tr>
                                            </thead>
                                            <tbody id="tbl_brand_target_tbody">
                                                
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        <!--end::Chart-->
                    </div>
                    <!--end::Body-->
                </div>
                <!--end::Charts Widget 1-->
            </div> 
        </div>
        
        
        <div class="row gy-5 g-xl-8" >
            <div class="col-xl-12" >
                <div class="card card-xl-stretch mb-xl-8">  
                    <div class="row ">
                        <div class="col-sm-12 text-center mt-2">
                            <h5 class="categoryHeadTitle">Sales vs Target</h5>
                            <h6>(Category)</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <!--begin::Chart-->
                            @php
                                $year1 = date("Y");
                                $endyear = $year1-10;
                            @endphp
                            <div class="row">
                                <label class="col-auto col-form-label col-form-label-sm" for="">Customer</label>
                                <div class="col-sm-3">
                                    <select class="form-control form-control-sm form-control-solid" data-control="select2" data-hide-search="false" name="filter_customer_category"  data-placeholder="Select">
                                        {{-- <option value=""></option> --}}
                                    </select>
                                </div>
                                <label class="col-auto col-form-label col-form-label-sm" for="">Category</label>
                                <div class="col-sm-2">
                                    <select class="form-control form-control-sm form-control-solid select_category" data-control="select2" data-hide-search="false" name="filter_category"  data-placeholder="Select">
                                        <option value=""></option>
                                    </select>
                                </div>
                                <label class="col-auto col-form-label col-form-label-sm" for="">Year</label>
                                <div class="col-sm-2">
                                    <select class="form-control form-control-sm form-control-solid" data-control="select2" data-hide-search="false" name="year_category" data-placeholder="Select">
                                        {{-- <option value=""></option> --}}
                                        @for ($year = $year1; $year >= $endyear; $year--)
                                            <option value="{{$year}}">{{ $year }}</option>
                                        @endfor  
                                    </select>
                                </div>
                                <div class="col-sm-1">
                                    <div class="d-flex justify-content-end ">
                                        <button class="btn btn-sm btn-primary" id="resync_categorychart-data">search</i></button>
                                    </div>
                                </div>
                                <div class="col-sm-1 d-flex">
                                    <div class="align-self-center">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="categoryRadio" id="quarterCategory" isCheck="no">
                                            <label class="form-check-label" for="quarterCategory">
                                              Quarter
                                            </label>
                                        </div>
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="radio" name="categoryRadioYearComparison" id="yearComparisonCategory" isCheck="no">
                                            <label class="form-check-label" for="yearComparisonCategory" style="min-width: 115px !important;">
                                              Monthly Comparison
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="d-block text-center mt-4 d-none" id="category-chart-loader">
                                    <button class="btn btn-primary mt-2" type="button"  disabled>
                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                        Loading...
                                    </button>
                                </div>
                                <div class="col">
                                    <div id="bdp_target_category_column_chart" ></div>
                                </div>
                            </div> 
                            
                            <div class="row">
                                <div class="col-12">
                                    <div class="container table-responsive">
                                        <table  class="table table-bordered table-striped display nowrap">
                                            <thead class="bg-dark text-white">
                                                <tr> 
                                                    <td>Month</td>
                                                    <td>Category</td>
                                                    <td>Target</td>
                                                    <td>Actual</td>
                                                    <td>Short</td>
                                                    <td>Over</td>
                                                    <td>Percentage</td>
                                                    <td>Result</td>
                                                </tr>
                                            </thead>
                                            <tbody id="tbl_category_target_tbody">
                                                
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            
                        <!--end::Chart-->
                    </div>
                    <!--end::Body-->
                </div>
                <!--end::Charts Widget 1-->
            </div> 
        </div>



        @endif
        @if(@Auth::user()->role_id == 4)
        <div class="row gy-5 g-xl-8">
            <div class="col-xl-6">
                <div class="card card-xl-stretch mb-5 mb-xl-8">
                    <div class="card-header border-0 pt-5 min-0">
                      <h5 class="text-info">Number of Sales Orders</h5>
                    </div>
                    <div class="card-body">
                      <div class="row">
                        <div class="bg-light-warning py-4 rounded-2 me-7 mb-2 min-w-150 col-box-6 position-relative">
                          <a href="javascript:" class="text-warning fw-bold fs-4">Total Pending </a>
                          <span class="count text-warning fw-bold fs-4 number_of_sales_orders_pending_count float-end text-end">0</span>
                        </div>
                      </div>
                      <div class="row">
                        <div class="bg-light-dark py-4 rounded-2 me-7 mb-2 min-w-150 col-box-6 position-relative">
                            <a href="javascript:" class="text-dark fw-bold fs-4">Total On Process</a>
                            <span class="count text-dark fw-bold fs-4 number_of_sales_orders_on_process_count float-end text-end">0</span>
                        </div>
                      </div>
                      <div class="row">
                        <div class="bg-light-info py-4 rounded-2 me-7 mb-2 min-w-150 col-box-6 position-relative d-none">
                            <a href="javascript:" class="text-info fw-bold fs-4">Total For Delivery</a>
                            <span class="count text-info fw-bold fs-4 number_of_sales_orders_for_delivery_count float-end text-end">0</span>
                        </div>
                      </div>
                      <div class="row">
                        <div class="bg-light-primary py-4 rounded-2 me-7 mb-2 min-w-150 col-box-6 position-relative">
                            <a href="javascript:" class="text-primary fw-bold fs-4">Total Partially Served</a>
                            <span class="count text-primary fw-bold fs-4 number_of_sales_orders_partially_served_count float-end text-end">0</span>
                        </div>
                      </div>
                      <div class="row">
                        <div class="bg-light-success py-4 rounded-2 me-7 mb-2 min-w-150 col-box-6 position-relative">
                            <a href="javascript:" class="text-success fw-bold fs-4">Total Completed</a>
                            <span class="count text-success fw-bold fs-4 number_of_sales_orders_completed_count float-end text-end">0</span>
                        </div>
                      </div>
                      <div class="row">
                        <div class="bg-light-danger py-4 rounded-2 me-7 mb-2 min-w-150 col-box-6 position-relative">
                            <a href="javascript:" class="text-danger fw-bold fs-4">Total Cancelled</a>
                            <span class="count text-danger fw-bold fs-4 number_of_sales_orders_cancelled_count float-end text-end">0</span>
                        </div>
                      </div>

                    </div>
                  </div>
            </div>
            <div class="col-xl-6">
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

        {{-- <div class="row gy-5 g-xl-8 d-none">
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

        <div class="row gy-5 g-xl-8 d-none">
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

        <div class="row gy-5 g-xl-8 d-none">
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
        </div> --}}
        @endif
     </div>
     <!--end::Container-->
  </div>
  <!--end::Post-->
</div>
<!--end::Content-->
@endsection

@push('css')
<link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<style>
    .custom-shadow{
        font-size:8pt;
        padding:2px; 
        color:white;
        text-shadow: 1px 1px #010101c7;
    }

    .custom-tooltip {
        background-color: #fff;
        border: 1px solid #ddd;
        padding: 10px;
        border-radius: 5px;
    }

    .flotTip {
        padding: 3px 5px;
        background-color: #000;
        z-index: 100;
        color: #fff;
        opacity: .80;
        filter: alpha(opacity=85);
    }
</style>
@endpush

@push('js')
<script src="{{ asset('assets') }}/assets/plugins/custom/flotcharts/flotcharts.bundle.js"></script>
{{-- <script src="http://www.flotcharts.org/flot/source/jquery.flot.legend.js"></script> --}}
{{-- <script src="https://envato.stammtec.de/themeforest/melon/plugins/flot/jquery.flot.min.js"></script>
<script src="https://envato.stammtec.de/themeforest/melon/plugins/flot/jquery.flot.pie.min.js"></script>
<script src="https://envato.stammtec.de/themeforest/melon/plugins/flot/jquery.flot.tooltip.min.js"></script> --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/flot.tooltip/0.9.0/jquery.flot.tooltip.js"></script>

<script type="text/javascript" src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script type="text/javascript">
$(document).ready(function() {
    /**@if(@Auth::user()->role_id == 1)

        // getData(); //previous charts hidden

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

            // @if(is_null(@$sales_order_to_invoice_lead_time->value) || is_null(@$invoice_to_delivery_lead_time->value))
            //     render_report_data();
            // @endif

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

        

    @endif **/


@if(in_array(@Auth::user()->role_id, [1,4,14]))

@if(@Auth::user()->role_id == 4)
    // Get Status Counting
    $.ajax({
        url: '{{ route('reports.sales-order-report.count-stat') }}',
        method: "GET",
        data: {
                _token:'{{ csrf_token() }}',
            }
    })
    .done(function(result) {
        if(result.status == false){
            toast_error(result.message);
        }else{
            $('.number_of_sales_orders_pending_count').text(result.data.pending);
            $('.number_of_sales_orders_cancelled_count').text(result.data.cancelled);
            $('.number_of_sales_orders_on_process_count').text(result.data.on_process);
            $('.number_of_sales_orders_partially_served_count').text(result.data.partially_served);
            $('.number_of_sales_orders_completed_count').text(result.data.completed);
        var response = [];
        response[0] = {
            name: "Total",
            data: [result.data.pending, result.data.on_process, result.data.partially_served, result.data.completed, result.data.cancelled]
        };

        render_status_chart_graph(response, ['Pending', 'On Process', 'Partially Served', 'Completed', 'Cancelled']);
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
            colors: [
                function ({ value, seriesIndex, dataPointIndex, w }) {
                    
                    var color = '';
                    switch(dataPointIndex){
                        case 0:
                            color = '#ffc700';
                            break;
                        case 1:
                            color = '#181c32';
                            break;
                        case 2:
                            color = '#009ef7';
                            break;
                        case 3:
                            color = '#50cd89';
                            break;
                        case 4:
                            color = '#f1416c';
                            break;
                        default:
                            color = '#D9534F';
                            break;
                    }
                    return color;
                }
            ]
        };

        var backOrderChart = new ApexCharts(document.querySelector("#status_count_chart"), options);
        if (backOrderChart.ohYeahThisChartHasBeenRendered) {
            backOrderChart.destroy();
        }
        backOrderChart.render();
    }
@endif

    $('[name="filter_customer_balance"]').on('change', function(){
        fetchDueBalance();
    });
    
    function fetchDueBalance(){
        $.ajax({
            url: "{{ route('fetchDueBalances') }}",
            method: "GET",
            data: {
                customer_id: [$('[name="filter_customer_balance"]').val()]
            }
        })
        .done(function(result) { 
            $('.total_due_balance').text(result.total_due);
            $('.within_due_balance').text(result.within_due);
            $('.over_due_balance').text(result.over_due);
        }).fail(function() {
            toast_error("error");
        });
    }

    var top_products_per_quantity = $('#top_products_per_quantity').DataTable({
                                        // processing: true,
                                        // serverSide: true,
                                        // ajax: {
                                        //     'url': "{{ route('reports.fetch-top-products') }}",
                                        //     'type': 'GET',
                                        //     headers: {
                                        //     'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                        //     },
                                        //     data: filter_datas
                                        // },
                                        // drawCallback: function(settings) {
                                        //     $('#top-products-loader').addClass('d-none');
                                        //     $('#top-products-loader-canvas').addClass('d-none');
                                        //     $('#top-products-table-wrapper').removeClass('d-none');
                                        //     $('#top_products_per_quantity_chart canvas').removeClass('d-none');
                                        // console.log(settings.json);
                                        // //do whatever  
                                        // },
                                        // columns: [
                                        //     {data: 'DT_RowIndex'},
                                        //     {data: 'customer'},
                                        //     {data: 'item'},
                                        //     {data: 'total'}
                                        // ],
                                        pageLength : 20,
                                        lengthMenu: [[10, 20, 30, 40, 50], [10, 20, 30, 40, 50]],
                                        // aoColumnDefs: [{ "bVisible": false, "aTargets": hide_targets }],
                                        columnDefs: [
                                                {
                                                    className: 'text-center',
                                                    targets: [0]
                                                },
                                                {
                                                    className: 'text-end',
                                                    targets: -1
                                                },
                                                // { orderable: false, targets: -1 } //last row
                                            ]
                                    });
                                    
    var total_amount_of_top_products = 0;
    var total_clicks = 0;
    var defaultCustomerforTopProducts = [];

    @if(!empty(@$default_customer_top_products))
        defaultCustomerforTopProducts.push(
            {
                id: {{@$default_customer_top_products->id}}, 
                text: `{!! @$default_customer_top_products->card_name !!}` + " (Code: " + '{{@$default_customer_top_products->card_code}}' + ")", 
                selected: true, 
                card_code: '{{@$default_customer_top_products->card_code}}', 
                sap_connection_id: '{{@$default_customer_top_products->real_sap_connection_id}}' 
            });
    @endif

    $('[name="filter_customer_top_prod"]').select2({
      ajax: {
        url: "{{route('customer-promotion.get-customer')}}",
        type: "post",
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                _token: "{{ csrf_token() }}",
                search: params.term,
                sap_connection_id: $('#total_performing_db').find('option:selected').val(),
            };
        },
        processResults: function (response) {
          return {
            results:  $.map(response, function (item) {
                          return {
                            text: item.card_name + " (Code: " + item.card_code + ")",
                            id: item.id,
                            card_code: item.card_code,
                            sap_connection_id: item.sap_connection_id
                          }
                      })
          };
        },
        cache: true
      }, 
      data: defaultCustomerforTopProducts
    });
    $('#kt_daterangepicker_1').daterangepicker({
        startDate: "{{ $quotation_date['startDate'] }}",
        endDate: "{{ $quotation_date['endDate'] }}",
    });

    getProductData();

    $(document).on("change",'#total_performing_type, #total_performing_orders, #total_performing_db, [name="filter_customer_top_prod"]',function(){
        total_amount_of_top_products = 0;
        total_clicks = 0;
        getProductData();
    });

    $('#kt_daterangepicker_1').on('apply.daterangepicker', function(ev, picker){
        total_amount_of_top_products = 0;
        total_clicks = 0;
        getProductData();
    });

    // $(document).on("change","#total_performing_orders",function(){
    //     getProductData();
    // });

    function getProductData(){
        top_products_per_quantity.clear().draw();
        
        $('#top-products-loader').removeClass('d-none');
        $('#top-products-loader-canvas').removeClass('d-none');
        $('#business-share-loader-canvas').removeClass('d-none');
        $('#top-products-table-wrapper').addClass('d-none');
        $('#top_products_per_quantity_chart canvas, span.pieLabel').remove();
        $('#bussiness_share_chart canvas, span.pieLabel').remove();

        var type = $("#total_performing_type").val();
        var order = $("#total_performing_orders").val();
        var filter_date_range = $('[name="filter_date_range"]').val();
        var filter_company = $('#total_performing_db').val();
        var filter_customer = $('[name="filter_customer_top_prod"]').val();

        var filter_datas = {
                    _token: '{{ csrf_token() }}',
                    type: type,
                    order: order,
                    filter_date_range: filter_date_range
                }

        var hide_targets = [];

        // $('#business_share_dashboard_div').removeClass('d-none');
        $('#business_share_dashboard_div').find('div.row, svg').remove(); 
        $('#top-products-div').prependTo('#business_share_dashboard_div');  
        $('#common_three_user_dasboard').find('.col-xl-6').addClass('col-xl-12'); 
        $('#top-products-div').find('h6').text( $('#common_three_user_dasboard').find('.card-title a').text() );
        $('#top-products-div').find('i').removeClass('d-none');
         
        var html = '<div class="form-check">'+
                    '<input class="form-check-input border border-5 border-white" type="checkbox" value="" id="flexCheckDefault" style="background-color: #034F84 ">'+
                    '<label class="form-check-label" for="flexCheckDefault">'+
                        'Top <b id="top_product_sales_count"></b> Product Sales'+
                    '</label>'+
                    '</div>'+
                    '<div class="form-check mt-1">'+
                    '<input class="form-check-input border border-5 border-white " type="checkbox" value="" id="flexCheckChecked" style="background-color: #FA7A35 ">'+
                    '<label class="form-check-label" for="flexCheckChecked">'+
                        'Remaining Product Sales'+
                    '</label>'+
                    '</div>';

        $('#bussiness_share_chart').after('<div class="row">'+html+'</div>');          
        
        @if(@Auth::user()->role_id == 1)
            // $('#total_performing_db').on('change', function(){
            //     $('[name="filter_customer_top_prod"]').val('').trigger('change');
            // });
            filter_datas['filter_company'] = filter_company;
            filter_datas['filter_customer_code'] = $('[name="filter_customer_top_prod"]').select2('data')[0]['card_code'];
        // @else
        //     $('#business_share_dashboard_div').addClass('d-none');
        @endif

        @if(@Auth::user()->role_id == 4)
            $('[name="filter_customer_top_prod"]').parent().remove();
            top_products_per_quantity.column( 1 ).visible( false );
        @endif

        @if(@Auth::user()->role_id == 14)
            filter_datas['filter_customer'] = filter_customer;
        @endif

        // Get Top Product Data
        $.ajax({
            // url: "{{ route('reports.back-order-report.get-product-data') }}",
            // method: "POST",
            url: "{{ route('reports.fetch-top-products') }}",
            method: "GET",
            data: filter_datas
        })
        .done(function(result) {  
            $('#top-products-loader').addClass('d-none');
            $('#top-products-loader-canvas').addClass('d-none');
            $('#business-share-loader-canvas').addClass('d-none');
            $('#top-products-table-wrapper').removeClass('d-none');          

            if(result.status == false){
                toast_error(result.message);
            }else{
                var html = '';
                if(result.data.length > 0){
                    $.each(result.data, function( index, value ) {
                        // html += '<tr>';
                        // html += '<td>'+(index+1)+'</td>';
                        // @if(@Auth::user()->role_id == 1)
                        // html += '<td>'+value.card_name+'</td>';
                        // @endif
                        // html += '<td>'+value.item_description+'</td>';
                        // html += '<td>'+(value.total_order).toLocaleString()+'</td>';
                        // html += '</tr>';

                        top_products_per_quantity.row.add([(index+1), value.card_name, value.item_code, value.item_description, (value.total_order).toLocaleString()]);
                        total_amount_of_top_products = total_amount_of_top_products + value.total_order;
                    });
                }else{
                    total_amount_of_top_products = 1 //to display the Top Products vs Total Sales chart (if no data)
                    // var cspan = ('@Auth::user()->role_id == 1') ? 4 : 3;
                    // html += '<tr><td colspan="'+cspan+'" class="text-center">No Data Available.</td></tr>';
                    // top_products_per_quantity.row.add();
                }

                top_products_per_quantity.draw();
                // $('td.dataTables_empty').addClass('text-center');
                // $('#top_products_per_quantity_tbody').html(html);

                if(result.data1.length > 0){
                    render_top_product_quantity_graph(result.data1);
                }else{
                    var svg = '<svg width="127px" height="127px" viewBox="-6.4 -6.4 76.80 76.80" id="svg5" version="1.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:svg="http://www.w3.org/2000/svg" fill="#4bd999">'+
                                    '<g id="SVGRepo_bgCarrier" stroke-width="0">'+
                                        '<path transform="translate(-6.4, -6.4), scale(2.4)" d="M16,28.419758373405784C18.706044986601906,28.78538711285768,21.456883896542053,30.888030942637958,23.94099374092184,29.754204621863074C26.391026377574274,28.63593221676837,27.06291763631191,25.455917154776063,28.013905746111014,22.936231716535985C28.855199192087518,20.707187185620015,29.00904204184031,18.37933986667975,29.13215167541057,16C29.26134725491711,13.503037057264299,30.110158573515733,10.737019221760887,28.753784291067696,8.636599206365648C27.39761351407382,6.536494330334742,24.493642191313846,6.25500488093523,22.198811897775162,5.263342846490975C20.1576345732142,4.381291540111231,18.206727431998335,3.3987702501033663,16,3.125322964042425C13.586887194214794,2.8263013730055473,10.715394359972786,2.053890878639736,8.845885996008295,3.6087110619463445C6.919897450126046,5.210504304317709,8.103126039586186,8.548223359032683,6.862695733182685,10.724574921885502C5.632982427599568,12.88212336346328,2.3465832239985116,13.58132382695202,1.7833642065525055,15.999999999999998C1.2243707866452378,18.400529853823564,2.708613371944921,20.829943994060446,4.024178829049042,22.914243576815345C5.309043566468769,24.9499028605392,7.021105473892899,26.758096746929812,9.208289701957248,27.763587306498792C11.310639195070399,28.73007771051505,13.706970084915113,28.109934389869288,16,28.419758373405784" fill="#7ed0ec" strokewidth="0"></path>'+
                                    '</g>'+
                                    '<g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>'+
                                        '<g id="SVGRepo_iconCarrier"> <defs id="defs2"></defs> <g id="layer1" transform="translate(-384,-96)">'+
                                            '<path d="m 393.99999,105 h 49 v 6 h -49 z" id="path27804" style="fill:#3e4f59;fill-opacity:1;fill-rule:evenodd;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:4.1"></path>'+
                                            '<path d="m 393.99999,111 h 49 v 40 h -49 z" id="path27806" style="fill:#acbec2;fill-opacity:1;fill-rule:evenodd;stroke-width:2.00001;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:4.1"></path>'+
                                            '<path d="m 393.99999,111 v 40 h 29.76954 a 28.484051,41.392605 35.599482 0 0 18.625,-40 z" id="path27808" style="fill:#e8edee;fill-opacity:1;fill-rule:evenodd;stroke:none;stroke-width:2.00002;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:4.1"></path>'+
                                            '<path d="m 395.99999,104 c -1.64501,0 -3,1.355 -3,3 v 40 c 0,0.55229 0.44772,1 1,1 0.55229,0 1,-0.44771 1,-1 v -40 c 0,-0.56413 0.43587,-1 1,-1 h 45 c 0.56414,0 1,0.43587 1,1 v 3 h -42 c -0.55228,0 -1,0.44772 -1,1 0,0.55229 0.44772,1 1,1 h 42 v 37 c 0,0.56413 -0.43586,1 -1,1 h -49 c -0.55228,0 -1,0.44772 -1,1 0,0.55229 0.44772,1 1,1 h 49 c 1.64501,0 3,-1.35499 3,-3 0,-14 0,-28 0,-42 0,-1.645 -1.35499,-3 -3,-3 z" id="path27810" style="color:#181c32;fill:#181c32;fill-rule:evenodd;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:4.1"></path>'+
                                            '<path d="m 438.99999,107 c -0.55228,0 -1,0.44772 -1,1 0,0.55229 0.44772,1 1,1 0.55229,0 1,-0.44771 1,-1 0,-0.55228 -0.44771,-1 -1,-1 z" id="path27812" style="color:#181c32;fill:#ed7161;fill-opacity:1;fill-rule:evenodd;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:4.1;-inkscape-stroke:none"></path>'+
                                            '<path d="m 434.99999,107 c -0.55228,0 -1,0.44772 -1,1 0,0.55229 0.44772,1 1,1 0.55229,0 1,-0.44771 1,-1 0,-0.55228 -0.44771,-1 -1,-1 z" id="path27814" style="color:#181c32;fill:#181c32;fill-opacity:1;fill-rule:evenodd;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:4.1;-inkscape-stroke:none"></path>'+
                                            '<path d="m 430.99999,107 c -0.55228,0 -1,0.44772 -1,1 0,0.55229 0.44772,1 1,1 0.55229,0 1,-0.44771 1,-1 0,-0.55228 -0.44771,-1 -1,-1 z" id="path27816" style="color:#181c32;fill:#42b05c;fill-opacity:1;fill-rule:evenodd;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:4.1;-inkscape-stroke:none"></path>'+
                                            '<path d="m 388.99999,150 a 1,1 0 0 0 -1,1 1,1 0 0 0 1,1 1,1 0 0 0 1,-1 1,1 0 0 0 -1,-1 z" id="path27818" style="color:#181c32;fill:#181c32;fill-rule:evenodd;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:4.1;-inkscape-stroke:none"></path>'+
                                            '<path d="m 396.99999,110 c -0.55228,0 -1,0.44772 -1,1 0,0.55229 0.44772,1 1,1 0.55229,0 1,-0.44771 1,-1 0,-0.55228 -0.44771,-1 -1,-1 z" id="path27820" style="color:#181c32;fill:#181c32;fill-rule:evenodd;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:4.1;-inkscape-stroke:none"></path> <rect height="22" id="rect4427" rx="2" ry="2" style="fill:#e8edee;fill-opacity:1;fill-rule:evenodd;stroke:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:4.1" width="29" x="404" y="120"></rect>'+
                                            '<path d="m 406,120 c -1.108,0 -2,0.892 -2,2 v 18 c 0,1.108 0.892,2 2,2 h 19.58398 A 19.317461,16.374676 0 0 0 430.2207,131.36719 19.317461,16.374676 0 0 0 424.80273,120 Z" id="path27648" style="fill:#e8edee;fill-opacity:1;fill-rule:evenodd;stroke:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:4.1"></path>'+
                                            '<rect height="6" id="rect8552" style="fill:#181c32;fill-opacity:1;fill-rule:evenodd;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:4.1" width="29" x="404" y="120"></rect> <path d="m 404,120 v 6 h 24.58984 a 14,8.5 0 0 0 0.10938,-1 14,8.5 0 0 0 -2.67969,-5 z" id="path8626" style="fill:#181c32;fill-opacity:1;fill-rule:evenodd;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:4.1"></path>'+
                                            '<g id="path4429" transform="translate(0,-4)">'+
                                                '<path d="m 404,130 h 29" id="path7162" style="color:#181c32;fill:#918383;fill-rule:evenodd;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:4.1;-inkscape-stroke:none"></path>'+ 
                                                '<path d="m 406,123 c -1.6447,0 -3,1.3553 -3,3 0,1.97201 0,3.94401 0,5.91602 0,0.55228 0.44772,1 1,1 0.55228,0 1,-0.44772 1,-1 V 131 h 27 v 6 c 0,0.55228 0.44772,1 1,1 0.55228,0 1,-0.44772 1,-1 0,-3.66667 0,-7.33333 0,-11 0,-1.6447 -1.3553,-3 -3,-3 z m 0,2 h 25 c 0.5713,0 1,0.4287 1,1 v 3 h -27 v -3 c 0,-0.5713 0.4287,-1 1,-1 z m -2,10 c -0.55228,0 -1,0.44772 -1,1 v 8 c 0,1.6447 1.3553,3 3,3 h 25 c 1.6447,0 3,-1.3553 3,-3 v -3 c 0,-0.55228 -0.44772,-1 -1,-1 -0.55228,0 -1,0.44772 -1,1 v 3 c 0,0.5713 -0.4287,1 -1,1 h -25 c -0.5713,0 -1,-0.4287 -1,-1 v -8 c 0,-0.55228 -0.44772,-1 -1,-1 z" id="path7164" style="color:#181c32;fill:#181c32;fill-rule:evenodd;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:4.1;-inkscape-stroke:none"></path>'+ 
                                            '</g>'+
                                            '<path d="m 409.93555,129.00195 c -0.45187,0.0293 -0.82765,0.35863 -0.91602,0.80274 l -1,5 C 407.89645,135.42313 408.36944,135.99975 409,136 h 3 v 2 c 0,0.55228 0.44772,1 1,1 0.55228,0 1,-0.44772 1,-1 0,-1.66667 0,-3.33333 0,-5 0,-0.55228 -0.44772,-1 -1,-1 -0.55228,0 -1,0.44772 -1,1 v 1 h -1.78125 l 0.76172,-3.80469 c 0.10771,-0.54147 -0.24375,-1.06778 -0.78516,-1.17578 -0.0854,-0.0172 -0.17278,-0.0231 -0.25976,-0.0176 z" id="path8873" style="color:#181c32;fill:#181c32;fill-rule:evenodd;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:4.1;-inkscape-stroke:none"></path>'+
                                            '<path d="m 418.99999,130 c 1.10801,0 2.00002,0.89201 2.00002,2.00002 v 2.99996 c 0,1.10801 -0.89201,2.00002 -2.00002,2.00002 -1.10801,0 -2.00002,-0.89201 -2.00002,-2.00002 v -2.99996 c 0,-1.10801 0.89201,-2.00002 2.00002,-2.00002 z" id="rect5745" style="fill:#181c32;fill-opacity:1;fill-rule:evenodd;stroke:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:4.1"></path>'+
                                            '<path d="m 419,129 c -1.64471,0 -3,1.35529 -3,3 v 3 c 0,1.64471 1.35529,3 3,3 1.64471,0 3,-1.35529 3,-3 v -3 a 1,1 0 0 0 -1,-1 1,1 0 0 0 -1,1 v 3 c 0,0.57131 -0.42869,1 -1,1 -0.57131,0 -1,-0.42869 -1,-1 v -3 c 0,-0.57131 0.42869,-1 1,-1 a 1,1 0 0 0 1,-1 1,1 0 0 0 -1,-1 z" id="path7169" style="color:#181c32;fill:#181c32;fill-rule:evenodd;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:4.1;-inkscape-stroke:none"></path>'+ 
                                            '<path d="m 425.93555,129.00195 c -0.45187,0.0293 -0.82765,0.35863 -0.91602,0.80274 l -1,5 C 423.89645,135.42313 424.36944,135.99975 425,136 h 3 v 2 c 0,0.55228 0.44772,1 1,1 0.55228,0 1,-0.44772 1,-1 0,-1.66667 0,-3.33333 0,-5 0,-0.55228 -0.44772,-1 -1,-1 -0.55228,0 -1,0.44772 -1,1 v 1 h -1.78125 l 0.76172,-3.80469 c 0.10771,-0.54147 -0.24375,-1.06778 -0.78516,-1.17578 -0.0854,-0.0172 -0.17278,-0.0231 -0.25976,-0.0176 z" id="path69785" style="color:#181c32;fill:#181c32;fill-rule:evenodd;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:4.1;-inkscape-stroke:none"></path>'+
                                        '</g>'+ 
                                    '</g>'+
                                '</svg>';

                    $('#top_products_per_quantity_chart').html(svg);
                    $('#bussiness_share_chart').html(svg);
                    $('#top-products-div2').find('.row').remove();
                }
            }
        })
        .fail(function() {
            toast_error("error");
        });
    }

    $('#top_products_per_quantity_paginate').on('click', function(){
        var data = fetchCurrentPageIn_topProducts();
        render_top_product_quantity_graph(data);
    })

    $('select[name=top_products_per_quantity_length]').on('change', function(){
        var data = fetchCurrentPageIn_topProducts();
        render_top_product_quantity_graph(data);
    })

    function fetchCurrentPageIn_topProducts(){
        var data = [];
        var index = 0;
        top_products_per_quantity.rows( {page:'current'} ).every( function () {
            var d = this.data();
            data[index] = {
                name: d[2],
                description: d[3],
                key: d[4].replace(/\,/g,'') //remove comma
            }
            index++;
        });
        // console.log(data);
        // console.log(top_products_per_quantity.row( 6 ).data());
        total_clicks++

        return data;
    }

    function render_top_product_quantity_graph(result){ 
        var data = [];
        var data2 = [];
        var hex = '';
        var total_ten = 0;
        var total_rest = 0;

        for (var key in result) {
            switch(key) {
                case '0':
                hex = '#006B54'; //green
                    break;
                case '1':
                hex = '#FFA500'; //yellow
                    break;
                case '2':
                hex = '#034F84'; //blue
                    break;
                case '3':
                hex = '#CD212A'; //red
                    break;
                case '4':
                hex = '#00758F'; //turqoise
                    break;
                case '5':
                hex = '#810CA8'; //violet
                    break;
                case '6':
                hex = '#87431D';
                    break;
                case '7':
                hex = '#FA7A35'; //brown
                    break;
                case '8':
                hex = '#519872';
                    break;
                case '9':
                hex = '#E8A798';
                    break;
                case '10':
                hex = '#b30000';
                    break;
                case '11':
                hex = '#7c1158';
                    break;
                case '12':
                hex = '#4421af';
                    break;
                case '13':
                hex = '#1a53ff';
                    break;
                case '14':
                hex = '#0d88e6';
                    break;
                case '15':
                hex = '#00b7c7';
                    break;
                case '16':
                hex = '#5ad45a';
                    break;
                case '17':
                hex = '#8be04e';
                    break;
                case '18':
                hex = '#ebdc78';
                    break;
                case '19':
                hex = '#fd7f6f';
                    break;
            }
            if(key <= 19){ 
                data[key] =  { label: result[key].name, label2: result[key].description, data: result[key].key, color: hex }
                total_ten = total_ten + parseFloat(result[key].key);
            }
        }
        total_rest = total_amount_of_top_products - total_ten ;

        data2[0] = {label: 'MM17039', color: '#034F84', data: total_ten}
        data2[1] = {label: 'MM17039', color: '#FA7A35', data: Math.round(total_rest)}
        
        $.plot('#top_products_per_quantity_chart', data, {
            series: {
            pie: {
                show: true,
                innerRadius:0.5,            
                radius: 1,

                label: {
                show: true,
                radius: 3/4,
                formatter: labelFormatter,
                //   threshold: 0.1,
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
            tooltip: true, // Enable tooltips
            tooltipOpts: {
                    cssClass: "flotTip",
                    content: toolTipFormatter,
                    shifts: {
                        x: 20,
                        y: 0
                    },
                    defaultTheme: false
            },
        });

        if(total_clicks === 0){
            $('#top_product_sales_count').text('20');
            $.plot('#bussiness_share_chart', data2, {
                series: {
                pie: {
                    show: true,
                    innerRadius:0.5,            
                    radius: 1,

                    label: {
                    show: true,
                    radius: 3/4,
                    formatter: businessSharelabelFormatter,
                    //   threshold: 0.1,
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
                tooltip: false, // Enable tooltips
                tooltipOpts: {
                    cssClass: "flotTip",
                    content: "%s (%p.0%)",
                    shifts: {
                        x: 20,
                        y: 0
                    },
                    defaultTheme: false
                },
            });
        }

    }

    // Add tooltip functionality
    // $("#top_products_per_quantity_chart").on("plothover", function (event, pos, item) {
    //     if (item) {
    //         console.log(item);
    //         var percent = parseFloat(item.series.percent).toFixed(2);
    //         // Customize the tooltip content as needed
    //         $("#tooltip").html(item.series.label + ": " + percent + "%")
    //             .css({ top: item.pageY + 5, left: item.pageX + 5 })
    //             .fadeIn(200);
    //     } else {
    //         $("#tooltip").hide();
    //     }
    // });

    // // Hide the tooltip on mouse leave
    // $("#top_products_per_quantity_chart").mouseleave(function () {
    //     $("#tooltip").hide();
    // });

    

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


    //============================= START FOR BRAND COLUMN CHART ========================================
    var count_customer_acc = '';
    var role_customer_acc = '{{@Auth::user()->role_id}}';
    @if(@Auth::user()->role_id == 4)
        @php 
            $cus = explode(',', Auth::user()->multi_customer_id);
        @endphp

        count_customer_acc = +'{{ count($cus)}}';

        $('[name="filter_customer_brand"]').parent().prev().text('Account');
        $('[name="filter_customer_category"]').parent().prev().text('Account');
        if (count_customer_acc !== undefined && count_customer_acc < 2) {
            $('[name="filter_customer_brand"]').parent().prev().remove();
            $('[name="filter_customer_brand"]').parent().remove();
            $('[name="filter_customer_category"]').parent().prev().remove();
            $('[name="filter_customer_category"]').parent().remove();

        }
    @endif

    $('#resync_brandchart-data').on('click', function(e){
        var sap_connection_id = null;
        var brand_code = $('[name="filter_brand"]').select2('data')[0]['code'];
        var customer_code =  null;

        if(($('[name="filter_customer_brand"]').val() === null )){
            alert_filters('Customer');
        }else if(($('[name="filter_brand"]').val() === '' )){
            alert_filters('Brand');
        }
        
        
        if(count_customer_acc < 2 && role_customer_acc == 4){
            sap_connection_id = '{{@Auth::user()->sap_connection->id}}';
            customer_code = '{{@Auth::user()->customer->card_code}}';
        }else{
            sap_connection_id = ($('[name="filter_customer_brand"]').val() !== null ) ? $('[name="filter_customer_brand"]').select2('data')[0]['sap_connection_id'] : null;
            customer_code =  $('[name="filter_customer_brand"]').select2('data')[0]['code'];
        }

        $('#bdp_target_brand_column_chart').find('.apexcharts-canvas').remove();
        $('#brand-chart-loader').removeClass('d-none');
        $.ajax({
              url: "{{ route('customer-sales-target.fetch') }}",
              method: "GET",
              data: {
                  sap_connection_id: sap_connection_id,
                  customer_id: $('[name="filter_customer_brand"]').val(),
                  customer_code: customer_code,
                  brand: brand_code,
                  brand_id: $('[name="filter_brand"]').val(),
                  year: $('[name="year_brand"]').val()
              }
          })
          .done(function(result) {
            $('#brand-chart-loader').addClass('d-none');
            if(result.status == false){
                toast_error(result.message);
            }else{
                // $('#quarterBrand').prop("checked", false);
                chart_datas_brand = result.data;
                var response = ($('#quarterBrand').attr("isCheck") == 'yes') ? result.data.quarter : result.data.year;
                
                if($('#yearComparisonBrand').attr("isCheck") == 'yes' && $('#quarterBrand').attr("isCheck") == 'yes'){
                   response =  result.data.quarterly_comparison;
                }
                if($('#yearComparisonBrand').attr("isCheck") == 'yes' && $('#quarterBrand').attr("isCheck") == 'no'){
                   response =  result.data.monthly_comparison;
                }
                
                render_target_column_chart(response, 'bdp_target_brand_column_chart', 'filter_brand', 'tbl_brand_target_tbody');
            }
          }).fail(function() {
              toast_error("error");
          });
    
    });

    $('#quarterBrand').on('click', function(){
        $('#bdp_target_brand_column_chart').find('.apexcharts-canvas').remove();
        
        var result = []; 
        if($(this).attr("isCheck") == 'no') {
            // $('#yearComparisonBrand').attr("isCheck", "no");
            // $('#yearComparisonBrand').prop("checked", false); 
            $(this).attr("isCheck", "yes");
            $(this).prop("checked", true);
            $('#yearComparisonBrand').next().text('Quarterly Comparison');
            $("#tbl_brand_target_tbody").parent().find('thead tr td:nth-child(3)').html("Target");
            $("#tbl_brand_target_tbody").parent().find('thead tr td:nth-child(4)').html("Actual");

            if($('#yearComparisonBrand').attr("isCheck") == 'yes'){
                result = chart_datas_brand.quarterly_comparison;
            }else{
                result = chart_datas_brand.quarter;
            }
        }else{
            $(this).attr("isCheck", "no");
            $(this).prop("checked", false);
            $('#yearComparisonBrand').next().text('Monthly Comparison');
            if($('#yearComparisonBrand').attr("isCheck") == 'yes'){
                result = chart_datas_brand.monthly_comparison;
            }else{
                result = chart_datas_brand.year
            }
        }
        
        render_target_column_chart(result, 'bdp_target_brand_column_chart', 'filter_brand', 'tbl_brand_target_tbody');
    });

    $('#yearComparisonBrand').on('click', function(){
        $('#bdp_target_brand_column_chart').find('.apexcharts-canvas').remove();
        
        var result = []; 
        if($(this).attr("isCheck") == 'no') { 
            // $('#quarterBrand').attr("isCheck", "no");
            // $('#quarterBrand').prop("checked", false);
            $('.brandHeadTitle').text('Previous Year vs Current Year');
            $(this).attr("isCheck", "yes");
            $(this).prop("checked", true);
            $("#tbl_brand_target_tbody").parent().find('thead tr td:nth-child(3)').html("Previous Year");
            $("#tbl_brand_target_tbody").parent().find('thead tr td:nth-child(4)').html("Current Year");

            if($('#quarterBrand').attr("isCheck") == 'yes'){
                result = chart_datas_brand.quarterly_comparison;
            }else{
                result = chart_datas_brand.monthly_comparison;
            }
        }else{
            $('.brandHeadTitle').text('Sales vs Target');
            $(this).attr("isCheck", "no");
            $(this).prop("checked", false);
            $("#tbl_brand_target_tbody").parent().find('thead tr td:nth-child(3)').html("Target");
            $("#tbl_brand_target_tbody").parent().find('thead tr td:nth-child(4)').html("Actual");

            if($('#quarterBrand').attr("isCheck") == 'yes'){
                result = chart_datas_brand.quarter;
            }else{
                result = chart_datas_brand.year;
            }
        }

        render_target_column_chart(result, 'bdp_target_brand_column_chart', 'filter_brand', 'tbl_brand_target_tbody');
    });



    $('[name="filter_customer_brand"], [name="filter_customer_category"], [name="filter_customer_balance"]').select2({ //Make IT AVAILABLE FOR BOTH COLUMN CHART
      ajax: {
        url: "{{ route('customer-promotion.get-customer') }}",
        type: "post",
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
              _token: "{{ csrf_token() }}",
              search: params.term,
            //   sap_connection_id: $('[name="filter_company"]').find('option:selected').val(),
            };
        },
        processResults: function (response) {
          return {
            results:  $.map(response, function (item) {
                          return {
                            text: item.card_name + " (Code: " + item.card_code + " -"+item.sap_connection.company_name+")",
                            id: item.id,
                            code: item.card_code,
                            sap_connection_id : item.sap_connection_id
                          }
                      })
          };
        },
        cache: true
      },
    //   tags: true,
    //   minimumInputLength: 2,
    });

    
    $(document).find(".select_brand").select2({
        ajax: {
            url: "{{route('customers-sales-specialist.get-product-brand')}}",
            type: "post",
            dataType: 'json',
            delay: 250,
            data: function (params) {

              var sap_connection_id = null;
              if(count_customer_acc < 2 && role_customer_acc == 4){
                sap_connection_id = '{{@Auth::user()->sap_connection->id}}';
              }else{
                sap_connection_id = ($('[name="filter_customer_brand"]').val() !== null ) ? $('[name="filter_customer_brand"]').select2('data')[0]['sap_connection_id'] : null;
              }

              return {
                _token: "{{ csrf_token() }}",
                search: params.term,
                sap_connection_id: sap_connection_id
              };
            },
            processResults: function (response) {
                return {
                    results: response
                };
            },
            cache: true
        },
        placeholder: 'Select Brand',
        // multiple: true,
    });

    var chart_datas_brand = {year: {series : [{name: 'Actual Sales', data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0] }, 
                                              {name: 'Target Sales', data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0] }
                                             ],
                                    bar: {columnWidth: '55%'},
                                    stroke: { width: 3},
                                    categories : ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                                    colors: ['#034F84', '#FA7A35']
                              },
                            quarter : {series : [{name: 'Actual Sales', data: [0, 0, 0, 0] }, 
                                                {name: 'Target Sales', data: [0, 0, 0, 0] }
                                                ],
                                        bar: {columnWidth: '-10%'},
                                        stroke: { width: 20},
                                        categories : ['Q1', 'Q2', 'Q3', 'Q4'],
                                        colors: ['#afafaf', '#12365d']
                                    },
                            monthly_comparison: {series : [{name: 'Previous Year', data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0] }, 
                                              {name: 'Current Year', data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0] }
                                             ],
                                    bar: {columnWidth: '55%'},
                                    stroke: { width: 3},
                                    categories : ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                                    colors: ['#034F84', '#FA7A35']
                              },
                            quarterly_comparison : {series : [{name: 'Previous Year', data: [0, 0, 0, 0] }, 
                                                         {name: 'Current Year', data: [0, 0, 0, 0] }
                                                        ],
                                bar: {columnWidth: '-10%'},
                                stroke: { width: 20},
                                categories : ['Q1', 'Q2', 'Q3', 'Q4'],
                                colors: ['#afafaf', '#12365d']
                            }
                            }; //dummy datas
    var chart_datas_category =  chart_datas_brand;

    render_target_column_chart(chart_datas_brand.year, 'bdp_target_brand_column_chart', 'filter_brand', 'tbl_brand_target_tbody');

    //============================= END FOR BRAND COLUMN CHART ========================================


    //============================= START FOR CATEGORY COLUMN CHART ===================================

    $('#resync_categorychart-data').on('click', function(e){
        var sap_connection_id = null;
        var category_code = $('[name="filter_category"]').select2('data')[0]['code'];
        var customer_code =  null;

        if(($('[name="filter_customer_category"]').val() === null )){
            alert_filters('Customer');
        }else if(($('[name="filter_category"]').val() === '' )){
            alert_filters('Brand');
        }

        if(count_customer_acc < 2 && role_customer_acc == 4){
            sap_connection_id = '{{@Auth::user()->sap_connection->id}}';
            customer_code = '{{@Auth::user()->customer->card_code}}';
        }else{
            sap_connection_id = ($('[name="filter_customer_category"]').val() !== null ) ? $('[name="filter_customer_category"]').select2('data')[0]['sap_connection_id'] : null;
            customer_code =  $('[name="filter_customer_category"]').select2('data')[0]['code'];
        }
        
        $('#bdp_target_category_column_chart').find('.apexcharts-canvas').remove();
        $('#category-chart-loader').removeClass('d-none');
        $.ajax({
              url: "{{ route('customer-sales-target.fetch') }}",
              method: "GET",
              data: {
                  sap_connection_id: sap_connection_id,
                  customer_id: $('[name="filter_customer_category"]').val(),
                  customer_code: customer_code,
                  category: category_code,
                  category_id: $('[name="filter_category"]').val(),
                  year: $('[name="year_category"]').val()
              }
          })
          .done(function(result) {
            $('#category-chart-loader').addClass('d-none');
            if(result.status == false){
                toast_error(result.message);
            }else{
                // $('#quarterQuarter').prop("checked", false);
                chart_datas_category = result.data;
                var response = ($('#quarterCategory').attr("isCheck") == 'yes') ? result.data.quarter : result.data.year;
                
                if($('#yearComparisonCategory').attr("isCheck") == 'yes' && $('#quarterCategory').attr("isCheck") == 'yes'){
                    response = result.data.quarterly_comparison;
                }

                if($('#yearComparisonCategory').attr("isCheck") == 'yes' && $('#quarterCategory').attr("isCheck") == 'no'){
                    response = result.data.monthly_comparison;
                }

                render_target_column_chart(response, 'bdp_target_category_column_chart', 'filter_category', 'tbl_category_target_tbody');
            }
          }).fail(function() {
              toast_error("error");
          });
    });


    $('#quarterCategory').on('click', function(){
        $('#bdp_target_category_column_chart').find('.apexcharts-canvas').remove();

        var result = [];
        if($(this).attr("isCheck") == 'no') { 
            // $('#yearComparisonCategory').attr("isCheck", "no");
            // $('#yearComparisonCategory').prop("checked", false); 
            $(this).attr("isCheck", "yes");
            $(this).prop("checked", true);
            $('#yearComparisonCategory').next().text('Quarterly Comparison');
            $("#tbl_category_target_tbody").parent().find('thead tr td:nth-child(3)').html("Target");
            $("#tbl_category_target_tbody").parent().find('thead tr td:nth-child(4)').html("Actual");

            if($('#yearComparisonCategory').attr("isCheck") == 'yes'){
                result =  chart_datas_category.quarterly_comparison;
            }else{
                result = chart_datas_category.quarter;
            }
        }else{
            $(this).attr("isCheck", "no");
            $(this).prop("checked", false);
            $('#yearComparisonCategory').next().text('Monthly Comparison');

            if($('#yearComparisonCategory').attr("isCheck") == 'yes'){
                result =  chart_datas_category.monthly_comparison;
            }else{
                result = chart_datas_category.year
            }
        }
        
        render_target_column_chart(result, 'bdp_target_category_column_chart', 'filter_category', 'tbl_category_target_tbody');
    });

    $('#yearComparisonCategory').on('click', function(){
        $('#bdp_target_category_column_chart').find('.apexcharts-canvas').remove();
        
        var result = [];
        if($(this).attr("isCheck") == 'no') { 
            // $('#quarterCategory').attr("isCheck", "no");
            // $('#quarterCategory').prop("checked", false);
            $('.categoryHeadTitle').text('Previous Year vs Current Year');
            $(this).attr("isCheck", "yes");
            $(this).prop("checked", true);
            $("#tbl_category_target_tbody").parent().find('thead tr td:nth-child(3)').html("Previous Year");
            $("#tbl_category_target_tbody").parent().find('thead tr td:nth-child(4)').html("Current Year");

            if($('#quarterCategory').attr("isCheck") == 'yes'){
                result = chart_datas_category.quarterly_comparison;
            }else{
                result = chart_datas_category.monthly_comparison;
            }
        }else{
            $('.categoryHeadTitle').text('Sales vs Target');
            $(this).attr("isCheck", "no");
            $(this).prop("checked", false);
            $("#tbl_category_target_tbody").parent().find('thead tr td:nth-child(3)').html("Target");
            $("#tbl_category_target_tbody").parent().find('thead tr td:nth-child(4)').html("Actual");

            if($('#quarterCategory').attr("isCheck") == 'yes'){
                result = chart_datas_category.quarter;
            }else{
                result = chart_datas_category.year;
            }
        }

        render_target_column_chart(result, 'bdp_target_category_column_chart', 'filter_category', 'tbl_category_target_tbody');
    });

    $(document).find(".select_category").select2({
        ajax: {
            url: "{{route('customers-sales-specialist.get-product-category')}}",
            type: "post",
            dataType: 'json',
            delay: 250,
            data: function (params) {

              var sap_connection_id = null;
              if(count_customer_acc < 2 && role_customer_acc == 4){
                sap_connection_id = '{{@Auth::user()->sap_connection->id}}';
              }else{
                sap_connection_id = ($('[name="filter_customer_category"]').val() !== null ) ? $('[name="filter_customer_category"]').select2('data')[0]['sap_connection_id'] : null;
              }

              return {
                _token: "{{ csrf_token() }}",
                search: params.term,
                sap_connection_id: sap_connection_id
              };
            },
            processResults: function (response) {
                return {
                    results: response
                };
            },
            cache: true
        },
        placeholder: 'Select Category',
    });


    render_target_column_chart(chart_datas_category.year, 'bdp_target_category_column_chart', 'filter_category', 'tbl_category_target_tbody');

    function render_target_column_chart(data, tableID, opt, tbl){
        var categories = data.categories
        var options = {
            series: data.series,
            chart: {
                type: 'bar',
                height: 350
            },
            plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: data.bar.columnWidth, //55
                // barWidth: '150%',
                endingShape: 'rounded'
            },
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: data.stroke.width, //3
                colors: ['transparent']
            },
            xaxis: {
            categories: categories,
            },
            yaxis: {
            title: {
                text: '(Quantity)'
            }
            },
            fill: {
            opacity: 1
            },
            tooltip: {
            y: {
                formatter: function (val, series) {
                    var condition = ($('#yearComparisonBrand').attr("isCheck") == 'yes' && tbl == 'tbl_brand_target_tbody') || 
                                    ($('#yearComparisonCategory').attr("isCheck") == 'yes' && tbl == 'tbl_category_target_tbody');
                    var sales_index = ( condition ) ? [1,0] : [0,1]; //need to switch cause it was switch in table
                    var sIdx = series.seriesIndex;
                    var dIdx = series.dataPointIndex;
                    var actualSales = series.series[ sales_index[0] ][dIdx];
                    var targetSales = series.series[ sales_index[1] ][dIdx];
                    var catName = series.w.globals.labels[dIdx];
                    // var diff = 0;
                    // var percentage = 0;
                    var adetails = '';

                    diff = actualSales - targetSales;
                    diffAbs = Math.abs(diff);
                    percentage = (diffAbs / targetSales) * 100;

                    adetails += '| ';
                    if(diff > 0 && targetSales > 0){ //exceed
                        adetails += 'Exceed: '+ (diff).toLocaleString() +' ('+Math.round(100 + percentage)+'%)';
                    }else if(diff < 0 && targetSales > 0){ //short
                        adetails += 'Short: '+ (diffAbs).toLocaleString() +' ('+Math.round(100 - percentage)+'%)';
                    }else if(diff == 0 && targetSales > 0){ //meet
                        adetails += 'Meet (100%)';
                    }else{
                        var status = ( condition ) ? 'No Previous Year Sales' : 'Undefined Target';
                        adetails += status+': - (0%)';
                    }

                    return "" + (val).toLocaleString()+" "+adetails;
                }
                
            }
            // custom: function({ series, seriesIndex, dataPointIndex, w }) {
            //     return '<div class="custom-tooltip">Value: ' + series[seriesIndex][dataPointIndex] + '</div>';
            // },
            },
            colors: data.colors
        };

        var chart_brand = new ApexCharts(document.querySelector("#"+tableID), options);
        chart_brand.render();

        render_target_tbl(data.series, categories, opt, tbl );
    }

    function render_target_tbl(data, categories, opt, tbl){
        var html = '';
        var condition = ($('#yearComparisonBrand').attr("isCheck") == 'yes' && tbl == 'tbl_brand_target_tbody') || 
                        ($('#yearComparisonCategory').attr("isCheck") == 'yes' && tbl == 'tbl_category_target_tbody'); 
        var sales_index = ( condition ) ? [1,0] : [0,1]; //need to switch cause it was switch in table
        
        $.each(categories, function(index, value) {
            // console.log('Month ' + (index + 1) + ': ' + value);
            var actualSales = data[ sales_index[0] ].data[index];
            var targetSales = data[ sales_index[1] ].data[index];

            diff = actualSales - targetSales;
            diffAbs = Math.abs(diff);
            percentage = (diffAbs / targetSales) * 100;

            html += '<tr>'+
                    '<td>'+value+'</td>'+
                    '<td>'+$('[name="'+opt+'"]').select2('data')[0]['text']+'</td>'+
                    '<td>'+(targetSales).toLocaleString()+'</td>'+
                    '<td>'+(actualSales).toLocaleString()+'</td>';

            var status = ( condition ) ? 'No Previous Year Sales' : 'Undefined Target';
            var short = '-';
            var over = '-';
            var percent = '0%';
            if(diff > 0 && targetSales > 0){ //exceed
                status = 'Exceed';
                over = (diff).toLocaleString();
                percent = Math.round(100 + percentage)+'%';
            }else if(diff < 0 && targetSales > 0){ //short
                status = 'Short';
                short = (diffAbs).toLocaleString();
                percent = Math.round(100 - percentage)+'%';
            }else if(diff == 0 && targetSales > 0){ //meet
                status = 'Meet';
                percent = '100%';
            }

            html += '<td>'+short+'</td>'+
                    '<td>'+over+'</td>'+
                    '<td>'+percent+'</td>'+
                    '<td>'+status+'</td>'+
                    '</tr>';
        });

        $('#'+tbl).html(html);
    }
    
    //============================= END FOR CATEGORY COLUMN CHART ===================================

@endif

function labelFormatter(label, series) {
    return "<div class='default_label' style='text-align:center;'> <small class='custom-shadow'>" + label + "</small><br> <small class='custom-shadow'>" + Math.round(series.percent) + "% </small></div>";
}

function toolTipFormatter(label, series, x, w){
    return ""+w.series.label+" | " +w.series.label2+ " | ("+ Math.round(w.series.percent) + "%)";
}

function businessSharelabelFormatter(label, series) {
    return "<div class='default_label' style='text-align:center;'> <small class='custom-shadow'>" + Math.round(series.percent) + "% </small></div>";
}

function alert_filters(field){
    Swal.fire({
        title: "Please select "+field+".",
        icon: "info",
        confirmButtonColor: "#3085d6",
    });
}

});
</script>     
@endpush
