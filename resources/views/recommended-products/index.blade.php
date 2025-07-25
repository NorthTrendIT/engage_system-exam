@extends('layouts.master')

@section('title','Recommended Products')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Recommended Products</h1>
      </div>

        @if(userrole() == 14)
        <div class="d-flex align-items-center py-1">
            <a href="javascript:" class="btn btn-sm btn-primary goToCart" style="display:none">Go to cart</a>
        </div>
        @else
        <div class="d-flex align-items-center py-1">
            <a href="{{ route('product-list.index') }}" class="btn btn-sm btn-primary">Back</a>
        </div>
        @endif

    </div>
  </div>

  <div class="post d-flex flex-column-fluid" id="kt_post">
    <div id="kt_content_container" class="container-xxl">
      <div class="row gy-5 g-xl-8">
        <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
          <div class="card card-xl-stretch mb-5 mb-xl-8">
            <div class="card-header border-0 pt-5">
              {{-- <h5>{{ isset($edit) ? "Update" : "Add" }} Details</h5> --}}
              <h5>Recommended Products</h5>
            </div>
            <div class="card-body pt-0">
                <div class="row">
                    @if(userrole() == 14)
                    <div class="col-xl-4 col-lg-4 col-md-4 col-12">
                        <div class="form-group">
                            <label class="col-form-label text-right">Select Customers<span class="asterisk">*</span></label>
                            <select class="form-select form-select-solid" id='selectCustomers' data-control="select2" data-hide-search="false" name="customer_id" @if(isset($edit)) disabled="disabled" @endif>
                                @if(isset($edit))
                                <option value="{{ $edit->customer_id }}" selected>{{ $edit->customer->card_name }}</option>
                                @else
                                <option value="">Select Customer</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    @else
                    {{-- <div class="col-md-6 mt-5">
                        <div class="input-icon">
                            <input type="text" class="form-control form-control-lg form-control-solid" placeholder="Search here..." name = "filter_search" autocomplete="off">
                            <span>
                            <i class="flaticon2-search-1 text-muted"></i>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-3 mt-5">
                        <a href="javascript:" class="btn btn-primary px-6 font-weight-bold search">Search</a>
                        <a href="javascript:" class="btn btn-light-dark font-weight-bold clear-search">Clear</a>
                    </div> --}}
                    @endif
                </div>
              <div class="row mb-5 mt-5">
                <div class="col-md-12">
                  <div class="form-group">
                    <div class="table-responsive" style="display: @if(Auth::user()->role_id == 2) none @endif">
                       <table class="table table-row-gray-300 align-middle gs-0 gy-4 table-bordered display nowrap" id="recommendedPTable">
                          <thead class="bg-dark text-white">
                            <tr>
                              <th style="width: 55px!important">No.</th>
                              <th>Name</th>
                              {{-- <th>Brand</th> --}}
                              {{-- <th>Code</th> --}}
                              <th>Price</th>
                              <th>Qty</th>
                              <th>Action</th>
                            </tr>
                          </thead>
                          <tbody>

                          </tbody>
                       </table>
                    </div>
                    @if(userrole() == 14)
                    <div class="text-center selectCustomerDiv">
                        <h2>Please select Customer.</h2>
                    </div>
                    @endif

                  </div>

                </div>
              </div>

            </div>
          </div>
        </div>
      </div>


      <div class="row gy-5 g-xl-8">
        <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
          <div class="card card-xl-stretch mb-5 mb-xl-8">
            <div class="card-header border-0 pt-5">
              <h5>Suggested Products</h5>
            </div>
            <div class="card-body pt-0">
              <div class="row mb-5 mt-5">
                <div class="col-md-12">
                  <div class="form-group">
                    <div class="table-responsive" style="display: @if(Auth::user()->role_id == 2) none @endif">
                       <table class="table table-row-gray-300 align-middle gs-0 gy-4 table-bordered display nowrap" id="orderPTable">
                          <thead class="bg-dark text-white">
                            <tr>
                              <th style="width: 55px!important">No.</th>
                              <th>Name</th>
                              <th>Price</th>
                              <th>Qty</th>
                              <th>Action</th>
                            </tr>
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
      </div>

    </div>
  </div>

  <!-- <div class="post d-flex flex-column-fluid" id="kt_post">
    <div id="kt_content_container" class="container-xxl">
      <div class="row gy-5 g-xl-8">
        <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
          <div class="card card-xl-stretch mb-5 mb-xl-8">
            {{-- <div class="card-header border-0 pt-5 min-0">
              <h5>View Details</h5>
            </div> --}}
            <div class="card-body">

              <div class="row mb-6">
                <div class="col-md-7">
                  <div class="input-icon">
                    <input type="text" class="form-control form-control-lg form-control-solid" placeholder="Search here..." name = "filter_search">
                    <span>
                      <i class="flaticon2-search-1 text-muted"></i>
                    </span>
                  </div>
                </div>

                <div class="col-md-3">
                  <a href="javascript:" class="btn btn-primary px-6 font-weight-bold search">Search</a>
                  <a href="javascript:" class="btn btn-light-dark font-weight-bold clear-search">Clear</a>
                </div>
              </div>

              <hr>

              <div class="row mb-5">
                <div class="col-md-12">
                  <div class="form-group">

                    <div class="row" id="product_list_row">

                    </div>

                    <div class="row mt-10">
                      <div class="col-md-12 d-flex justify-content-center" id="view_more_col">
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
  </div> -->
