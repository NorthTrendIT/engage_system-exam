@extends('layouts.master')

@section('title','Dashboard')

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

  <div class="post d-flex flex-column-fluid" id="kt_post">
     <div id="kt_content_container" class="container-xxl">
        <div class="row gy-5 g-xl-8">
            <div class="card card-xl-stretch">
                <div class="card-body p-0">
                    <div class="card-p">
                        <div class="row">
                            <div class="col-md-3 bg-light-warning px-6 py-8 rounded-2 me-7 mb-7 min-w-150">
                                <span class="svg-icon svg-icon-3x svg-icon-warning d-block my-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <rect x="8" y="9" width="3" height="10" rx="1.5" fill="black"></rect>
                                    <rect opacity="0.5" x="13" y="5" width="3" height="14" rx="1.5" fill="black"></rect>
                                    <rect x="18" y="11" width="3" height="8" rx="1.5" fill="black"></rect>
                                    <rect x="3" y="13" width="3" height="6" rx="1.5" fill="black"></rect>
                                    </svg>
                                </span>
                                <a href="{{ route('report.promotion.index') }}" class="text-warning fw-bold fs-6">Promotions Report </a>
                            </div>

                            <div class="col-md-3 bg-light-primary px-6 py-8 rounded-2 me-7 mb-7 min-w-150">
                                <span class="svg-icon svg-icon-3x svg-icon-primary d-block my-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <rect x="8" y="9" width="3" height="10" rx="1.5" fill="black"></rect>
                                    <rect opacity="0.5" x="13" y="5" width="3" height="14" rx="1.5" fill="black"></rect>
                                    <rect x="18" y="11" width="3" height="8" rx="1.5" fill="black"></rect>
                                    <rect x="3" y="13" width="3" height="6" rx="1.5" fill="black"></rect>
                                    </svg>
                                </span>
                                <a href="{{ route('reports.sales-report.index') }}" class="text-primary fw-bold fs-6">Sales Report </a>
                            </div>

                            
                            {{-- <div class="col-md-3 bg-light-success px-6 py-8 rounded-2 me-7 mb-7 min-w-150">
                                <span class="svg-icon svg-icon-3x svg-icon-success d-block my-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <rect x="8" y="9" width="3" height="10" rx="1.5" fill="black"></rect>
                                    <rect opacity="0.5" x="13" y="5" width="3" height="14" rx="1.5" fill="black"></rect>
                                    <rect x="18" y="11" width="3" height="8" rx="1.5" fill="black"></rect>
                                    <rect x="3" y="13" width="3" height="6" rx="1.5" fill="black"></rect>
                                    </svg>
                                </span>
                                <a href="{{ route('reports.sales-order-report.index') }}" class="text-success fw-bold fs-6">Sales Order Report </a>
                            </div> --}}

                        </div>
                    </div>
                </div>
            </div>
        </div>
     </div>
  </div>
</div>
@endsection

