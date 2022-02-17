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
              <h5>View Details</h5>
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

                            @if(in_array(userrole(),[1,3]))
                            <tr>
                              <th> <b>Business Unit :</b> </th>
                              <td>{{ @$data->user->sap_connection->company_name ?? "-" }}</td>
                            </tr>

                            <tr>
                              <th> <b>Customer Name :</b> </th>
                              <td>{{ @$data->user->sales_specialist_name ?? "-" }}</td>
                            </tr>
                            @endif

                            <tr>
                              <th> <b>Customer Email :</b> </th>
                              <td>{{ @$data->customer_email ?? "-" }}</td>
                            </tr>

                            <tr>
                              <th> <b>Customer Phone :</b> </th>
                              <td>{{ @$data->customer_phone ?? "-" }}</td>
                            </tr>

                            <tr>
                              <th> <b>Customer Location :</b> </th>
                              <td>{{ @$data->customer_location ?? "-" }}</td>
                            </tr>

                            <tr>
                              <th> <b>Customer Telephone :</b> </th>
                              <td>{{ @$data->customer_telephone ?? "-" }}</td>
                            </tr>

                            <tr>
                              <th> <b>Customer Address :</b> </th>
                              <td>{{ @$data->customer_address ?? "-" }}</td>
                            </tr>

                            <tr>
                              <th> <b>Dealer Name :</b> </th>
                              <td>{{ @$data->dealer_name ?? "-" }}</td>
                            </tr>

                            <tr>
                              <th> <b>Dealer Location :</b> </th>
                              <td>{{ @$data->dealer_location ?? "-" }}</td>
                            </tr>

                            <tr>
                              <th> <b>Dealer Telephone :</b> </th>
                              <td>{{ @$data->dealer_telephone ?? "-" }}</td>
                            </tr>

                            <tr>
                              <th> <b>Dealer Fax :</b> </th>
                              <td>{{ @$data->dealer_fax ?? "-" }}</td>
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
              <h5>Tire & Vehicle Info</h5>
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
              <h5>Claim Points</h5>
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
                            
                            <tr>
                                <td></td>
                                <td>Answer</td>
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
              <h5>Tire Manifistation Probable Cause</h5>
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
              <h5>Pictures of the Tire focusing on Damage Areas</h5>
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

    </div>
  </div>
</div>
@endsection

@push('css')

@endpush

@push('js')

@endpush