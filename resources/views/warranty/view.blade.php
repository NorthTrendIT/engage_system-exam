@extends('layouts.master')

@section('title','Warranty')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Warranty</h1>
      </div>

      <!--begin::Actions-->
      <div class="d-flex align-items-center py-1">
        <!--begin::Button-->
        <a href="{{ route('warranty.index') }}" class="btn btn-sm btn-primary sync-products">Back</a>
        <!--end::Button-->
      </div>
      <!--end::Actions-->
      
    </div>
  </div>
  
  <div class="post d-flex flex-column-fluid detail-view-table" id="kt_post">
    <div id="kt_content_container" class="container-xxl">
      <div class="row gy-5 g-xl-8">
        <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
          <div class="card card-xl-stretch mb-5 mb-xl-8">
            <div class="card-header border-0 pt-5 min-0">
              <h5 class="text-info">General Details</h5>
            </div>
            <div class="card-body">
              
              <div class="row mb-5">
                <div class="col-md-12">
                  <div class="form-group">
                    <!--begin::Table container-->
                    <div class="table-responsive">
                       <!--begin::Table-->
                       <table class="table table-bordered" id="myTable">
                          <!--begin::Table head-->
                          <thead>
                            <tr>
                              <th> <b>Date Time :</b> </th>
                              <td>{{ date('M d, Y',strtotime($data->created_at)) }}</td>
                            </tr>
                            
                            <tr>
                              <th> <b>Type of Warranty Claim :</b> </th>
                              <td>{{ @$data->warranty_claim_type ?? "-" }}</td>
                            </tr>

                            <tr>
                              <th> <b>Ref No :</b> </th>
                              <td>{{ @$data->ref_no ?? "-" }}</td>
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


      @if(in_array(userrole(),[1]))
      <div class="row gy-5 g-xl-8">
        <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
          <div class="card card-xl-stretch mb-5 mb-xl-8">
            <div class="card-header border-0 pt-5 min-0">
              <h5 class="text-info">Dealer Details</h5>
            </div>
            <div class="card-body">
              
              <div class="row mb-5">
                <div class="col-md-12">
                  <div class="form-group">
                    <!--begin::Table container-->
                    <div class="table-responsive">
                       <!--begin::Table-->
                       <table class="table table-bordered" id="myTable">
                          <!--begin::Table head-->
                          <thead>
                            

                            <tr>
                              <th> <b>Business Unit :</b> </th>
                              <td>{{ @$data->user->sap_connection->company_name ?? "-" }}</td>
                            </tr>

                            <tr>
                              <th> <b>Name :</b> </th>
                              <td>{{ @$data->user->sales_specialist_name ?? "-" }}</td>
                            </tr>

                            <tr>
                              <th> <b>Email :</b> </th>
                              <td>{{ @$data->user->email ?? "-" }}</td>
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
      @endif

      <div class="row gy-5 g-xl-8">
        <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
          <div class="card card-xl-stretch mb-5 mb-xl-8">
            <div class="card-header border-0 pt-5 min-0">
              <h5 class="text-info">Customer Details</h5>
            </div>
            <div class="card-body">
              
              <div class="row mb-5">
                <div class="col-md-12">
                  <div class="form-group">
                    <!--begin::Table container-->
                    <div class="table-responsive">
                       <!--begin::Table-->
                       <table class="table table-bordered" id="myTable">
                          <!--begin::Table head-->
                          <thead>
                            
                            <tr>
                              <th> <b>Email :</b> </th>
                              <td>{{ @$data->customer_email ?? "-" }}</td>
                            </tr>

                            <tr>
                              <th> <b>Phone :</b> </th>
                              <td>{{ @$data->customer_phone ?? "-" }}</td>
                            </tr>

                            <tr>
                              <th> <b>Location :</b> </th>
                              <td>{{ @$data->customer_location ?? "-" }}</td>
                            </tr>

                            <tr>
                              <th> <b>Telephone :</b> </th>
                              <td>{{ @$data->customer_telephone ?? "-" }}</td>
                            </tr>

                            <tr>
                              <th> <b>Address :</b> </th>
                              <td>{{ @$data->customer_address ?? "-" }}</td>
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


      <div class="row gy-5 g-xl-8">
        <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
          <div class="card card-xl-stretch mb-5 mb-xl-8">
            <div class="card-header border-0 pt-5 min-0">
              <h5 class="text-info">Tire & Vehicle Info</h5>
            </div>
            <div class="card-body">
              
              <div class="row mb-5">
                <div class="col-md-12">
                  <div class="form-group">
                    <!--begin::Table container-->
                    <div class="table-responsive">
                       <!--begin::Table-->
                       <table class="table table-bordered" id="myTable">
                          <!--begin::Table head-->
                          <thead>
                            
                            <tr>
                              <th> <b>Vehicle Maker :</b> </th>
                              <td>{{ @$data->vehicle->vehicle_maker ?? "-" }}</td>
                            </tr>
                            
                            <tr>
                              <th> <b>Vehicle Model :</b> </th>
                              <td>{{ @$data->vehicle->vehicle_model ?? "-" }}</td>
                            </tr>

                            <tr>
                              <th> <b>Vehicle Mileage :</b> </th>
                              <td>{{ @$data->vehicle->vehicle_mileage ?? "-" }}</td>
                            </tr>

                            <tr>
                              <th> <b>Year :</b> </th>
                              <td>{{ @$data->vehicle->year ?? "-" }}</td>
                            </tr>

                            <tr>
                              <th> <b>License Plate :</b> </th>
                              <td>{{ @$data->vehicle->license_plate ?? "-" }}</td>
                            </tr>


                            <tr>
                              <th> <b>PC/LT Tire Position :</b> </th>
                              <td>{{ @$data->vehicle->lt_tire_position ?? "-" }}</td>
                            </tr>

                            <tr>
                              <th> <b>PC/LT Tire Mileage :</b> </th>
                              <td>{{ @$data->vehicle->lt_tire_mileage ?? "-" }}</td>
                            </tr>

                            <tr>
                              <th> <b>PC/LT Tire Serial No.  :</b> </th>
                              <td>{{ @$data->vehicle->lt_tire_serial_no ?? "-" }}</td>
                            </tr>

                            <tr>
                              <th> <b>2 Wheels/TB Tire Position :</b> </th>
                              <td>{{ @$data->vehicle->tb_tire_position ?? "-" }}</td>
                            </tr>

                            <tr>
                              <th> <b>2 Wheels/TB Tire Mileage :</b> </th>
                              <td>{{ @$data->vehicle->tb_tire_mileage ?? "-" }}</td>
                            </tr>

                            <tr>
                              <th> <b>2 Wheels/TB Tire Serial No. :</b> </th>
                              <td>{{ @$data->vehicle->tb_tire_serial_no ?? "-" }}</td>
                            </tr>


                            <tr>
                              <th> <b>Reason for tire return :</b> </th>
                              <td>{{ @$data->vehicle->reason_for_tire_return ?? "-" }}</td>
                            </tr>

                            <tr>
                              <th> <b>Location of damage :</b> </th>
                              <td>{{ @$data->vehicle->location_of_damage ?? "-" }}</td>
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


      <div class="row gy-5 g-xl-8">
        <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
          <div class="card card-xl-stretch mb-5 mb-xl-8">
            <div class="card-header border-0 pt-5 min-0">
              <h5 class="text-info">Claim Points</h5>
            </div>
            <div class="card-body">
              
              <div class="row mb-5">
                <div class="col-md-12">
                  <div class="form-group">
                    <!--begin::Table container-->
                    <div class="table-responsive">
                       <!--begin::Table-->
                       <table class="table table-bordered" id="myTable">
                          <!--begin::Table head-->
                          <thead>
                            
                          </thead>
                          <!--end::Table head-->
                          <!--begin::Table body-->
                          <tbody>
                            
                            <tr class="text-end">
                              <td colspan="2">Answer</td>
                            </tr>
                            @foreach($claim_points as $key => $point)
                            <tr>
                                <td colspan="2"><b>{{ $key + 1}}. {{ $point->title }}</b></td>
                            </tr>
                                @foreach($point->sub_titles as $s_key => $s_point)
                                <tr>
                                    <td><span style="margin-left: 15px;">- {{ $s_point->title }}</span></td>
                                    <td>
                                        
                                        @if(isset($warranty_claim_points) && @$warranty_claim_points[$s_point->id] == 1) Yes @endif

                                        @if(isset($warranty_claim_points) && @$warranty_claim_points[$s_point->id] == 0) No @endif

                                    </td>
                                </tr>
                                @endforeach
                            @endforeach

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


      <div class="row gy-5 g-xl-8">
        <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
          <div class="card card-xl-stretch mb-5 mb-xl-8">
            <div class="card-header border-0 pt-5 min-0">
              <h5 class="text-info">Tire Manifistation Probable Cause</h5>
            </div>
            <div class="card-body">
              
              <div class="row mb-5">
                <div class="col-md-12">
                  <div class="form-group">
                    <!--begin::Table container-->
                    <div class="table-responsive">
                       <!--begin::Table-->
                       <table class="table table-bordered" id="myTable">
                          <!--begin::Table head-->
                          <thead>
                            <tr>
                                <th>No.</th>
                                <th>Image</th>
                                <th>Manifistation</th>
                                <th>Probable Cause(s)</th>
                                <th>Answer</th>
                            </tr>

                          </thead>
                          <!--end::Table head-->
                          <!--begin::Table body-->
                          <tbody>
                            
                            
                            @foreach($tire_manifistations as $key => $m)
                                <tr>   
                                    <td>{{ $key+1}}.</td>
                                    <td>
                                        @if($m->image && get_valid_file_url('sitebucket/tire-manifistation',$m->image))
                                            <a href="{{ get_valid_file_url('sitebucket/tire-manifistation',$m->image) }}" class="fancybox" title="View Full"><img src="{{ get_valid_file_url('sitebucket/tire-manifistation',$m->image) }}" height="100" width="100"></a>
                                        @endif
                                    </td>
                                    <td>{!! $m->manifistation !!}</td>
                                    <td>{!! $m->probable_cause !!}</td>
                                    <td>
                                        @if(isset($warranty_tire_manifistations) && @$warranty_tire_manifistations[$m->id] == 1) Yes @endif

                                        @if(isset($warranty_tire_manifistations) && @$warranty_tire_manifistations[$m->id] == 0) No @endif
                                    </td>
                                </tr>
                            @endforeach

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


      <div class="row gy-5 g-xl-8">
        <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
          <div class="card card-xl-stretch mb-5 mb-xl-8">
            <div class="card-header border-0 pt-5 min-0">
              <h5 class="text-info">Pictures of the Tire focusing on Damage Areas</h5>
            </div>
            <div class="card-body">
              
              <div class="row mb-5">
                <div class="col-md-12">
                  <div class="form-group">
                    <!--begin::Table container-->
                    <div class="table-responsive">
                       <!--begin::Table-->
                       <table class="table table-bordered" id="myTable">
                          <!--begin::Table head-->
                          <thead>
                            <tr>
                                <th>No.</th>
                                <th>Title</th>
                                <th>Image</th>
                            </tr>

                          </thead>
                          <!--end::Table head-->
                          <!--begin::Table body-->
                          <tbody>
                            
                            
                            @foreach($data->pictures as $key => $p)
                                <tr>   
                                    <td>{{ $key+1}}.</td>
                                    <td>{!! $p->title !!}</td>
                                    <td>
                                        @if($p->image && get_valid_file_url('sitebucket/warranty-pictures',$p->image))
                                            <a href="{{ get_valid_file_url('sitebucket/warranty-pictures',$p->image) }}" class="fancybox" title="View Full"><img src="{{ get_valid_file_url('sitebucket/warranty-pictures',$p->image) }}" height="100" width="100"></a>
                                        @endif
                                    </td>
                                    
                                </tr>
                            @endforeach

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


      <!-- Access only for admin-->
      @if(userrole() == 1)
      <div class="row gy-5 g-xl-8">
        <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
          <div class="card card-xl-stretch mb-5 mb-xl-8">
            <div class="card-header border-0 pt-5 min-0">
              <h5 class="text-info">Assignment</h5>
            </div>
            <div class="card-body">
              <form id="myAssignmentForm" method="post">
                @csrf
                <input type="hidden" name="warranty_id" value="{{ @$data->id }}">
                <div class="row">
                  <div class="col-md-4 mt-5">
                    <div class="form-group">
                      <label>Department</label>
                      <select class="form-control form-control-lg form-control-solid" name="department_id" data-control="select2" data-hide-search="false" data-placeholder="Select a department" data-allow-clear="true">
                        <option value=""></option>
                      </select>
                    </div>
                  </div>

                  <div class="col-md-8 mt-5 user_div" @if(empty(@$data->assigned_user_id)) style="display:none;" @endif>
                    <div class="form-group">
                      <label>User</label>
                      <select class="form-control form-control-lg form-control-solid" name="user_id" data-control="select2" data-hide-search="false" data-placeholder="Select a user" data-allow-clear="true">
                        <option value=""></option>
                      </select>
                    </div>
                  </div>

                  <div class="col-md-4 mt-5">
                    <div class="form-group">
                      <button type="submit" class="btn btn-success mt-6">Save</button>
                    </div>
                  </div>

                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      @endif

    </div>
  </div>
