@extends('layouts.master')

@section('title','Role')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Role</h1>
      </div>

      <!--begin::Actions-->
      <div class="d-flex align-items-center py-1">
        <!--begin::Button-->
        <a href="{{ route('role.index') }}" class="btn btn-sm btn-primary">Back</a>
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
            <div class="card-header border-bottom pt-5">
              <h1 class="text-dark fw-bolder fs-3 my-1">{{ isset($edit) ? "Update" : "Add" }} Details</h1>
            </div>
            <div class="card-body">
              <form method="post" id="myForm">
                @csrf

                @if(isset($edit))
                  <input type="hidden" name="id" value="{{ $edit->id }}">
                @endif

                <div class="row mb-5 d-flex justify-content-between">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Role Name<span class="asterisk">*</span></label>
                      <input type="text" class="form-control form-control-solid" placeholder="Enter role name" name="name" @if(isset($edit)) value="{{ $edit->name }}" @endif >
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Parent</label>
                      <select class="form-control form-control-solid" name="parent_id">
                        <option value=""></option>
                        @foreach($parents as $parent)
                          <option value="{{ $parent->id }}" @if(isset($edit) && $edit->parent_id == $parent->id) selected="" @endif>{{ $parent->name }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Select Access<span class="asterisk">*</span></label>
                      <select class="form-select form-select-solid" data-control="select2" data-hide-search="true" name="all_module_access">
                        <option value="">Select Access </option>
                        <option value="1" @if(isset($edit) && $edit->all_module_access == 1) selected="" @endif>All Menu Access</option>
                        <option value="0" @if(isset($edit) && $edit->all_module_access == 0) selected="" @endif>Custom Menu Access</option>
                      </select>
                    </div>
                  </div>
                </div>

                <div class="row mb-5 mt-10">
                  <div class="col-md-12">
                    <div class="form-group">
                      
                      {{-- <input type="checkbox" value="1" name="modules[{{ @$modules['user']['id'] }}]"  @if(isset($role_module_access) && @$role_module_access[@$modules['user']['id']]['access'] == 1) checked="" @endif>{{ @$modules['user']['title']}} --}}

                    <div class="hummingbird-treeview">
                      <ul id="module_treeview" class="hummingbird-base">

                        {{-- User Management --}}
                        @if(@$modules['user']['id'])
                        <li>
                          <i class="fa fa-plus"></i>
                          <label>
                            <input data-id="usermgt" type="checkbox" 
                              @if(@$role_module_access[@$modules['location']['id']]['access'] == 1 && @$role_module_access[@$modules['user']['id']]['access'] == 1 && @$role_module_access[@$modules['department']['id']]['access'] == 1) checked="" @endif
                            /> User Management
                          </label>
                          <ul>

                            {{-- User --}}
                            @if(@$modules['user']['id'])
                            <li>
                              <i class="fa fa-plus"></i>
                              <label>
                                <input data-id="{{ @$modules['user']['id'] }}" type="checkbox"  name="modules[{{ @$modules['user']['id'] }}]"   /> {{ @$modules['user']['title'] }}
                              </label>
                              <ul>
                                <li>
                                  <label>
                                    <input data-id="{{ @$modules['add-user']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['add-user']['id'] }}]"   /> {{ @$modules['add-user']['title'] }}
                                  </label>
                                </li>

                                <li>
                                  <label>
                                    <input data-id="{{ @$modules['edit-user']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['edit-user']['id'] }}]"   /> {{ @$modules['edit-user']['title'] }}
                                  </label>
                                </li>

                                <li>
                                  <label>
                                    <input data-id="{{ @$modules['view-user']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['view-user']['id'] }}]"   /> {{ @$modules['view-user']['title'] }}
                                  </label>
                                </li>

                                <li>
                                  <label>
                                    <input data-id="{{ @$modules['delete-user']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['delete-user']['id'] }}]"   /> {{ @$modules['delete-user']['title'] }}
                                  </label>
                                </li>
                              </ul>
                            </li>
                            @endif

                            {{-- Locations --}}
                            @if(@$modules['location']['id'])
                            <li>
                              <i class="fa fa-plus"></i>
                              <label>
                                <input data-id="{{ @$modules['location']['id'] }}" type="checkbox"  name="modules[{{ @$modules['location']['id'] }}]"   /> {{ @$modules['location']['title'] }}
                              </label>
                              <ul>
                                <li>
                                  <label>
                                    <input data-id="{{ @$modules['add-location']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['add-location']['id'] }}]"  /> {{ @$modules['add-location']['title'] }}
                                  </label>
                                </li>

                                <li>
                                  <label>
                                    <input data-id="{{ @$modules['edit-location']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['edit-location']['id'] }}]"   /> {{ @$modules['edit-location']['title'] }}
                                  </label>
                                </li>

                                <li>
                                  <label>
                                    <input data-id="{{ @$modules['view-location']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['view-location']['id'] }}]" /> {{ @$modules['view-location']['title'] }}
                                  </label>
                                </li>

                                <li>
                                  <label>
                                    <input data-id="{{ @$modules['delete-location']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['delete-location']['id'] }}]"   /> {{ @$modules['delete-location']['title'] }}
                                  </label>
                                </li>
                              </ul>
                            </li>
                            @endif

                            {{-- department --}}
                            @if(@$modules['department']['id'])
                            <li>
                              <i class="fa fa-plus"></i>
                              <label>
                                <input data-id="{{ @$modules['department']['id'] }}" type="checkbox"  name="modules[{{ @$modules['department']['id'] }}]"  /> {{ @$modules['department']['title'] }}
                              </label>
                              <ul>
                                <li>
                                  <label>
                                    <input data-id="{{ @$modules['add-department']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['add-department']['id'] }}]"  /> {{ @$modules['add-department']['title'] }}
                                  </label>
                                </li>

                                <li>
                                  <label>
                                    <input data-id="{{ @$modules['edit-department']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['edit-department']['id'] }}]" /> {{ @$modules['edit-department']['title'] }}
                                  </label>
                                </li>

                                <li>
                                  <label>
                                    <input data-id="{{ @$modules['view-department']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['view-department']['id'] }}]" /> {{ @$modules['view-department']['title'] }}
                                  </label>
                                </li>

                                <li>
                                  <label>
                                    <input data-id="{{ @$modules['delete-department']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['delete-department']['id'] }}]" /> {{ @$modules['delete-department']['title'] }}
                                  </label>
                                </li>
                              </ul>
                            </li>
                            @endif

                          </ul>
                        </li>
                        @endif


                        {{-- Customer Management --}}
                        @if(@$modules['customer']['id'])
                        <li>
                          <i class="fa fa-plus"></i>
                          <label>
                            <input data-id="usermgt" type="checkbox" 
                              @if(@$role_module_access[@$modules['customer-group']['id']]['access'] == 1 && @$role_module_access[@$modules['class']['id']]['access'] == 1 && @$role_module_access[@$modules['customer']['id']]['access'] == 1) checked="" @endif
                              /> Customer Management
                          </label>
                          <ul>

                            {{-- class --}}
                            @if(@$modules['class']['id'])
                            <li>
                              <i class="fa fa-plus"></i>
                              <label>
                                <input data-id="{{ @$modules['class']['id'] }}" type="checkbox"  name="modules[{{ @$modules['class']['id'] }}]" /> {{ @$modules['class']['title'] }}
                              </label>
                              <ul>

                                <li>
                                  <label>
                                    <input data-id="{{ @$modules['view-class']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['view-class']['id'] }}]"  /> {{ @$modules['view-class']['title'] }}
                                  </label>
                                </li>
                                
                              </ul>
                            </li>
                            @endif

                            {{-- customer --}}
                            @if(@$modules['customer']['id'])
                            <li>
                              <i class="fa fa-plus"></i>
                              <label>
                                <input data-id="{{ @$modules['customer']['id'] }}" type="checkbox"  name="modules[{{ @$modules['customer']['id'] }}]" /> {{ @$modules['customer']['title'] }}
                              </label>
                              <ul>
                                <li>
                                  <label>
                                    <input data-id="{{ @$modules['add-customer']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['add-customer']['id'] }}]"  /> {{ @$modules['add-customer']['title'] }}
                                  </label>
                                </li>

                                <li>
                                  <label>
                                    <input data-id="{{ @$modules['view-customer']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['view-customer']['id'] }}]"  /> {{ @$modules['view-customer']['title'] }}
                                  </label>
                                </li>

                              </ul>
                            </li>
                            @endif

                            {{-- Customer Group --}}
                            @if(@$modules['customer-group']['id'])
                            <li>
                              <i class="fa fa-plus"></i>
                              <label>
                                <input data-id="{{ @$modules['customer-group']['id'] }}" type="checkbox"  name="modules[{{ @$modules['customer-group']['id'] }}]"  /> {{ @$modules['customer-group']['title'] }}
                              </label>
                              <ul>
                                <li>
                                  <label>
                                    <input data-id="{{ @$modules['add-customer-group']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['add-customer-group']['id'] }}]"  /> {{ @$modules['add-customer-group']['title'] }}
                                  </label>
                                </li>

                                <li>
                                  <label>
                                    <input data-id="{{ @$modules['view-customer-group']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['view-customer-group']['id'] }}]"  /> {{ @$modules['view-customer-group']['title'] }}
                                  </label>
                                </li>

                              </ul>
                            </li>
                            @endif

                          </ul>
                        </li>
                        @endif

                        {{-- Orders --}}
                        @if(@$modules['order']['id'])
                        <li>
                          <i class="fa fa-plus"></i>
                          <label>
                            <input data-id="{{ @$modules['order']['id'] }}" type="checkbox"  name="modules[{{ @$modules['order']['id'] }}]"/> {{ @$modules['order']['title'] }}
                          </label>
                          <ul>
                            <li>
                              <label>
                                <input data-id="{{ @$modules['add-order']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['add-order']['id'] }}]"  /> {{ @$modules['add-order']['title'] }}
                              </label>
                            </li>

                            <li>
                              <label>
                                <input data-id="{{ @$modules['view-order']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['view-order']['id'] }}]"  /> {{ @$modules['view-order']['title'] }}
                              </label>
                            </li>
                          </ul>
                        </li>
                        @endif

                        {{-- Invoice --}}
                        @if(@$modules['invoice']['id'])
                        <li>
                          <i class="fa fa-plus"></i>
                          <label>
                            <input data-id="{{ @$modules['invoice']['id'] }}" type="checkbox"  name="modules[{{ @$modules['invoice']['id'] }}]" /> {{ @$modules['invoice']['title'] }}
                          </label>
                          <ul>
                            <li>
                              <label>
                                <input data-id="{{ @$modules['add-invoice']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['add-invoice']['id'] }}]" /> {{ @$modules['add-invoice']['title'] }}
                              </label>
                            </li>

                            <li>
                              <label>
                                <input data-id="{{ @$modules['view-invoice']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['view-invoice']['id'] }}]"  /> {{ @$modules['view-invoice']['title'] }}
                              </label>
                            </li>
                          </ul>
                        </li>
                        @endif

                        {{-- Product --}}
                        @if(@$modules['product']['id'])
                        <li>
                          <i class="fa fa-plus"></i>
                          <label>
                            <input data-id="{{ @$modules['product']['id'] }}" type="checkbox"  name="modules[{{ @$modules['product']['id'] }}]" /> {{ @$modules['product']['title'] }}
                          </label>
                          <ul>
                            <li>
                              <label>
                                <input data-id="{{ @$modules['add-product']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['add-product']['id'] }}]" /> {{ @$modules['add-product']['title'] }}
                              </label>
                            </li>

                            <li>
                              <label>
                                <input data-id="{{ @$modules['edit-product']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['edit-product']['id'] }}]"   /> {{ @$modules['edit-product']['title'] }}
                              </label>
                            </li>

                            <li>
                              <label>
                                <input data-id="{{ @$modules['view-product']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['view-product']['id'] }}]"  /> {{ @$modules['view-product']['title'] }}
                              </label>
                            </li>
                          </ul>
                        </li>
                        @endif

                        {{-- Product List --}}
                        @if(@$modules['product-list']['id'])
                        <li>
                          <i class="fa fa-plus"></i>
                          <label>
                            <input data-id="{{ @$modules['product-list']['id'] }}" type="checkbox"  name="modules[{{ @$modules['product-list']['id'] }}]"  /> {{ @$modules['product-list']['title'] }}
                          </label>
                          <ul>
                            <li>
                              <label>
                                <input data-id="{{ @$modules['add-product-list']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['add-product-list']['id'] }}]" /> {{ @$modules['add-product-list']['title'] }}
                              </label>
                            </li>

                            <li>
                              <label>
                                <input data-id="{{ @$modules['view-product-list']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['view-product-list']['id'] }}]" /> {{ @$modules['view-product-list']['title'] }}
                              </label>
                            </li>
                            
                          </ul>
                        </li>
                        @endif

                        {{-- Promotions --}}
                        @if(@$modules['promotion']['id'])
                        <li>
                          <i class="fa fa-plus"></i>
                          <label>
                            <input data-id="{{ @$modules['promotion']['id'] }}" type="checkbox"  name="modules[{{ @$modules['promotion']['id'] }}]"  /> {{ @$modules['promotion']['title'] }}
                          </label>
                          <ul>
                            <li>
                              <label>
                                <input data-id="{{ @$modules['add-promotion']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['add-promotion']['id'] }}]" /> {{ @$modules['add-promotion']['title'] }}
                              </label>
                            </li>

                            <li>
                              <label>
                                <input data-id="{{ @$modules['edit-promotion']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['edit-promotion']['id'] }}]"  /> {{ @$modules['edit-promotion']['title'] }}
                              </label>
                            </li>

                            <li>
                              <label>
                                <input data-id="{{ @$modules['view-promotion']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['view-promotion']['id'] }}]"  /> {{ @$modules['view-promotion']['title'] }}
                              </label>
                            </li>

                            <li>
                              <label>
                                <input data-id="{{ @$modules['delete-promotion']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['delete-promotion']['id'] }}]" /> {{ @$modules['delete-promotion']['title'] }}
                              </label>
                            </li>
                          </ul>
                        </li>
                        @endif


                        {{-- territories --}}
                        @if(@$modules['territories']['id'])
                        <li>
                          <i class="fa fa-plus"></i>
                          <label>
                            <input data-id="{{ @$modules['territories']['id'] }}" type="checkbox"  name="modules[{{ @$modules['territories']['id'] }}]"  /> {{ @$modules['territories']['title'] }}
                          </label>
                          <ul>
                            <li>
                              <label>
                                <input data-id="{{ @$modules['add-territories']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['add-territories']['id'] }}]"  /> {{ @$modules['add-territories']['title'] }}
                              </label>
                            </li>

                            <li>
                              <label>
                                <input data-id="{{ @$modules['view-territories']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['view-territories']['id'] }}]"  /> {{ @$modules['view-territories']['title'] }}
                              </label>
                            </li>
                          </ul>
                        </li>
                        @endif


                        {{-- activity-log --}}
                        @if(@$modules['activity-log']['id'])
                        <li>
                          <i class="fa fa-plus"></i>
                          <label>
                            <input data-id="{{ @$modules['activity-log']['id'] }}" type="checkbox"  name="modules[{{ @$modules['activity-log']['id'] }}]"  /> {{ @$modules['activity-log']['title'] }}
                          </label>
                          <ul>
                            <li>
                              <label>
                                <input data-id="{{ @$modules['view-activity-log']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['activity-log']['id'] }}]" /> {{ @$modules['view-activity-log']['title'] }}
                              </label>
                            </li>
                          </ul>
                        </li>
                        @endif

                      </ul>
                    </div>

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


@push('js')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/additional-methods.min.js"></script>
<link rel="stylesheet" href="{{ asset('assets/assets/plugins/custom') }}/hummingbird-treeview/hummingbird-treeview.min.css">

<script src="{{ asset('assets/assets/plugins/custom') }}/hummingbird-treeview/hummingbird-treeview.min.js"></script>
<script>
  $("#module_treeview").hummingbird();

  $.fn.hummingbird.defaults.collapsedSymbol = "fa-caret-right";

  $.fn.hummingbird.defaults.expandedSymbol = "fa-caret-down";

  $("#module_treeview").hummingbird();

  @if(isset($role_module_access))
    $("#module_treeview").hummingbird("expandNode",{sel:"data-id",vals:<?php echo json_encode(array_keys($role_module_access)); ?>,expandParents:true});

    $("#module_treeview").hummingbird("checkNode",{sel:"data-id", vals:<?php echo json_encode(array_keys($role_module_access)); ?>});
  @endif


  $(document).ready(function() {
    
    $('[name="parent_id"]').select2({
      placeholder: "Select a parent",
      allowClear: true
    });

    $("#module_treeview").hummingbird("checkNode",{sel:"id", vals:["hum_1","hum_2","hum_3"]});

    @if(isset($edit) && $edit->all_module_access == 1)
    $("#module_treeview").hummingbird("checkAll");
    @endif

    $('body').on("change", '[name="all_module_access"]', function (e) {
      
      if($(this).find('option:selected').val() == 1){
        $("#module_treeview").hummingbird("checkAll");
      }else{
        $("#module_treeview").hummingbird("uncheckAll");
      }

    });

    $('body').on("submit", "#myForm", function (e) {
      e.preventDefault();
      var validator = validate_form();
      
      if (validator.form() != false) {
        $('[type="submit"]').prop('disabled', true);
        $.ajax({
          url: "{{route('role.store')}}",
          type: "POST",
          data: new FormData($("#myForm")[0]),
          async: false,
          processData: false,
          contentType: false,
          success: function (data) {
            if (data.status) {
              toast_success(data.message)
              setTimeout(function(){
                window.location.href = '{{ route('role.index') }}';
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
            name:{
              required: true,
              maxlength: 185,
            },
            all_module_access:{
              required: true,
            }
          },
          messages: {
            name:{
              required: "Please enter role name.",
              maxlength:'Please enter role name less than 185 character',
            },
            all_module_access:{
              required: "Please select module access.",
            },
          },
      });

      return validator;
    }
  
  });
</script>
@endpush