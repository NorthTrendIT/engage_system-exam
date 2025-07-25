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
              <img alt="user" src="{{ get_valid_file_url('sitebucket/users',$user->profile) }}" />
            @else
              <div class="symbol-label fs-3" style="color:{{ convert_hex_to_rgba($user->default_profile_color) }};background-color:{{ convert_hex_to_rgba($user->default_profile_color,0.5) }};"><b>{{ get_sort_char($user->sales_specialist_name) }}</b></div>
            @endif
            {{-- <div class="symbol-badge bg-success start-100 top-100 border-4 h-15px w-15px ms-n2 mt-n2"></div> --}}
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

        @php
          $unread_message_count = @$key->messages()->where('user_id','!=',userid())->where('is_read',false)->count();
        @endphp

        @if($unread_message_count > 0)
        <span class="badge badge-sm badge-circle badge-light-success unread_message_count">{{ $unread_message_count  }}</span>
        @endif

      </div>
      <!--end::Lat seen-->
   </div>
   <!--end::User-->
    <!--begin::Separator-->
    <div class="separator separator-dashed d-none"></div>
    <!--end::Separator-->
@endforeach