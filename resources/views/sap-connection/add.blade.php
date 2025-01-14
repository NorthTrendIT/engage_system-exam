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
        <a href="{{ route('sap-connection.index') }}" class="btn btn-sm btn-primary">Back</a>
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
            <div class="card-header bg-dark border-bottom pt-5">
              <h1 class="text-white fw-bolder fs-3"><span class="fas fa-pencil"></span> {{ isset($edit) ? "Update" : "Add" }} Details</h1>
            </div>
            <div class="card-body bg-secondary">
              
              <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                  <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true">Home</button>
                  <button class="nav-link" id="nav-url-tab" data-bs-toggle="tab" data-bs-target="#nav-url" type="button" role="tab" aria-controls="nav-url" aria-selected="false">Hosts</button>
                </div>
              </nav>
              <div class="tab-content bg-light" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                  <form method="post" id="myForm" class="p-5">
                    @csrf
    
                    @if(isset($edit))
                      <input type="hidden" name="id" value="{{ $edit->id }}">
                    @endif
    
                    <div class="row mb-5">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label>Company Name<span class="asterisk">*</span></label>
                          <input type="text" class="form-control form-control-solid" placeholder="Enter company name" name="company_name" @if(isset($edit)) value="{{ $edit->company_name }}" @endif>
                        </div>
                      </div>
    
                      <div class="col-md-6">
                        <div class="form-group">
                          <label>Database Name<span class="asterisk">*</span></label>
                          <input type="text" class="form-control form-control-solid" placeholder="Enter database name" name="db_name" @if(isset($edit)) value="{{ $edit->db_name }}" @endif>
                        </div>
                      </div>
    
                      
                    </div>
    
                    <div class="row mb-5">
                      
                      <div class="col-md-6">
                        <div class="form-group">
                          <label>User Name<span class="asterisk">*</span></label>
                          <input type="text" class="form-control form-control-solid" placeholder="Enter user name" name="user_name" @if(isset($edit)) value="{{ $edit->user_name }}" @endif>
                        </div>
                      </div>
                      
                      <div class="col-md-6">
                        <div class="form-group">
                          <label>Password<span class="asterisk">*</span></label>
                          {{-- <input type="text" class="form-control form-control-solid" placeholder="Enter password" name="password" @if(isset($edit)) value="{{ $edit->password }}" @endif> --}}
    
                          <div class="input-group input-group-solid">
                            <input type="password" class="form-control form-control-solid" placeholder="Enter password" name="password" id="password" @if(isset($edit)) value="{{ $edit->password }}" @endif>
                            <div class="input-group-append password_icon_div cursor-pointer pt-2">
                              <span class="input-group-text">
                                <i class="fas fa-eye-slash password_icon"></i>
                              </span>
                            </div>
                          </div>
    
                        </div>
                      </div>
                    </div>
    
                    <div class="row mb-5">
                      <div class="col-md-12 d-flex justify-content-end">
                        <div class="form-group ">
                          <input type="submit" value="{{ isset($edit) ? "Update" : "Save" }}" class="btn btn-primary btn-sm">
                        </div>
                      </div>
                    </div>
    
                  </form>
                </div>
                <div class="tab-pane fade" id="nav-url" role="tabpanel" aria-labelledby="nav-url-tab">
                  <div class="row p-2">
                    <div class="col-md-12 d-flex justify-content-end">
                      <button class="btn btn-dark btn-sm add_host-url" data-bs-toggle="modal" data-bs-target="#hostsUrlModal"><span class="fas fa-plus"></span> Add</button>
                    </div>
                    <div class="col-md-12">
                      <table class="table  table-bordered table-hover" id="host_tbl">
                        <thead class="table-dark table-group-divider">
                          <tr>
                            <th>No.</th>
                            <th>Network Url</th>
                            <th>Status</th>
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
</div>


<!-- Modal -->
<div class="modal fade" id="hostsUrlModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="hostsUrlModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="hostsUrlModalLabel"> <span class="fas fa-link"></span> Add Host</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <div class="mb-3">
            <label for="exampleInputEmail1" class="form-label">Url</label>
            <input type="text"  name="host_url" class="form-control col-md-8" required>
          </div>
          <div class="mb-3">
            <label for="exampleInputPassword1" class="form-label">Status</label>
            <select name="status" class="form-control col-md-3" id="" required>
              <option value="1">Active</option>
              <option value="0">Inactive</option>
            </select>
          </div>
          <input type="hidden" name="submitType">
          <input type="hidden" name="submitUrl">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-dark btn-sm" id="submitHost">Submit</button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('css')
<link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="https://cdn.datatables.net/select/1.7.0/css/select.dataTables.min.css">
@endpush


