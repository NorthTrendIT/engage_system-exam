@extends('layouts.master')

@section('title','Product List')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Product List</h1>
      </div>

      <div class="d-flex align-items-center py-1">
        <!--begin::Button-->
        <a href="{{ route('recommended-products.index') }}" class="btn btn-sm btn-primary create-btn">Recommended Products</a>
        <!--end::Button-->
      </div>

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
                {{-- <div class="col-md-6 mt-5">
                  <div class="input-icon">
                    <input type="text" class="form-control form-control-lg form-control-solid" placeholder="Search here..." name = "filter_search" autocomplete="off">
                    <span>
                      <i class="flaticon2-search-1 text-muted"></i>
                    </span>
                  </div>
                </div> --}}

                <div class="col-md-6 mt-5">
                  <select class="form-control form-control-lg form-control-solid" name="filter_product" data-hide-search="false" data-placeholder="Search product" data-allow-clear="true">
                    <option value=""></option>
                  </select>
                </div>

                <div class="col-md-3 mt-5">
                  <select class="form-control form-control-lg form-control-solid" name="filter_brand" data-control="select2" data-hide-search="false" data-placeholder="Select brand" data-allow-clear="true">
                    <option value=""></option>
                    @foreach($c_product_groups as $key)
                    <option value="{{ $key->product_group->group_name }}">{{ $key->product_group->group_name }}</option>
                    @endforeach
                  </select>
                </div>

                <div class="col-md-3 mt-5">
                  <select class="form-control form-control-lg form-control-solid" name="filter_product_category" data-control="select2" data-hide-search="false" data-placeholder="Select product category" data-allow-clear="true">
                    <option value=""></option>
                    @foreach($c_product_category as $key => $c)
                    <option value="{{ $c }}">{{ $c }}</option>
                    @endforeach
                  </select>
                </div>

                <div class="col-md-3 mt-5">
                  <select class="form-control form-control-lg form-control-solid" name="filter_product_line" data-control="select2" data-hide-search="false" data-placeholder="Select product line" data-allow-clear="true">
                    <option value=""></option>
                    @foreach($c_product_line as $key => $l)
                    <option value="{{ $l->u_item_line }}">{{ @$l->u_item_line_sap_value->value ?? $l->u_item_line }}</option>
                    @endforeach
                  </select>
                </div>

                <div class="col-md-3 mt-5">
                  <a href="javascript:" class="btn btn-primary px-6 font-weight-bold search">Search</a>
                  <a href="javascript:" class="btn btn-light-dark font-weight-bold clear-search">Clear</a>
                </div>

              </div>
              <div class="row mb-5 mt-5">
                <div class="col-md-12">
                  <div class="form-group">
                    <div class="table-responsive">
                       <table class="table table-row-gray-300 align-middle gs-0 gy-4 table-bordered display nowrap" id="myTable">
                          <thead>
                            <tr>
                              <th style="width:24px !important">No.</th>
                              <th>Name</th>
                              <!-- <th>Brand</th>
                              <th>Code</th> -->
                              <th>Brand</th>
                              <th>Product Line</th>
                              <th>Product Category</th>
                              @if(userrole() != 2)
                              <th>Price</th>
                              @endif
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

      $filter_search = $('[name="filter_product"]').val();
      $filter_brand = $('[name="filter_brand"]').find('option:selected').val();
      $filter_product_category = $('[name="filter_product_category"]').find('option:selected').val();
      $filter_product_line = $('[name="filter_product_line"]').find('option:selected').val();

      table.DataTable({
          processing: true,
          serverSide: true,
          scrollX: true,
          order: [],
          ajax: {
              'url': "{{ route('product-list.get-all') }}",
              'type': 'POST',
              headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              data:{
                filter_search : $filter_search,
                filter_brand : $filter_brand,
                filter_product_category : $filter_product_category,
                filter_product_line : $filter_product_line,
              }
          },
          columns: [
              {data: 'DT_RowIndex', name: 'DT_RowIndex',orderable:false,searchable:false},
              {data: 'item_name', name: 'item_name'},
            //   {data: 'brand', name: 'brand'},
            //   {data: 'item_code', name: 'item_code'},
              {data: 'brand', name: 'brand'},
              {data: 'u_item_line', name: 'u_item_line'},
              {data: 'u_tires', name: 'u_tires'},
              @if(userrole() != 2)
              {data: 'price', name: 'price', orderable:false,searchable:false},
              @endif
              {data: 'action', name: 'action', orderable:false,searchable:false},
          ],
          drawCallback:function(){
              $(function () {
                $('[data-toggle="tooltip"]').tooltip()
                $('table tbody tr td:last-child').attr('nowrap', 'nowrap');
              })
          },
          rowCallback: function( row, data, index ) {
              var split_price = (data['price']).split(' ');
              var price = split_price[1].split('.');
              if (price[0] <= 0) {
                  $(row).hide();
              }
          },  
          initComplete: function () {
          }
        });
    }

    $(document).on('click', '.search', function(event) {
      render_table();
    });

    $(document).on('click', '.clear-search', function(event) {
      $('input').val('');
      $('select').val('').trigger('change');
      render_table();
    })
