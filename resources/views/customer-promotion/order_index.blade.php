@extends('layouts.master')

@section('title','Claimed Promotions')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Claimed Promotions</h1>
      </div>
      
      <!--begin::Actions-->
      <div class="d-flex align-items-center py-1">

        <a href="{{ route('customer-promotion.index') }}" class="btn btn-sm btn-primary sync-products">Back</a>
        <!--end::Button-->
      </div>
      <!--end::Actions-->
      
    </div>
  </div>
  
  <div class="post d-flex flex-column-fluid" id="kt_post">
    <div id="kt_content_container" class="container-xxl">
      <div class="row gy-5 g-xl-8">
        <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
          <div class="card card-xl-stretch mb-5 mb-xl-8">
           {{--  <div class="card-header border-0 pt-5">
              <h5>{{ isset($edit) ? "Update" : "Add" }} Details</h5>
            </div> --}}
            <div class="card-body">
              <div class="row">

                <div class="col-md-3 mt-5">
                  <div class="input-icon">
                    <input type="text" class="form-control form-control-lg form-control-solid" placeholder="Search here..." name = "filter_search">
                    <span>
                      <i class="flaticon2-search-1 text-muted"></i>
                    </span>
                  </div>
                </div>

                @if(in_array(userrole(),[1,2]))
                <div class="col-md-4 mt-5">
                  <select class="form-control form-control-lg form-control-solid" name="filter_customer" data-control="select2" data-hide-search="false" data-placeholder="Select customer" data-allow-clear="true">
                    <option value=""></option>
                  </select>
                </div>
                @endif

                @if(in_array(userrole(),[1]))
                <div class="col-md-3 mt-5">
                  <select class="form-control form-control-lg form-control-solid" data-control="select2" data-hide-search="false" name="filter_company" data-allow-clear="true" data-placeholder="Select company">
                    <option value=""></option>
                    @foreach($company as $c)
                      <option value="{{ $c->id }}">{{ $c->company_name }}</option>
                    @endforeach
                  </select>
                </div>
                @endif
                
                <div class="col-md-2 mt-5">
                  <select class="form-control form-control-lg form-control-solid" name="filter_status" data-control="select2" data-hide-search="true" data-placeholder="Select status" data-allow-clear="true">
                    <option value=""></option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="canceled">Canceled</option>
                  </select>
                </div>

                <div class="col-md-3 mt-5">
                  <div class="input-icon">
                    <input type="text" class="form-control form-control-lg form-control-solid" placeholder="Selecte date range" name = "filter_date_range" id="kt_daterangepicker_1" readonly>
                    <span>
                    </span>
                  </div>
                </div>

                <div class="col-md-6 mt-5">
                  <a href="javascript:" class="btn btn-primary px-6 font-weight-bold search">Search</a>
                  <a href="javascript:" class="btn btn-light-dark font-weight-bold clear-search mr-10">Clear</a>
                  @if(in_array(userrole(),[1]))
                  <a href="javascript:" class="btn btn-success font-weight-bold download_excel ">Download Excel</a>
                  @endif
                </div>

              </div>
              <div class="row mb-5 mt-5">
                <div class="col-md-12">
                  <div class="form-group">
                    <!--begin::Table container-->
                    <div class="table-responsive">
                       <!--begin::Table-->
                       <table class="table table-row-gray-300 align-middle gs-0 gy-4 table-bordered display nowrap" id="myTable">
                          <!--begin::Table head-->
                          <thead>
                            <tr>
                              <th>No.</th>
                              @if(in_array(userrole(),[1]))
                              <th>Company</th>
                              @endif
                              <th>Promotion</th>
                              @if(in_array(userrole(),[1,2]))
                              <th>Customer</th>
                              @endif
                              <th>Date Time</th>
                              <th>Status</th>
                              <th>Action</th>
                            </tr>
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

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('css')
<link href="{{ asset('assets')}}/assets/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet" type="text/css" />
@endpush

@push('js')
<script src="{{ asset('assets') }}/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<script src="{{ asset('assets') }}/assets/plugins/custom/sweetalert2/sweetalert2.all.min.js"></script>
<script>
  $(document).ready(function() {

    render_table();

    function render_table(){
      var table = $("#myTable");
      table.DataTable().destroy();

      $filter_search = $('[name="filter_search"]').val();
      $filter_date_range = $('[name="filter_date_range"]').val();
      $filter_status = $('[name="filter_status"]').find('option:selected').val();
      $filter_customer = $('[name="filter_customer"]').find('option:selected').val();
      $filter_company = $('[name="filter_company"]').find('option:selected').val();

      table.DataTable({
          processing: true,
          serverSide: true,
          scrollX: true,
          order: [],
          ajax: {
              'url': "{{ route('customer-promotion.order.get-all') }}",
              'type': 'POST',
              headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              data:{
                filter_search : $filter_search,
                filter_date_range : $filter_date_range,
                filter_status : $filter_status,
                filter_customer : $filter_customer,
                filter_company : $filter_company,
              }
          },
          columns: [
              {data: 'DT_RowIndex', name: 'DT_RowIndex',orderable:false,searchable:false},
              @if(in_array(userrole(),[1]))
              {data: 'company', name: 'company'},
              @endif
              {data: 'promotion', name: 'promotion'},
              @if(in_array(userrole(),[1,2]))
              {data: 'user', name: 'user'},
              @endif
              {data: 'created_at', name: 'created_at'},
              {data: 'status', name: 'status'},
              {data: 'action', name: 'action', orderable: false, searchable: false},
          ],
          drawCallback:function(){
              $(function () {
                $('[data-toggle="tooltip"]').tooltip()
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
      $('[name="filter_date_range"]').val('');
      $('[name="filter_status"]').val('').trigger('change');
      $('[name="filter_customer"]').val('').trigger('change');
      $('[name="filter_company"]').val('').trigger('change');
      render_table();
    })

    @if(in_array(userrole(),[1,2]))
      $('[name="filter_customer"]').select2({
        ajax: {
            url: "{{route('customer-promotion.get-customer')}}",
            type: "post",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    _token: "{{ csrf_token() }}",
                    search: params.term
                };
            },
            processResults: function (response) {
              return {
                results:  $.map(response, function (item) {
                              return {
                                text: item.card_name + " (Code: " + item.card_code + ")",
                                id: item.user.id
                              }
                          })
              };
            },
            cache: true
        },
      });
    @endif


    @if(in_array(userrole(),[1]))
      $(document).on("click", ".download_excel", function(e) {
        var url = "{{route('customer-promotion.order.export')}}";

        var data = {};
        data.filter_search = $('[name="filter_search"]').val();
        data.filter_date_range = $('[name="filter_date_range"]').val();
        data.filter_status = $('[name="filter_status"]').find('option:selected').val();
        data.filter_customer = $('[name="filter_customer"]').find('option:selected').val();
        data.filter_company = $('[name="filter_company"]').find('option:selected').val();

        // console.log((JSON.stringify(data)));
        // console.log(btoa(JSON.stringify(data)));
        url = url + '?data=' + btoa(JSON.stringify(data));
        
        window.location.href = url;
      });
    @endif

  })
</script>
@endpush