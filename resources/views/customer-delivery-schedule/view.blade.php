@extends('layouts.master')

@section('title','Customer Delivery Schedule')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Customer Delivery Schedule</h1>
      </div>

      <!--begin::Actions-->
      <div class="d-flex align-items-center py-1">
        <!--begin::Button-->
        <a href="{{ route('customer-delivery-schedule.index') }}" class="btn btn-sm btn-primary">Back</a>
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
            {{-- <div class="card-header border-0 pt-5">
              <h5>Schedule Details</h5>
            </div> --}}
            <div class="card-body">
              
              <div class="row mb-5">
                <div class="col-md-12">
                  <div class="form-group">
                    <h4>Customer : {{ @$data->sales_specialist_name ?? "-" }}</h4>
                  </div>
                </div>
              </div>


              <div class="row mb-5">
                <div class="col-md-12">
                  <div class="form-group text-center">
                    <h3>Schedule Details</h3>
                    <hr>
                  </div>
                </div>
              </div>


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
    </div>
  </div>
</div>
@endsection

@push('css')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.9.0/main.min.css">
@endpush

@push('js')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.9.0/main.min.js"></script>

<script>
  $(document).ready(function() {

    function render_calendar(response) {
      var calendarEl = document.getElementById('kt_calendar_app');

      var calendar = new FullCalendar.Calendar(calendarEl, {
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek'
        },
        navLinks: !0,
        selectable: !0,
        selectMirror: !0,
        editable: !0,
        dayMaxEvents: !0,
        // timeZone: 'CST',
        initialDate: '{{ date('Y-m-d') }}',
        events: response,
      });

      calendar.render();
    }
    
    render_calendar({!! json_encode($dates) !!});
  })
</script>
@endpush
