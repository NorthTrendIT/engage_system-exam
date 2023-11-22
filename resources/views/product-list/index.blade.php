@extends('layouts.master')

@section('title','Product List')

@section('content')
<style type="text/css">
  .btn-check:active+.btn.btn-active-color-primary i, .btn-check:checked+.btn.btn-active-color-primary i, .btn.btn-active-color-primary.active i, .btn.btn-active-color-primary.show i, .btn.btn-active-color-primary:active:not(.btn-active) i, .btn.btn-active-color-primary:focus:not(.btn-active) i, .btn.btn-active-color-primary:hover:not(.btn-active) i, .show>.btn.btn-active-color-primary i{
    color: #50cd89 !important;
  }
  .fa-shopping-cart{
    color: #009ef7;
  }
 .tipClick>a{
    font-size: 20px;
    line-height: 21px
  }
  .tipClick{
   
    display:block;
    position:relative
  }
  .tipClick > a:focus + .tooltipT{
    /*TOOLTIP VISIBLE ON CLICKING THE ICON*/
      display:block;
  }
  .tooltipT{
      display:none;/*=====INITIAL HIDDEN STATE=====*/
      position: absolute;
      background: #fff;
      color-stop(0%,rgba(51,51,51,0.85)), color-stop(100%,rgba(0,0,0,0.85))); /* webkit */
      -moz-box-shadow: 0 0 6px 1px #666666;
      -webkit-box-shadow: 0 0 6px 1px #666666;
      box-shadow: 0 0 6px 1px #666666;
      border:solid #FFFFFF;
      color: #636363;
      padding:15px 5px 5px;
      font-family:Arial;
      font-size: .75em;
      text-align: center;
      text-align:left;
      bottom:30px;
      right:0px;
      z-index: 99;
      width: 384px;

    @media screen and (max-width: 600px) {
      font-size: .60em;
      width: 280px;
      left: -230px;
    }
  }
  .tooltipT p{
       position: relative !important;
       margin:10px;
    }
    /*=====CLOSE BUTTON=====*/
  .tooltipT span{
      position: absolute;
      top:10px;
      right: 10px;
      font-size: 20px;
      line-height: 1;
  }
  .tooltipT span a{
      text-decoration: none;
      color: #00529B;
  }
  .tooltipT span:focus .tooltipT{
      display: none;/*CLOSE TOOLTIP ON CLICK*/
  }
  /*======ARROW=====*/
  .tooltipT>.arrow, .tooltipT>.arrow:after {
      position: absolute;
      display: block;
      width: 0;
      height: 0;
      border-color: transparent;
      border-style: solid
  }
  .tipClick>.tooltipT>.arrow {
      border-width: 11px;
      bottom: -14px;
      right: 0px;
      margin-left: -11px;
      border-top-color: #999;
      border-top-color: rgba(0, 0, 0, .25);
      border-bottom-width: 0;
  }
  .tipClick>.tooltipT>.arrow:after {
      content: "";
      border-width: 10px;
      bottom: 1px;
      margin-left: -10px;
      content: " ";
      border-top-color: #fff;
      border-bottom-width: 0
  }


.product-img-container {
  display: flex;
  position: relative;
  height: 253px;
  /* width: 50%;
  max-width: 300px; */
  align-items: center !important;
}

.product-img-container img{
  height: 100%;
  width: auto;
}

.image {
  display: block;
  width: 100%;
  height: auto;
}

.product-img-overlay {
  position: absolute; 
  top: -7px; 
  left: -5px;
  /* background: rgb(0, 0, 0); */
  /*background: rgba(0, 0, 0, 0.5); */ /* Black see-through */
  color: #f1f1f1; 
  width: 45%;
  transition: .5s ease;
  opacity:1;
  color: white;
  font-size: 20px;
  padding: 20px;
  text-align: center;
}

.product-img-container:hover .product-img-overlay {
  opacity: 1;
}

.benefits-container{
  display: flex;
  position: relative;
}

