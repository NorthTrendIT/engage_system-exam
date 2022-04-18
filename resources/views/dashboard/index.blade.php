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
                                <a href="{{ route('news-and-announcement.show',$item->id) }}" class="fw-bolder text-gray-800 text-hover-primary fs-6">{{ $item->title }}</a>
                                <span class="text-muted fw-bold d-block">{{ getNotificationType($item->type) }}</span>
                            </div>
                        </div>
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
                        <div id="promotion_report_cart" style="height: 350px; min-height: 365px;">

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
                        <div id="product_report_cart" style="height: 350px; min-height: 365px;">

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
                        <div id="back_order_report_cart" style="height: 350px; min-height: 365px;">

                        </div>
                        <!--end::Chart-->
                    </div>
                    <!--end::Body-->
                </div>
                <!--end::Charts Widget 1-->
            </div>
        </div>
     </div>
     <!--end::Container-->
  </div>
  <!--end::Post-->
</div>
<!--end::Content-->
@endsection

@push('js')
@if(@Auth::user()->role_id == 1)
<script>
    getData();

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
@endpush
