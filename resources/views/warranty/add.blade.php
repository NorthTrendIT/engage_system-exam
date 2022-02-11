@extends('layouts.master')

@section('title','Warranty')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="toolbar" id="kt_toolbar">
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
                <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Warranty</h1>
            </div>
            <div class="d-flex align-items-center py-1">
                <a href="{{ route('warranty.index') }}" class="btn btn-sm btn-primary">Back</a>
            </div>
        </div>
    </div>

    <div class="post d-flex flex-column-fluid" id="kt_post">
        <div id="kt_content_container" class="container-xxl">
            <div class="row gy-5 g-xl-8">
                <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
                    <div class="card card-xl-stretch mb-5 mb-xl-8">
                        <div class="card-header border-bottom pt-5">
                            <h1 class="text-dark fw-bolder fs-3 my-1">{{ isset($edit) ? "Update" : "Add" }} Details</h1>
                        </div>

                        <div class="card-body">
                            <form method="post" id="myForm" autocomplete="off">
                                @csrf

                                @if(isset($edit))
                                <input type="hidden" name="id" value="{{ $edit->id }}">
                                <input type="hidden" name="user_id" value="{{ $edit->user_id }}">
                                @endif

                                <div class="row mb-5">
                                    <!-- Date -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Date<span class="asterisk">*</span></label>
                                            <input type="text" class="form-control form-control-solid" readonly="" disabled="" @if(isset($edit)) value="{{ date('F d, Y',strtotime($edit->created_at)) }}" @else value="{{ date('F d, Y') }}" @endif >
                                        </div>
                                    </div>

                                    <!-- Customer Name -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Type of Warranty Claim<span class="asterisk">*</span></label>
                                            <select class="form-control form-control-lg form-control-solid" name="warranty_claim_type" data-control="select2" data-hide-search="false" data-allow-clear="true" data-placeholder="Select type of warranty claim">
                                                <option value=""></option>
                                                @foreach($warranty_claim_types as $key => $value)
                                                <option value="{{ $value }}" @if(isset($edit) && $edit->warranty_claim_type == $value) selected @endif >{{ $value }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>


                                <div class="row mb-5">
                                    <!-- Customer Name -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Customer Name<span class="asterisk">*</span></label>
                                            <input type="text" class="form-control form-control-solid" readonly="" disabled="" @if(isset($edit)) value="{{ $edit->user->sales_specialist_name }}" @else value="{{ Auth::user()->sales_specialist_name }}" @endif >
                                        </div>
                                    </div>
                                </div>


                                <div class="row mb-5">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Customer Email<span class="asterisk">*</span></label>
                                            <input type="email" class="form-control form-control-solid" name="customer_email" placeholder="Enter customer email" @if(isset($edit)) value="{{ $edit->customer_email }}" @endif autocomplete="off">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Customer Phone<span class="asterisk">*</span></label>
                                            <input type="number" class="form-control form-control-solid" name="customer_phone" placeholder="Enter customer phone"  @if(isset($edit)) value="{{ $edit->customer_phone }}" @endif autocomplete="off">
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-5">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Customer Location<span class="asterisk">*</span></label>
                                            <input type="text" class="form-control form-control-solid" name="customer_location" placeholder="Enter customer location"  @if(isset($edit)) value="{{ $edit->customer_location }}" @endif >
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Customer Telephone<span class="asterisk">*</span></label>
                                            <input type="number" class="form-control form-control-solid" name="customer_telephone" placeholder="Enter customer telephone"  @if(isset($edit)) value="{{ $edit->customer_telephone }}" @endif autocomplete="off">
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-5">
                                    <!-- Address -->
                                    <div class="col-md-12">
                                        <label>Customer Address<span class="asterisk">*</span></label>
                                        <textarea class="form-control form-control-solid" name="customer_address" placeholder="Enter customer address">@if(isset($edit)) {{ $edit->customer_address }} @endif</textarea>
                                    </div>
                                </div>


                                <div class="row mb-5 mt-10">
                                    <!-- Dealer's Name -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Dealer's Name<span class="asterisk">*</span></label>
                                            <input type="text" class="form-control form-control-solid" name="dealer_name" placeholder="Enter dealer's name" @if(isset($edit)) value="{{ $edit->dealer_name }}" @endif >
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-5">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Dealer's Location<span class="asterisk">*</span></label>
                                            <input type="text" class="form-control form-control-solid" name="dealer_location" placeholder="Enter dealer's location" @if(isset($edit)) value="{{ $edit->dealer_location }}" @endif >
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Dealer's Telephone<span class="asterisk">*</span></label>
                                            <input type="number" class="form-control form-control-solid" name="dealer_telephone" placeholder="Enter dealer's telephone" @if(isset($edit)) value="{{ $edit->dealer_telephone }}" @endif autocomplete="off">
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-5">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Dealer's Fax</label>
                                            <input type="text" class="form-control form-control-solid" name="dealer_fax" placeholder="Enter dealer's fax" @if(isset($edit)) value="{{ $edit->dealer_fax }}" @endif >
                                        </div>
                                    </div>
                                </div>


                                <div class="row mb-5 mt-10">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <h4 class="text-info">Tire & Vehicle Info</h4>
                                            {{-- <hr> --}}
                                        </div>
                                    </div>
                                </div>


                                <div class="row mb-5">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Vehicle Maker<span class="asterisk">*</span></label>
                                            <input type="text" class="form-control form-control-solid" name="vehicle_maker" placeholder="Enter vehicle maker" @if(isset($edit)) value="{{ @$edit->vehicle->vehicle_maker }}" @endif >
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Year<span class="asterisk">*</span></label>
                                            <input type="number" class="form-control form-control-solid" name="year" placeholder="Enter year" @if(isset($edit)) value="{{ @$edit->vehicle->year }}" @endif >
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-5">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Vehicle Model<span class="asterisk">*</span></label>
                                            <input type="text" class="form-control form-control-solid" name="vehicle_model" placeholder="Enter vehicle model" @if(isset($edit)) value="{{ @$edit->vehicle->vehicle_model }}" @endif >
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>License Plate<span class="asterisk">*</span></label>
                                            <input type="text" class="form-control form-control-solid" name="license_plate" placeholder="Enter license plate" @if(isset($edit)) value="{{ @$edit->vehicle->license_plate }}" @endif >
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-5">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Vehicle Mileage<span class="asterisk">*</span></label>
                                            <input type="text" class="form-control form-control-solid" name="vehicle_mileage" placeholder="Enter vehicle mileage" @if(isset($edit)) value="{{ @$edit->vehicle->vehicle_mileage }}" @endif >
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="row mb-5 mt-10">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <h5>PC/LT Tire Position</h5>
                                                    <hr>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mb-5">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label><input type="checkbox" name="lt_tire_position[]" class="form-check-input mr-10" value="LF" title="LF" @if(isset($lt_tire_position) && in_array('LF', $lt_tire_position)) checked @endif >LF</label>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label><input type="checkbox" name="lt_tire_position[]" class="form-check-input mr-10" value="RF" title="RF" @if(isset($lt_tire_position) && in_array('RF', $lt_tire_position)) checked @endif >RF</label>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label><input type="checkbox" name="lt_tire_position[]" class="form-check-input mr-10" value="LR" title="LR" @if(isset($lt_tire_position) && in_array('LR', $lt_tire_position)) checked @endif >LR</label>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label><input type="checkbox" name="lt_tire_position[]" class="form-check-input mr-10" value="RR" title="RR" @if(isset($lt_tire_position) && in_array('RR', $lt_tire_position)) checked @endif >RR</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mb-5">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Tire Mileage</label>
                                                    <input type="text" class="form-control form-control-solid" name="lt_tire_mileage" placeholder="Enter tire mileage" @if(isset($edit)) value="{{ @$edit->vehicle->vehicle_maker }}" @endif >
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="row mb-5 mt-10">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <h5>2 Wheels/TB Tire Position</h5>
                                                    <hr>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mb-5">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label><input type="checkbox" name="tb_tire_position[]" class="form-check-input mr-10" value="Front" title="Front" @if(isset($tb_tire_position) && in_array('Front', $tb_tire_position)) checked @endif >Front</label>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label><input type="checkbox" name="tb_tire_position[]" class="form-check-input mr-10" value="Drive" title="Drive" @if(isset($tb_tire_position) && in_array('Drive', $tb_tire_position)) checked @endif >Drive</label>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label><input type="checkbox" name="tb_tire_position[]" class="form-check-input mr-10" value="Trailer" title="Trailer" @if(isset($tb_tire_position) && in_array('Trailer', $tb_tire_position)) checked @endif >Trailer</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mb-5">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Tire Mileage</label>
                                                    <input type="text" class="form-control form-control-solid" name="tb_tire_mileage" placeholder="Enter tire mileage" @if(isset($edit)) value="{{ @$edit->vehicle->vehicle_maker }}" @endif >
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-5 mt-5">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <hr>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-5">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Reason for tire return<span class="asterisk">*</span></label>
                                            <textarea class="form-control form-control-solid" name="reason_for_tire_return" placeholder="Enter reason for tire return">@if(isset($edit)){{ @$edit->vehicle->reason_for_tire_return }}@endif</textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-5">
                                    <label class="mb-4">Location of damage</label>
                                    <div class="d-flex justify-content-between">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label><input type="checkbox" name="location_of_damage[]" class="form-check-input mr-10" value="Tread" title="Tread" @if(isset($location_of_damage) && in_array('Tread', $location_of_damage)) checked @endif >Tread</label>
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label><input type="checkbox" name="location_of_damage[]" class="form-check-input mr-10" value="Sidewall" title="Sidewall" @if(isset($location_of_damage) && in_array('Sidewall', $location_of_damage)) checked @endif >Sidewall</label>
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label><input type="checkbox" name="location_of_damage[]" class="form-check-input mr-10" value="Shoulder" title="Shoulder" @if(isset($location_of_damage) && in_array('Shoulder', $location_of_damage)) checked @endif >Shoulder</label>
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label><input type="checkbox" name="location_of_damage[]" class="form-check-input mr-10" value="Bead" title="Bead" @if(isset($location_of_damage) && in_array('Bead', $location_of_damage)) checked @endif >Bead</label>
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label><input type="checkbox" name="location_of_damage[]" class="form-check-input mr-10" value="Others" title="Others" @if(isset($location_of_damage) && in_array('Others', $location_of_damage)) checked @endif >Others</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="row mb-5 mt-15">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th><h4 class="text-info">Claim Points</h4></th>
                                                        <th>Yes</th>
                                                        <th>No</th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                    @foreach($claim_points as $key => $point)
                                                    <tr>
                                                        <td colspan="3"><b>{{ $key + 1}}. {{ $point->title }}</b></td>
                                                    </tr>
                                                        @foreach($point->sub_titles as $s_key => $s_point)
                                                        <tr>
                                                            <td><span style="margin-left: 15px;">- {{ $s_point->title }}</span></td>
                                                            <td><input type="checkbox" class="form-check-input yes_no_checkbox" name="claim_point[{{ $s_point->id }}]" value="1" title="Yes" @if(isset($warranty_claim_points) && @$warranty_claim_points[$s_point->id] == 1) checked @endif ></td>
                                                            <td><input type="checkbox" class="form-check-input yes_no_checkbox" name="claim_point[{{ $s_point->id }}]" value="0" title="No" @if(isset($warranty_claim_points) && @$warranty_claim_points[$s_point->id] == 0) checked @endif ></td>
                                                        </tr>
                                                        @endforeach
                                                    @endforeach
                                                </tbody>

                                            </table>
                                        </div>
                                    </div>
                                </div>


                                <div class="row mb-5 mt-10">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th colspan="6"><h4 class="text-info">Tire Manifistation</h4></th>
                                                    </tr>
                                                    <tr>
                                                        <th>No.</th>
                                                        <th>Image</th>
                                                        <th>Manifistation</th>
                                                        <th>Probable Cause(s)</th>
                                                        <th>Yes</th>
                                                        <th>No</th>
                                                    </tr>
                                                </thead>
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
                                                            <td><input type="checkbox" class="form-check-input yes_no_checkbox" name="tire_manifistation[{{ $m->id }}]" value="1" title="Yes" @if(isset($warranty_tire_manifistations) && @$warranty_tire_manifistations[$m->id] == 1) checked @endif ></td>
                                                            <td><input type="checkbox" class="form-check-input yes_no_checkbox" name="tire_manifistation[{{ $m->id }}]" value="0" title="No" @if(isset($warranty_tire_manifistations) && @$warranty_tire_manifistations[$m->id] == 0) checked @endif ></td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>

                                            </table>
                                        </div>
                                    </div>
                                </div>


                                <div class="row mb-5 mt-10">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <h4 class="text-info">Pictures</h4>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-5 mt-10">
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label>Title <span class="asterisk">*</span></label>
                                            <input type="text" name="default_pictures[title][1]" class="form-control form-control-solid default_pictures_title" value="Tread Area" readonly>
                                        </div>
                                    </div>

                                    @if(isset($edit))
                                        @php
                                            $default_picture = @$edit->pictures()->where('title','Tread Area')->first();
                                        @endphp

                                        <input type="hidden" class="default_pictures_id" name="default_pictures[id][1]" value="{{ @$default_picture->id }}">
                                        <input type="hidden" class="default_pictures_image" name="default_pictures[image][1]" value="{{ @$default_picture->image }}">
                                    @endif

                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label>Upload Image <span class="asterisk">*</span></label>
                                            <input type="file" name="default_pictures[image][1]" class="form-control form-control-solid default_pictures_image" accept="image/*" capture>
                                        </div>
                                    </div>

                                    @if(@$default_picture->image && get_valid_file_url('sitebucket/warranty-pictures',$default_picture->image))
                                      <div class="col-md-2 image_preview">
                                        <div class="form-group">
                                          <a href="{{ get_valid_file_url('sitebucket/warranty-pictures',$default_picture->image) }}" class="fancybox"><img src="{{ get_valid_file_url('sitebucket/warranty-pictures',$default_picture->image) }}" height="100" width="100" class=""></a>
                                        </div>
                                      </div>
                                    @endif

                                </div>
                                <div class="row mb-5 mt-10">
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label>Title <span class="asterisk">*</span></label>
                                            <input type="text" name="default_pictures[title][2]" class="form-control form-control-solid default_pictures_title" value="Sidewall Area" readonly>
                                        </div>
                                    </div>

                                    @if(isset($edit))
                                        @php
                                            $default_picture = @$edit->pictures()->where('title','Sidewall Area')->first();
                                        @endphp

                                        <input type="hidden" class="default_pictures_id" name="default_pictures[id][2]" value="{{ @$default_picture->id }}">
                                        <input type="hidden" class="default_pictures_image" name="default_pictures[image][2]" value="{{ @$default_picture->image }}">
                                    @endif

                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label>Upload Image <span class="asterisk">*</span></label>
                                            <input type="file" name="default_pictures[image][2]" class="form-control form-control-solid default_pictures_image" accept="image/*" capture>
                                        </div>
                                    </div>

                                    @if(@$default_picture->image && get_valid_file_url('sitebucket/warranty-pictures',$default_picture->image))
                                        <div class="col-md-2 image_preview">
                                            <div class="form-group">
                                                <a href="{{ get_valid_file_url('sitebucket/warranty-pictures',$default_picture->image) }}" class="fancybox"><img src="{{ get_valid_file_url('sitebucket/warranty-pictures',$default_picture->image) }}" height="100" width="100" class=""></a>
                                            </div>
                                        </div>
                                    @endif

                                </div>
                                <div class="row mb-5 mt-10">
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label>Title <span class="asterisk">*</span></label>
                                            <input type="text" name="default_pictures[title][3]" class="form-control form-control-solid default_pictures_title" value="Bead Area" readonly>
                                        </div>
                                    </div>

                                    @if(isset($edit))
                                        @php
                                            $default_picture = @$edit->pictures()->where('title','Bead Area')->first();
                                        @endphp

                                        <input type="hidden" class="default_pictures_id" name="default_pictures[id][3]" value="{{ @$default_picture->id }}">
                                        <input type="hidden" class="default_pictures_image" name="default_pictures[image][3]" value="{{ @$default_picture->image }}">
                                    @endif

                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label>Upload Image <span class="asterisk">*</span></label>
                                            <input type="file" name="default_pictures[image][3]" class="form-control form-control-solid default_pictures_image" accept="image/*" capture>
                                        </div>
                                    </div>

                                    @if(@$default_picture->image && get_valid_file_url('sitebucket/warranty-pictures',$default_picture->image))
                                      <div class="col-md-2 image_preview">
                                        <div class="form-group">
                                          <a href="{{ get_valid_file_url('sitebucket/warranty-pictures',$default_picture->image) }}" class="fancybox"><img src="{{ get_valid_file_url('sitebucket/warranty-pictures',$default_picture->image) }}" height="100" width="100" class=""></a>
                                        </div>
                                      </div>
                                    @endif
                                        

                                </div>
                                <div class="row mb-5 mt-10">
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label>Title <span class="asterisk">*</span></label>
                                            <input type="text" name="default_pictures[title][4]" class="form-control form-control-solid default_pictures_title" value="Inner Liner Area" readonly>
                                        </div>
                                    </div>

                                    @if(isset($edit))
                                        @php
                                            $default_picture = @$edit->pictures()->where('title','Inner Liner Area')->first();
                                        @endphp

                                        <input type="hidden" class="default_pictures_id" name="default_pictures[id][4]" value="{{ @$default_picture->id }}">
                                        <input type="hidden" class="default_pictures_image" name="default_pictures[image][4]" value="{{ @$default_picture->image }}">
                                    @endif

                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label>Upload Image <span class="asterisk">*</span></label>
                                            <input type="file" name="default_pictures[image][4]" class="form-control form-control-solid default_pictures_image" accept="image/*" capture>
                                        </div>
                                    </div>

                                    @if(@$default_picture->image && get_valid_file_url('sitebucket/warranty-pictures',$default_picture->image))
                                      <div class="col-md-2 image_preview">
                                        <div class="form-group">
                                          <a href="{{ get_valid_file_url('sitebucket/warranty-pictures',$default_picture->image) }}" class="fancybox"><img src="{{ get_valid_file_url('sitebucket/warranty-pictures',$default_picture->image) }}" height="100" width="100" class=""></a>
                                        </div>
                                      </div>
                                    @endif

                                </div>

                                <div data-repeater-list="other_pictures" class="mt-10">
                                    @if(isset($edit))

                                        @foreach(@$edit->pictures()->where('type','other')->get() as $key => $other)
                                            <div class="row mb-5" data-repeater-item>
                                                <div class="col-md-5">
                                                    <div class="form-group">
                                                        <label>Title <span class="asterisk">*</span></label>
                                                        <input type="text" name="title" class="form-control form-control-solid other_pictures_title" placeholder="Enter title" value="{{ $other->title }}">
                                                    </div>
                                                </div>

                                                <input type="hidden" class="other_pictures_id" name="id" value="{{ @$other->id }}">
                                                <input type="hidden" class="other_pictures_image" name="image" value="{{ @$other->image }}">

                                                <div class="col-md-5">
                                                    <div class="form-group">
                                                        <label>Upload Image <span class="asterisk">*</span></label>
                                                        <input type="file" class="form-control form-control-solid other_pictures_image" name="image" accept="image/*" capture>

                                                        @if(@$other->image && get_valid_file_url('sitebucket/warranty-pictures',$other->image))
                                                            <br>
                                                            <a href="{{ get_valid_file_url('sitebucket/warranty-pictures',$other->image) }}" class="fancybox image_preview"><img src="{{ get_valid_file_url('sitebucket/warranty-pictures',$other->image) }}" height="100" width="100" class=""></a>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                    <a href="javascript:" class="btn btn-icon btn-bg-light btn-active-color-primary btn-md btn-color-danger mt-6" data-repeater-delete><i class="fa fa-trash"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach

                                    @else
                                    <div class="row mb-5" data-repeater-item>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label>Title <span class="asterisk">*</span></label>
                                                <input type="text" name="title" class="form-control form-control-solid other_pictures_title" placeholder="Enter title">
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label>Upload Image <span class="asterisk">*</span></label>
                                                <input type="file" class="form-control form-control-solid other_pictures_image" name="image" accept="image/*" capture>
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-group">
                                            <a href="javascript:" class="btn btn-icon btn-bg-light btn-active-color-primary btn-md btn-color-danger mt-6" data-repeater-delete><i class="fa fa-trash"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                </div>

                                <div class="row mb-5">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                          <a href="javascript:" class="btn btn-success btn-sm" data-repeater-create >Add more</a>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-5">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <input type="submit" value="{{ isset($edit) ? "Update" : "Save" }}" class="btn btn-primary">
                                        </div>
                                    </div>
                                </div>
                            </form>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/additional-methods.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.repeater/1.2.1/jquery.repeater.min.js"></script>
{{-- <script src="{{ asset('assets')}}/assets/js/custom/bootstrap-datepicker.js"/></script> --}}
<script>

$(document).ready(function() {

    show_loader();

    // Select yes or no 
    $('.yes_no_checkbox').on('change', function() {
        $('input[name="' + this.name + '"]').not(this).prop('checked', false);
    });

    $('body').on("submit", "#myForm", function (e) {
        e.preventDefault();
        var validator = validate_form();

        if (validator.form() != false) {
        $('[type="submit"]').prop('disabled', true);
        $.ajax({
          url: "{{route('warranty.store')}}",
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
                    window.location.href = '{{ route('warranty.index') }}';
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
                warranty_claim_type:{
                  required: true,
                },
                dealer_name:{
                    required: true,
                    maxlength: 185,
                },
                customer_address:{
                    required: true,
                },
                customer_email:{
                    required: true,
                    maxlength: 185,
                },
                customer_phone:{
                    required: true,
                    minlength:10,
                    maxlength:10,
                    digits:true,
                },
                customer_location:{
                    required: true,
                    maxlength: 185,
                },
                customer_telephone:{
                    required: true,
                    minlength:10,
                    maxlength:10,
                    digits:true,
                },
                dealer_location:{
                    required: true,
                    maxlength: 185,
                },
                dealer_telephone:{
                    required: true,
                    minlength:10,
                    maxlength:10,
                    digits:true,
                },
                vehicle_maker:{
                    maxlength: 185,
                    required: true,
                },
                year:{
                    required: true,
                    minlength:4,
                    maxlength:4,
                    digits:true,
                },
                vehicle_model:{
                    required: true,
                    maxlength: 185,
                },
                license_plate:{
                    required: true,
                    maxlength: 185,
                },
                vehicle_mileage:{
                    required: true,
                    maxlength: 185,
                },
                reason_for_tire_return:{
                    required: true,
                },
            },
            messages: {
                title:{
                    required: "Please enter promotion title.",
                },
            },
        });

        $('.default_pictures_title').each(function() {
            $(this).rules('add', {
                required: true,
                maxlength: 185,
            });
        });

        $('.default_pictures_image').each(function() {
            /*$(this).rules('add', {
                @if(!isset($edit))
                required: true,
                @endif
            });*/

            var pre_image = $(this).closest('.row').find('.default_pictures_id').val();

            $(this).rules('add', {
                required: function () {
                            if(!pre_image){
                                return true;
                            }else{
                                return false;
                            }
                        },
                messages: {
                    accept : "Allow only .jpeg .jpg .png .eps .bmp .tif .tiff .webp files."
                }
            });

        });


        $('.other_pictures_title').each(function() {
            $(this).rules('add', {
                required: true,
                maxlength: 185,
            });
        });

        $('.other_pictures_image').each(function() {
            var pre_image = $(this).closest('.row').find('.other_pictures_id').val();

            $(this).rules('add', {
                required: function () {
                          if(!pre_image){
                              return true;
                          }else{
                              return false;
                          }
                      },
                messages: {
                    accept : "Allow only .jpeg .jpg .png .eps .bmp .tif .tiff .webp files."
                }
            });
        });

        return validator;
    }

    $('#myForm').repeater({
        @if(isset($edit) && count(@$edit->pictures()->where('type','other')->get()))
        initEmpty: false,
        @else
        initEmpty: true,
        @endif
        show: function () {
            $(this).find('.product_images_value').remove();
            $(this).find('.image_preview').remove();
            $(this).slideDown();
        },
        hide: function (deleteElement) {
            if(confirm('Are you sure you want to delete this element?')) {
                $(this).slideUp(deleteElement);
            }
        },
        ready: function (setIndexes) {
        },
        isFirstItemUndeletable: false,
    });

    hide_loader();

});
</script>
@endpush
