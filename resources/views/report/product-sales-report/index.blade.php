@extends('layouts.master')

@section('title','Product Reports')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Product Sales Reports</h1>
      </div>

      <!--begin::Actions-->
      <div class="d-flex align-items-center py-1">
        <!--begin::Button-->
        <a href="{{ route('report.index') }}" class="btn btn-sm btn-primary mr-10">Back</a>
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
            <div class="card-body">
              <div class="row">

                <!-- Business Unit-->
                <div class="col-md-3 mt-5">
                  <select class="form-control form-control-lg form-control-solid" data-control="select2" data-hide-search="false" name="filter_company" data-allow-clear="true" data-placeholder="Select business unit">
                    <option value=""></option>
                    @foreach($company as $c)
                      <option value="{{ $c->id }}">{{ $c->company_name }}</option>
                    @endforeach
                  </select>
                </div>

                <!-- Brand -->
                <div class="col-md-3 mt-5">
                    <select class="form-control form-control-lg form-control-solid" data-control="select2" data-hide-search="false" data-allow-clear="true" name="filter_brand">
                        <option value=""></option>
                    </select>
                </div>

                <!-- Product Category -->
                <div class="col-md-3 mt-5 product_filter">
                    <select class="form-control form-control-lg form-control-solid" data-control="select2" data-hide-search="false" data-allow-clear="true" name="filter_product_category">
                        <option value=""></option>
                    </select>
                </div>

                <!-- Product Line -->
                <div class="col-md-3 mt-5 product_filter">
                    <select class="form-control form-control-lg form-control-solid" data-control="select2" data-hide-search="false" data-allow-clear="true" name="filter_product_line">
                        <option value=""></option>
                    </select>
                </div>

                <!-- Product Class -->
                <div class="col-md-3 mt-5 product_filter">
                    <select class="form-control form-control-lg form-control-solid" data-control="select2" data-hide-search="false" data-allow-clear="true" name="filter_product_class">
                        <option value=""></option>
                    </select>
                </div>

                <!-- Product Type -->
                <div class="col-md-3 mt-5 product_filter">
                    <select class="form-control form-control-lg form-control-solid" data-control="select2" data-hide-search="false" data-allow-clear="true" name="filter_product_type">
                        <option value=""></option>
                    </select>
                </div>

                <!-- Product Application -->
                <div class="col-md-3 mt-5 product_filter">
                    <select class="form-control form-control-lg form-control-solid" data-control="select2" data-hide-search="false" data-allow-clear="true" name="filter_product_application">
                        <option value=""></option>
                    </select>
                </div>

                <!-- Product Pattern -->
                <div class="col-md-3 mt-5 product_filter">
                    <select class="form-control form-control-lg form-control-solid" data-control="select2" data-hide-search="false" data-allow-clear="true" name="filter_product_pattern">
                        <option value=""></option>
                    </select>
                </div>

                <div class="col-md-6 mt-5">
                  <a href="javascript:" class="btn btn-primary px-6 font-weight-bold search">Search</a>
                  <a href="javascript:" class="btn btn-light-dark font-weight-bold clear-search mr-10">Clear</a>
                  <a href="javascript:" class="btn btn-success font-weight-bold download_excel mr-10">Export Excel</a>
                </div>

              </div>
              <div class="row mb-5 mt-5">
                <div class="col-md-12">
                  <div class="form-group">
                    <!--begin::Table container-->
                    <div class="table-responsive">
                       <!--begin::Table-->
                       <table class="table table-striped table-row-bordered table-row-gray-300 align-middle gs-0 gy-4 table-bordered display nowrap" id="myTable">
                          <!--begin::Table head-->
                          <thead>
                            <tr>
                              <th>No</th>
                              <th>Product Code</th>
                              <th>Product Name</th>
                              <th>Brand</th>
                              <th>Business Unit</th>
                              <th>Total Quantity</th>
                              <th>Total Price</th>
                              <th>Total Price After VAT</th>
                            </tr>
                          </thead>
                          <tbody class="report-data">

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

<style type="text/css">
  .product_filter{
    display: none;
  }
</style>
@endpush