//   getProductList();

//   function getProductList($id = ""){
//     $('#view_more_btn').remove();

//     $filter_search = $('[name="filter_search"]').val();

//     $.ajax({
//       url: '{{ route('product-list.get-all') }}',
//       type: 'POST',
//       dataType:'json',
//       data: {
//               id: $id,
//               filter_search : $filter_search,
//               _token:'{{ csrf_token() }}',
//             },
//     })
//     .done(function(data) {
//       $('#product_list_row').append(data.output);
//       $('#view_more_col').html(data.button);
//     })
//     .fail(function() {
//       toast_error("error");
//     });
//   }

//   $(document).on('click', '#view_more_btn', function(event) {
//     event.preventDefault();
//     $id = $(this).attr('data-id');
//     getProductList($id);
//   });

//   $(document).on('click', '.search', function(event) {
//     $('#product_list_row').html("");
//     getProductList();
//   });

//   $(document).on('click', '.clear-search', function(event) {
//     $('#product_list_row').html("");
//     $('[name="filter_search"]').val('');
//     getProductList();
//   })

//   $('input[name=filter_search]').on('keydown', function(e) {
//     if (e.which == 13) {
//       $('.search').trigger('click')
//     }
//   });

  @if(userdepartment() != 1)
    $(document).on('click', '.addToCart', function(event) {
      event.preventDefault();
      $url = $(this).attr('data-url');
      $addToCartBtn = $(this);
      $goToCartBtn = $(this).parent().find('.goToCart');
        $.ajax({
            url: $url,
            method: "POST",
            data: {
                    _token:'{{ csrf_token() }}'
                    }
            })
            .done(function(result) {
                if(result.status == false){
                    toast_error(result.message);
                }else{
                    $addToCartBtn.hide();
                    $goToCartBtn.show();
                    if(result.count > 0){
                        $('.cartCount').show();
                        $('.cartCount').html(result.count);
                    }
                    toast_success(result.message);
                    // setTimeout(function(){
                    //     window.location.reload();
                    // },1500)
                }
            })
            .fail(function() {
                toast_error("error");
            });
    });
  @endif

  $('[name="filter_product"]').select2({
    ajax: {
      url: "{{route('product-list.get-products')}}",
      type: "post",
      dataType: 'json',
      delay: 250,

      data: function (params) {
          return {
              _token: "{{ csrf_token() }}",
              filter_search: params.term
          };
      },
      processResults: function (response) {
        return {
          results:  $.map(response, function (item) {
                        return {
                          text: item.item_name,
                          id: item.item_name
                        }
                    })
        };
      },
      cache: true
    },
    tags: true,
    minimumInputLength: 2,
  });
});
</script>
@endpush
