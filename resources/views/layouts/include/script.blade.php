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
		$.LoadingOverlay("show", {
    // image       : "{{ asset('assets/logo_icon.png') }}",
    // imageAnimation : "1500ms rotate_right",
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

  $(document).ready(function() {
    @if (request()->is('sales-specialist-orders/create') || request()->is('cart'))
      if (localStorage.getItem('page_refresh_count')) { 
          // Get the current count, increment it, and update localStorage 
          var count = parseInt(localStorage.getItem('page_refresh_count'));
          count++;
          localStorage.setItem('page_refresh_count', count); 
      } else { 
          // Initialize the count to 1 if it doesn't exist 
          var count = 1; 
          localStorage.setItem('page_refresh_count', count); 
      }

      if(localStorage.getItem('page_refresh_count') == 1){
        show_loader();
        $.ajax({
          url: "{{route('sap-connection.test',$api_conn->id)}}",
          method: "GET",
        })
        .done(function(result) {
            hide_loader();
            if(result.status){
              localStorage.setItem('api_error', 0);
            }else{
              localStorage.setItem('api_error', 1);
              show_check_api_error();
            }
        })
        .fail(function() {
          hide_loader();
          toast_error("error");
        });

      } else { // refreshed multiple times

        if (localStorage.getItem('api_error') == 1) {
          show_check_api_error();
        }

      }
    @else
          localStorage.setItem('page_refresh_count', 0); 
    @endif
    // Display the current count 
    // $('#refreshCount').text(localStorage.getItem('page_refresh_count'));

    function show_check_api_error(){
      Swal.fire({
          title: 'SAP Server Maintenance.',
          text: 'You cannot place order at the moment, Please try again later.',
          icon: 'warning',
          showCancelButton: false,
          confirmButtonColor: '#3085d6',
          confirmButtonText: '<a href="{{ route('home') }}" style="color:white;">Go to Dashboard</a>',
          allowOutsideClick: false,
          allowEscapeKey: false,
          allowEnterKey: false,
          showCloseButton: false
      });
    }
});

</script>

@stack('js')


