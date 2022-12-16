<!--begin::Javascript-->
<!--begin::Global Javascript Bundle(used by all pages)-->
<script src="{{ asset('assets') }}/assets/plugins/global/plugins.bundle.js"></script>
<script src="{{ asset('assets') }}/assets/js/scripts.bundle.js"></script>
<!--end::Global Javascript Bundle-->
<!--begin::Page Custom Javascript(used by this page)-->
<script src="{{ asset('assets') }}/assets/js/custom/widgets.js"></script>
<!--end::Page Custom Javascript-->
<!--end::Javascript-->

<script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.js" ></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-loading-overlay/2.1.7/loadingoverlay.min.js"></script>

<script src="{{ asset('assets') }}/assets/js/common/common_function.js"></script>

<script>

	$(".fancybox").fancybox();

	@if(Session::has('role_access_error_message'))
  toast_error("{{Session::get('role_access_error_message')}}")
  @endif

  @if(Session::has('error_message'))
  toast_error("{{Session::get('error_message')}}")
  @endif

  @if(Session::has('login_success_message'))
  toast_success("{{Session::get('login_success_message')}}")
  @endif

	function show_loader() {
		$.LoadingOverlay("show",{
		    // image       : "{{ asset('assets/logo_icon.png') }}",
		    // imageAnimation : "1500ms rotate_right"
		});
	}
	function hide_loader() {
		$.LoadingOverlay("hide",true);
	}

  function number_format(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  }

	@if(!in_array(Route::currentRouteName(), ['home', 'conversation.index','cart.index','sales-specialist-orders.create']))
    
		$(document).ajaxStart(function() {
		  show_loader();
		});

		// $(document).ajaxStop(function() {
		//   hide_loader();
		// });

    $(document).ajaxComplete(function() {
      hide_loader();
    });
	@endif


	$('#kt_daterangepicker_1').daterangepicker({
    autoUpdateInput: false,
    locale: {
      cancelLabel: 'Clear'
    }
  });

  $('#kt_daterangepicker_1').on('apply.daterangepicker', function(ev, picker) {
    $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
  });

  $('#kt_daterangepicker_1').on('cancel.daterangepicker', function(ev, picker) {
    $(this).val('');
  });


  $(document).on('click', '.password_generate', function(event) {
    event.preventDefault();

    password = generate_password();

    $('[name="password"]').val(password);
    $('[name="new_password"]').val(password);
    $('[name="confirm_password"]').val(password);

    // Create a "hidden" input
    var aux = document.createElement("input");
    // Assign it the value of the specified element
    aux.setAttribute("value", password);
    // Append it to the body
    document.body.appendChild(aux);
    // Highlight its content
    aux.select();
    // Copy the highlighted text
    document.execCommand("copy");
    // Remove it from the body
    document.body.removeChild(aux);
    toast_success("Generated password copied successfully !");
  });

  $(document).on('click', '.password_icon_div', function(event) {
    event.preventDefault();
    element = $(this).prev();
    children = $(this).children();

    if(element.attr('type') == "text"){
      element.attr('type', 'password');
      children.find('.password_icon').addClass('fa-eye-slash');
      children.find('.password_icon').removeClass('fa-eye');
    }else{
      element.attr('type', 'text');
      children.find('.password_icon').removeClass('fa-eye-slash');
      children.find('.password_icon').addClass('fa-eye');
    }
  });

</script>

@stack('js')

{{-- Socket Chat --}}
@if(!in_array(request()->getHttpHost(),['localhost']))
<!-- <script src="http://205.134.254.135:3031/socket.io/socket.io.js"></script> -->
<script src="http://20.127.228.253/socket.io/socket.io.js"></script>
@else
<script src="http://{{ request()->getHttpHost() }}:3031/socket.io/socket.io.js"></script>
@endif

<script>
	@if(!in_array(request()->getHttpHost(),['localhost']))
	/*const socket = io('http://205.134.254.135:3031')*/
  const socket = io('http://20.127.228.253:3031')
	@else
	const socket = io('http://{{ request()->getHttpHost() }}:3031')
	@endif

	// Add User
	socket.emit('adduser','{{ userid() }}')

	@if(!in_array( Route::currentRouteName(), ['conversation.index']))

		@if(get_login_user_un_read_message_count() > 0)
			$('.new-message').show();
		@endif

		// Receive Message
		socket.on('receiveMessage', data => {
			if(data.to == '{{ userid() }}'){
				toast_success("You have received one new message !");

        var count = parseInt($('.new-message').text()) + 1;
        $('.new-message').text(count);
				$('.new-message').show();
			}

		})
	@else
		$('.new-message').hide();
	@endif

</script>
