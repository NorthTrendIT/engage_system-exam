@extends('layouts.master')

@section('title','Conversation')

@section('content')


@push('css')
  <style>
    .conversation_list.active{
      background-color: #f1f1f1;
    }
  </style>
@endpush

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Conversation</h1>
      </div>

      <!--begin::Actions-->
      <div class="d-flex align-items-center py-1">
        <!--begin::Button-->
        <a href="{{ route('conversation.create') }}" class="btn btn-sm btn-primary sync-products">New Conversation</a>
        <!--end::Button-->
      </div>
      <!--end::Actions-->
      
    </div>
  </div>
  
  
  <div class="post d-flex flex-column-fluid" id="kt_post">
    <!--begin::Container-->
    <div id="kt_content_container" class="container-xxl">
      <!--begin::Layout-->
      <div class="d-flex flex-column flex-lg-row">
        <!--begin::Sidebar-->
        <div class="flex-column flex-lg-row-auto w-100 w-lg-300px w-xl-400px mb-10 mb-lg-0">
          <!--begin::Contacts-->
          <div class="card card-flush">
            <!--begin::Card header-->
            <div class="card-header pt-7" id="kt_chat_contacts_header">
              <!--begin::Form-->
              <form class="w-100 position-relative" autocomplete="off">
                <!--begin::Icon-->
                <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                <span class="svg-icon svg-icon-2 svg-icon-lg-1 svg-icon-gray-500 position-absolute top-50 ms-5 translate-middle-y">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                    <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                  </svg>
                </span>
                <!--end::Svg Icon-->
                <!--end::Icon-->
                <!--begin::Input-->
                <input type="text" class="form-control form-control-solid px-15" id="conversation_search" name="conversation_search" value="" placeholder="Search by name ..." />
                <!--end::Input-->
              </form>
              <!--end::Form-->
            </div>
            <!--end::Card header-->
            <!--begin::Card body-->
            <div class="card-body pt-5" id="kt_chat_contacts_body">
              <!--begin::List-->
              <div class="scroll-y me-n5 pe-5 h-lg-auto" id="conversation_list_div" style="height: 320px !important;">

                
                
              </div>
              <!--end::List-->
            </div>
            <!--end::Card body-->
          </div>
          <!--end::Contacts-->
        </div>
        <!--end::Sidebar-->
        <!--begin::Content-->
        <div class="flex-lg-row-fluid ms-lg-7 ms-xl-10">
          <!--begin::Messenger-->
          <div class="card" id="conversation_message_div" style="display:none;">
            <!--begin::Card header-->
            <div class="card-header" id="kt_chat_messenger_header">
              <!--begin::Title-->
              <div class="card-title">
                <!--begin::Avatar-->
                {{-- <div class="symbol symbol-45px symbol-circle mr-10">
                  <img src="{{ asset('assets') }}/assets/media/default_user.png" alt="user" />
                </div> --}}
                <!--begin::User-->
                <div class="d-flex justify-content-center flex-column me-3">
                  <span href="javascript:" class="fs-4 fw-bolder text-gray-900 me-1 mb-2 lh-1 conversation_user_name">-</span>
                  <!--begin::Info-->
                  <div class="mb-0 lh-1 conversation_user_status" data-value="active" style="display: none;">
                    <span class="badge badge-success badge-circle w-10px h-10px me-1"></span>
                    <span class="fs-7 fw-bold text-muted">Active</span>
                  </div>
                  <div class="mb-0 lh-1 conversation_user_status" data-value="inactive" style="display: none;">
                    <span class="badge badge-danger badge-circle w-10px h-10px me-1"></span>
                    <span class="fs-7 fw-bold text-muted">Inactive</span>
                  </div>
                  <!--end::Info-->
                </div>
                <!--end::User-->
              </div>
              <!--end::Title-->
              <!--begin::Card toolbar-->
              <div class="card-toolbar">
                <!--begin::Menu-->
                <div class="me-n3">
                  {{-- <button class="btn btn-sm btn-icon btn-active-light-primary conversation_delete" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" style="display: none;">
                    <i class="fa fa-trash" style="font-size: 20px;"></i>
                  </button> --}}
                  
                </div>
                <!--end::Menu-->
              </div>
              <!--end::Card toolbar-->
            </div>
            <!--end::Card header-->
            <!--begin::Card body-->
            <div class="card-body">
              <!--begin::Messages-->

              <div id="view_more_message_div">
                
              </div>

              <div class="scroll-y me-n5 pe-5 h-lg-auto"  id="conversation_message_list_div" style="height: 280px!important;">
              

                
              </div>
              <!--end::Messages-->
            </div>
            <!--end::Card body-->
            <!--begin::Card footer-->
            <div class="card-footer pt-4" id="kt_chat_messenger_footer">
              
              <div class="">
                <textarea class="form-control form-control-flush mb-3 message" rows="1" data-kt-element="input" placeholder="Type a message"></textarea>
              </div>
              
              <button class="btn btn-primary send_message_btn" type="button" data-kt-element="send">Send</button>
            </div>
            <!--end::Card footer-->
          </div>
          <!--end::Messenger-->
        </div>
        <!--end::Content-->
      </div>
      <!--end::Layout-->
      
    </div>
    <!--end::Container-->
  </div>



