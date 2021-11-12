<!--begin::Javascript-->
<!--begin::Global Javascript Bundle(used by all pages)-->
<script src="{{ asset('assets') }}/assets/plugins/global/plugins.bundle.js"></script>
<script src="{{ asset('assets') }}/assets/js/scripts.bundle.js"></script>
<!--end::Global Javascript Bundle-->
<!--begin::Page Custom Javascript(used by this page)-->
<script src="{{ asset('assets') }}/assets/js/custom/widgets.js"></script>
<!--end::Page Custom Javascript-->
<!--end::Javascript-->

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.js" ></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-loading-overlay/2.1.7/loadingoverlay.min.js"></script>

<script src="{{ asset('assets') }}/assets/js/common/common_function.js"></script>

<script>
	
	@if(Session::has('role_access_error_message'))
    toast_error("{{Session::get('role_access_error_message')}}")
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
	$(document).ajaxStart(function() {
	    show_loader();
	});
	
	$(document).ajaxStop(function() {
	  	hide_loader();
	});
		
</script>
@stack('js')