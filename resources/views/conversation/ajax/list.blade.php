@foreach(@$data as $key)
   @php
      if($key->sender_id == userid()){
         $user = $key->receiver;
      }else{
         $user = $key->sender;
      }
   @endphp

   <!--begin::User-->
   <div class="d-flex flex-stack py-4 conversation_list" style="cursor: pointer;padding: 10px;" data-id="{{ $key->id }}">
      <!--begin::Details-->
      <div class="d-flex align-items-center">
        <!--begin::Avatar-->
         <div class="symbol symbol-45px symbol-circle">
            @if($user->profile && get_valid_file_url('sitebucket/users',$user->profile))
              <a href="{{ get_valid_file_url('sitebucket/users',$user->profile) }}" class="fancybox">
                <img alt="user" src="{{ get_valid_file_url('sitebucket/users',$user->profile) }}" />
              </a>
            @else
              <img src="{{ asset('assets') }}/assets/media/default_user.png" alt="user" />
            @endif
            <div class="symbol-badge bg-success start-100 top-100 border-4 h-15px w-15px ms-n2 mt-n2"></div>
        </div>
        <!--end::Avatar-->
        <!--begin::Details-->
        <div class="ms-5">
          <span class="fs-5 fw-bolder text-gray-900 mb-2">{{ $user->sales_specialist_name ?? "" }}</span>
          <div class="fw-bold text-muted">{{ @$key->messages->last()->message ?? "-" }}</div>
        </div>
        <!--end::Details-->
      </div>
      <!--end::Details-->
      <!--begin::Lat seen-->
      <div class="d-flex flex-column align-items-end ms-2">
        <span class="text-muted fs-7 mb-1">{{ date_difference($key->updated_at) }}</span>
        {{-- <span class="badge badge-sm badge-circle badge-light-success">2</span> --}}
      </div>
      <!--end::Lat seen-->
   </div>
   <!--end::User-->
    <!--begin::Separator-->
    <div class="separator separator-dashed d-none"></div>
    <!--end::Separator-->
@endforeach