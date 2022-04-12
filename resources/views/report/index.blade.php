@extends('layouts.master')

@section('title','Reports')

@section('content')
<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
     <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
        <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
           <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">Reports</h1>
        </div>

        <!-- <div class="d-flex align-items-center py-1">
           <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_create_app" id="kt_toolbar_primary_button">Create</a>
        </div> -->
     </div>
  </div>

  <div class="post d-flex flex-column-fluid reports" id="kt_post">
     <div id="kt_content_container" class="container-xxl">
        <div class="row gy-5 g-xl-8">
            <div class="card card-xl-stretch">
                <div class="card-body p-0">
                    <div class="card-p">
                        <div class="row">
                            <div class="col-md-3 mb-5">
                                <div class="bg-light-warning px-6 py-8 rounded-2 min-w-150 box">
                                    <span class="svg-icon svg-icon-3x svg-icon-warning d-block my-2">
                                    
                                    <object data="{{ asset('assets/assets/media')}}/promotion.svg" type="image/svg+xml"></object>
                                    </span>
                                    <a href="{{ route('reports.promotion-report.index') }}" class="text-warning fw-bold fs-6">Promotions Report </a>
                                </div>
                            </div>

                            <div class="col-md-3 mb-5">
                                <div class="bg-light-primary px-6 py-8 rounded-2 min-w-150 box">
                                        <span class="svg-icon svg-icon-3x svg-icon-primary d-block my-2">
                                       
                                        <object data="{{ asset('assets/assets/media')}}/sales.svg" type="image/svg+xml"></object>
                                        </span>
                                        <a href="{{ route('reports.sales-report.index') }}" class="text-primary fw-bold fs-6">Sales Report </a>
                                </div>
                            </div>

                            <div class="col-md-3 mb-5">
                                <div class="bg-light-success px-6 py-8 rounded-2 min-w-150 box">
                                        <span class="svg-icon svg-icon-3x svg-icon-success d-block my-2">
                                        
                                        <object data="{{ asset('assets/assets/media')}}/sales-order.svg" type="image/svg+xml"></object>
                                        </span>
                                        <a href="{{ route('reports.sales-order-report.index') }}" class="text-success fw-bold fs-6">Sales Order Report </a>
                                </div>
                            </div>

                            <div class="col-md-3 mb-5">
                                <div class="bg-light-dark px-6 py-8 rounded-2 min-w-150 box">
                                    <span class="svg-icon svg-icon-3x svg-icon-dark d-block my-2">
                                    
                                    <object data="{{ asset('assets/assets/media')}}/invoice.svg" type="image/svg+xml"></object>
                                    </span>
                                    <a href="{{ route('reports.overdue-sales-invoice-report.index') }}" class="text-dark fw-bold fs-6">Overdue Sales Invoice Report </a>
                                </div>
                            </div>

                            <div class="col-md-3 mb-5">
                                <div class="bg-light-danger px-6 py-8 rounded-2 min-w-150 box">
                                        <span class="svg-icon svg-icon-3x svg-icon-danger d-block my-2">
                                       
                                        <object data="{{ asset('assets/assets/media')}}/back-report.svg" type="image/svg+xml"></object>
                                        </span>
                                        <a href="{{ route('reports.back-order-report.index') }}" class="text-danger fw-bold fs-6">Back Order Report </a>
                                </div>
                            </div>

                            <div class="col-md-3 mb-5">
                                <div class="bg-light-orange px-6 py-8 rounded-2 min-w-150 box">
                                        <span class="svg-icon svg-icon-3x svg-icon-warning d-block my-2">
                                        
                                        <object data="{{ asset('assets/assets/media')}}/product-sales.svg" type="image/svg+xml"></object>
                                        </span>
                                        <a href="{{ route('reports.product-sales-report.index') }}" class="text-orange fw-bold fs-6">Product Sales Report</a>
                                </div>
                            </div>

                            <div class="col-md-3 mb-5">
                                <div class="bg-light-purple px-6 py-8 rounded-2 min-w-150 box">
                                        <span class="svg-icon svg-icon-3x svg-icon-primary d-block my-2">
                                        
                                        <object data="{{ asset('assets/assets/media')}}/product-report.svg" type="image/svg+xml"></object>
                                        </span>
                                        <a href="{{ route('reports.product-report.index') }}" class="text-purple fw-bold fs-6">Product Report </a>
                                </div>
                            </div>

                            <div class="col-md-3 mb-5">
                                <div class="bg-light-light px-6 py-8 rounded-2 min-w-150 box">
                                    <span class="svg-icon svg-icon-3x svg-icon-success d-block my-2">
                                        <object data="{{ asset('assets/assets/media')}}/credit-report.svg" type="image/svg+xml"></object>
                                    </span>
                                    <a href="{{ route('reports.credit-memo-report.index') }}" class="text-primary fw-bold fs-6">Credit Memo Report </a>
                                </div>
                            </div>

                            <div class="col-md-3 mb-5">
                                <div class="bg-light-green px-6 py-8 rounded-2 min-w-150 box">
                                    <span class="svg-icon svg-icon-3x svg-icon-dark d-block my-2">
                                        <object data="{{ asset('assets/assets/media')}}/debit-report.svg" type="image/svg+xml"></object>
                                    </span>
                                    <a href="{{ route('reports.debit-memo-report.index') }}" class="text-green fw-bold fs-6">Debit Memo Report </a>
                                </div>
                            </div>

                            <div class="col-md-3 mb-5">
                                <div class="bg-light-chocolate px-6 py-8 rounded-2 min-w-150 box">
                                    <span class="svg-icon svg-icon-3x svg-icon-danger d-block my-2">
                                        <object data="{{ asset('assets/assets/media')}}/return-order.svg" type="image/svg+xml"></object>
                                    </span>
                                    <a href="{{ route('reports.return-order-report.index') }}" class="text-chocolate fw-bold fs-6">Return Order Report </a>
                                </div>
                            </div>

                            <div class="col-md-3 mb-5">
                                <div class="bg-light-blue px-6 py-8 rounded-2 min-w-150 box">
                                    <span class="svg-icon svg-icon-3x svg-icon-danger d-block my-2">
                                        <object data="{{ asset('assets/assets/media')}}/sales-order-invoice-time.svg" type="image/svg+xml"></object>
                                    </span>
                                    <a href="{{ route('reports.sales-order-to-invoice-lead-time-report.index') }}" class="text-blue fw-bold fs-6">Sales Order to Invoice Lead Time </a>
                                </div>
                            </div>

                            <div class="col-md-3 mb-5">
                                <div class="bg-light-red px-6 py-8 rounded-2 min-w-150 box">
                                    <span class="svg-icon svg-icon-3x svg-icon-danger d-block my-2">
                                        <object data="{{ asset('assets/assets/media')}}/delivery-time.svg" type="image/svg+xml"></object>
                                    </span>
                                    <a href="{{ route('reports.invoice-to-delivery-lead-time-report.index') }}" class="text-red fw-bold fs-6">Invoice to Delivery Lead Time </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
     </div>
  </div>
</div>
@endsection

