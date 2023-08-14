@extends('layouts.master')

@section('title','Recommended Products')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Recommended Products</h1>
      </div>
      <div class="d-flex align-items-center py-1">
        <a href="{{ route('product.recommended-create') }}" class="btn btn-sm btn-primary">Create</a>
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


                <div class="col-md-3 mt-5">
                  <div class="input-icon">
                    <input type="text" class="form-control form-control-lg form-control-solid" placeholder="Search here..." name="filter_search" autocomplete="off">
                  </div>
                </div>

                <div class="col-md-6 mt-5">
                  <a href="javascript:" class="btn btn-primary px-6 font-weight-bold search">Search</a>
                  <a href="javascript:" class="btn btn-light-dark font-weight-bold clear-search mx-2">Clear</a>
                </div>

              </div>
              <div class="row mb-5 mt-5">
                <div class="col-md-12">
                  <div class="table-responsive">
                    <table class="table" id="recommended_products_tbl">
                      <thead class="bg-dark text-white">
                        <tr>
                          <th>No.</th>
                          <th>Business Unit</th>
                          <th>Title</th>
                          {{-- <th>Customers</th> --}}
                          <th>Products</th>
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
@endsection

@push('css')
<link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<style>
  #recommended_products_tbl .custom_width{
      max-width: 250px !important;
      white-space: nowrap; 
      width: 50px; 
      overflow: hidden;
      text-overflow: ellipsis;
  }  
</style>
@endpush

@push('js')
<script type="text/javascript" src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
  $(document).ready(function() {
    render_table();

    $('.search').on('click', function(){
      render_table();
    });

    $('.clear-search').on('click', function(){
      $('[name="filter_company"]').val('').trigger('change');
      $('[name="filter_search"]').val('');
      render_table();
    })

    function render_table(){
      var table = $("#recommended_products_tbl");
      table.DataTable().destroy();
      
      $filter_search = $('[name="filter_search"]').val();
      $filter_company = $('[name="filter_company"]').find('option:selected').val();

      var table = table.DataTable({
                                      processing: true,
                                      serverSide: true,
                                      searching: false,
                                      bLengthChange: false,
                                      ajax: {
                                        'url': "{{ route('product.recommended.lists') }}",
                                        'type': 'GET',
                                        headers: {
                                          'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                        },
                                        data:{
                                          filter_search : $filter_search,
                                          filter_company : $filter_company,
                                        }
                                      },
                                      columns: [
                                        {data: 'DT_RowIndex'},
                                        {data: 'bu', name: 'bu'},
                                        {data: 'title', name: 'title'},
                                        // {data: 'customers', name: 'customers'},
                                        {data: 'products', name: 'products'},
                                        {data: 'action', name: 'action'},
                                      ],
                                      columnDefs: [
                                            {targets: [0], className: "ps-2" },
                                            {targets: [2,3], className: "custom_width" },
                                            {targets: [4], className: "text-center" }
                                      ],
                                  });
    }


    $(document).on('click', '.delete', function(event) {
      event.preventDefault();
      $url = $(this).attr('data-url');

      console.log($url);
      Swal.fire({
        title: 'Are you sure?',
        text: "Once deleted, you will not be able to recover this record!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: $url,
            method: "DELETE",
            data: {
                    _token:'{{ csrf_token() }}'
                  }
          })
          .done(function(result) {
            if(result.status == false){
              toast_error(result.message);
            }else{
              console.log(result);
              toast_success(result.message);
              render_table();
            }
          })
          .fail(function() {
            toast_error("error");
          });
        }
      })
    });

  })
</script>
@endpush