</div>
@endsection

@push('css')
<link href="{{ asset('assets')}}/assets/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet" type="text/css" />
<style>
  .button-wrap {
      display: flex;
      align-items: center;
      width: 169px;
  }
  .button-wrap .counter {
      margin-right: 22px;
      display: flex;
      align-items: center;
  }
  .button-wrap .counter i {
      color: black;
  }
  .button-wrap .counter a {
      color: black;
      background: #FAFAFB;
      border: 0.5px solid #E1E1FB !important;
      Width: 32px !important;
      Height: 32px !important;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 0;
      font-weight: bold;
  }
  .button-wrap input {
      color: black;
      background: #FAFAFB;
      border: 0.5px solid #E1E1FB !important;
      Width: 100px !important;
      Height: 32px !important;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 0;
      font-weight: bold;
  }
  .button-wrap .remove a {
      background-color: black;
      padding: 4px 25px 6px;
      color: white;
      text-transform: uppercase;
  }
</style>
@endpush

@push('js')
<script src="{{ asset('assets') }}/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<script src="{{ asset('assets') }}/assets/plugins/custom/sweetalert2/sweetalert2.all.min.js"></script>
<script>
$(document).ready(function() {
    @if(userrole() == 14)
    $('body').on('change' ,'#selectCustomers', function(){
            $customer_id = $('[name="customer_id"]').val();
            $cart_url = "{{ route('recommended-products.goToCart', '#') }}";
            $cart_url = $cart_url.replace("#", $customer_id);
            if($customer_id){
                render_recom_table($customer_id);
                $('.table-responsive').show();
                $('.goToCart').prop('href', $cart_url).show();
                $('.selectCustomerDiv').hide();
            } else {
                $('.table-responsive').hide();
                $('.goToCart').prop('href', 'javascript:;').hide();
                $('.selectCustomerDiv').show();
            }
        });
    @else
        render_recom_table();
        render_orderp_table();
    @endif

    function render_recom_table($customer_id = null){
      var table = $("#recommendedPTable");
      table.DataTable().destroy();

      $filter_search = $('[name="filter_search"]').val();

      table.DataTable({
          processing: true,
          serverSide: true,
          scrollX: true,
          order: [],
          ajax: {
              'url': "{{ route('recommended-products.get-all') }}",
              'type': 'POST',
              headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              data:{
                filter_search : $filter_search,
                customer_id: $customer_id,
              }
          },
          columns: [
              {data: 'DT_RowIndex', name: 'DT_RowIndex',orderable:false,searchable:false},
              {data: 'item_name', name: 'item_name', orderable:false},
              {data: 'price', name: 'price', orderable:false,searchable:false},
              {data: 'qty', name: 'qty', orderable:false,searchable:false},
              {data: 'action', name: 'action', orderable:false,searchable:false},
          ],
          columnDefs: [
            {targets: [0,4], className: "text-center" },
            {targets: [1,3], className: "d-flex justify-content-center" },
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

    function render_orderp_table($customer_id = null){
      var table = $("#orderPTable");
      table.DataTable().destroy();

      $filter_search = $('[name="filter_search"]').val();

      table.DataTable({
          processing: true,
          serverSide: true,
          scrollX: true,
          order: [],
          ajax: {
              'url': "{{ route('order-history-products.get-all') }}",
              'type': 'POST',
              headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              data:{
                filter_search : $filter_search,
                customer_id: $customer_id,
              }
          },
          columns: [
              {data: 'DT_RowIndex', name: 'DT_RowIndex',orderable:false,searchable:false},
              {data: 'item_name', name: 'item_name', orderable:false},
              {data: 'price', name: 'price', orderable:false,searchable:false},
              {data: 'qty', name: 'qty', orderable:false,searchable:false},
              {data: 'action', name: 'action', orderable:false,searchable:false},
          ],
          columnDefs: [
            {targets: [0,4], className: "text-center" },
            {targets: [1,3], className: "d-flex justify-content-center" },
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
      render_recom_table();
    });

    $(document).on('click', '.clear-search', function(event) {
      $('[name="filter_search"]').val('');
      render_recom_table();
    })

@if(userdepartment() != 1)
    $(document).on('click', '.addToCart', function(event) {
        event.preventDefault();
        $url = $(this).attr('data-url');
        $addToCartBtn = $(this);
        $goToCartBtn = $(this).parent().find('.goToCart');
        var value = $url.split("/")[5];
        $qty = $("#qty_"+value).val();
        if($qty < 1){
          Swal.fire({
            title: "Error",
            text: "Invalid Qty!",
            icon: "warning"
          });
        }else{
          $.ajax({
              url: $url,
              method: "POST",
              data: {
                      _token:'{{ csrf_token() }}',
                      qty: $qty,
                      @if(userrole()== 2)
                      customer_id: $('[name="customer_id"]').val(),
                      @endif
                  }
              })
              .done(function(result) {
                  if(result.status == false){
                      toast_error(result.message);
                  }else{
                      // $addToCartBtn.hide();
                      $goToCartBtn.show();
                      toast_success(result.message);

                      if(result.count > 0){
                          $('.cartCount').show();
                          $('.cartCount').html(result.count);
                      }
                  }
              })
              .fail(function() {
                  toast_error("error");
          });
        }
    });
@endif

@if(userrole() == 14)
    $("#selectCustomers").select2({
        ajax: {
            url: "{{route('sales-specialist-orders.getCustomers')}}",
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
                    results: response
                };
            },
            cache: true
        },
        placeholder: 'Select Customers',
        // minimumInputLength: 1,
        multiple: false,
        // data: $initialOptions
    });
@endif


$(document).on('click', '.qtyMinus', function(event) {
    $qty = parseInt($(this).parent().find('.qty').val());
    $self = $(this);
    if($qty >= 2){
      $(this).parent().find('.qty').val($qty - 1); 
    }       
});

$(document).on('click', '.qtyPlus', function(event) {
    $qty = parseInt($(this).parent().find('.qty').val());
    $self = $(this);
    $(this).parent().find('.qty').val($qty + 1);        
});


});
</script>
@endpush