</div>
@endsection

@push('css')

@endpush

@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/additional-methods.min.js"></script>

<script src="{{ asset('assets') }}/assets/plugins/custom/sweetalert2/sweetalert2.all.min.js"></script>
<script>
  $(document).ready(function() {
    // <!-- Access only for admin-->
    @if(userrole() == 1)
      $initialDepartmentOptions = [];
      $initialDepartmentUserOptions = [];

      @if(!empty(@$data->assigned_user_id))
        var initialOption = {
            id: {{ @$data->assigned_user->department->id }},
            text: '{{ @$data->assigned_user->department->name }}',
            selected: true
        }
        $initialDepartmentOptions.push(initialOption);

        var initialOption = {
            id: {{ @$data->assigned_user->id }},
            text: '{{ @$data->assigned_user->sales_specialist_name }} (Email: {{ @$data->assigned_user->email }}, Role: {{ @$data->assigned_user->role->name }})',
            selected: true
        }
        $initialDepartmentUserOptions.push(initialOption);
      @endif

      $('#myAssignmentForm [name="department_id"]').select2({
        ajax: {
          url: "{{route('warranty.get-department')}}",
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
                              text: item.name,
                              id: item.id
                            }
                        })
            };
          },
          cache: true
        },
        data: $initialDepartmentOptions
      });


      $('#myAssignmentForm [name="user_id"]').select2({
        ajax: {
          url: "{{route('warranty.get-department-user')}}",
          type: "post",
          dataType: 'json',
          delay: 250,
          data: function (params) {
              return {
                  _token: "{{ csrf_token() }}",
                  search: params.term,
                  department_id: $('#myAssignmentForm [name="department_id"]').find('option:selected').val(),
                  user_id: '{{ @$data->user_id }}',
              };
          },
          processResults: function (response) {
            return {
              results:  $.map(response, function (item) {
                            return {
                              text: item.sales_specialist_name +" (Email: "+item.email+", Role: "+item.role.name+")",
                              id: item.id
                            }
                        })
            };
          },
          cache: true
        },
        data: $initialDepartmentUserOptions
      });


      $(document).on('change', '#myAssignmentForm [name="department_id"]', function(event) {
        event.preventDefault();
        $('#myAssignmentForm [name="user_id"]').val('').trigger('change');

        if($(this).find('option:selected').val() != ""){
          $('#myAssignmentForm .user_div').show();
        }else{
          $('#myAssignmentForm .user_div').hide();
        }
      });

      $('body').on("submit", "#myAssignmentForm", function (e) {
        e.preventDefault();
        var validator = validate_assignment_form();
        
        if (validator.form() != false) {

          Swal.fire({
            title: 'Are you sure want to assign?',
            //text: "Once deleted, you will not be able to recover this record!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, update it!'
          }).then((result) => {
            if (result.isConfirmed) {
              $('[type="submit"]').prop('disabled', true);
              $.ajax({
                url: "{{route('warranty.store-assignment')}}",
                type: "POST",
                data: new FormData($("#myAssignmentForm")[0]),
                async: false,
                processData: false,
                contentType: false,
                success: function (data) {
                  if (data.status) {
                    toast_success(data.message)
                    setTimeout(function(){
                      window.location.reload();
                    },500)
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
        }
      });

      function validate_assignment_form(){
        var validator = $("#myAssignmentForm").validate({
            errorClass: "is-invalid",
            validClass: "is-valid",
            rules: {
              department_id:{
                required:true,
              },
              user_id:{
                required:true,
              },
            },
            messages: {
              
            },
        });
        return validator;
      }
    @endif
  });
</script>
@endpush