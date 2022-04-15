@extends('layouts.master')

@section('title','Product Tagging')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Product Tagging</h1>
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

                <div class="col-md-3 mt-5">
                  <select class="form-control form-control-lg form-control-solid" data-control="select2" data-hide-search="false" name="filter_company" data-allow-clear="true" data-placeholder="Select business unit">
                    <option value=""></option>
                    @foreach($company as $c)
                      <option value="{{ $c->id }}">{{ $c->company_name }}</option>
                    @endforeach
                  </select>
                </div>

                <div class="col-md-3 mt-5 filter_brand_div" style="display:none;">
                  <select class="form-control form-control-lg form-control-solid" name="filter_brand" data-control="select2" data-hide-search="false" data-placeholder="Select brand" data-allow-clear="true">
                    <option value=""></option>
                    {{-- @foreach($product_groups as $product_group)
                    <option value="{{ $product_group->number }}">{{ $product_group->group_name }} @if(in_array(userrole(),[1,2]) && @$product_group->sap_connection->company_name) ({{ @$product_group->sap_connection->company_name }}) @endif</option>
                    @endforeach --}}
                  </select>
                </div>

                <div class="col-md-3 mt-5 other_filter_div">
                  <select class="form-control form-control-lg form-control-solid" name="filter_product_category" data-control="select2" data-hide-search="false" data-placeholder="Select product category" data-allow-clear="true">
                    <option value=""></option>
                    {{-- @foreach($product_category as $key => $c)
                    <option value="{{ $key }}">{{ $key }}</option>
                    @endforeach --}}
                  </select>
                </div>

                <div class="col-md-3 mt-5 other_filter_div">
                  <select class="form-control form-control-lg form-control-solid" name="filter_product_line" data-control="select2" data-hide-search="false" data-placeholder="Select product line" data-allow-clear="true">
                    <option value=""></option>
                    {{-- @foreach($product_line as $key => $l)
                    <option value="{{ $key }}">{{ $key }}</option>
                    @endforeach --}}
                  </select>
                </div>
                

                <div class="col-md-3 mt-5 other_filter_div">
                  <select class="form-control form-control-lg form-control-solid" name="filter_product_class" data-control="select2" data-hide-search="false" data-placeholder="Select product class" data-allow-clear="true">
                    <option value=""></option>
                  </select>
                </div>


                <div class="col-md-3 mt-5 other_filter_div">
                  <select class="form-control form-control-lg form-control-solid" name="filter_product_type" data-control="select2" data-hide-search="false" data-placeholder="Select product type" data-allow-clear="true">
                    <option value=""></option>
                  </select>
                </div>


                <div class="col-md-6 mt-5 other_filter_div">
                  <select class="form-control form-control-lg form-control-solid" name="filter_product_application" data-control="select2" data-hide-search="false" data-placeholder="Select product application" data-allow-clear="true">
                    <option value=""></option>
                  </select>
                </div>


                <div class="col-md-3 mt-5 other_filter_div">
                  <select class="form-control form-control-lg form-control-solid" name="filter_product_pattern" data-control="select2" data-hide-search="false" data-placeholder="Select product pattern" data-allow-clear="true">
                    <option value=""></option>
                  </select>
                </div>

                <div class="col-md-3 mt-5">
                  <div class="input-icon">
                    <input type="text" class="form-control form-control-lg form-control-solid" placeholder="Search here..." name="filter_search" autocomplete="off">
                  </div>
                </div>

                <div class="col-md-6 mt-5">
                  <a href="javascript:" class="btn btn-primary px-6 font-weight-bold search">Search</a>
                  <a href="javascript:" class="btn btn-light-dark font-weight-bold clear-search mx-2">Clear</a>
                  @if(in_array(userrole(),[1]))
                  <a href="javascript:" class="btn btn-success font-weight-bold download_excel ">Export Excel</a>
                  @endif
                </div>

              </div>
              <div class="row mb-5 mt-5">
                <div class="col-md-12">
                  <div class="form-group">
                    <!--begin::Table container-->
                    <div class="table-responsive column-left-right-fix-scroll-hidden">
                       <!--begin::Table-->
                       <table class="table table-row-gray-300 align-middle gs-0 gy-4 table-bordered display nowrap" id="myTable">
                          <!--begin::Table head-->
                          <thead>
                            <tr>
                              <th>No.</th>
                              <th>Product Code</th>
                              <th>Product Name</th>
                              <th>Brand</th>
                              <th>Product Category</th>
                              <th>Business Unit</th>
                              <th>Product Line</th>
                              
                              <th>Unit</th>
                              <th>RDLP</th>
                              <th>Commercial Price</th>
                              <th>SRP</th>

                              <th>Product Application</th>
                              <th>Product Type</th>
                              <th>Product Class</th>
                              <th>Product Pattern</th>
                              <th>Product Technology</th>

                              {{-- Tires Category --}}
                              <th>Tread Pattern Type</th>
                              <th>Section Width</th>
                              <th>Series</th>
                              <th>Tire Diameter</th>
                              <th>Load Index</th>
                              <th>Speed Symbol</th>
                              <th>Ply Rating</th>
                              <th>Tire Construction</th>
                              <th>Fitment Configuration</th>


                              {{-- Battery Category --}}
                              <th>L</th>
                              <th>W</th>
                              <th>H</th>
                              <th>TH</th>
                              <th>RC</th>
                              <th>CCA</th>
                              <th>AH</th>
                              <th>Handle</th>
                              <th>Polarity</th>
                              <th>Terminal</th>
                              <th>Hold down</th>
                              <th>Lead Weight(kg)</th>
                              <th>Total Weight(kg)</th>
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
<link href="{{ asset('assets')}}/assets/css/switch.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/fixedcolumns/4.0.1/css/fixedColumns.dataTables.min.css">

