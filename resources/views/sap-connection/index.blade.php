@extends('layouts.master')

@section('title','SAP Connections')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">SAP Connections</h1>
      </div>

      <!--begin::Actions-->
      <div class="d-flex align-items-center py-1">
        <!--begin::Button-->

        <a href="{{ route('sap-connection.create') }}" class="btn btn-sm btn-primary">Create</a>
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
              <div class="row mt-5" style="display: flex;justify-content: right;">

                <div class="col-md-2">
                  {{-- <a href="javascript:" class="btn btn-primary px-6 font-weight-bold update">Update</a> --}}
                </div>

                <div class="col-md-2 mt-3" style="text-align:right;">
                  <h3>API URL: </h3>
                </div>

                <div class="col-md-4">
                  <input type="text" class="form-control form-control-lg form-control-solid" placeholder="Enter API URL" name = "url" value="{{ get_sap_api_url() }}" disabled>
                  {{-- <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                      {{ get_sap_api_url() }}
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                      @foreach ($api_urls as $u)
                       @php
                        $icon = ($u->active) ? 'fas fa-check' : 'fas fa-times' ; 
                       @endphp
                        <li><hr class="dropdown-divider"></li>
                        <li data-value="{{ $u->url }}"><a class="dropdown-item" href="#"><i class="{{$icon}}"></i> {{$u->url}}</a></li>
                      @endforeach
                    </ul>
                </div>             
                <input type="hidden" name="url" id="selectedOption"> --}}
                
                
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
                                <th>Company Name</th>
                                <th>Database Name</th>
                                <th>User Name</th>
                                <th>Connection</th>
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
      $filter_status = $('[name="filter_status"]').find('option:selected').val();

      table.DataTable({
          processing: true,
          serverSide: true,
          scrollX: true,
          order: [],
          ajax: {
              'url': "{{ route('sap-connection.get-all') }}",
              'type': 'POST',
              headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              data:{
                filter_search : $filter_search,
                filter_status : $filter_status,
              }
          },
          columns: [
              {data: 'DT_RowIndex', name: 'DT_RowIndex',orderable:false,searchable:false},
              {data: 'company_name', name: 'company_name'},
              {data: 'db_name', name: 'db_name'},
              {data: 'user_name', name: 'user_name'},
              {data: 'connection', name: 'connection'},
              {data: 'action', name: 'action'},
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
      $('[name="filter_status"]').val('').trigger('change');
      render_table();
    })

    $(document).on('click', '.test-api', function(event) {
      event.preventDefault();
      $url = $(this).attr('data-url');

      $.ajax({
        url: $url,
        method: "GET",
      })
      .done(function(result) {
          $('input[name="url"]').val("{{ get_sap_api_url() }}");
          if(result.status){
              Swal.fire({
                  title: 'API working!',
                  // text: result.message,
                  icon: 'success',
                  showCancelButton: false,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  confirmButtonText: 'Ok'
              });
              // toast_success(result.message);
          }else{
              Swal.fire({
                  title: 'API not working!',
                  text: result.message,
                  icon: 'warning',
                  showCancelButton: false,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  confirmButtonText: 'Ok'
              });
          }
      })
      .fail(function() {
        toast_error("error");
      });
    });

    $(document).on('click', '.update', function(event) {
      event.preventDefault();
      url = $('[name="url"]').val();
      if(url == ""){
        toast_error("The url field is required.");
        return false;
      }

      Swal.fire({
        title: 'Are you sure want to update?',
        // text: "Syncing process will run in background and it may take some time to sync all Orders Data.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, do it!'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: '{{ route('sap-connection.update-api-url') }}',
            method: "POST",
            data: {
                    _token:'{{ csrf_token() }}',
                    url:url
                  }
          })
          .done(function(result) {
            if(result.status == false){
              toast_error(result.message);
            }else{
              toast_success(result.message);
            }
          })
          .fail(function() {
            toast_error("error");
          });
        }
      })

    });

  });
  $('input[name="url"]').val("{{ get_sap_api_url() }}");
    // document.addEventListener('DOMContentLoaded', function() {
    //     let dropdownItems = document.querySelectorAll('.dropdown-menu li');
    //     let dropdownButton = document.getElementById('dropdownMenuButton');
    //     let hiddenInput = document.getElementById('selectedOption');

    //     dropdownItems.forEach(item => {
    //         item.addEventListener('click', function() {
    //             let selectedText = this.textContent.trim();
    //             let selectedValue = this.getAttribute('data-value');
    //             dropdownButton.textContent = selectedText;
    //             hiddenInput.value = selectedValue;
    //         });
    //     });
    // });

</script>
@endpush
