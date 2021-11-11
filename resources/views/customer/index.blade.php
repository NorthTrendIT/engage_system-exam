@extends('layouts.master')

@section('title','Customer List')

@section('content')
<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <!--begin::Toolbar-->
  <div class="toolbar" id="kt_toolbar">
     <!--begin::Container-->
     <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
        <!--begin::Page title-->
        <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
           <!--begin::Title-->
           <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Customers List</h1>
           <!--begin::Separator-->
           <!--end::Separator-->
           <!--end::Title-->
        </div>
        <!--end::Page title-->
        <!--begin::Actions-->
        <div class="d-flex align-items-center py-1">
           <!--begin::Wrapper-->
           <!--end::Wrapper-->
           <!--begin::Button-->
           <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_create_app" id="kt_toolbar_primary_button">Sync Customers</a>
           <!--end::Button-->
        </div>
        <!--end::Actions-->
     </div>
     <!--end::Container-->
  </div>
  <!--end::Toolbar-->
  <!--begin::Post-->
  <div class="post d-flex flex-column-fluid" id="kt_post">
     <!--begin::Container-->
     <div id="kt_content_container" class="container-xxl">
        <!--begin::Row-->
        <div class="row gy-5 g-xl-8">
           <!--begin::Col-->
           <div class="col-xl-12">
              <!--begin::Tables Widget 9-->
              <div class="card card-xl-stretch mb-5 mb-xl-8">
                 <!--begin::Header-->
                 <div class="card-header border-0 pt-5">
                    <div class="row w-100">
                       <div class="col-md-4">
                          
                          <input class="form-control form-control-lg form-control-solid" type="text" autocomplete="off" placeholder="search">
                       </div>
                       <div class="col-md-2">
                          <select class="form-select form-select-solid" data-control="select2" data-hide-search="true" data-placeholder="Filters" name="target_assign" tabindex="-1" aria-hidden="false">
                             <option value="">Filters</option>
                             <option value="1">demo</option>
                             <option value="2">demo</option>
                             <option value="3">demo</option>
                             <option value="4">demo</option>
                             <option value="5">demo</option>
                          </select>
                       </div>
                       <div class="col-md-2">
                          <select class="form-select form-select-solid" data-control="select2" data-hide-search="true" data-placeholder="Class" name="target_assign" tabindex="-1" aria-hidden="false">
                             <option value="">Filters</option>
                             <option value="1">demo</option>
                             <option value="2">demo</option>
                             <option value="3">demo</option>
                             <option value="4">demo</option>
                             <option value="5">demo</option>
                          </select>
                       </div>
                       <div class="col-md-2">
                          <select class="form-select form-select-solid" data-control="select2" data-hide-search="true" data-placeholder="Status" name="target_assign" tabindex="-1" aria-hidden="false">
                             <option value="">Filters</option>
                             <option value="1">demo</option>
                             <option value="2">demo</option>
                             <option value="3">demo</option>
                             <option value="4">demo</option>
                             <option value="5">demo</option>
                          </select>
                       </div>
                        <div class="col-md-2">
                          <select class="form-select form-select-solid" data-control="select2" data-hide-search="true" data-placeholder="30 per page" name="target_assign" tabindex="-1" aria-hidden="false">
                             <option value="">Filters</option>
                             <option value="1">demo</option>
                             <option value="2">demo</option>
                             <option value="3">demo</option>
                             <option value="4">demo</option>
                             <option value="5">demo</option>
                          </select>
                       </div>
                    </div>
                    
                    
                 </div>
                 <!--end::Header-->
                 <!--begin::Body-->
                 <div class="card-body py-3">
                    <!--begin::Table container-->
                    <div class="table-responsive">
                       <!--begin::Table-->
                       <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                          <!--begin::Table head-->
                          <thead>
                             <tr class="fw-bolder text-muted">
                                <th class="w-25px">
                                   <div class="form-check form-check-sm form-check-custom form-check-solid">
                                      <input class="form-check-input" type="checkbox" value="1" data-kt-check="true" data-kt-check-target=".widget-9-check">
                                   </div>
                                </th>
                                <th class="min-w-150px">Name</th>
                                <th class="min-w-140px">City</th>
                                <th class="min-w-120px">Date</th>
                                <th class="min-w-140px">Class</th>
                                <th class="min-w-140px">Status</th>
                                <th class="min-w-100px">Action</th>
                             </tr>
                          </thead>
                          <!--end::Table head-->
                          <!--begin::Table body-->
                          <tbody>

                            @foreach($data as $key => $value)
                              <tr>
                                <td>
                                   <div class="form-check form-check-sm form-check-custom form-check-solid">
                                      <input class="form-check-input widget-9-check" type="checkbox" value="1">
                                   </div>
                                </td>
                                <td>
                                   <div class="d-flex align-items-center">
                                      <div class="symbol symbol-45px me-5">
                                         <img src="{{ asset('assets') }}/assets/media/150-11.jpg" alt="">
                                      </div>
                                      <div class="d-flex justify-content-start flex-column">
                                         <a href="#" class="text-dark fw-bolder text-hover-primary fs-6">{{ @$value['ContactPerson'] ?? @$value['CardName'] ?? " " }}</a>
                                         <span class="text-muted fw-bold text-muted d-block fs-7">Code: {{ @$value['CardCode'] }} | Email: {{ @$value['EmailAddress'] }}</span>
                                      </div>
                                   </div>
                                </td>
                                <td>
                                  <span class="text-muted me-2 fs-7 fw-bold">{{ @$value['City'] }}</span>
                                </td>
                                <td class="text-end">
                                   <div class="d-flex flex-column w-100 me-2">
                                      <div class="d-flex flex-stack mb-2">
                                         <span class="text-muted me-2 fs-7 fw-bold">{{ @$value['CreateDate'] }}</span>
                                      </div>
                                   </div>
                                </td>
                                <td class="text-end">
                                   <div class="d-flex flex-column w-100 me-2">
                                      <div class="d-flex flex-stack mb-2">
                                         <span class="text-muted me-2 fs-7 fw-bold">Base</span>
                                      </div>
                                   </div>
                                </td>
                                <td class="text-end">
                                   <div class="d-flex flex-column w-100 me-2">
                                      <div class="d-flex flex-stack mb-2">
                                         <span class="text-muted me-2 fs-7 fw-bold"> {{ @$value['Valid'] == "tYES" ? "Active" : "Inactive" }}</span>
                                      </div>
                                   </div>
                                </td>
                                <td class="text-end">
                                  <div class="d-flex flex-shrink-0">
                                      <a href="#" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
                                         <!--begin::Svg Icon | path: icons/duotune/general/gen019.svg-->
                                         <span class="svg-icon svg-icon-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                               <path d="M17.5 11H6.5C4 11 2 9 2 6.5C2 4 4 2 6.5 2H17.5C20 2 22 4 22 6.5C22 9 20 11 17.5 11ZM15 6.5C15 7.9 16.1 9 17.5 9C18.9 9 20 7.9 20 6.5C20 5.1 18.9 4 17.5 4C16.1 4 15 5.1 15 6.5Z" fill="black"></path>
                                               <path opacity="0.3" d="M17.5 22H6.5C4 22 2 20 2 17.5C2 15 4 13 6.5 13H17.5C20 13 22 15 22 17.5C22 20 20 22 17.5 22ZM4 17.5C4 18.9 5.1 20 6.5 20C7.9 20 9 18.9 9 17.5C9 16.1 7.9 15 6.5 15C5.1 15 4 16.1 4 17.5Z" fill="black"></path>
                                            </svg>
                                         </span>
                                         <!--end::Svg Icon-->
                                      </a>
                                      <a href="#" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
                                         <!--begin::Svg Icon | path: icons/duotune/art/art005.svg-->
                                         <span class="svg-icon svg-icon-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                               <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="black"></path>
                                               <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="black"></path>
                                            </svg>
                                         </span>
                                         <!--end::Svg Icon-->
                                      </a>
                                      <a href="#" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm">
                                         <!--begin::Svg Icon | path: icons/duotune/general/gen027.svg-->
                                         <span class="svg-icon svg-icon-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                               <path d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="black"></path>
                                               <path opacity="0.5" d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z" fill="black"></path>
                                               <path opacity="0.5" d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="black"></path>
                                            </svg>
                                         </span>
                                         <!--end::Svg Icon-->
                                      </a>
                                   </div>
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
                 <!--begin::Body-->
              </div>
              <!--end::Tables Widget 9-->
           </div>
           <!--end::Col-->
        </div>
        <!--end::Row-->
        <!--begin::Row-->
        <div class="row gy-5 g-xl-8">
           <div class="col-xl-12">
              <div class="d-flex flex-stack flex-wrap pt-10">
                 <div class="fs-6 fw-bold text-gray-700">Showing 1 to 10 of 50 entries</div>
                 <!--begin::Pages-->
                 <ul class="pagination">
                    <li class="page-item previous">
                       <a href="#" class="page-link">
                       <i class="previous"></i>
                       </a>
                    </li>
                    <li class="page-item active">
                       <a href="#" class="page-link">1</a>
                    </li>
                    <li class="page-item">
                       <a href="#" class="page-link">2</a>
                    </li>
                    <li class="page-item">
                       <a href="#" class="page-link">3</a>
                    </li>
                    <li class="page-item">
                       <a href="#" class="page-link">4</a>
                    </li>
                    <li class="page-item">
                       <a href="#" class="page-link">5</a>
                    </li>
                    <li class="page-item">
                       <a href="#" class="page-link">6</a>
                    </li>
                    <li class="page-item next">
                       <a href="#" class="page-link">
                       <i class="next"></i>
                       </a>
                    </li>
                 </ul>
                 <!--end::Pages-->
              </div>
           </div>
        </div>
        <!--end::Row-->
     </div>
     <!--end::Container-->
  </div>
  <!--end::Post-->
</div>
<!--end::Content-->
@endsection