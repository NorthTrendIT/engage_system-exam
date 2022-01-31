@extends('layouts.master')

@section('title','Customer Delivery Schedule')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Customer Delivery Schedule</h1>
      </div>

      
    </div>
  </div>

  <div class="post d-flex flex-column-fluid" id="kt_post">
    <div id="kt_content_container" class="container-xxl">
      
      <div class="row gy-5 g-xl-8">
        <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
          <div class="card card-xl-stretch mb-5 mb-xl-8">
            {{-- <div class="card-header border-0 pt-5">
              <h5>Select Customer</h5>
            </div> --}}
            <div class="card-body">
              
              <div class="row mb-5">
                <div class="col-md-12">
                  <div class="form-group">
                    <h5>Select Customer</h5>
                    <select class="form-control form-control-lg form-control-solid" name="filter_customer" data-control="select2" data-hide-search="false" data-placeholder="Select customer" data-allow-clear="true">
                      <option value=""></option>
                    </select>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>

      <div class="row gy-5 g-xl-8 customer_row" style="display:none;">
        <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
          <div class="card card-xl-stretch mb-5 mb-xl-8">
            <div class="card-header border-0 pt-5">
              <h5>Schedule Details</h5>
            </div>
            <div class="card-body">

              <div class="row mb-5">
                <div class="col-md-12">
                  <div class="form-group">
                    <!--begin::Calendar-->
                    <div id="kt_calendar_app"></div>
                    <!--end::Calendar-->
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>

      <div class="row gy-5 g-xl-8 customer_row" style="display:none;">
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
{{-- <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.9.0/main.min.css"> --}}
<link href="{{ asset('assets')}}/assets/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="https://unpkg.com/js-year-calendar@latest/dist/js-year-calendar.min.css">
@endpush

@push('js')
{{-- <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.9.0/main.min.js"></script> --}}
<script src="{{ asset('assets') }}/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<script src="{{ asset('assets') }}/assets/plugins/custom/sweetalert2/sweetalert2.all.min.js"></script>

<script src="https://unpkg.com/js-year-calendar@latest/dist/js-year-calendar.min.js"></script>

<script>
  $(document).ready(function() {

    $('[name="filter_customer"]').select2({
      ajax: {
        url: "{{route('customer-delivery-schedule.get-ss-customer-list')}}",
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
            results: $.map(response, function (item) {
                            return {
                              id: item.id,
                              text: item.card_name,
                            }
                        })
          };
        },
        cache: true
      },
    });


    $(document).on('change', '[name="filter_customer"]', function(event) {
      event.preventDefault();

      if($(this).val() != ""){
        render_table();
        render_calendar_data();
        $('.customer_row').show();
      }else{
        $('.customer_row').hide();
      }
    });

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
                customer_id : $('[name="filter_customer"]').val(),
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

    function render_calendar(response) {

      // var calendarEl = document.getElementById('kt_calendar_app');

      // var calendar = new FullCalendar.Calendar(calendarEl, {
      //   headerToolbar: {
      //     left: 'prev,next today',
      //     center: 'title',
      //     right: 'dayGridMonth,timeGridWeek'
      //   },
      //   navLinks: !0,
      //   selectable: !0,
      //   selectMirror: !0,
      //   editable: !0,
      //   dayMaxEvents: !0,
      //   // timeZone: 'CST',
      //   initialDate: '{{ date('Y-m-d') }}',
      //   events: response,
      // });

      // calendar.render();

      const currentYear = new Date().getFullYear();

      new Calendar('#kt_calendar_app', {
        dataSource: response,
        // maxDate:new Date(),
        style: 'custom',    
        customDataSourceRenderer: function(element, date, events) {    
            for (var i=0; i<events.length; i++) {                        
              if(events[i].class == 'active') {                    
                $(element).addClass('active');
              }             
            }
        },
      })
    }

    function render_calendar_data(){
      $.ajax({
        url: '{{route('customer-delivery-schedule.ss-view')}}',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        data:{
          customer_id : $('[name="filter_customer"]').val(),
        }
      })
      .done(function(response) {
        var data = [];
        $.each(response, function(index, val) {
          data.push({
              startDate: new Date(val.start),
              endDate: new Date(val.end),
              class : 'active'
          });
        });
        render_calendar(data);
      })
      .fail(function() {
        toast_error("error");
      });
      
    }

  })
</script>
@endpush
