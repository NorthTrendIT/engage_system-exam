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
        <!--begin::Actions-->
        <div class="d-flex align-items-center py-1">
           <!--begin::Wrapper-->
           <div class="me-4">
              <!--begin::Menu-->
              <a href="#" class="btn btn-sm btn-flex btn-light btn-active-primary fw-bolder" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
              <!--begin::Svg Icon | path: icons/duotune/general/gen031.svg-->
              <span class="svg-icon svg-icon-5 svg-icon-gray-500 me-1">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                 <path fill-rule="evenodd" clip-rule="evenodd" d="M11 16.9656L11.3892 16.6653C11.8265 16.328 12.4544 16.409 12.7918 16.8463C13.1291 17.2836 13.0481 17.9115 12.6108 18.2489L10.6277 19.7787C10.5675 19.8274 10.5025 19.8682 10.4342 19.901C10.3808 19.928 10.309 19.9545 10.234 19.9725C10.1375 19.9951 10.0448 20.0033 9.95295 19.9989C9.81197 19.9924 9.6786 19.9567 9.55877 19.8976C9.4931 19.8654 9.43047 19.8257 9.37226 19.7787L7.38919 18.2489C6.9519 17.9115 6.87088 17.2836 7.20823 16.8463C7.54557 16.409 8.17353 16.328 8.61081 16.6653L9 16.9656V6.45711C9 5.90482 9.44772 5.45711 10 5.45711C10.5523 5.45711 11 5.90482 11 6.45711V16.9656ZM10.5 0C13.0609 0 15.2376 1.76105 15.8369 4.17236C18.2436 4.77356 20 6.95407 20 9.518C20 12.3327 17.8828 14.6868 15.1101 14.9939C14.5612 15.0547 14.0669 14.659 14.0061 14.1101C13.9453 13.5612 14.341 13.0669 14.8899 13.0061C16.6514 12.8109 18 11.3114 18 9.518C18 7.71741 16.6408 6.21401 14.8706 6.02783C14.4009 5.97843 14.0298 5.60718 13.9806 5.13748C13.7947 3.36183 12.2947 2 10.5 2C8.70372 2 7.20292 3.36415 7.01891 5.14171C6.96154 5.69596 6.46222 6.0964 5.90874 6.03205C5.77394 6.01638 5.63757 6.00847 5.5 6.00847C3.56748 6.00847 2 7.57926 2 9.518C2 11.3114 3.34862 12.8109 5.11011 13.0061C5.65903 13.0669 6.05473 13.5612 5.99392 14.1101C5.93311 14.659 5.43882 15.0547 4.88989 14.9939C2.11715 14.6868 0 12.3327 0 9.518C0 6.57497 2.30383 4.17018 5.20435 4.01629C5.85706 1.68561 7.99449 0 10.5 0Z" fill="#92929D"/>
                 </svg>

              </span>
              <!--end::Svg Icon--></a>
              <!--begin::Menu 1-->
              <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px" data-kt-menu="true" id="kt_menu_616838fb2edc9">
                 <!--begin::Header-->
                 <div class="px-7 py-5">
                    <div class="fs-5 text-dark fw-bolder">Filter Options</div>
                 </div>
                 <!--end::Header-->
                 <!--begin::Menu separator-->
                 <div class="separator border-gray-200"></div>
                 <!--end::Menu separator-->
                 <!--begin::Form-->
                 <div class="px-7 py-5">
                    <!--begin::Input group-->
                    <div class="mb-10">
                       <!--begin::Label-->
                       <label class="form-label fw-bold">Status:</label>
                       <!--end::Label-->
                       <!--begin::Input-->
                       <div>
                          <select class="form-select form-select-solid" data-kt-select2="true" data-placeholder="Select option" data-dropdown-parent="#kt_menu_616838fb2edc9" data-allow-clear="true">
                             <option></option>
                             <option value="1">Approved</option>
                             <option value="2">Pending</option>
                             <option value="2">In Process</option>
                             <option value="2">Rejected</option>
                          </select>
                       </div>
                       <!--end::Input-->
                    </div>
                    <!--end::Input group-->
                    <!--begin::Input group-->
                    <div class="mb-10">
                       <!--begin::Label-->
                       <label class="form-label fw-bold">Member Type:</label>
                       <!--end::Label-->
                       <!--begin::Options-->
                       <div class="d-flex">
                          <!--begin::Options-->
                          <label class="form-check form-check-sm form-check-custom form-check-solid me-5">
                             <input class="form-check-input" type="checkbox" value="1" />
                             <span class="form-check-label">Author</span>
                          </label>
                          <!--end::Options-->
                          <!--begin::Options-->
                          <label class="form-check form-check-sm form-check-custom form-check-solid">
                             <input class="form-check-input" type="checkbox" value="2" checked="checked" />
                             <span class="form-check-label">Customer</span>
                          </label>
                          <!--end::Options-->
                       </div>
                       <!--end::Options-->
                    </div>
                    <!--end::Input group-->
                    <!--begin::Input group-->
                    <div class="mb-10">
                       <!--begin::Label-->
                       <label class="form-label fw-bold">Notifications:</label>
                       <!--end::Label-->
                       <!--begin::Switch-->
                       <div class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                          <input class="form-check-input" type="checkbox" value="" name="notifications" checked="checked" />
                          <label class="form-check-label">Enabled</label>
                       </div>
                       <!--end::Switch-->
                    </div>
                    <!--end::Input group-->
                    <!--begin::Actions-->
                    <div class="d-flex justify-content-end">
                       <button type="reset" class="btn btn-sm btn-light btn-active-light-primary me-2" data-kt-menu-dismiss="true">Reset</button>
                       <button type="submit" class="btn btn-sm btn-primary" data-kt-menu-dismiss="true">Apply</button>
                    </div>
                    <!--end::Actions-->
                 </div>
                 <!--end::Form-->
              </div>
              <!--end::Menu 1-->
              <!--end::Menu-->
           </div>
           <!--end::Wrapper-->
           <!--begin::Button-->
           <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_create_app" id="kt_toolbar_primary_button">Create</a>
           <!--end::Button-->
        </div>
        <!--end::Actions-->
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
                    <div class="card-header border-0">
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
           <div class="col-xl-4">
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

                <!-- Pending Promotion -->
                <div class="card card-custom gutter-b mt-5">
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
           @endif
        </div>

        <div class="row gy-5 g-xl-8 pt-6">
            <div class="col-xl-6">
                <div class="card card-xl-stretch mb-xl-8">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <a href="{{ route('report.promotion.index') }}" class="text-dark text-hover-primary fw-bolder fs-3">Promotion Reports</a>
                        </h3>
                        <!-- <div class="card-toolbar">
                            <button type="button" class="btn btn-sm btn-icon btn-color-primary btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                <span class="svg-icon svg-icon-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <rect x="5" y="5" width="5" height="5" rx="1" fill="#000000"></rect>
                                            <rect x="14" y="5" width="5" height="5" rx="1" fill="#000000" opacity="0.3"></rect>
                                            <rect x="5" y="14" width="5" height="5" rx="1" fill="#000000" opacity="0.3"></rect>
                                            <rect x="14" y="14" width="5" height="5" rx="1" fill="#000000" opacity="0.3"></rect>
                                        </g>
                                    </svg>
                                </span>
                            </button>
                            <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px" data-kt-menu="true" id="kt_menu_622acb65b541a">
                                <div class="px-7 py-5">
                                    <div class="fs-5 text-dark fw-bolder">Filter Options</div>
                                </div>

                                <div class="separator border-gray-200"></div>
                                <div class="px-7 py-5">
                                    <div class="mb-10">
                                        <label class="form-label fw-bold">Business Unit:</label>
                                        <div>
                                            <select class="form-select form-select-solid select2-hidden-accessible" data-kt-select2="true" data-placeholder="Select Business Unit"  data-allow-clear="true" tabindex="-1"  name="filter_company">
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mb-10">
                                        <label class="form-label fw-bold">Member Type:</label>
                                        <div class="d-flex">
                                            <label class="form-check form-check-sm form-check-custom form-check-solid me-5">
                                                <input class="form-check-input" type="checkbox" value="1">
                                                <span class="form-check-label">Author</span>
                                            </label>
                                            <label class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="2" checked="checked">
                                                <span class="form-check-label">Customer</span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="mb-10">
                                        <label class="form-label fw-bold">Notifications:</label>
                                        <div class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                                            <input class="form-check-input" type="checkbox" value="" name="notifications" checked="checked">
                                            <label class="form-check-label">Enabled</label>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        <button type="reset" class="btn btn-sm btn-light btn-active-light-primary me-2" data-kt-menu-dismiss="true">Reset</button>
                                        <button type="submit" class="btn btn-sm btn-primary" data-kt-menu-dismiss="true">Apply</button>
                                    </div>
                                </div>
                            </div>
                        </div> -->
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
        $.ajax({
            url: '{{ route('report.promotion.get-chart-data') }}',
            method: "POST",
            data: {
                    _token:'{{ csrf_token() }}',
                }
          })
          .done(function(result) {
            if(result.status == false){
              toast_error(result.message);
            }else{
                render_peomorion_graph(result.data, result.category)
            }
          })
          .fail(function() {
            toast_error("error");
          });
    }

    function render_peomorion_graph(data, category){

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
</script>
@endif
@endpush