@push('js')
<script src="{{ asset('assets') }}/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<script>
$(document).ready(function() {

    render_table();

    function render_table(){
      var table = $("#myTable");
      table.DataTable().destroy();

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
          order: [],
          ajax: {
              'url': "{{ route('reports.product-sales-report.get-all') }}",
              'type': 'POST',
              headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              data:{
                filter_company : $filter_company,
                filter_brand: $filter_brand,
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
              {data: 'company', name: 'company'},
              {data: 'total_quantity', name: 'total_quantity'},
              {data: 'total_price', name: 'total_price'},
              {data: 'total_price_after_vat', name: 'total_price_after_vat'},
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
        $('[name="filter_company"]').val(null).trigger('change');
        $('[name="filter_brand"]').val(null).trigger('change');
        $('[name="filter_product_category"]').val(null).trigger('change');
        $('[name="filter_product_line"]').val(null).trigger('change');
        $('[name="filter_product_class"]').val(null).trigger('change');
        $('[name="filter_product_type"]').val(null).trigger('change');
        $('[name="filter_product_application"]').val(null).trigger('change');
        $('[name="filter_product_pattern"]').val(null).trigger('change');
        render_table();
    })

    $(document).on('change', '[name="filter_brand"]', function(event) {
        event.preventDefault();
        $('[name="filter_product_category"]').val('').trigger('change');
        $('[name="filter_product_line"]').val('').trigger('change');
        $('[name="filter_product_class"]').val('').trigger('change');
        $('[name="filter_product_type"]').val('').trigger('change');
        $('[name="filter_product_application"]').val('').trigger('change');
        $('[name="filter_product_pattern"]').val('').trigger('change');

        if($(this).find('option:selected').val() != ""){
            $('.product_filter').show();
        }else{
            $('.product_filter').hide();
        }
    });

    // Brand
    $('[name="filter_brand"]').select2({
        ajax: {
            url: "{{ route('common.getBrands') }}",
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
                    results: response
                };
            },
            cache: true
        },
        placeholder: 'By Brand',
        // minimumInputLength: 1,
        multiple: false,
    });

    // Product Category
    $('[name="filter_product_category"]').select2({
        ajax: {
            url: "{{route('common.getProductCategory')}}",
            type: "post",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    _token: "{{ csrf_token() }}",
                    search: params.term,
                    sap_connection_id: $('[name="filter_company"]').find('option:selected').val(),
                    items_group_code: $('[name="filter_brand"]').find('option:selected').val()
                };
            },
            processResults: function (response) {
                return {
                    results: response
                };
            },
            cache: true
        },
        placeholder: 'Select Product Category',
        // minimumInputLength: 1,
        multiple: false,
    });

    // Product Line
    $('[name="filter_product_line"]').select2({
        ajax: {
            url: "{{route('common.getProductLine')}}",
            type: "post",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    _token: "{{ csrf_token() }}",
                    search: params.term,
                    sap_connection_id: $('[name="filter_company"]').find('option:selected').val(),
                    items_group_code: $('[name="filter_brand"]').find('option:selected').val()
                };
            },
            processResults: function (response) {
                return {
                    results: response
                };
            },
            cache: true
        },
        placeholder: 'Select Product Line',
        // minimumInputLength: 1,
        multiple: false,
    });

    // Product Class
    $('[name="filter_product_class"]').select2({
        ajax: {
            url: "{{route('common.getProductClass')}}",
            type: "post",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    _token: "{{ csrf_token() }}",
                    search: params.term,
                    sap_connection_id: $('[name="filter_company"]').find('option:selected').val(),
                    items_group_code: $('[name="filter_brand"]').find('option:selected').val()
                };
            },
            processResults: function (response) {
                return {
                    results: response
                };
            },
            cache: true
        },
        placeholder: 'Select Product Class',
        // minimumInputLength: 1,
        multiple: false,
    });

    // Product Type
    $('[name="filter_product_type"]').select2({
        ajax: {
            url: "{{route('common.getProductType')}}",
            type: "post",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    _token: "{{ csrf_token() }}",
                    search: params.term,
                    sap_connection_id: $('[name="filter_company"]').find('option:selected').val(),
                    items_group_code: $('[name="filter_brand"]').find('option:selected').val()
                };
            },
            processResults: function (response) {
                return {
                    results: response
                };
            },
            cache: true
        },
        placeholder: 'Select Product Type',
        // minimumInputLength: 1,
        multiple: false,
    });

    // Product Application
    $('[name="filter_product_application"]').select2({
        ajax: {
            url: "{{route('common.getProductApplication')}}",
            type: "post",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    _token: "{{ csrf_token() }}",
                    search: params.term,
                    sap_connection_id: $('[name="filter_company"]').find('option:selected').val(),
                    items_group_code: $('[name="filter_brand"]').find('option:selected').val()
                };
            },
            processResults: function (response) {
                return {
                    results: response
                };
            },
            cache: true
        },
        placeholder: 'Select Product Application',
        // minimumInputLength: 1,
        multiple: false,
    });

    // Product Pattern
    $('[name="filter_product_pattern"]').select2({
        ajax: {
            url: "{{route('common.getProductPattern')}}",
            type: "post",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    _token: "{{ csrf_token() }}",
                    search: params.term,
                    sap_connection_id: $('[name="filter_company"]').find('option:selected').val(),
                    items_group_code: $('[name="filter_brand"]').find('option:selected').val()
                };
            },
            processResults: function (response) {
                return {
                    results: response
                };
            },
            cache: true
        },
        placeholder: 'Select Product Pattern',
        // minimumInputLength: 1,
        multiple: false,
    });

    // Export Report
    $(document).on("click", ".download_excel", function(e) {
        var url = "{{route('reports.product-sales-report.export')}}";

        var data = {};
        data.filter_search = $('[name="filter_search"]').val();
        data.filter_company = $('[name="filter_company"]').find('option:selected').val(),
        data.filter_brand = $('[name="filter_brand"]').find('option:selected').val(),
        data.filter_product_category = $('[name="filter_product_category"]').find('option:selected').val(),
        data.filter_product_line = $('[name="filter_product_line"]').find('option:selected').val(),
        data.filter_product_class = $('[name="filter_product_class"]').find('option:selected').val(),
        data.filter_product_type = $('[name="filter_product_type"]').find('option:selected').val(),
        data.filter_product_application = $('[name="filter_product_application"]').find('option:selected').val(),
        data.filter_product_pattern = $('[name="filter_product_pattern"]').find('option:selected').val(),

        url = url + '?data=' + btoa(JSON.stringify(data));

        window.location.href = url;
    });
});
</script>
@endpush
