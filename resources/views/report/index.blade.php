@extends('layouts.master')

@section('title','Dashboard')

@section('content')
<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <!--begin::Toolbar-->
  <div class="toolbar" id="kt_toolbar">
     <!--begin::Container-->
     <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
        <!--begin::Page title-->
        <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
           <!--begin::Title-->
           <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">Hi {{ @Auth::user()->first_name ?? "" }} {{ @Auth::user()->last_name ?? "" }},
           <!--end::Title-->
        </div>
        <!--end::Page title-->
        <!--begin::Actions-->
        <div class="d-flex align-items-center py-1">
           <!--begin::Wrapper-->
           <div class="me-4">
              <!--begin::Menu-->
              <a href="#" class="btn btn-sm btn-flex btn-light btn-active-primary fw-bolder" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
              <!--begin::Svg Icon | path: icons/duotune/general/gen031.svg-->
              <span class="svg-icon svg-icon-5 svg-icon-gray-500 me-1">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                 <path fill-rule="evenodd" clip-rule="evenodd" d="M11 16.9656L11.3892 16.6653C11.8265 16.328 12.4544 16.409 12.7918 16.8463C13.1291 17.2836 13.0481 17.9115 12.6108 18.2489L10.6277 19.7787C10.5675 19.8274 10.5025 19.8682 10.4342 19.901C10.3808 19.928 10.309 19.9545 10.234 19.9725C10.1375 19.9951 10.0448 20.0033 9.95295 19.9989C9.81197 19.9924 9.6786 19.9567 9.55877 19.8976C9.4931 19.8654 9.43047 19.8257 9.37226 19.7787L7.38919 18.2489C6.9519 17.9115 6.87088 17.2836 7.20823 16.8463C7.54557 16.409 8.17353 16.328 8.61081 16.6653L9 16.9656V6.45711C9 5.90482 9.44772 5.45711 10 5.45711C10.5523 5.45711 11 5.90482 11 6.45711V16.9656ZM10.5 0C13.0609 0 15.2376 1.76105 15.8369 4.17236C18.2436 4.77356 20 6.95407 20 9.518C20 12.3327 17.8828 14.6868 15.1101 14.9939C14.5612 15.0547 14.0669 14.659 14.0061 14.1101C13.9453 13.5612 14.341 13.0669 14.8899 13.0061C16.6514 12.8109 18 11.3114 18 9.518C18 7.71741 16.6408 6.21401 14.8706 6.02783C14.4009 5.97843 14.0298 5.60718 13.9806 5.13748C13.7947 3.36183 12.2947 2 10.5 2C8.70372 2 7.20292 3.36415 7.01891 5.14171C6.96154 5.69596 6.46222 6.0964 5.90874 6.03205C5.77394 6.01638 5.63757 6.00847 5.5 6.00847C3.56748 6.00847 2 7.57926 2 9.518C2 11.3114 3.34862 12.8109 5.11011 13.0061C5.65903 13.0669 6.05473 13.5612 5.99392 14.1101C5.93311 14.659 5.43882 15.0547 4.88989 14.9939C2.11715 14.6868 0 12.3327 0 9.518C0 6.57497 2.30383 4.17018 5.20435 4.01629C5.85706 1.68561 7.99449 0 10.5 0Z" fill="#92929D"/>
                 </svg>

              </span>
              <!--end::Svg Icon--></a>
              <!--begin::Menu 1-->
              <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px" data-kt-menu="true" id="kt_menu_616838fb2edc9">
                 <!--begin::Header-->
                 <div class="px-7 py-5">
                    <div class="fs-5 text-dark fw-bolder">Filter Options</div>
                 </div>
                 <!--end::Header-->
                 <!--begin::Menu separator-->
                 <div class="separator border-gray-200"></div>
                 <!--end::Menu separator-->
                 <!--begin::Form-->
                 <div class="px-7 py-5">
                    <!--begin::Input group-->
                    <div class="mb-10">
                       <!--begin::Label-->
                       <label class="form-label fw-bold">Status:</label>
                       <!--end::Label-->
                       <!--begin::Input-->
                       <div>
                          <select class="form-select form-select-solid" data-kt-select2="true" data-placeholder="Select option" data-dropdown-parent="#kt_menu_616838fb2edc9" data-allow-clear="true">
                             <option></option>
                             <option value="1">Approved</option>
                             <option value="2">Pending</option>
                             <option value="2">In Process</option>
                             <option value="2">Rejected</option>
                          </select>
                       </div>
                       <!--end::Input-->
                    </div>
                    <!--end::Input group-->
                    <!--begin::Input group-->
                    <div class="mb-10">
                       <!--begin::Label-->
                       <label class="form-label fw-bold">Member Type:</label>
                       <!--end::Label-->
                       <!--begin::Options-->
                       <div class="d-flex">
                          <!--begin::Options-->
                          <label class="form-check form-check-sm form-check-custom form-check-solid me-5">
                             <input class="form-check-input" type="checkbox" value="1" />
                             <span class="form-check-label">Author</span>
                          </label>
                          <!--end::Options-->
                          <!--begin::Options-->
                          <label class="form-check form-check-sm form-check-custom form-check-solid">
                             <input class="form-check-input" type="checkbox" value="2" checked="checked" />
                             <span class="form-check-label">Customer</span>
                          </label>
                          <!--end::Options-->
                       </div>
                       <!--end::Options-->
                    </div>
                    <!--end::Input group-->
                    <!--begin::Input group-->
                    <div class="mb-10">
                       <!--begin::Label-->
                       <label class="form-label fw-bold">Notifications:</label>
                       <!--end::Label-->
                       <!--begin::Switch-->
                       <div class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                          <input class="form-check-input" type="checkbox" value="" name="notifications" checked="checked" />
                          <label class="form-check-label">Enabled</label>
                       </div>
                       <!--end::Switch-->
                    </div>
                    <!--end::Input group-->
                    <!--begin::Actions-->
                    <div class="d-flex justify-content-end">
                       <button type="reset" class="btn btn-sm btn-light btn-active-light-primary me-2" data-kt-menu-dismiss="true">Reset</button>
                       <button type="submit" class="btn btn-sm btn-primary" data-kt-menu-dismiss="true">Apply</button>
                    </div>
                    <!--end::Actions-->
                 </div>
                 <!--end::Form-->
              </div>
              <!--end::Menu 1-->
              <!--end::Menu-->
           </div>
           <!--end::Wrapper-->
           <!--begin::Button-->
           <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_create_app" id="kt_toolbar_primary_button">Create</a>
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
            <!-- <div class="col-xl-4"> -->
                <div class="card card-xl-stretch">
                    <div class="card-body p-0">
                        <div class="card-p">
                            <div class="row">
                                <div class="col-md-3 bg-light-warning px-6 py-8 rounded-2 me-7 mb-7 min-w-150">
                                    <span class="svg-icon svg-icon-3x svg-icon-warning d-block my-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <rect x="8" y="9" width="3" height="10" rx="1.5" fill="black"></rect>
                                        <rect opacity="0.5" x="13" y="5" width="3" height="14" rx="1.5" fill="black"></rect>
                                        <rect x="18" y="11" width="3" height="8" rx="1.5" fill="black"></rect>
                                        <rect x="3" y="13" width="3" height="6" rx="1.5" fill="black"></rect>
                                        </svg>
                                    </span>
                                    <a href="{{ route('promotion-report.index') }}" class="text-warning fw-bold fs-6">Promotions Report </a>
                                </div>
                                <!-- <div class="col-md-3 bg-light-primary px-6 py-8 rounded-2 me-7 mb-7 min-w-150">
                                    <span class="svg-icon svg-icon-3x svg-icon-primary d-block my-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path opacity="0.3" d="M20 15H4C2.9 15 2 14.1 2 13V7C2 6.4 2.4 6 3 6H21C21.6 6 22 6.4 22 7V13C22 14.1 21.1 15 20 15ZM13 12H11C10.5 12 10 12.4 10 13V16C10 16.5 10.4 17 11 17H13C13.6 17 14 16.6 14 16V13C14 12.4 13.6 12 13 12Z" fill="black"></path>
                                        <path d="M14 6V5H10V6H8V5C8 3.9 8.9 3 10 3H14C15.1 3 16 3.9 16 5V6H14ZM20 15H14V16C14 16.6 13.5 17 13 17H11C10.5 17 10 16.6 10 16V15H4C3.6 15 3.3 14.9 3 14.7V18C3 19.1 3.9 20 5 20H19C20.1 20 21 19.1 21 18V14.7C20.7 14.9 20.4 15 20 15Z" fill="black"></path>
                                        </svg>
                                    </span>
                                    <a href="#" class="text-primary fw-bold fs-6">New Projects</a>
                                </div>
                                <div class="col-md-3 bg-light-danger px-6 py-8 rounded-2 me-7 mb-7 min-w-150">
                                    <span class="svg-icon svg-icon-3x svg-icon-danger d-block my-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path opacity="0.3" d="M21.25 18.525L13.05 21.825C12.35 22.125 11.65 22.125 10.95 21.825L2.75 18.525C1.75 18.125 1.75 16.725 2.75 16.325L4.04999 15.825L10.25 18.325C10.85 18.525 11.45 18.625 12.05 18.625C12.65 18.625 13.25 18.525 13.85 18.325L20.05 15.825L21.35 16.325C22.35 16.725 22.35 18.125 21.25 18.525ZM13.05 16.425L21.25 13.125C22.25 12.725 22.25 11.325 21.25 10.925L13.05 7.62502C12.35 7.32502 11.65 7.32502 10.95 7.62502L2.75 10.925C1.75 11.325 1.75 12.725 2.75 13.125L10.95 16.425C11.65 16.725 12.45 16.725 13.05 16.425Z" fill="black"></path>
                                        <path d="M11.05 11.025L2.84998 7.725C1.84998 7.325 1.84998 5.925 2.84998 5.525L11.05 2.225C11.75 1.925 12.45 1.925 13.15 2.225L21.35 5.525C22.35 5.925 22.35 7.325 21.35 7.725L13.05 11.025C12.45 11.325 11.65 11.325 11.05 11.025Z" fill="black"></path>
                                        </svg>
                                    </span>
                                    <a href="#" class="text-danger fw-bold fs-6 mt-2">Item Orders</a>
                                </div>
                                <div class="col-md-3 bg-light-success px-6 py-8 rounded-2 me-7 mb-7 min-w-150">
                                    <span class="svg-icon svg-icon-3x svg-icon-success d-block my-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path d="M6 8.725C6 8.125 6.4 7.725 7 7.725H14L18 11.725V12.925L22 9.725L12.6 2.225C12.2 1.925 11.7 1.925 11.4 2.225L2 9.725L6 12.925V8.725Z" fill="black"></path>
                                        <path opacity="0.3" d="M22 9.72498V20.725C22 21.325 21.6 21.725 21 21.725H3C2.4 21.725 2 21.325 2 20.725V9.72498L11.4 17.225C11.8 17.525 12.3 17.525 12.6 17.225L22 9.72498ZM15 11.725H18L14 7.72498V10.725C14 11.325 14.4 11.725 15 11.725Z" fill="black"></path>
                                        </svg>
                                    </span>
                                    <a href="#" class="text-success fw-bold fs-6 mt-2">Bug Reports</a>
                                </div> -->
                            </div>
                        </div>
                    </div>
              </div>
            <!-- </div> -->
           <!--end::Col-->
        </div>
        <!--end::Row-->
     </div>
     <!--end::Container-->
  </div>
  <!--end::Post-->