</div>
@endsection


@push('js')
<script>
  
  $(document).ready(function() {

    var active_conversation_id = "";
    var active_user_id = "";

    // get_conversation_list();

    @if(isset($_GET['id']))
      get_conversation_list({{$_GET['id']}});
      get_conversation_messages({{$_GET['id']}});
    @else
      get_conversation_list();
    @endif

    $(document).on('change keyup', '#conversation_search', function(event) {
      event.preventDefault();
      get_conversation_list();
    });


    $(document).on('click', '.conversation_list', function(event) {
      event.preventDefault();
      var id = $(this).data('id');
      get_conversation_messages(id);
    });


    $(document).on('click', '.view_more_message', function(event) {
      event.preventDefault();
      var id = $(this).attr('data-id');
      get_conversation_messages(active_conversation_id, id);
    });


    $(document).on('click', '.conversation_delete', function(event) {
      event.preventDefault();

      url = '{{ route('conversation.destroy',[':id']) }}';
      id = $(this).attr('data-id');

      url = url.replace(':id', id);

      Swal.fire({
        title: 'Are you sure want to delete conversation?',
        //text: "Once deleted, you will not be able to recover this record!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, do it!'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: url,
            method: "DELETE",
            data: {
                    _token:'{{ csrf_token() }}'
                  }
          })
          .done(function(result) {
            if(result.status == false){
              toast_error(result.message);
            }else{
              reset_conversation();
              toast_success(result.message);
            }
          })
          .fail(function() {
            toast_error("error");
          });
        }
      })
    });

    $(document).on('click', '.send_message_btn', function(event) {
      event.preventDefault();

      show_loader();

      var $message = $('.message').val();

      if($message == "" && active_conversation_id && active_user_id){
        toast_error("Please enter message text");
      }else{

        var $send = {
                      'to': active_user_id, 
                      'conversation_id': active_conversation_id, 
                      'user_id': '{{ userid() }}', 
                      'message': $message, 
                    };

        store_message_response = storeMessage($send);          
        if(store_message_response){
          store_message_response = JSON.parse(store_message_response);

          $('.message').val("");

          $('#conversation_message_list_div').append(store_message_response.html);

          $("#conversation_message_list_div").scrollTop(9999999);
          
          get_conversation_list(active_conversation_id);

          hide_loader();

          socket.emit('sendMessage',$send);

        }
      }

    });



    // Get User Active or Not Response
    socket.on('receiveIsActive', data => {
      $('.conversation_user_status').hide();
      if(data.is_active){
        $('.conversation_user_status[data-value="active"]').show();
      }else{
        $('.conversation_user_status[data-value="inactive"]').show();
      }
    });


    // Receive Message Response
    socket.on('receiveMessage', data => {
      if(data.conversation_id == active_conversation_id){
        update_message_response = updateMessage(active_conversation_id);          
        if(update_message_response){
          update_message_response = JSON.parse(update_message_response);

          $('#conversation_message_list_div').append(update_message_response.html);

          $("#conversation_message_list_div").scrollTop(9999999);

          get_conversation_list(active_conversation_id);
        }
        
      }else{
        get_conversation_list(active_conversation_id);
      }
    });




    function get_conversation_list(conversation_id = "") {
      $('#conversation_list_div').html("");

      $search = $('#conversation_search').val();

      $.ajax({
        url: "{{route('conversation.get-conversation-list')}}",
        type: "POST",
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        data:{
          search : $search,
        },
        async: false,
        success: function (data) {
          if (data.html) {
            $('#conversation_list_div').html(data.html);

            if(conversation_id){
              $('.conversation_list[data-id="'+conversation_id+'"]').trigger('click');
            }

          }
        },
        error: function () {
          toast_error("Something went to wrong !");
        },
      });
    }

    // Store Message
    function storeMessage(insert_data){
      var result = $.ajax({
                    url: '{{ route("conversation.store-message") }}',
                    type: 'POST',
                    data: insert_data,
                    async : false,
                    headers: {
                      'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(data) {
                      if (data.status == false) {
                        toast_error("Something went to wrong !");
                        return false;
                      }else{
                        return data;
                      }
                    },
                    error: function () {
                      toast_error("Something went to wrong !");
                      return false;
                    }
                  }).responseText;
      return result;
    }

    // Update Message
    function updateMessage(conversation_id){
      var result = $.ajax({
                    url: '{{ route("conversation.update-message") }}',
                    type: 'POST',
                    data: {
                      conversation_id: conversation_id
                    },
                    async : false,
                    headers: {
                      'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(data) {
                      if (data.status == false) {
                        toast_error("Something went to wrong !");
                        return false;
                      }else{
                        return data;
                      }
                    },
                    error: function () {
                      toast_error("Something went to wrong !");
                      return false;
                    }
                  }).responseText;
      return result;
    }

    function get_conversation_messages(conversation_id, id = ""){
      $('.conversation_user_status').hide();
      $('.conversation_list').removeClass('active');
      $('.conversation_list[data-id="'+conversation_id+'"]').addClass('active');
      $('.conversation_list[data-id="'+conversation_id+'"]').find('.unread_message_count').remove();
      $('.conversation_delete').removeAttr('data-id');
      $('#conversation_message_div').show();

      active_conversation_id = conversation_id;

      $('.view_more_message').remove();
      if(id == ""){
        $('#conversation_message_list_div').html("");
      }

      $.ajax({
        url: "{{route('conversation.get-conversation-message-list')}}",
        type: "POST",
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        data:{
          conversation_id : conversation_id,
          id : id,
        },
        async: false,
        success: function (data) {

          $('#conversation_message_list_div').prepend(data.html);

          if(id == ""){
            $("#conversation_message_list_div").scrollTop(9999999);
          }else{
            $('#conversation_message_list_div').scrollTop($("#conversation_message_list_item"+id).position().top);
          }

          if (data.button) {
            $('.conversation_message_list_item:eq(0)').before(data.button);
            // $('#view_more_message_div').html(data.button);
          }

          if(data.user){
            active_user_id = data.user.id;

            $('.conversation_delete').attr('data-id', conversation_id);
            $('.conversation_user_name').text(data.user.sales_specialist_name);
            $('.conversation_delete').show();

            // Sent user id to socket for check user status
            socket.emit('sendIsActive', active_user_id);
          }

        },
        error: function () {
          toast_error("Something went to wrong !");
        }
      });
    }
    
    function reset_conversation(){
      active_conversation_id = "";
      $('.conversation_list').removeClass('active');
      $('.conversation_delete').removeAttr('data-id');
      $('#view_more_message_div').html("");
      $('#conversation_message_list_div').html("");
      $('#conversation_message_div').hide();
      get_conversation_list();
    }

  });

</script>
@endpush