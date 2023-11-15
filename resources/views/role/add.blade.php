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
                      <input type="text" class="form-control form-control-solid" placeholder="Enter role name" name="name" @if(isset($edit)) value="{{ $edit->name }}" @if(in_array($edit->id, [2])) readonly="" @endif @endif >
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
                      <ul id="module_treeview" class="hummingbird-base" style="display:none;">

                        {{-- User Management --}}
                        @if(@$modules['user-management']['id'])
                        <li>
                          <i class="fa fa-plus"></i>
                          <label>
                            <input data-id="{{ @$modules['user-management']['id'] }}" type="checkbox" 
                              {{-- @if(@$role_module_access[@$modules['location']['id']]['access'] == 1 && @$role_module_access[@$modules['user']['id']]['access'] == 1 && @$role_module_access[@$modules['department']['id']]['access'] == 1) checked="" @endif --}}
                              @if(@$role_module_access[@$modules['user']['id']]['access'] == 1 && @$role_module_access[@$modules['department']['id']]['access'] == 1) checked="" @endif
                            /> {{ @$modules['user-management']['title'] }}
                          </label>
                          <ul>

                            {{-- Role --}}
                            @if(@$modules['role']['id'])
                            <li>
                              <i class="fa fa-plus"></i>
                              <label>
                                <input data-id="{{ @$modules['role']['id'] }}" type="checkbox" /> {{ @$modules['role']['title'] }}
                              </label>
                              <ul>

                                @if(@$modules['add-role']['id'])
                                <li>
                                  <label>
                                    <input data-id="{{ @$modules['add-role']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['add-role']['id'] }}]"   /> {{ @$modules['add-role']['title'] }}
                                  </label>
                                </li>
                                @endif

                                @if(@$modules['edit-role']['id'])
                                <li>
                                  <label>
                                    <input data-id="{{ @$modules['edit-role']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['edit-role']['id'] }}]"   /> {{ @$modules['edit-role']['title'] }}
                                  </label>
                                </li>
                                @endif

                                @if(@$modules['view-role']['id'])
                                <li>
                                  <label>
                                    <input data-id="{{ @$modules['view-role']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['view-role']['id'] }}]"   /> {{ @$modules['view-role']['title'] }}
                                  </label>
                                </li>
                                @endif

                                @if(@$modules['delete-role']['id'])
                                <li>
                                  <label>
                                    <input data-id="{{ @$modules['delete-role']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['delete-role']['id'] }}]"   /> {{ @$modules['delete-role']['title'] }}
                                  </label>
                                </li>
                                @endif

                              </ul>
                            </li>
                            @endif

                            {{-- User --}}
                            @if(@$modules['user']['id'])
                            <li>
                              <i class="fa fa-plus"></i>
                              <label>
                                <input data-id="{{ @$modules['user']['id'] }}" type="checkbox" /> {{ @$modules['user']['title'] }}
                              </label>
                              <ul>

                                @if(@$modules['add-user']['id'])  
                                <li>
                                  <label>
                                    <input data-id="{{ @$modules['add-user']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['add-user']['id'] }}]"   /> {{ @$modules['add-user']['title'] }}
                                  </label>
                                </li>
                                @endif
  
                                @if(@$modules['edit-user']['id'])
                                <li>
                                  <label>
                                    <input data-id="{{ @$modules['edit-user']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['edit-user']['id'] }}]"   /> {{ @$modules['edit-user']['title'] }}
                                  </label>
                                </li>
                                @endif

                                @if(@$modules['view-user']['id'])
                                <li>
                                  <label>
                                    <input data-id="{{ @$modules['view-user']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['view-user']['id'] }}]"   /> {{ @$modules['view-user']['title'] }}
                                  </label>
                                </li>
                                @endif

                                @if(@$modules['delete-user']['id'])
                                <li>
                                  <label>
                                    <input data-id="{{ @$modules['delete-user']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['delete-user']['id'] }}]"   /> {{ @$modules['delete-user']['title'] }}
                                  </label>
                                </li>
                                @endif

                              </ul>
                            </li>
                            @endif

                            {{-- Locations --}}
                            {{-- @if(@$modules['location']['id'])
                            <li>
                              <i class="fa fa-plus"></i>
                              <label>
                                <input data-id="{{ @$modules['location']['id'] }}" type="checkbox" /> {{ @$modules['location']['title'] }}
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
                            @endif --}}

                            {{-- department --}}
                            @if(@$modules['department']['id'])
                            <li>
                              <i class="fa fa-plus"></i>
                              <label>
                                <input data-id="{{ @$modules['department']['id'] }}" type="checkbox"  /> {{ @$modules['department']['title'] }}
                              </label>
                              <ul>

                                @if(@$modules['add-department']['id'])
                                <li>
                                  <label>
                                    <input data-id="{{ @$modules['add-department']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['add-department']['id'] }}]"  /> {{ @$modules['add-department']['title'] }}
                                  </label>
                                </li>
                                @endif

                                @if(@$modules['edit-department']['id'])
                                <li>
                                  <label>
                                    <input data-id="{{ @$modules['edit-department']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['edit-department']['id'] }}]" /> {{ @$modules['edit-department']['title'] }}
                                  </label>
                                </li>
                                @endif

                                @if(@$modules['view-department']['id'])
                                <li>
                                  <label>
                                    <input data-id="{{ @$modules['view-department']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['view-department']['id'] }}]" /> {{ @$modules['view-department']['title'] }}
                                  </label>
                                </li>
                                @endif

                                @if(@$modules['delete-department']['id'])
                                <li>
                                  <label>
                                    <input data-id="{{ @$modules['delete-department']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['delete-department']['id'] }}]" /> {{ @$modules['delete-department']['title'] }}
                                  </label>
                                </li>
                                @endif

                              </ul>
                            </li>
                            @endif

                          </ul>
                        </li>
                        @endif


                        {{-- Customer Management --}}
                        @if(@$modules['customer-management']['id'])
                        <li>
                          <i class="fa fa-plus"></i>
                          <label>
                            <input data-id="{{ @$modules['customer-management']['id'] }}" type="checkbox" 
                              @if(@$role_module_access[@$modules['customer-group']['id']]['access'] == 1 && @$role_module_access[@$modules['class']['id']]['access'] == 1 && @$role_module_access[@$modules['customer']['id']]['access'] == 1) checked="" @endif
                              /> {{ @$modules['customer-management']['title'] }}
                          </label>
                          <ul>

                            {{-- class --}}
                            @if(@$modules['class']['id'])
                            <li>
                              <i class="fa fa-plus"></i>
                              <label>
                                <input data-id="{{ @$modules['class']['id'] }}" type="checkbox" /> {{ @$modules['class']['title'] }}
                              </label>
                              <ul>

                                @if(@$modules['view-class']['id'])
                                <li>
                                  <label>
                                    <input data-id="{{ @$modules['view-class']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['view-class']['id'] }}]"  /> {{ @$modules['view-class']['title'] }}
                                  </label>
                                </li>
                                @endif

                              </ul>
                            </li>
                            @endif

                            {{-- customer --}}
                            @if(@$modules['customer']['id'])
                            <li>
                              <i class="fa fa-plus"></i>
                              <label>
                                <input data-id="{{ @$modules['customer']['id'] }}" type="checkbox"  /> {{ @$modules['customer']['title'] }}
                              </label>
                              <ul>

                                @if(@$modules['add-customer']['id'])
                                <li>
                                  <label>
                                    <input data-id="{{ @$modules['add-customer']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['add-customer']['id'] }}]"  /> {{ @$modules['add-customer']['title'] }}
                                  </label>
                                </li>
                                @endif

                                @if(@$modules['view-customer']['id'])
                                <li>
                                  <label>
                                    <input data-id="{{ @$modules['view-customer']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['view-customer']['id'] }}]"  /> {{ @$modules['view-customer']['title'] }}
                                  </label>
                                </li>
                                @endif

                              </ul>
                            </li>
                            @endif

                            {{-- Customer Group --}}
                            @if(@$modules['customer-group']['id'])
                            <li>
                              <i class="fa fa-plus"></i>
                              <label>
                                <input data-id="{{ @$modules['customer-group']['id'] }}" type="checkbox"  /> {{ @$modules['customer-group']['title'] }}
                              </label>
                              <ul>

                                @if(@$modules['add-customer-group']['id'])
                                <li>
                                  <label>
                                    <input data-id="{{ @$modules['add-customer-group']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['add-customer-group']['id'] }}]"  /> {{ @$modules['add-customer-group']['title'] }}
                                  </label>
                                </li>
                                @endif

                                @if(@$modules['view-customer-group']['id'])
                                <li>
                                  <label>
                                    <input data-id="{{ @$modules['view-customer-group']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['view-customer-group']['id'] }}]"  /> {{ @$modules['view-customer-group']['title'] }}
                                  </label>
                                </li>
                                @endif

                              </ul>
                            </li>
                            @endif

                            @if(@$modules['delivery-schedule']['id'])
                            <li>
                              <i class="fa fa-plus"></i>
                              <label>
                                <input data-id="{{ @$modules['delivery-schedule']['id'] }}" type="checkbox" /> {{ @$modules['delivery-schedule']['title'] }}
                              </label>

                              <ul>
                                @if(@$modules['add-schedule']['id'])
                                <li>
                                  <label>
                                    <input data-id="{{ @$modules['add-schedule']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['add-schedule']['id'] }}]" /> {{ @$modules['add-schedule']['title'] }}
                                  </label>
                                </li>
                                @endif

                                @if(@$modules['view-schedule']['id'])
                                <li>
                                  <label>
                                    <input data-id="{{ @$modules['view-schedule']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['view-schedule']['id'] }}]" /> {{ @$modules['view-schedule']['title'] }}
                                  </label>
                                </li>
                                @endif

                                @if(@$modules['edit-schedule']['id'])
                                <li>
                                  <label>
                                    <input data-id="{{ @$modules['edit-schedule']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['edit-schedule']['id'] }}]" /> {{ @$modules['edit-schedule']['title'] }}
                                  </label>
                                </li>
                                @endif

                                @if(@$modules['delete-schedule']['id'])
                                <li>
                                  <label>
                                    <input data-id="{{ @$modules['delete-schedule']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['delete-schedule']['id'] }}]" /> {{ @$modules['delete-schedule']['title'] }}
                                  </label>
                                </li>
                                @endif

                                @if(@$modules['view-all-schedule']['id'])
                                <li>
                                  <label>
                                    <input data-id="{{ @$modules['view-all-schedule']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['view-all-schedule']['id'] }}]" /> {{ @$modules['view-all-schedule']['title'] }}
                                  </label>
                                </li>
                                @endif
                              </ul>
                            </li>
                            @endif

                            {{-- @if(@$modules['customer-delivery-schedule']['id'])
                            <li>
                              <i class="fa fa-plus"></i>
                              <label>
                                <input data-id="{{ @$modules['customer-delivery-schedule']['id'] }}" type="checkbox" /> {{ @$modules['customer-delivery-schedule']['title'] }}
                              </label>

                              <ul>
                                @if(@$modules['view-all-customer-delivery-schedule']['id'])
                                <li>
                                  <label>
                                    <input data-id="{{ @$modules['view-all-customer-delivery-schedule']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['view-all-customer-delivery-schedule']['id'] }}]" /> {{ @$modules['view-all-customer-delivery-schedule']['title'] }}
                                  </label>
                                </li>
                                @endif
                               
                              </ul>
                            </li>
                            @endif --}}

                            @if(@$modules['sales-specialist-assignment']['id'])
                            <li>
                              <i class="fa fa-plus"></i>
                              <label>
                                <input data-id="{{ @$modules['sales-specialist-assignment']['id'] }}" type="checkbox" /> {{ @$modules['sales-specialist-assignment']['title'] }}
                              </label>

                              <ul>
                                @if(@$modules['add-sales-specialist-assignment']['id'])
                                <li>
                                  <label>
                                    <input data-id="{{ @$modules['add-sales-specialist-assignment']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['add-sales-specialist-assignment']['id'] }}]" /> {{ @$modules['add-sales-specialist-assignment']['title'] }}
                                  </label>
                                </li>
                                @endif

                                @if(@$modules['view-sales-specialist-assignment']['id'])
                                <li>
                                  <label>
                                    <input data-id="{{ @$modules['view-sales-specialist-assignment']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['view-sales-specialist-assignment']['id'] }}]" /> {{ @$modules['view-sales-specialist-assignment']['title'] }}
                                  </label>
                                </li>
                                @endif

                                @if(@$modules['edit-sales-specialist-assignment']['id'])
                                <li>
                                  <label>
                                    <input data-id="{{ @$modules['edit-sales-specialist-assignment']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['edit-sales-specialist-assignment']['id'] }}]" /> {{ @$modules['edit-sales-specialist-assignment']['title'] }}
                                  </label>
                                </li>
                                @endif

                                @if(@$modules['delete-sales-specialist-assignment']['id'])
                                <li>
                                  <label>
                                    <input data-id="{{ @$modules['delete-sales-specialist-assignment']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['delete-sales-specialist-assignment']['id'] }}]" /> {{ @$modules['delete-sales-specialist-assignment']['title'] }}
                                  </label>
                                </li>
                                @endif
                              </ul>
                            </li>
                            @endif

                            {{-- Customer Target --}}
                            @if(@$modules['customer-target']['id'])
                            <li>
                              <i class="fa fa-plus"></i>
                              <label>
                                <input data-id="{{ @$modules['customer-target']['id'] }}" type="checkbox"  /> {{ @$modules['customer-target']['title'] }}
                              </label>
                              <ul>

                                @if(@$modules['add-customer-target']['id'])
                                <li>
                                  <label>
                                    <input data-id="{{ @$modules['add-customer-target']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['add-customer-target']['id'] }}]"  /> {{ @$modules['add-customer-target']['title'] }}
                                  </label>
                                </li>
                                @endif

                                @if(@$modules['edit-customer-target']['id'])
                                <li>
                                  <label>
                                    <input data-id="{{ @$modules['edit-customer-target']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['edit-customer-target']['id'] }}]"  /> {{ @$modules['edit-customer-target']['title'] }}
                                  </label>
                                </li>
                                @endif

                                @if(@$modules['view-customer-target']['id'])
                                <li>
                                  <label>
                                    <input data-id="{{ @$modules['view-customer-target']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['view-customer-target']['id'] }}]"  /> {{ @$modules['view-customer-target']['title'] }}
                                  </label>
                                </li>
                                @endif

                              </ul>
                            </li>
                            @endif


                          </ul>
                        </li>
                        @endif


                        {{-- Product Management --}}
                        @if(@$modules['product-management']['id'])
                        <li>
                          <i class="fa fa-plus"></i>
                          <label>
                            <input data-id="{{ @$modules['product-management']['id'] }}" type="checkbox" 
                              @if(@$role_module_access[@$modules['product-group']['id']]['access'] == 1 && @$role_module_access[@$modules['product']['id']]['access'] == 1) checked="" @endif
                              /> {{ @$modules['product-management']['title'] }}
                          </label>
                          <ul>

                            {{-- product --}}
                            @if(@$modules['product']['id'])
                            <li>
                              <i class="fa fa-plus"></i>
                              <label>
                                <input data-id="{{ @$modules['product']['id'] }}" type="checkbox"  /> {{ @$modules['product']['title'] }}
                              </label>
                              <ul>

                                @if(@$modules['add-product']['id'])
                                <li>
                                  <label>
                                    <input data-id="{{ @$modules['add-product']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['add-product']['id'] }}]" /> {{ @$modules['add-product']['title'] }}
                                  </label>
                                </li>
                                @endif

                                @if(@$modules['edit-product']['id'])
                                <li>
                                  <label>
                                    <input data-id="{{ @$modules['edit-product']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['edit-product']['id'] }}]"   /> {{ @$modules['edit-product']['title'] }}
                                  </label>
                                </li>
                                @endif

                                @if(@$modules['view-product']['id'])
                                <li>
                                  <label>
                                    <input data-id="{{ @$modules['view-product']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['view-product']['id'] }}]"  /> {{ @$modules['view-product']['title'] }}
                                  </label>
                                </li>
                                @endif

                              </ul>
                            </li>
                            @endif

                            {{-- Product Group --}}
                            @if(@$modules['product-group']['id'])
                            <li>
                              <i class="fa fa-plus"></i>
                              <label>
                                <input data-id="{{ @$modules['product-group']['id'] }}" type="checkbox"  /> {{ @$modules['product-group']['title'] }}
                              </label>
                              <ul>

                                @if(@$modules['add-product-group']['id'])
                                <li>
                                  <label>
                                    <input data-id="{{ @$modules['add-product-group']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['add-product-group']['id'] }}]"  /> {{ @$modules['add-product-group']['title'] }}
                                  </label>
                                </li>
                                @endif

                                @if(@$modules['view-product-group']['id'])
                                <li>
                                  <label>
                                    <input data-id="{{ @$modules['view-product-group']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['view-product-group']['id'] }}]"  /> {{ @$modules['view-product-group']['title'] }}
                                  </label>
                                </li>
                                @endif

                              </ul>
                            </li>
                            @endif


                            {{-- Recommended Product --}}
                            @if(@$modules['recommended-products']['id'])
                            <li>
                              <i class="fa fa-plus"></i>
                              <label>
                                <input data-id="{{ @$modules['recommended-products']['id'] }}" type="checkbox"  /> {{ @$modules['recommended-products']['title'] }}
                              </label>
                              <ul>

                                @if(@$modules['add-recommended-product']['id'])
                                <li>
                                  <label>
                                    <input data-id="{{ @$modules['add-recommended-product']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['add-recommended-product']['id'] }}]"  /> {{ @$modules['add-recommended-product']['title'] }}
                                  </label>
                                </li>
                                @endif

                                @if(@$modules['edit-recommended-product']['id'])
                                <li>
                                  <label>
                                    <input data-id="{{ @$modules['edit-recommended-product']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['edit-recommended-product']['id'] }}]"  /> {{ @$modules['edit-recommended-product']['title'] }}
                                  </label>
                                </li>
                                @endif

                                @if(@$modules['view-recommended-product']['id'])
                                <li>
                                  <label>
                                    <input data-id="{{ @$modules['view-recommended-product']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['view-recommended-product']['id'] }}]"  /> {{ @$modules['view-recommended-product']['title'] }}
                                  </label>
                                </li>
                                @endif

                              </ul>
                            </li>
                            @endif


                            {{-- Product Benefits --}}
                            @if(@$modules['product-benefits']['id'])
                            <li>
                              <i class="fa fa-plus"></i>
                              <label>
                                <input data-id="{{ @$modules['product-benefits']['id'] }}" type="checkbox"  /> {{ @$modules['product-benefits']['title'] }}
                              </label>
                              <ul>

                                @if(@$modules['add-product-benefits']['id'])
                                <li>
                                  <label>
                                    <input data-id="{{ @$modules['add-product-benefits']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['add-product-benefits']['id'] }}]"  /> {{ @$modules['add-product-benefits']['title'] }}
                                  </label>
                                </li>
                                @endif

                                @if(@$modules['edit-product-benefits']['id'])
                                <li>
                                  <label>
                                    <input data-id="{{ @$modules['edit-product-benefits']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['edit-product-benefits']['id'] }}]"  /> {{ @$modules['edit-product-benefits']['title'] }}
                                  </label>
                                </li>
                                @endif

                                @if(@$modules['view-product-benefits']['id'])
                                <li>
                                  <label>
                                    <input data-id="{{ @$modules['view-product-benefits']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['view-product-benefits']['id'] }}]"  /> {{ @$modules['view-product-benefits']['title'] }}
                                  </label>
                                </li>
                                @endif

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
                            <input data-id="{{ @$modules['order']['id'] }}" type="checkbox" /> {{ @$modules['order']['title'] }}
                          </label>
                          <ul>

                            @if(@$modules['add-order']['id'])
                            <li>
                              <label>
                                <input data-id="{{ @$modules['add-order']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['add-order']['id'] }}]"  /> {{ @$modules['add-order']['title'] }}
                              </label>
                            </li>
                            @endif

                            @if(@$modules['view-order']['id'])
                            <li>
                              <label>
                                <input data-id="{{ @$modules['view-order']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['view-order']['id'] }}]"  /> {{ @$modules['view-order']['title'] }}
                              </label>
                            </li>
                            @endif

                          </ul>
                        </li>
                        @endif

                        {{-- Invoice --}}
                        @if(@$modules['invoice']['id'])
                        <li>
                          <i class="fa fa-plus"></i>
                          <label>
                            <input data-id="{{ @$modules['invoice']['id'] }}" type="checkbox" /> {{ @$modules['invoice']['title'] }}
                          </label>
                          <ul>

                            @if(@$modules['add-invoice']['id'])
                            <li>
                              <label>
                                <input data-id="{{ @$modules['add-invoice']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['add-invoice']['id'] }}]" /> {{ @$modules['add-invoice']['title'] }}
                              </label>
                            </li>
                            @endif

                            @if(@$modules['view-invoice']['id'])
                            <li>
                              <label>
                                <input data-id="{{ @$modules['view-invoice']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['view-invoice']['id'] }}]"  /> {{ @$modules['view-invoice']['title'] }}
                              </label>
                            </li>
                            @endif

                          </ul>
                        </li>
                        @endif

                        {{-- Promotions --}}
                        @if(@$modules['promotion']['id'])
                        <li>
                          <i class="fa fa-plus"></i>
                          <label>
                            <input data-id="{{ @$modules['promotion']['id'] }}" type="checkbox" /> {{ @$modules['promotion']['title'] }}
                          </label>
                          <ul>

                            @if(@$modules['add-promotion']['id'])
                            <li>
                              <label>
                                <input data-id="{{ @$modules['add-promotion']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['add-promotion']['id'] }}]" /> {{ @$modules['add-promotion']['title'] }}
                              </label>
                            </li>
                            @endif

                            @if(@$modules['edit-promotion']['id'])
                            <li>
                              <label>
                                <input data-id="{{ @$modules['edit-promotion']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['edit-promotion']['id'] }}]"  /> {{ @$modules['edit-promotion']['title'] }}
                              </label>
                            </li>
                            @endif

                            @if(@$modules['view-promotion']['id'])
                            <li>
                              <label>
                                <input data-id="{{ @$modules['view-promotion']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['view-promotion']['id'] }}]"  /> {{ @$modules['view-promotion']['title'] }}
                              </label>
                            </li>
                            @endif

                            @if(@$modules['delete-promotion']['id'])
                            <li>
                              <label>
                                <input data-id="{{ @$modules['delete-promotion']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['delete-promotion']['id'] }}]" /> {{ @$modules['delete-promotion']['title'] }}
                              </label>
                            </li>
                            @endif

                          </ul>
                        </li>
                        @endif


                        {{-- territories --}}
                        @if(@$modules['territories']['id'])
                        <li>
                          <i class="fa fa-plus"></i>
                          <label>
                            <input data-id="{{ @$modules['territories']['id'] }}" type="checkbox" /> {{ @$modules['territories']['title'] }}
                          </label>
                          <ul>

                            @if(@$modules['add-territories']['id'])
                            <li>
                              <label>
                                <input data-id="{{ @$modules['add-territories']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['add-territories']['id'] }}]"  /> {{ @$modules['add-territories']['title'] }}
                              </label>
                            </li>
                            @endif

                            @if(@$modules['view-territories']['id'])
                            <li>
                              <label>
                                <input data-id="{{ @$modules['view-territories']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['view-territories']['id'] }}]"  /> {{ @$modules['view-territories']['title'] }}
                              </label>
                            </li>
                            @endif

                          </ul>
                        </li>
                        @endif

                        {{-- my-promotions --}}
                        @if(@$modules['my-promotions']['id'])
                        <li>
                          <i class="fa fa-plus"></i>
                          <label>
                            <input data-id="{{ @$modules['my-promotions']['id'] }}" type="checkbox" /> {{ @$modules['my-promotions']['title'] }}
                          </label>
                          <ul>

                            @if(@$modules['view-my-promotions']['id'])
                            <li>
                              <label>
                                <input data-id="{{ @$modules['view-my-promotions']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['view-my-promotions']['id'] }}]" /> {{ @$modules['view-my-promotions']['title'] }}
                              </label>
                            </li>
                            @endif

                          </ul>
                        </li>
                        @endif

                        {{-- product-list --}}
                        @if(@$modules['product-list']['id'])
                        <li>
                          <i class="fa fa-plus"></i>
                          <label>
                            <input data-id="{{ @$modules['product-list']['id'] }}" type="checkbox" /> {{ @$modules['product-list']['title'] }}
                          </label>
                          <ul>

                            @if(@$modules['view-product-list']['id'])
                            <li>
                              <label>
                                <input data-id="{{ @$modules['view-product-list']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['view-product-list']['id'] }}]" /> {{ @$modules['view-product-list']['title'] }}
                              </label>
                            </li>
                            @endif

                          </ul>
                        </li>
                        @endif


                        {{-- warranty --}}
                        @if(@$modules['warranty']['id'])
                        <li>
                          <i class="fa fa-plus"></i>
                          <label>
                            <input data-id="{{ @$modules['warranty']['id'] }}" type="checkbox" /> {{ @$modules['warranty']['title'] }}
                          </label>
                          <ul>

                            @if(@$modules['add-warranty']['id'])
                            <li>
                              <label>
                                <input data-id="{{ @$modules['add-warranty']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['add-warranty']['id'] }}]" /> {{ @$modules['add-warranty']['title'] }}
                              </label>
                            </li>
                            @endif

                            @if(@$modules['edit-warranty']['id'])
                            <li>
                              <label>
                                <input data-id="{{ @$modules['edit-warranty']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['edit-warranty']['id'] }}]" /> {{ @$modules['edit-warranty']['title'] }}
                              </label>
                            </li>
                            @endif

                            @if(@$modules['view-warranty']['id'])
                            <li>
                              <label>
                                <input data-id="{{ @$modules['view-warranty']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['view-warranty']['id'] }}]" /> {{ @$modules['view-warranty']['title'] }}
                              </label>
                            </li>
                            @endif

                            @if(@$modules['delete-warranty']['id'])
                            <li>
                              <label>
                                <input data-id="{{ @$modules['delete-warranty']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['delete-warranty']['id'] }}]" /> {{ @$modules['delete-warranty']['title'] }}
                              </label>
                            </li>
                            @endif

                          </ul>
                        </li>
                        @endif


                        {{-- customer-delivery-schedule --}}
                        @if(@$modules['customer-delivery-schedule']['id'])
                        <li>
                          <i class="fa fa-plus"></i>
                          <label>
                            <input data-id="{{ @$modules['customer-delivery-schedule']['id'] }}" type="checkbox" /> {{ @$modules['customer-delivery-schedule']['title'] }}
                          </label>
                          <ul>

                            @if(@$modules['view-all-customer-delivery-schedule']['id'])
                            <li>
                              <label>
                                <input data-id="{{ @$modules['view-all-customer-delivery-schedule']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['view-all-customer-delivery-schedule']['id'] }}]" /> {{ @$modules['view-all-customer-delivery-schedule']['title'] }}
                              </label>
                            </li>
                            @endif

                          </ul>
                        </li>
                        @endif

                        {{-- reports --}}
                        @if(@$modules['reports']['id'])
                        <li>
                          <i class="fa fa-plus"></i>
                          <label>
                            <input data-id="{{ @$modules['reports']['id'] }}" type="checkbox" /> {{ @$modules['reports']['title'] }}
                          </label>
                          <ul>

                            @if(@$modules['promotion-report']['id'])
                            <li>
                              <label>
                                <input data-id="{{ @$modules['promotion-report']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['promotion-report']['id'] }}]" /> {{ @$modules['promotion-report']['title'] }}
                              </label>
                            </li>
                            @endif

                            @if(@$modules['sales-report']['id'])
                            <li>
                              <label>
                                <input data-id="{{ @$modules['sales-report']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['sales-report']['id'] }}]" /> {{ @$modules['sales-report']['title'] }}
                              </label>
                            </li>
                            @endif

                            @if(@$modules['sales-order-report']['id'])
                            <li>
                              <label>
                                <input data-id="{{ @$modules['sales-order-report']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['sales-order-report']['id'] }}]" /> {{ @$modules['sales-order-report']['title'] }}
                              </label>
                            </li>
                            @endif

                            @if(@$modules['overdue-sales-invoice-report']['id'])
                            <li>
                              <label>
                                <input data-id="{{ @$modules['overdue-sales-invoice-report']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['overdue-sales-invoice-report']['id'] }}]" /> {{ @$modules['overdue-sales-invoice-report']['title'] }}
                              </label>
                            </li>
                            @endif

                            @if(@$modules['back-order-report']['id'])
                            <li>
                              <label>
                                <input data-id="{{ @$modules['back-order-report']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['back-order-report']['id'] }}]" /> {{ @$modules['back-order-report']['title'] }}
                              </label>
                            </li>
                            @endif

                            @if(@$modules['product-sales-report']['id'])
                            <li>
                              <label>
                                <input data-id="{{ @$modules['product-sales-report']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['product-sales-report']['id'] }}]" /> {{ @$modules['product-sales-report']['title'] }}
                              </label>
                            </li>
                            @endif

                            @if(@$modules['product-report']['id'])
                            <li>
                              <label>
                                <input data-id="{{ @$modules['product-report']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['product-report']['id'] }}]" /> {{ @$modules['product-report']['title'] }}
                              </label>
                            </li>
                            @endif

                            @if(@$modules['credit-memo-report']['id'])
                            <li>
                              <label>
                                <input data-id="{{ @$modules['credit-memo-report']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['credit-memo-report']['id'] }}]" /> {{ @$modules['credit-memo-report']['title'] }}
                              </label>
                            </li>
                            @endif

                            @if(@$modules['debit-memo-report']['id'])
                            <li>
                              <label>
                                <input data-id="{{ @$modules['debit-memo-report']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['debit-memo-report']['id'] }}]" /> {{ @$modules['debit-memo-report']['title'] }}
                              </label>
                            </li>
                            @endif

                            @if(@$modules['return-order-report']['id'])
                            <li>
                              <label>
                                <input data-id="{{ @$modules['return-order-report']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['return-order-report']['id'] }}]" /> {{ @$modules['return-order-report']['title'] }}
                              </label>
                            </li>
                            @endif

                            @if(@$modules['sales-order-to-invoice-lead-time-report']['id'])
                            <li>
                              <label>
                                <input data-id="{{ @$modules['sales-order-to-invoice-lead-time-report']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['sales-order-to-invoice-lead-time-report']['id'] }}]" /> {{ @$modules['sales-order-to-invoice-lead-time-report']['title'] }}
                              </label>
                            </li>
                            @endif

                            @if(@$modules['invoice-to-delivery-lead-time-report']['id'])
                            <li>
                              <label>
                                <input data-id="{{ @$modules['invoice-to-delivery-lead-time-report']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['invoice-to-delivery-lead-time-report']['id'] }}]" /> {{ @$modules['invoice-to-delivery-lead-time-report']['title'] }}
                              </label>
                            </li>
                            @endif

                          </ul>
                        </li>
                        @endif

                        {{-- activity-log --}}
                        @if(@$modules['activity-log']['id'])
                        <li>
                          <i class="fa fa-plus"></i>
                          <label>
                            <input data-id="{{ @$modules['activity-log']['id'] }}" type="checkbox" /> {{ @$modules['activity-log']['title'] }}
                          </label>
                          <ul>

                            @if(@$modules['view-activity-log']['id'])
                            <li>
                              <label>
                                <input data-id="{{ @$modules['view-activity-log']['id'] }}" type="checkbox" class="hummingbird-end-node"  name="modules[{{ @$modules['activity-log']['id'] }}]" /> {{ @$modules['view-activity-log']['title'] }}
                              </label>
                            </li>
                            @endif

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

    $("#module_treeview").show();

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
                @if(isset($edit->id))
                  window.location.reload(); 
                @else
                  window.location.href = '{{ route('role.index') }}';
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