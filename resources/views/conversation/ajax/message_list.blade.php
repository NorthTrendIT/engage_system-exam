@if(@$message)
  
  @if($message->user_id != userid())
    <!--begin::Message(in)-->
    <div class="d-flex justify-content-start mb-10 conversation_message_list_item" id="conversation_message_list_item{{ $message->id }}">
      <!--begin::Wrapper-->
      <div class="d-flex flex-column align-items-start">
        <!--begin::User-->
        <div class="d-flex align-items-center mb-2">
          <!--begin::Avatar-->
          <div class="symbol symbol-35px symbol-circle">
            @if($message->user->profile && get_valid_file_url('sitebucket/users',$message->user->profile))
              <img alt="user" src="{{ get_valid_file_url('sitebucket/users',$message->user->profile) }}" />
            @else
              <img src="{{ asset('assets') }}/assets/media/default_user.png" alt="user" />
            @endif
          </div>
          <!--end::Avatar-->
          <!--begin::Details-->
          <div class="ms-3">
            <span class="fs-5 fw-bolder text-gray-900 me-1">{{ $message->user->sales_specialist_name ?? "-" }}</span>
            <span class="text-muted fs-7 mb-1">{{ date_difference($message->created_at) }}</span>
          </div>
          <!--end::Details-->
        </div>
        <!--end::User-->
        <!--begin::Text-->
        <div class="p-5 rounded bg-light-info text-dark fw-bold mw-lg-400px text-start" data-kt-element="message-text">{{ $message->message ?? "-" }}</div>
        <!--end::Text-->
      </div>
      <!--end::Wrapper-->
    </div>
    <!--end::Message(in)-->

  @else

    <!--begin::Message(out)-->
    <div class="d-flex justify-content-end mb-10 conversation_message_list_item" id="conversation_message_list_item{{ $message->id }}">
      <!--begin::Wrapper-->
      <div class="d-flex flex-column align-items-end">
        <!--begin::User-->
        <div class="d-flex align-items-center mb-2">
          <!--begin::Details-->
          <div class="me-3">
            <span class="text-muted fs-7 mb-1">{{ date_difference($message->created_at) }}</span>
            <span class="fs-5 fw-bolder text-gray-900 ms-1">You</span>
          </div>
          <!--end::Details-->
          <!--begin::Avatar-->
          <div class="symbol symbol-35px symbol-circle">
            @if(get_login_user_profile())
              <img src="{{ get_login_user_profile() }}" alt="user" />
            @else
              <img src="{{ asset('assets') }}/assets/media/default_user.png" alt="user" />
            @endif
          </div>
          <!--end::Avatar-->
        </div>
        <!--end::User-->
        <!--begin::Text-->
        <div class="p-5 rounded bg-light-primary text-dark fw-bold mw-lg-400px text-end" data-kt-element="message-text">{{ $message->message ?? "-" }}</div>
        <!--end::Text-->
      </div>
      <!--end::Wrapper-->
    </div>
    <!--end::Message(out)-->

  @endif
@endif