.benefits-img-container {
  display: flex;
  padding: 1px;
  height: 27px;
  /* width: 25%; */
  max-width: 300px;
  align-items: center !important;
}


</style>
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

                  <!-- <div class="col-md-6 mt-5">
                    <select class="form-control form-control-lg form-control-solid" name="filter_product" id="filter_product" data-hide-search="false" data-placeholder="Search product" data-allow-clear="true">
                      <option value=""></option>
                    </select>
                  </div> -->

                <div class="col-md-5 ">
                  <div class="input-group">
                    <input type="text" class="form-control form-control-lg form-control-solid" placeholder="Search product" name="filter_search1" value="{{ request()->search }}" autocomplete="off">
                    {{-- <span class="input-group-text" id="basic-addon2">@example.com</span> --}}
                    <div class="input-group-text p-0">
                      <div class="tipClick ">
                        <a href="#"><img src="{{ asset('assets') }}/assets/media/help_icon.png" class="img-fluid" width="35" height="35"></a>
                        <strong class="tooltipT">
                          <p class="d-re"> Search products here by name e.g. ‘4T-10W' and code e.g. '3428396’</p>
                          <span><a href="#">&#10005;</a></span>
                          <div class="arrow"></div>
                        </strong>
                      </div>
                    </div>
                  </div>
                </div>

                 {{-- <div class="col-md-1 mt-5">
                  <!-- <a id="pro_btn" href="#" title="Hello from speech bubble!" class="tooltip">CSS Tooltip! Hover me!</a> -->

                   
                </div> --}}

                 

                <div class="col-md-2">
                  <select class="form-control form-control-lg form-control-solid" name="filter_brand" id="filter_brand" data-control="select2" data-hide-search="false" data-placeholder="Select brand" data-allow-clear="true">
                    <option value=""></option>
                    @foreach($c_product_groups as $key)
                      <option value="{{ urlencode($key->product_group->group_name) }}" {{ request()->brand === $key->product_group->group_name ? 'selected' : '' }}>{{ $key->product_group->group_name }}</option>
                    @endforeach
                  </select>
                </div>

                <div class="col-md-3">
                  <select class="form-control form-control-lg form-control-solid" name="filter_product_category" id="filter_product_category" data-control="select2" data-hide-search="false" data-placeholder="Select product category" data-allow-clear="true">
                    <option value=""></option>
                    @foreach($c_product_category as $key => $c)
                    <option value="{{ urlencode($c) }}" {{ request()->cat === $c ? 'selected' : '' }}>{{ $c }}</option>
                    @endforeach
                  </select>
                </div>

                {{-- <div class="col-md-3 mt-5">
                  <select class="form-control form-control-lg form-control-solid" name="filter_product_line" id="filter_product_line" data-control="select2" data-hide-search="false" data-placeholder="Select product line" data-allow-clear="true">
                    <option value=""></option>
                    @foreach($c_product_line as $key => $l)
                    <option value="{{ urlencode($l->u_item_line) }}" {{ request()->line === $l->u_item_line ? 'selected' : '' }}>{{ @$l->u_item_line_sap_value->value ?? $l->u_item_line }}</option>
                    @endforeach
                  </select>
                </div> --}}

                <div class="mt-5">
                  <a href="javascript:" class="btn btn-primary px-6 font-weight-bold search">Search</a>
                  <a href="javascript:" class="btn btn-light-dark font-weight-bold clear-search">Clear</a>

                  <div class="form-check d-inline-block mt-5">
                    <input class="form-check-input" name="products" type="checkbox" value="" id="products_chx">
                    <label class="form-check-label" for="products_chx">
                      Show Products Images
                    </label>
                  </div>
                </div>

              </div>
              <div class="row mb-5 mt-5">
                <div class="col-md-12">
                  <div class="form-group mb-3">
                    <div class="table-responsive d-none">
                       <table class="table table-row-gray-300 align-middle gs-0 gy-4 table-bordered display nowrap" id="myTable">
                          <thead class="bg-dark text-white">
                            <tr>
                              <th style="width:55px !important">No.</th>
                              <!-- <th>Product Code</th> -->
                              <th>Name</th>
                              <!-- <th>Brand</th> -->
                              
                              <th>Brand</th>
                              <th>Product Line</th>
                              <th>Product Category</th>
                              @if(userrole() != 2)
                              <th>Price</th>
                              <th class="text-center">Qty</th>
                              @endif
                              <th>Action</th>
                            </tr>
                          </thead>
                          <tbody>

                          </tbody>
                       </table>
                    </div>

                    <div class="row row-cols-1 row-cols-md-4 g-4 d-none">
                      @if($product_lists->total() > 0)
                        @foreach($product_lists as $p)
                          <div class="col">
                            <div class="card h-100 border border-3 m-0">
                              <div class="product-img-container justify-content-center">
                                @if($p->product_images->count() > 0)
                                  <img src="{{ get_valid_file_url('sitebucket/products', $p->product_images->first()->image) }}" class="card-img-top" alt="...">
                                @else
                                  @if($p->group->group_name === "DELKOR")
                                    <img src="{{ get_valid_file_url('sitebucket/products/default', 'delkor.png') }}" class="card-img-top" alt="...">
                                  @elseif($p->group->group_name === "CASTROL")
                                    <img src="{{ get_valid_file_url('sitebucket/products/default', 'castrol-1L.png') }}" class="card-img-top" alt="...">
                                  @else
                                    <img src="{{ get_valid_file_url('sitebucket/products/default', 'tire.png') }}" class="card-img-top" alt="...">
                                  @endif

                                  {{-- <svg class="bd-placeholder-img card-img-top" width="100%" height="180" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: No Image" preserveAspectRatio="xMidYMid slice" focusable="false"><title>No Image</title><rect width="100%" height="100%" fill="#868e96"></rect><text x="50%" y="50%" fill="#dee2e6" dy=".3em" dx="-2.7em">No Image</text></svg> --}}
                                @endif
                                <div class="product-img-overlay">
                                  @if (strpos($p->group->group_name, 'CASTROL') !== false)
                                    <img src="{{ get_valid_file_url('sitebucket/products/brand', 'CASTROL.png') }}" class="card-img-top" alt="...">
                                  @elseif (strpos($p->group->group_name, 'MAXXIS') !== false)
                                    <img src="{{ get_valid_file_url('sitebucket/products/brand', 'MAXXIS.png') }}" class="card-img-top" alt="...">
                                  @elseif (strpos($p->group->group_name, 'CST') !== false)
                                    <img src="{{ get_valid_file_url('sitebucket/products/brand', 'CST.png') }}" class="card-img-top" alt="...">
                                  @elseif (strpos($p->group->group_name, 'ARISUN') !== false)
                                    <img src="{{ get_valid_file_url('sitebucket/products/brand', 'arisun-badge.png') }}" class="card-img-top" alt="...">
                                  @elseif (strpos($p->group->group_name, 'BFG') !== false)
                                    <img src="{{ get_valid_file_url('sitebucket/products/brand', 'BFGOODRICH.png') }}" class="card-img-top" alt="...">
                                  @elseif (strpos($p->group->group_name, 'DELKOR') !== false)
                                    <img src="{{ get_valid_file_url('sitebucket/products/brand', 'DELKOR.png') }}" class="card-img-top" alt="...">
                                  @elseif (strpos($p->group->group_name, 'MICHELIN') !== false)
                                    <img src="{{ get_valid_file_url('sitebucket/products/brand', 'MICHELIN.png') }}" class="card-img-top" alt="...">
                                  @elseif (strpos($p->group->group_name, 'PRESA') !== false)
                                    <img src="{{ get_valid_file_url('sitebucket/products/brand', 'PRESA.png') }}" class="card-img-top" alt="...">
                                  @endif
                                </div>
                              </div>
                              <div class="benefits-container justify-content-center">
                                @php
                                  $ids = explode(",",$p->product_benefits);
                                  $benefits_list = \App\Models\ProductBenefits::whereIn('id', $ids)->get();
                                @endphp
                                @foreach($benefits_list as $bnf)
                                  <div class="benefits-img-container border border-3">
                                    <img src="{{ asset('storage/products/benefits/'.$bnf->icon.'')}}" class="card-img-top rounded " title="{{$bnf->name}}" height="20">
                                  </div>
                                @endforeach
                              </div>
                              <div class="card-body p-2" style="position: relative;">
                                <h5 class="card-title m-0">{{ $p->item_name }}</h5>
                                <p class="card-text text-muted">{{ $p->item_code }}</p>
                                <p class="card-text h4">
                                  @php
                                    $sap_connection_id = $p->sap_connection_id;
                                    $currency_symbol = '';
                                    $price = 0;
                                    foreach($customer_vat as $cust){
                                        if($sap_connection_id === $cust->real_sap_connection_id){                                       
                                            $currency_symbol = get_product_customer_currency(@$p->item_prices, $cust->price_list_num);
                                            $price = get_product_customer_price(@$p->item_prices,@$customer_price_list_no[$sap_connection_id]);
                                        }
                                    }

                                    if(round($p->quantity_on_stock - $p->quantity_ordered_by_customers) < 1){
                                        echo $currency_symbol.' '.number_format_value($price);
                                    }else{
                                        echo $currency_symbol." ".number_format_value($price);
                                    }
                                  @endphp
                                </p>

                              </div>
                              <div class="card-footer p-2 d-flex align-items-center">
                                @php
                                  $qty = '1';
                                  $html= '<div class="button-wrap">
                                                <div class="counter">
                                                    <a href="javascript:;" class="btn btn-xs btn-icon mr-2 qtyMinus">
                                                        <i class="fas fa-minus"></i>
                                                    </a>

                                                    <input class="form-control qty text-center" type="number" min="1" value="'.$qty.'" id="qty_'.$p->id.'">

                                                    <a href="javascript:;" class="btn btn-xs btn-icon mr-2 qtyPlus">
                                                        <i class="fas fa-plus"></i>
                                                    </a>
                                                </div>
                                            </div>';
                                  echo $html;    
                                @endphp
                                <button class="btn btn-primary btn-sm addToCart" data-url="{{ route('cart.add',@$p->id) }}" title="Add to Cart" {{ $price > 0 ? '' : 'disabled' }}><i class="fa fa-shopping-cart"></i></button>
                              </div>
                            </div>
                          </div>
                        @endforeach
                      @else
                        <div class="col-md-12">
                           <h1 class="text-center mt-15"><em><span class="fa fa-search text-danger"></span> No result found.</em></h1>
                        </div>
                      @endif
                      <div class="col-md-12">
                        {{ $product_lists->links('pagination::bootstrap-4') }}
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
    render_table();
    if(window.location.search.indexOf("checked=yes") === -1){
      $('.table-responsive').removeClass('d-none');
    }else{
      $('.table-responsive').next().removeClass('d-none');
      $('#products_chx').trigger('click');
    }

    function render_table(){
      var table = $("#myTable");
      table.DataTable().destroy();

      $filter_search = $('[name="filter_product"]').val();
      $filter_search1 = $('[name="filter_search1"]').val();
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
                filter_search1 : $filter_search1,
                filter_brand : $filter_brand,
                filter_product_category : $filter_product_category,
                filter_product_line : $filter_product_line,
              }
          },
          columns: [
              {data: 'DT_RowIndex', name: 'DT_RowIndex',orderable:false,searchable:false},
              // {data: 'item_code', name: 'item_code'},
              {data: 'item_name', name: 'item_name', orderable:false},
            //   {data: 'brand', name: 'brand'},
              
              {data: 'brand', name: 'brand', orderable:false},
              {data: 'u_item_line', name: 'u_item_line', orderable:false},
              {data: 'u_tires', name: 'u_tires', orderable:false},
              @if(userrole() != 2)
              {data: 'price', name: 'price', orderable:false,searchable:false},
              {data: 'qty', name: 'qty', orderable:false,searchable:false},
              @endif
              {data: 'action', name: 'action', orderable:false,searchable:false},
          ],
          columnDefs: [
            {targets: [0], className: "text-center" },
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
              // if (price[0] <= 0) {
              //     $(row).hide();
              // }
          },  
          initComplete: function () {
          }
        });
    }

    $(document).on('click', '.search', function(event) {
      var url = window.location;
      var chbx = "";
      if($('#products_chx').is(":checked")){
        chbx = "&checked=yes";
      }else{
        // render_table();
      }
      window.location.href =  url.origin + url.pathname + "?" + url_str() +chbx; 
    });

    $(document).on('click', 'a.page-link', function(event) {
      event.preventDefault();
      window.location.href = $(this).attr('href') +"&"+ url_str() +"&checked=yes";
    });

    $(document).on('click', '.clear-search', function(event) {
      $('input').val('');
      $('select').val('').trigger('change');
      render_table();
    })

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
      var value = $url.split("/")[5];
      if($('#products_chx').is(":checked")){
        $qty = $(this).parent().find("#qty_"+value).val();
      }else{
        $qty = $("#qty_"+value).val();
      }
      console.log($qty);
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
                    qty:$qty,
                    }
            })
            .done(function(result) {
                if(result.status == false){
                    toast_error(result.message);
                }else{
                    // $addToCartBtn.hide();
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
      }
    });
  @endif

  @if(@Auth::user()->role_id == 4) //customer
    // $('.table-responsive').remove();
  @else
    $('.table-responsive').next().remove();
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

  $(document).on('change','#filter_product',function(){
    $('.search').click();
  });

  $(document).on('change','#filter_brand',function(){
    $('.search').click();
  });

  $(document).on('change','#filter_product_category',function(){
    $('.search').click();
  });

  $(document).on('change','#filter_product_line',function(){
    $('.search').click();
  });

  $(document).on('click','#pro_btn',function(){
    var product = $('[name="filter_search1"]').val();
    $.ajax({
      url: "{{route('product-list.get-product-details')}}",
      method:'POST',
      data: {
         _token:'{{ csrf_token() }}',
        data : product,
      },
      success: function(results){
        //$("#results").append(html);
      }
    });
});