</div>
<!--end::Content-->
@endsection

@push('js')
@if(@Auth::user()->role_id == 1)
<script>
    $(document).on('click', '.push-all-order', function(event) {
      event.preventDefault();

      Swal.fire({
        title: 'Are you sure want to push all pending orders?',
        //text: "Once deleted, you will not be able to recover this record!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, do it!'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: '{{ route('orders.push-all-order') }}',
            method: "POST",
            data: {
                    _token:'{{ csrf_token() }}',
                }
          })
          .done(function(result) {
            if(result.status == false){
              toast_error(result.message);
            }else{
              toast_success(result.message);
              setTimeout(function(){
                window.location.reload();
              },500)
            }
          })
          .fail(function() {
            toast_error("error");
          });
        }
      })
    });

    $(document).on('click', '.push-all-promotion', function(event) {
      event.preventDefault();
      var id = $(this).data('id');
      Swal.fire({
        title: 'Are you sure want to push all promotion?',
        //text: "Once deleted, you will not be able to recover this record!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, do it!'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: '{{ route('orders.push-all-promotion') }}',
            method: "POST",
            data: {
                    _token:'{{ csrf_token() }}',
                }
          })
          .done(function(result) {
            if(result.status == false){
              toast_error(result.message);
            }else{
              toast_success(result.message);
              setTimeout(function(){
                window.location.reload();
              },500)
            }
          })
          .fail(function() {
            toast_error("error");
          });
        }
      })
    });
</script>
@endif
@endpush
