@extends('layouts.master')

@section('title','Organization Chart')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Organization Chart</h1>
      </div>
    </div>
  </div>
  
  <div class="post d-flex flex-column-fluid" id="kt_post">
    <div id="kt_content_container" class="container-xxl">
      <div class="row gy-5 g-xl-8">
        <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
          <div class="card card-xl-stretch mb-5 mb-xl-8">
            {{-- <div class="card-header border-0 pt-5 min-0">
              <h5>View Details</h5>
            </div> --}}
            <div class="card-body">
              
              <div class="row mb-5">
                <div class="col-md-12">
                  <div class="form-group">
                    
                    <div id="chart-container"></div>

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
<link href="https://cdnjs.cloudflare.com/ajax/libs/orgchart/2.1.3/css/jquery.orgchart.min.css" rel="stylesheet" />
<style>
  .orgchart {
    /*width: 100% !important;
    min-width: 300px;
    overflow: scroll !important;*/
  }
</style>
@endpush

@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/orgchart/2.1.3/js/jquery.orgchart.min.js"></script>

<script>
  var orgchart = $('#chart-container').orgchart({
    ajaxURL: '{{ route('organisation.index') }}',
    'nodeContent': 'title',
    'direction': 't2b',
    'collapsed': true,
    'pan': true,
    zoom:true,
    // verticalLevel:10,
    toggleSiblingsResp:true,
  });

  oc.$chartContainer.on('touchmove', function(event) {
    event.preventDefault();
  });
  
  //$('#chart-container').append(orgchart);
</script>
@endpush