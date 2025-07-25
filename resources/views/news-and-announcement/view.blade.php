@extends('layouts.master')

@section('title','Notification')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="toolbar" id="kt_toolbar">
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
        <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
            <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Notification</h1>
        </div>

        <div class="d-flex align-items-center py-1">
            <a href="{{ route('news-and-announcement.index') }}" class="btn btn-sm btn-primary sync-products">Back</a>
        </div>

        </div>
    </div>

    <div class="post d-flex flex-column-fluid detail-view-table" id="kt_post">
        <div id="kt_content_container" class="container-xxl">
            <div class="row gy-5 g-xl-8">
                <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
                <div class="card card-xl-stretch mb-5 mb-xl-8">

                    {{-- <div class="card-header border-0 pt-5 min-0">
                    <h5>Notification Details</h5>
                    </div> --}}

                    <div class="card-body">
                        <div class="notif-contents">
                            <div class="border border-5 border-secondary p-3">
                                <div class="row mb-5 message-notif" data-id="{{$data->id}}">
                                    <div class="col-md-12">
                                    <div class="form-group">
                                        <!--begin::Table container-->
                                        <div class="table-responsive">
                                        <!--begin::Table-->
                                        <table class="table table-bordered" id="myTable">
                                            <!--begin::Table head-->
                                            <thead>
                                                <tr>
                                                {{-- <th> </th> --}}
                                                <td class="text-center" colspan="2">
                                                    <p class="lead"><h3 class="display-6">{{ @$data->title ?? "" }}</h3></p>
                                                    <p class="">Posted: <i class="text-info">{{$data->created_at->toDayDateTimeString()}}</i></p>
                                                </td>
                                                </tr>
                                                @if(@Auth::user()->role_id == 1)
                                                <tr>
                                                <th> <b>Priority</b> </th>
                                                @if($data->is_important == 0)
                                                <td><button type="button" class="btn btn-light-info btn-sm">Normal</button></td>
                                                @elseif($data->is_important == 1)
                                                <td><button type="button" class="btn btn-light-danger btn-sm">Important</button></td>
                                                @endif
                                                </tr>
                                                @endif

                                                {{-- <tr>
                                                <th> <b>Notification Type</b> </th>
                                                <td>{{ getNotificationType($data->type) }}</td>
                                                </tr> --}}

                                                <tr>
                                                {{-- <th>  </th> --}}
                                                <td colspan="2">{!! @$data->message ?? "" !!}</td>
                                                </tr>

                                                @if(@Auth::user()->role_id == 1)
                                                <tr>
                                                    <th> <b>Customers :</b> </th>
                                                    <td>@if(@$data->module != 'all') By @endif {{ ucwords(str_replace("_"," ",@$data->module)) ?? "" }}</td>
                                                </tr>

                                                @if(@$data->module == 'customer_class')
                                                    <tr>
                                                        <th> <b>Customer Selection:</b> </th>
                                                        <td>{{ @$data->customer_selection == "all" ? "All" : "Specific"}} Customers</td>
                                                    </tr>
                                                @endif

                                                @if(@$data->module == 'market_sector')
                                                    @if(!empty(@$data->request_payload))
                                                    <tr>
                                                        <th> <b>Market Sectors:</b> </th>
                                                        <td>{!! implode(", ",json_decode(@$data->request_payload)); !!}</td>
                                                    </tr>
                                                    @endif
                                                @endif

                                                @if(@$data->module == 'brand')
                                                    @if(!empty($brands))
                                                    <tr>
                                                        <th> <b>Brands:</b> </th>
                                                        <td>{{ implode(", ",json_decode(@$brands)) }}</td>
                                                    </tr>
                                                    @endif
                                                @endif

                                                @if(@$data->module == 'territory')
                                                    @if(!empty($territories))
                                                    <tr>
                                                        <th> <b>Territories:</b> </th>
                                                        <td>{{ implode(", ",json_decode(@$territories)) }}</td>
                                                    </tr>
                                                    @endif
                                                @endif

                                                <tr>
                                                    <th> <b>Start Date:</b> </th>
                                                    <td>{{ date('M d, Y',strtotime($data->start_date)) }}</td>
                                                </tr>

                                                <tr>
                                                    <th> <b>End Date:</b> </th>
                                                    <td>{{ date('M d, Y',strtotime($data->end_date)) }}</td>
                                                </tr>

                                                <tr>
                                                    <th> <b>Is Active:</b> </th>
                                                    @if($data->is_active)
                                                    <td><button type="button" class="btn btn-sm btn-light-success font-weight-bold">Active</button></td>
                                                    @else
                                                    <td><button type="button" class="btn btn-sm btn-light-danger font-weight-bold">Inactive</button></td>
                                                    @endif
                                                </tr>
                                                @endif

                                            </thead>
                                            <!--end::Table head-->
                                            <!--begin::Table body-->
                                            <tbody>

                                            </tbody>
                                            <!--end::Table body-->
                                        </table>
                                        <!--end::Table-->
                                        </div>
                                        <!--end::Table container-->

                                    </div>

                                    </div>
                                </div>
                                @if(!empty($data->documents) && count($data->documents) > 0)
                                    <div class="row mb-5">
                                        <h1 class="d-block mb-5 text-center">Attached Documents</h1>
                                        @foreach($data->documents as $item)
                                        @php
                                            $temp = explode('.', $item->file);
                                            $ext = strtolower(end($temp));
                                        @endphp
                                            @if(in_array($ext, ['jpeg', 'jpg', 'png', 'bmp', 'tif', 'tiff', 'webp']))
                                                <div class="col-md-4">
                                                    <a href="{{ asset('sitebucket/news-and-announcement/'.$item->file) }}" class="fancybox">
                                                        <img class="img-thumbnail" src="{{ asset('sitebucket/news-and-announcement/'.$item->file) }}" height="240px" width="320px"/>
                                                    </a>
                                                </div>
                                            @elseif(in_array($ext, ['mp4']))
                                                <div class="col-md-4">
                                                    <video width="320" height="240" class="img-thumbnail" controls>
                                                        <source src="{{ asset('sitebucket/news-and-announcement/'.$item->file) }}" type="video/{{$ext}}">
                                                        Your browser does not support the video tag.
                                                    </video>
                                                </div>
                                            @else
                                                <div class="col-md-4">
                                                    <iframe src='https://docs.google.com/viewer?url={{ asset('sitebucket/news-and-announcement/'.$item->file) }}&embedded=true' frameborder='0'></iframe>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                                {{-- <div class="row">
                                    <div class="d-flex justify-content-end">
                                        <p class="">Posted: <i class="text-info">{{$data->created_at->toDayDateTimeString()}}</i></p>
                                    </div>
                                </div> --}}
                            </div>
                        </div>
                    </div>

                </div>
                </div>
            </div>
            @if(@auth::user()->role_id == 1)
            <div class="row gy-5 g-xl-8">
                <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
                    <div class="card card-xl-stretch mb-5 mb-xl-8">

                        <div class="card-header border-0 pt-5 min-0">
                            <h5>Users</h5>
                        </div>

                        <div class="card-body">
                            <div class="row mb-5">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="myDataTable">
                                            <thead>
                                                <th>No.</th>
                                                @if(@$data->module == 'customer' || @$data->module == 'customer_class' || @$data->module == 'territory' || @$data->module == 'market_sector' || @$data->module == 'brand' || @$data->module == 'all' || @$data->module == 'sales_specialist')
                                                <th>Customer Name</th>
                                                @endif
                                                {{--
                                                @if(@$data->module == 'sales_specialist')
                                                <th>Sales Specialist Name</th>
                                                @endif
                                                --}}
                                                @if(@$data->module == 'market_sector')
                                                <th>Market Sector</th>
                                                @endif
                                                {{--
                                                @if(@$data->module == 'brand')
                                                <th>Brand</th>
                                                @endif
                                                --}}
                                                @if(@$data->module == 'customer' || @$data->module == 'sales_specialist' || @$data->module == 'role' || @$data->module == 'all')
                                                <th>Role</th>
                                                @endif
                                                @if(@$data->module == 'customer_class')
                                                <th>Customer Class</th>
                                                @endif
                                                @if(@$data->module == 'territory')
                                                <th>Territory</th>
                                                @endif
                                                <th>Is Seen</th>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- @if(!empty($data->documents) && count($data->documents) > 0)
    <div class="post d-flex flex-column-fluid detail-view-table" id="kt_post">
        <div id="kt_content_container" class="container-xxl">
            <div class="row gy-5 g-xl-8">
                <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
                    <div class="card card-xl-stretch mb-5 mb-xl-8">

                        <div class="card-header border-0 pt-5 min-0">
                        <h5>Documents</h5>
                        </div>

                        <div class="card-body">
                            <div class="row mb-5">
                                @foreach($data->documents as $item)
                                @php
                                    $temp = explode('.', $item->file);
                                    $ext = strtolower(end($temp));
                                @endphp
                                    @if(in_array($ext, ['jpeg', 'jpg', 'png', 'bmp', 'tif', 'tiff', 'webp']))
                                        <div class="col-md-4">
                                            <a href="{{ asset('sitebucket/news-and-announcement/'.$item->file) }}" class="fancybox">
                                                <img src="{{ asset('sitebucket/news-and-announcement/'.$item->file) }}" height="120px"/>
                                            </a>
                                        </div>
                                    @else
                                        <div class="col-md-4">
                                            <video width="320" height="240" controls>
                                                <source src="{{ asset('sitebucket/news-and-announcement/'.$item->file) }}" type="video/{{$ext}}">
                                                Your browser does not support the video tag.
                                            </video>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif --}}
