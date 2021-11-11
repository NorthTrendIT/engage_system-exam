<!DOCTYPE html>

<html lang="en">
	<!--begin::Head-->
	<head>
		<title>@yield('title') | B2M</title>
		<meta charset="utf-8" />
		<meta name="description" content="" />
		<meta name="keywords" content="" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<meta property="og:locale" content="en_US" />
		<meta property="og:type" content="article" />
		
		<link rel="shortcut icon" href="{{ asset('assets') }}/assets/media/logo.png" />

		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
		<link href="{{ asset('assets') }}/assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
		<link href="{{ asset('assets') }}/assets/css/style.bundle.css" rel="stylesheet" type="text/css" />

		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.css" />

		<style>
			.is-invalid{
				color:red;
			}
		</style>
		@stack('css')
	</head>
	<!--end::Head-->
	<!--begin::Body-->
	<body id="kt_body" class="bg-body">
		<!--begin::Main-->
		<div class="d-flex flex-column flex-root">
			<!--begin::Authentication - Sign-in -->
			<div class="d-flex flex-column flex-column-fluid bgi-position-y-bottom position-x-center bgi-no-repeat bgi-size-contain bgi-attachment-fixed" style="background-image: url({{ asset('assets') }}/assets/media/14.png">
				<!--begin::Content-->
				<div class="d-flex flex-center flex-column flex-column-fluid p-10 pb-lg-20">
					<!--begin::Logo-->
					<a href="#" class="mb-12">
						<img alt="Logo" src="{{ asset('assets') }}/assets/media/logo.png" class="h-40px" />
					</a>
					<!--end::Logo-->
					<!--begin::Wrapper-->
					@yield('content')
					<!--end::Wrapper-->
				</div>
				<!--end::Content-->
				
			</div>
			<!--end::Authentication - Sign-in-->
		</div>
		<!--end::Main-->
		<script src="{{ asset('assets') }}/assets/plugins/global/plugins.bundle.js"></script>
		<script src="{{ asset('assets') }}/assets/js/scripts.bundle.js"></script>

		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.js" ></script>

		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>

		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/additional-methods.min.js"></script>

		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-loading-overlay/2.1.7/loadingoverlay.min.js"></script>

		<script src="{{ asset('assets') }}/assets/js/common/common_function.js"></script>

		<script>
			
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
	</body>
	<!--end::Body-->
</html>