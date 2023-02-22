@extends('layouts.master')

@section('title','Customers Sales Specialist')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Customers Sales Specialist</h1>
      </div>

      <!--begin::Actions-->
      <div class="d-flex align-items-center py-1">
        <!--begin::Button-->
        <a href="{{ route('customers-sales-specialist.index') }}" class="btn btn-sm btn-primary">Back</a>
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
                  <div class="form-group d-flex justify-content-between">
                    <h4>Customer : {{ @$data->card_name ?? "-" }}</h4>
                    <h5>Customer Group : {{ @$data->group->name ?? "-" }}</h4>

                  </div>
                </div>
              </div>
              <div class="row mb-5 mt-10">
                <div class="col-md-12">
                  <div class="form-group text-center">
                    <label>Sales Specialist</label>
                    <hr>
                  </div>
                </div>
                
                <div class="col-md-12">
                  <div class="form-group">
                    <ul>
                      @foreach (@$data->sales_specialist as $d)
                        <li>{!! $d->sales_person->sales_specialist_name !!} (Email: {!! $d->sales_person->email !!})</li>
                      @endforeach
                    </ul> 
                  </div>
                </div>

              </div>
              <div class="row mb-5 mt-10">

                <div class="col-md-12">
                  <div class="form-group text-center">
                    <label>Product Brand</label>
                    <hr>
                  </div>
                </div>

                <div class="col-md-12">
                  <div class="form-group">
                    <ul>
                      @foreach (@$data->product_groups as $d)
                        <li>{!! $d->product_group->group_name !!}</li>
                      @endforeach
                    </ul>
                  </div>
                </div>
              </div>
              <div class="row mb-5 mt-10">
                <div class="col-md-12">
                  <div class="form-group text-center">
                    <label>Product Line</label>
                    <hr>
                  </div>
                </div>

                <div class="col-md-12">
                  <div class="form-group">
                    <ul>
                      @foreach (@$data->product_item_lines as $d)
                        <li>{!! @$d->product_item_line->u_item_line_sap_value->value ?? @$d->product_item_line->u_item_line !!}</li>
                      @endforeach
                    </ul> 
                  </div>
                </div>
                
              </div>
              <div class="row mb-5 mt-10">
                

                <div class="col-md-12">
                  <div class="form-group text-center">
                    <label>Product Category</label>
                    <hr>
                  </div>
                </div>

                <div class="col-md-12">
                  <div class="form-group">
                    <ul>
                      @foreach (@$data->product_tires_categories as $d)
                        <li>{!! $d->product_tires_category->u_tires !!}</li>
                      @endforeach
                    </ul>
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

@endpush

@push('js')


<script>
  
</script>
@endpush