</div>
@endsection

@push('css')
<link href="{{ asset('assets')}}/assets/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet" type="text/css" />
@endpush

@push('js')
@if(@Auth::user()->role_id == 1)
<script src="{{ asset('assets') }}/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<script src="{{ asset('assets') }}/assets/plugins/custom/sweetalert2/sweetalert2.all.min.js"></script>
<script>
$(document).ready(function() {
    render_table();

    function render_table(){
      var table = $("#myDataTable");
      table.DataTable().destroy();

      $filter_search = $('[name="filter_search"]').val();
      $filter_type = $('[name="filter_type"]').find('option:selected').val();

      table.DataTable({
          processing: true,
          serverSide: true,
          scrollX: true,
          order: [],
          ajax: {
              @if(@$data->module == 'role')
              'url': "{{ route('news-and-announcement.getAllRole') }}",
              @endif
              @if(@$data->module == 'customer' || @$data->module == 'all')
              'url': "{{ route('news-and-announcement.getAllCustomer') }}",
              @endif
              @if(@$data->module == 'sales_specialist')
              'url': "{{ route('news-and-announcement.getAllSalesSpecialist') }}",
              @endif
              @if(@$data->module == 'customer_class')
              'url': "{{ route('news-and-announcement.getAllCustomerClass') }}",
              @endif
              @if(@$data->module == 'territory')
              'url': "{{ route('news-and-announcement.getAllTerritory') }}",
              @endif
              @if(@$data->module == 'market_sector')
              'url': "{{ route('news-and-announcement.getAllMarketSector') }}",
              @endif
              @if(@$data->module == 'brand')
              'url': "{{ route('news-and-announcement.getAllBrands') }}",
              @endif
              'type': 'POST',
              headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              data:{
                filter_search : $filter_search,
                filter_type : $filter_type,
                notification_id : "{{@$data->id}}",
              }
          },
          columns: [
              {data: 'DT_RowIndex', orderable: false},
              {data: 'user_name', name: 'user_name', orderable: false},
              @if(@$data->module == 'customer' || @$data->module == 'sales_specialist' || @$data->module == 'all')
              {data: 'role', name: 'role', orderable: false},
              @endif
              @if(@$data->module == 'market_sector')
              {data: 'market_sector', name: 'market_sector', orderable: false},
              @endif
              {{--
              @if(@$data->module == 'brand')
              {data: 'brand', name: 'brand', orderable: false},
              @endif
              --}}
              @if(@$data->module == 'customer_class')
              {data: 'class_name', name: 'class_name', orderable: false},
              @endif
              @if(@$data->module == 'territory')
              {data: 'territory', name: 'territory', orderable: false},
              @endif
              {data: 'is_seen', name: 'is_seen', orderable: false},
          ],
          drawCallback:function(){
              $(function () {
                $('[data-toggle="tooltip"]').tooltip();
                $('table tbody tr td:last-child').attr('nowrap', 'nowrap');
              })
          },
          initComplete: function () {
          }
        });
    }

    $(document).on('click', '.search', function(event) {
      render_table();
    });

    $(document).on('click', '.clear-search', function(event) {
      $('[name="filter_search"]').val('');
      $('[name="filter_status"]').val('').trigger('change');
      render_table();
    });
});
</script>
@else <!-- for customer -->
<script>
    $(window).scroll(function() {
        if($(window).scrollTop() + $(window).height() == $(document).height()) {

            var notif_id = $(document).find("div.notif-contents .message-notif").last().attr('data-id');

            $.get( "{{ route('news-and-announcement.feed-previous') }}", { notif_id : notif_id},  function(resp) {
                $(document).find('.notif-contents .notif-contents-end').parent().remove();
                $('.notif-contents').append(resp);
            })
            // .done(function() {
            //         alert( "second success" );
            //     })
            .fail(function(xhr, status, error) {
                if(xhr.status === 404){ //previous solution
                    if(!$('.notif-contents .notif-contents-end').length){
                        $('.notif-contents').append('<div class="row mt-10 bg-light p-5"><h1 class="notif-contents-end text-center">No more posts to show.</h1></div>');
                    }
                }else{
                    alert("An AJAX error occured: " + status + "\nError: " + error);
                }
            })
        }
    });
</script>
@endif
@endpush
