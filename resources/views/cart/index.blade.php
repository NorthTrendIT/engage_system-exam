@extends('layouts.master')

@section('title','My Cart')

@section('content')
    <div class="toolbar" id="kt_toolbar">
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
        <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
            <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">My Cart</h1>
        </div>
        </div>
    </div>
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <div id="kt_content_container" class="container-xxl">
            <div class="row gy-5 g-xl-8 mt-5">
                <!--begin::Col-->
                <div class="col-xl-8">
                <div class="card card-xl-stretch mb-5 mb-xl-8">
                        <!--begin::Header-->
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bolder text-dark">Products (4)</span>
                            </h3>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body pt-5">
                            <div class="d-flex align-items-sm-center mb-7">
                                <!--begin::Section-->
                                <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                    <div class="flex-grow-1 me-2">
                                        <span class="text-gray-800 fs-6 fw-bolder">ISZU DMAX 2011 LT TITATNIUM SILV</span>
                                        <span class="text-muted fw-bold d-block fs-7">CODE: UQZ551</span>
                                    </div>
                                    <span class="badge badge-light fw-bolder my-2">$ 100</span>
                                </div>
                                <!--end::Section-->
                            </div>
                        </div>
                        <!--end::Body-->
                        <div class="separator separator-solid"></div>
                        <!--begin::Body-->
                        <div class="card-body pt-5">
                            <div class="d-flex align-items-sm-center mb-7">
                                <!--begin::Section-->
                                <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                    <div class="flex-grow-1 me-2">
                                        <span class="text-gray-800 fs-6 fw-bolder">DRAWER - FPD2000 FIXED PEDESTAL DRAWER 400X500X400MM</span>
                                        <span class="text-muted fw-bold d-block fs-7">CODE: FF05-129</span>
                                    </div>
                                    <span class="badge badge-light fw-bolder my-2">$ 100</span>
                                </div>
                                <!--end::Section-->
                            </div>
                        </div>
                        <!--end::Body-->
                        <div class="separator separator-solid"></div>
                        <!--begin::Body-->
                        <div class="card-body pt-5">
                            <div class="d-flex align-items-sm-center mb-7">
                                <!--begin::Section-->
                                <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                    <div class="flex-grow-1 me-2">
                                        <span class="text-gray-800 fs-6 fw-bolder">UPS - APC BACK UPBX6250CI-MS 625 VA 230V W/ AVR BLACK</span>
                                        <span class="text-muted fw-bold d-block fs-7">CODE: DP00-014</span>
                                    </div>
                                    <span class="badge badge-light fw-bolder my-2">$ 100</span>
                                </div>
                                <!--end::Section-->
                            </div>
                        </div>
                        <!--end::Body-->
                        <div class="separator separator-solid"></div>
                        <!--begin::Body-->
                        <div class="card-body pt-5">
                            <div class="d-flex align-items-sm-center mb-7">
                                <!--begin::Section-->
                                <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                    <div class="flex-grow-1 me-2">
                                        <span class="text-gray-800 fs-6 fw-bolder">MAXXIS TUBE 6.50-14 TR13</span>
                                        <span class="text-muted fw-bold d-block fs-7">CODE: MXT0009</span>
                                    </div>
                                    <span class="badge badge-light fw-bolder my-2">$ 100</span>
                                </div>
                                <!--end::Section-->
                            </div>
                        </div>
                        <!--end::Body-->
                    </div>
                </div>
                <!--end::Col-->

                <!--begin::Col-->
                <div class="col-xl-4">
                    <!--begin::List Widget 4-->
                    <div class="card card-xl-stretch mb-5 mb-xl-8">
                        <!--begin::Header-->
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bolder text-dark">Price Details</span>
                            </h3>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body pt-5">
                            <!--begin::Item-->
                            <div class="d-flex align-items-sm-center mb-7">
                                <!--begin::Section-->
                                <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                    <div class="flex-grow-1 me-2">
                                        <span class="text-gray-800 fs-6 fw-bolder">Price</span>
                                    </div>
                                    <span class="fw-bolder my-2">$ 820</span>
                                </div>
                                <!--end::Section-->
                            </div>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <div class="d-flex align-items-sm-center mb-7">
                                <!--begin::Section-->
                                <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                    <div class="flex-grow-1 me-2">
                                        <span class="text-gray-800 fs-6 fw-bolder">Discount</span>
                                    </div>
                                    <span class="fw-bolder my-2">$ -20</span>
                                </div>
                                <!--end::Section-->
                            </div>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <div class="d-flex align-items-sm-center mb-7">
                                <!--begin::Section-->
                                <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                    <div class="flex-grow-1 me-2">
                                        <span class="text-gray-800 fs-6 fw-bolder">Delivery Charrges</span>
                                    </div>
                                    <span class="fw-bolder my-2">FREE</span>
                                </div>
                                <!--end::Section-->
                            </div>
                            <!--end::Item-->

                            <!--begin::Item-->
                            <div class="d-flex align-items-sm-center mb-7">
                                <!--begin::Section-->
                                <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                    <div class="flex-grow-1 me-2">
                                        <h3 class="text-gray-800 fs-6 fw-bolder">Total Amount</h3>
                                    </div>
                                    <span class="fw-bolder my-2">$ 800</span>
                                </div>
                                <!--end::Section-->
                            </div>
                            <!--end::Item-->
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::List Widget 4-->
                </div>
                <!--end::Col-->
            </div>
        </div>
    </div>
    <!--begin::Profile Personal Information-->

    <!--end::Profile Personal Information-->
@endsection
