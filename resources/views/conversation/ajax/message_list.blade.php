@if(@$message)
  
  @php
    $url = '~(?:(https?)://([^\s<]+)|(www\.[^\s<]+?\.[^\s<]+))(?<![\.,:])~i';
    $message_text = preg_replace($url, '<a href="$0" target="_blank" title="$0">$0</a>', $message->message ?? "-");
  @endphp

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
              <div class="symbol-label fs-3" style="color:{{ convert_hex_to_rgba($message->user->default_profile_color) }};background-color:{{ convert_hex_to_rgba($message->user->default_profile_color,0.5) }};"><b>{{ get_sort_char($message->user->sales_specialist_name) }}</b></div>
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
        <div class="p-5 rounded bg-light-info text-dark fw-bold mw-lg-400px text-start" data-kt-element="message-text">{!! $message_text !!}</div>
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
              <div class="symbol-label fs-3" style="color:{{ convert_hex_to_rgba(Auth::user()->default_profile_color) }};background-color:{{ convert_hex_to_rgba(Auth::user()->default_profile_color,0.5) }};"><b>{{ get_sort_char(Auth::user()->sales_specialist_name) }}</b></div>
            @endif
          </div>
          <!--end::Avatar-->
        </div>
        <!--end::User-->
        <!--begin::Text-->
        <div class="p-5 rounded bg-light-primary text-dark fw-bold mw-lg-400px text-end" data-kt-element="message-text">{!! $message_text !!}</div>
        <!--end::Text-->
      </div>
      <!--end::Wrapper-->
    </div>
    <!--end::Message(out)-->

  @endif
@endif