@push('js')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
  $(document).ready(function() {

    $('body').on("submit", "#myForm", function (e) {
      e.preventDefault();
      var validator = validate_form();

      if (validator.form() != false) {
        $('[type="submit"]').prop('disabled', true);
        $.ajax({
          url: "{{route('sap-connection.store')}}",
          type: "POST",
          data: new FormData($("#myForm")[0]),
          async: false,
          processData: false,
          contentType: false,
          success: function (data) {
            if (data.status) {
              toast_success(data.message)
              setTimeout(function(){

                @if(isset($edit->id))
                  window.location.reload(); 
                @else
                  window.location.href = '{{ route('sap-connection.index') }}';
                @endif
                
              },1500)
            } else {
              toast_error(data.message);
              $('[type="submit"]').prop('disabled', false);
            }
          },
          error: function () {
            toast_error("Something went to wrong !");
            $('[type="submit"]').prop('disabled', false);
          },
        });
      }
    });

    function validate_form(){
      var validator = $("#myForm").validate({
          errorClass: "is-invalid",
          validClass: "is-valid",
          rules: {
            company_name:{
              required: true,
              maxlength: 185,
            },
            user_name:{
              required: true,
              maxlength: 185,
            },
            db_name:{
              required:true,
              maxlength: 185,
            },
            password:{
              required:true,
              maxlength: 185,
            },
          },
          messages: {
            company_name:{
              required: "Please enter company name.",
              maxlength:'Please enter company name less than 185 character',
            },
            user_name:{
              required: "Please enter user name.",
              maxlength:'Please enter user name less than 185 character',
            },
            db_name:{
              required:"Please enter database name.",
              maxlength:'Please enter database name less than 185 character',
            },
            password:{
              required:"Please enter password.",
              maxlength:'Please enter password less than 185 character',
            },
          },
      });

      return validator;
    }

    $('button[data-bs-toggle="tab"]').on('show.bs.tab', function (e) {
       localStorage.setItem('activeTab', $(e.target).attr('data-bs-target'));
    }); 
    var activeTab = localStorage.getItem('activeTab'); 
    if (activeTab) { 
      var triggerEl = document.querySelector('button[data-bs-target="' + activeTab + '"]');
      var tab = new bootstrap.Tab(triggerEl); tab.show(); 
    } 

    var table = render_hostTbl();

    function render_hostTbl(){
      return $('#host_tbl').DataTable({
                    "lengthChange": false,
                    // "bPaginate": false,
                    "bFilter": false,
                    // "bInfo": false,
                    processing: true,
                    serverSide: true,
                    ajax: {
                        'url': "{{ route('sap-connection.get-all-hosts') }}",
                        'type': 'POST',
                        'headers': {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        // 'data': filter_data
                    },
                    columns: [
                        {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable:false, searchable:false},
                        {data: 'url', name: 'url', orderable:false, searchable:false},
                        {data: 'status', name: 'status', orderable:false, searchable:false},
                        {data: 'action', name: 'action',orderable:false, searchable:false},
                    ],
                    columnDefs: [
                        {targets: [0,2,3], className: "text-center" }
                    ],
                    pageLength: 10,
                    language: {
                        emptyTable: "No available host found."
                    }
                });
    }

    $(document).on('click', '.add_host-url', function(){
      $('#hostsUrlModalLabel').html('<span class="fas fa-link"></span> Add Host');
      $('select[name="status"]').parent().addClass('d-none');
      $('input[name="host_url"]').val('');
      $('select[name="status"]').val('');
      $('input[name="submitUrl"]').val("{{route('sap-connection.addHostUrl')}}");
      $('input[name="submitType"]').val('POST');
    });

    $(document).on('click', '.edit_host-url', function(){
      $('#hostsUrlModalLabel').html('<span class="fas fa-link"></span> Update Host');
      $('select[name="status"]').parent().removeClass('d-none');
      var row  = $(this).closest('tr');
      var data = table.row(row).data();
      console.log(data);

      $('input[name="host_url"]').val(data.url);
      $('select[name="status"]').val(data.active);
      $('input[name="submitUrl"]').val('/sap-connection/update-hosts-url/'+data.id);
      $('input[name="submitType"]').val('POST');

      
      $('#hostsUrlModal').modal('toggle');
    });

    $('#submitHost').on('click', function(e){
      submitHostUrl(e);
    });

    function submitHostUrl(e){
      e.preventDefault();
      $.ajax({
        url: $('input[name="submitUrl"]').val(),
        method: $('input[name="submitType"]').val(),
        headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        data: {
          url: $('input[name="host_url"]').val(),
          status: $('select[name="status"]').val()
        }
      })
      .done(function(result) {
          toastNotifMsg('Success', result.message);
          table.ajax.reload();
          $('#hostsUrlModal').modal('toggle');
      })
      .fail(function(x) {
          $('#user_form').find('button[type="submit"]').prop('disabled', false);
          var msg = (x.responseJSON) ? x.responseJSON.message : 'Something went wrong.';
          
          fetchErrorMsg(msg, x);
      });
    }


    $('#host_tbl').on('click', '.delete_host-url', function(){
        var row  = $(this).closest('tr');
		    var data = table.row(row).data();
        console.log(data);

        Swal.fire({
            title: data.url,
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Yes, delete it!"
            }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/sap-connection/delete-hosts-url/'+data.id,
                    method: "DELETE",
                    headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                })
                .done(function(result) {
                    table.ajax.reload();
                    toastNotifMsg('Success', result.message);
                })
                .fail(function() {
                    toastNotifMsg('Error', 'Something went wrong.');
                });
                
            }
        });
    });


  });
</script>
@endpush
