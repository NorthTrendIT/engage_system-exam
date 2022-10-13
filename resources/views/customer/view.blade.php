@extends('layouts.master')

@section('title','Customer')
@php
   $access = get_user_role_module_access(Auth::user()->role_id);
@endphp
@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Customer</h1>
      </div>

      <!--begin::Actions-->
      <div class="d-flex align-items-center py-1">
        <!--begin::Button-->
        <a href="{{ route('customer.index') }}" class="btn btn-sm btn-primary sync-products">Back</a>
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
              <h5>View Details</h5>
            </div>
            <div class="card-body">

              <div class="row mb-5">
                <div class="col-md-12">
                  <div class="form-group">
                    <!--begin::Table container-->
                    <div class="table-responsive">
                       <!--begin::Table-->
                       <table class="table table-bordered">
                          <!--begin::Table head-->
                          <thead>
                            @if(Auth::user()->role_id != 4)
                            <tr>
                              <th> <b>Business Unit:</b> </th>
                              <td>{{ @$data->sap_connection->company_name ?? "-" }}</td>
                            </tr>                            
                            <tr>
                              <th> <b>Other Business Units:</b> </th>
                              <td>{{ @$sap_connections != "" ? @$sap_connections : "-" }}</td>
                            </tr>
                            @endif
                            <tr>
                              <th> <b>Card Code:</b> </th>
                              <td>{{ @$data->card_code ?? "-" }}</td>
                            </tr>
                            <tr>
                              <th> <b>Universal Card Code:</b> </th>
                              <td>{{ @$data->u_card_code ?? "-" }}</td>
                            </tr>
                            <tr>
                              <th> <b>Card Name:</b> </th>
                              <td>{{ @$data->card_name ?? "-" }}</td>
                            </tr>

                            <tr>
                              <th> <b>Group Name:</b> </th>
                              <td>{{ @$data->group->name ?? "-" }}</td>
                            </tr>

                            <tr>
                              <th> <b>OMS Email:</b> </th>
                              <td>{{ @$data->user->email ?? "-" }}</td>
                            </tr>

                            <tr>
                              <th> <b>Email:</b> </th>
                              <td>{{ @$data->email ?? "-" }}</td>
                            </tr>

                            <tr>
                              <th> <b>Contact Person Name:</b> </th>
                              <td>{{ @$data->contact_person ?? "-" }}</td>
                            </tr>

                            <tr>
                              <th> <b>Class:</b> </th>
                              <td>{{ @$data->u_class ?? "-" }}</td>
                            </tr>

                            <tr>
                              <th> <b>Address:</b> </th>
                              <td>{{ @$data->address ?? "-" }}</td>
                            </tr>

                            <tr>
                              <th> <b>Territory:</b> </th>
                              <td>{{ @$data->territories->description ?? "-" }}</td>
                            </tr>

                            @if(userrole() == 1)
                            <tr>
                              <th> <b>Credit Limit:</b> </th>
                              <td>{{ @$data->credit_limit ?? "-" }}</td>
                            </tr>
                            @endif

                            {{-- <tr>
                              <th> <b>Max Commitment:</b> </th>
                              <td>{{ @$data->max_commitment ?? "-" }}</td>
                            </tr> --}}

                            <tr>
                              <th> <b>Federal Tax ID:</b> </th>
                              <td>{{ @$data->federal_tax_id ?? "-" }}</td>
                            </tr>

                            <tr>
                              <th> <b>Current Account Balance:</b> </th>
                              <td>{{ @$data->current_account_balance ?? "-" }}</td>
                            </tr>

                            <tr>
                              <th> <b>Created Date:</b> </th>
                              <td>{{ date('M d, Y',strtotime(@$data->created_at)) }}</td>
                            </tr>
                            <tr>
                              <th> <b>Status:</b> </th>
                              <td><b class="{{ @$data->is_active ? "text-success" : "text-danger" }}">{{ @$data->is_active == true ? "Active" : "Inactive" }}</b></td>
                            </tr>

                            <tr>
                              <th> <b>Total Overdue Amount:</b> </th>
                              <td>{{number_format(@$totalOverdueAmount)}}</td>
                            </tr>

                            <tr>
                              <th> <b>Total Outstanding Amount:</b> </th>
                              <td>{{ (number_format(@$data->current_account_balance)) ?? "-" }}</td>
                            </tr>

                            <tr>
                              <th> <b>Open Order Amount:</b> </th>
                              <td>{{ (number_format(@$data->open_orders_balance)) ?? "-" }}</td>
                            </tr>


                            <tr>
                              <th> <b>Total Exposure Amount:</b> </th>
                              <td>{{ number_format(@$data->current_account_balance + @$data->open_orders_balance)  ?? "-" }}</td>
                            </tr>

                            <tr>
                              <th> <b>Credit Limit:</b> </th>
                              <td>{{ (number_format(@$data->credit_limit)) ?? "-" }}</td>
                            </tr>

                            @if(@$data->credit_limit > (@$data->current_account_balance + @$data->open_orders_balance))
                            <tr>
                              <th> <b>Available Credit Limit:</b> </th>
                              <td>{{number_format(@$data->credit_limit - ($data->current_account_balance + @$data->open_orders_balance))}}</td>
                            </tr>
                            @else
                            <tr>
                              <th> <b>Over Credit Limit:</b> </th>
                              <td>{{number_format(($data->current_account_balance + @$data->open_orders_balance) - @$data->credit_limit)}}</td>
                            </tr>
                            @endif
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
            <div class="card-header border-0 pt-5">
              <h5>Customer's Address Details</h5>
            </div>
            <div class="card-body">

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
                              <th>Type</th>
                              <th>Address</th>
                              <th>Street</th>
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

      table.DataTable({
          processing: true,
          serverSide: true,
          scrollX: true,
          order: [],
          ajax: {
              'url': "{{ route('customer.get-all-bp-address') }}",
              'type': 'POST',
              headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              data:{
                customer_id : '{{ @$data->id }}',
              }
          },
          columns: [
              {data: 'DT_RowIndex', name: 'DT_RowIndex',orderable:false,searchable:false},
              {data: 'address_type', name: 'address_type'},
              {data: 'address', name: 'address'},
              {data: 'street', name: 'street'},
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

  })
</script>
@endpush
