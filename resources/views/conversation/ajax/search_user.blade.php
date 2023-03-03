<!--begin::User-->
<div class="d-flex flex-stack py-4">
  <!--begin::Details-->
  <div class="d-flex align-items-center">
    <!--begin::Avatar-->
    <div class="symbol symbol-45px symbol-circle">
      @if($user->profile && get_valid_file_url('sitebucket/users',$user->profile))
        <img alt="user" src="{{ get_valid_file_url('sitebucket/users',$user->profile) }}" />
      @else
        <div class="symbol-label fs-3" style="color:{{ convert_hex_to_rgba($user->default_profile_color) }};background-color:{{ convert_hex_to_rgba($user->default_profile_color,0.5) }};"><b>{{ get_sort_char($user->sales_specialist_name) }}</b></div>
      @endif
      {{-- <div class="symbol-badge bg-success start-100 top-100 border-4 h-15px w-15px ms-n2 mt-n2"></div> --}}
    </div>
    <!--end::Avatar-->
    <!--begin::Details-->
    <div class="ms-5">
      <a href="javascript:" class="fs-5 fw-bolder text-gray-900 text-hover-primary mb-2 create_conversation" data-id="{{ $user->id }}" title="Send message" >{{ $user->sales_specialist_name ?? "" }}</a>
      <div class="fw-bold text-muted">{{ $user->email ?? "" }}</div>
    </div>
    <!--end::Details-->
  </div>
  <!--end::Details-->
  <!--begin::Lat seen-->
  <div class="d-flex flex-column align-items-end ms-2">
    <a href="javascript:" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm create_conversation" data-id="{{ $user->id }}" title="Send message">
      <i class="fa fa-paper-plane"></i>
    </a>
  </div>
  <!--end::Lat seen-->
</div>
<!--end::User-->
<!--begin::Separator-->
<div class="separator separator-dashed d-none"></div>
<!--end::Separator-->