<style type="text/css">
  .other_filter_div{
    display: none;
  }
</style>
@endpush

@push('js')
<script src="{{ asset('assets') }}/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<script src="{{ asset('assets') }}/assets/plugins/custom/sweetalert2/sweetalert2.all.min.js"></script>
<script src="https://cdn.datatables.net/fixedcolumns/4.0.1/js/dataTables.fixedColumns.min.js"></script>
<script>
  $(document).ready(function() {

    render_table();

    function render_table(){
      var table = $("#myTable");
      table.DataTable().destroy();

      $filter_search = $('[name="filter_search"]').val();
      $filter_company = $('[name="filter_company"]').find('option:selected').val();
      $filter_brand = $('[name="filter_brand"]').find('option:selected').val();
      $filter_product_category = $('[name="filter_product_category"]').find('option:selected').val();
      $filter_product_line = $('[name="filter_product_line"]').find('option:selected').val();
      $filter_product_class = $('[name="filter_product_class"]').find('option:selected').val();
      $filter_product_type = $('[name="filter_product_type"]').find('option:selected').val();
      $filter_product_application = $('[name="filter_product_application"]').find('option:selected').val();
      $filter_product_pattern = $('[name="filter_product_pattern"]').find('option:selected').val();

      table.DataTable({
          processing: true,
          serverSide: true,
          scrollX: true,
          scrollY: "800px",
          scrollCollapse: true,
          paging: true,
          fixedColumns:   {
            left: 2,  
            right: 0
          },
          order: [],
          ajax: {
              'url': "{{ route('product.get-all') }}",
              'type': 'POST',
              headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              data:{
                filter_search : $filter_search,
                filter_company : $filter_company,
                filter_brand : $filter_brand,
                filter_product_category : $filter_product_category,
                filter_product_line : $filter_product_line,
                filter_product_class : $filter_product_class,
                filter_product_type : $filter_product_type,
                filter_product_application : $filter_product_application,
                filter_product_pattern : $filter_product_pattern,
              }
          },
          columns: [
              {data: 'DT_RowIndex', name: 'DT_RowIndex',orderable:false,searchable:false},
              {data: 'item_code', name: 'item_code'},
              {data: 'item_name', name: 'item_name'},
              {data: 'brand', name: 'brand'},
              {data: 'u_tires', name: 'u_tires'},
              {data: 'company', name: 'company'},
              {data: 'u_item_line', name: 'u_item_line'},

              {data: 'unit', name: 'unit',orderable:false,searchable:false},
              {data: 'rdlp_price', name: 'rdlp_price',orderable:false,searchable:false},
              {data: 'commercial_price', name: 'commercial_price',orderable:false,searchable:false},
              {data: 'srp_price', name: 'srp_price',orderable:false,searchable:false},

              {data: 'u_item_application', name: 'u_item_application'},
              {data: 'u_item_type', name: 'u_item_type'},
              {data: 'item_class', name: 'item_class'},
              {data: 'u_pattern2', name: 'u_pattern2'},
              {data: 'product_technology', name: 'product_technology'},

              {{-- Tires Category --}}
              {data: 'u_pattern_type', name: 'u_pattern_type'},
              {data: 'u_section_width', name: 'u_section_width'},
              {data: 'u_series', name: 'u_series'},
              {data: 'u_tire_diameter', name: 'u_tire_diameter'},
              {data: 'u_loadindex', name: 'u_loadindex'},
              {data: 'u_speed_symbol', name: 'u_speed_symbol'},
              {data: 'u_ply_rating', name: 'u_ply_rating'},
              {data: 'u_tire_const', name: 'u_tire_const'},
              {data: 'u_fitment_conf', name: 'u_fitment_conf'},


              {{-- Battery Category --}}
              {data: 'u_blength', name: 'u_blength'},
              {data: 'u_bwidth', name: 'u_bwidth'},
              {data: 'u_bheight', name: 'u_bheight'},
              {data: 'u_bthicknes', name: 'u_bthicknes'},
              {data: 'u_brsvdcapacity', name: 'u_brsvdcapacity'},
              {data: 'u_bcoldcrankamps', name: 'u_bcoldcrankamps'},
              {data: 'u_bamperhour', name: 'u_bamperhour'},
              {data: 'u_bhandle', name: 'u_bhandle'},
              {data: 'u_bpolarity', name: 'u_bpolarity'},
              {data: 'u_bterminal', name: 'u_bterminal'},
              {data: 'u_bholddown', name: 'u_bholddown'},
              {data: 'u_bleadweight', name: 'u_bleadweight'},
              {data: 'u_btotalweight', name: 'u_btotalweight'},
          ],
          drawCallback:function(){
              $(function () {
                $('[data-toggle="tooltip"]').tooltip()
                $('table tbody tr td:last-child').attr('nowrap', 'nowrap');

                for (var i = 13; i <=37; i++) {
                  $("#myTable").DataTable().column(i).visible(false);
                }

                $product_category = $('[name="filter_product_category"]').find('option:selected').val().toLowerCase();

                // Hide Product Type
                if(jQuery.inArray($product_category, ["tires"]) !== -1){
                  $("#myTable").DataTable().column(12).visible(false);
                }

                // Shows Product Class
                if(jQuery.inArray($product_category, ["lubes","tires"]) !== -1){
                  $("#myTable").DataTable().column(13).visible(true);
                }

                if(jQuery.inArray($product_category, ["tires"]) !== -1){
                  $("#myTable").DataTable().column(11).visible(true);
                  $("#myTable").DataTable().column(14).visible(true);
                }

                // Shows Product Technology
                if(jQuery.inArray($product_category, ["battery","autoparts"]) !== -1){
                  $("#myTable").DataTable().column(15).visible(true);
                }

                // Shows Tires Field
                if(jQuery.inArray($product_category, ["tires"]) !== -1){
                  for (var j = 16; j <=24; j++) {
                    $("#myTable").DataTable().column(j).visible(true);
                  }
                }

                // Shows Battery Field
                if(jQuery.inArray($product_category, ["battery"]) !== -1){
                  for (var k = 25; k <=37; k++) {
                    $("#myTable").DataTable().column(k).visible(true);
                  }
                }

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

      $('input').val('');
      $('select').val('').trigger('change');
      render_table();
    })

    @if(in_array(userrole(),[1]))
      $(document).on("click", ".download_excel", function(e) {
        var url = "{{route('product.export')}}";

        var data = {};
        data.module_type = 'product-tagging';
        data.filter_search = $('[name="filter_search"]').val();
        data.filter_date_range = $('[name="filter_date_range"]').val();
        data.filter_status = $('[name="filter_status"]').find('option:selected').val();
        data.filter_brand = $('[name="filter_brand"]').find('option:selected').val();
        data.filter_product_category = $('[name="filter_product_category"]').find('option:selected').val();
        data.filter_product_line = $('[name="filter_product_line"]').find('option:selected').val();
        data.filter_company = $('[name="filter_company"]').find('option:selected').val();

        // console.log((JSON.stringify(data)));
        // console.log(btoa(JSON.stringify(data)));
        url = url + '?data=' + btoa(JSON.stringify(data));

        window.location.href = url;
      });
    @endif


    $(document).on('change', '[name="filter_company"]', function(event) {
      event.preventDefault();
      $('[name="filter_brand"]').val('').trigger('change');
      
      if($(this).find('option:selected').val() != ""){
        $('.filter_brand_div').show();
      }else{
        $('.filter_brand_div').hide();
        $('.other_filter_div').hide();
      }

    });

    $(document).on('change', '[name="filter_brand"]', function(event) {
      event.preventDefault();
      $('[name="filter_product_category"]').val('').trigger('change');
      $('[name="filter_product_line"]').val('').trigger('change');
      $('[name="filter_product_class"]').val('').trigger('change');
      $('[name="filter_product_type"]').val('').trigger('change');
      $('[name="filter_product_application"]').val('').trigger('change');
      $('[name="filter_product_pattern"]').val('').trigger('change');

      if($(this).find('option:selected').val() != ""){
        $('.other_filter_div').show();
      }else{
        $('.other_filter_div').hide();
      }
    });


    $('[name="filter_brand"]').select2({
      ajax: {
        url: "{{route('product.get-brand-data')}}",
        type: "post",
        dataType: 'json',
        delay: 250,
        data: function (params) {
          return {
            _token: "{{ csrf_token() }}",
            search: params.term,
            sap_connection_id: $('[name="filter_company"]').find('option:selected').val(),
          };
        },
        processResults: function (response) {
          return {
            results:  $.map(response, function (item) {
                        return {
                          text: item.group_name,
                          id: item.number
                        }
                      })
          };
        },
        cache: true
      },
    });


    $('[name="filter_product_category"]').select2({
      ajax: {
        url: "{{route('product.get-product-category-data')}}",
        type: "post",
        dataType: 'json',
        delay: 250,
        data: function (params) {
          return {
            _token: "{{ csrf_token() }}",
            search: params.term,
            sap_connection_id: $('[name="filter_company"]').find('option:selected').val(),
            items_group_code: $('[name="filter_brand"]').find('option:selected').val(),
          };
        },
        processResults: function (response) {
          return {
            results:  $.map(response, function (item, index) {
                        return {
                          text: index,
                          id: index
                        }
                      })
          };
        },
        cache: true
      },
    });


    $('[name="filter_product_line"]').select2({
      ajax: {
        url: "{{route('product.get-product-line-data')}}",
        type: "post",
        dataType: 'json',
        delay: 250,
        data: function (params) {
          return {
            _token: "{{ csrf_token() }}",
            search: params.term,
            sap_connection_id: $('[name="filter_company"]').find('option:selected').val(),
            items_group_code: $('[name="filter_brand"]').find('option:selected').val(),
          };
        },
        processResults: function (response) {
          return {
            results:  $.map(response, function (item, index) {
                        return {
                          text: index,
                          id: index
                        }
                      })
          };
        },
        cache: true
      },
    });


    $('[name="filter_product_class"]').select2({
      ajax: {
        url: "{{route('product.get-product-class-data')}}",
        type: "post",
        dataType: 'json',
        delay: 250,
        data: function (params) {
          return {
            _token: "{{ csrf_token() }}",
            search: params.term,
            sap_connection_id: $('[name="filter_company"]').find('option:selected').val(),
            items_group_code: $('[name="filter_brand"]').find('option:selected').val(),
          };
        },
        processResults: function (response) {
          return {
            results:  $.map(response, function (item, index) {
                        return {
                          text: index,
                          id: index
                        }
                      })
          };
        },
        cache: true
      },
    });


    $('[name="filter_product_type"]').select2({
      ajax: {
        url: "{{route('product.get-product-type-data')}}",
        type: "post",
        dataType: 'json',
        delay: 250,
        data: function (params) {
          return {
            _token: "{{ csrf_token() }}",
            search: params.term,
            sap_connection_id: $('[name="filter_company"]').find('option:selected').val(),
            items_group_code: $('[name="filter_brand"]').find('option:selected').val(),
          };
        },
        processResults: function (response) {
          return {
            results:  $.map(response, function (item, index) {
                        return {
                          text: index,
                          id: index
                        }
                      })
          };
        },
        cache: true
      },
    });


    $('[name="filter_product_application"]').select2({
      ajax: {
        url: "{{route('product.get-product-application-data')}}",
        type: "post",
        dataType: 'json',
        delay: 250,
        data: function (params) {
          return {
            _token: "{{ csrf_token() }}",
            search: params.term,
            sap_connection_id: $('[name="filter_company"]').find('option:selected').val(),
            items_group_code: $('[name="filter_brand"]').find('option:selected').val(),
          };
        },
        processResults: function (response) {
          return {
            results:  $.map(response, function (item, index) {
                        return {
                          text: index,
                          id: index
                        }
                      })
          };
        },
        cache: true
      },
    });


    $('[name="filter_product_pattern"]').select2({
      ajax: {
        url: "{{route('product.get-product-pattern-data')}}",
        type: "post",
        dataType: 'json',
        delay: 250,
        data: function (params) {
          return {
            _token: "{{ csrf_token() }}",
            search: params.term,
            sap_connection_id: $('[name="filter_company"]').find('option:selected').val(),
            items_group_code: $('[name="filter_brand"]').find('option:selected').val(),
          };
        },
        processResults: function (response) {
          return {
            results:  $.map(response, function (item, index) {
                        return {
                          text: index,
                          id: index
                        }
                      })
          };
        },
        cache: true
      },
    });


  })
</script>
@endpush