function url_str(){
  $filter_search1 = $('[name="filter_search1"]').val();
  $filter_brand = $('[name="filter_brand"]').find('option:selected').val();
  $filter_product_category = $('[name="filter_product_category"]').find('option:selected').val();
  $filter_product_line = $('[name="filter_product_line"]').find('option:selected').val();

  return "search="+$filter_search1+"&brand="+$filter_brand+"&cat="+$filter_product_category;
}

$('#products_chx').on('click', function(e){
  var refresh = '';
  var url = window.location;
  var ins = (url.search === "") ? "?" : "&"; 
  var result = url.search.substring(0, url.search.indexOf("checked=yes"));
  var chbx_yes = url.search.substring(0, ( url.search.indexOf("checked=yes") - 1 ));
  var url_param = ( result === "" ) ? ins+'checked=yes' : '';

  if($(this).is(":checked")){
    $('.table-responsive').addClass('d-none');
    $('.table-responsive').next().removeClass('d-none');
    
    refresh = url.href + url_param;
  }else{
    $('.table-responsive').removeClass('d-none');
    $('.table-responsive').next().addClass('d-none');
    // render_table();
    $(window).trigger('resize');
    refresh = (chbx_yes === "") ? url.origin + url.pathname : chbx_yes;
  }

  window.history.pushState({ path: refresh }, '', refresh);
})

// $(window).resize(function() {
//     $('.table-responsive').height($(window).height() - 0);
// });


});

</script>
@endpush
