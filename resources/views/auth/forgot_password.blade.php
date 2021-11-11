@extends('layouts.auth')

@section('title','Forgot Password')

@section('content')

<!--begin::Wrapper-->
<div class="w-lg-500px bg-body rounded shadow-sm p-10 p-lg-15 mx-auto">
	<!--begin::Form-->
	<form class="form w-100" id="myForm" method="post">
		@csrf
		<!--begin::Heading-->
		<div class="text-center mb-10">
			<!--begin::Title-->
			<h1 class="text-dark mb-3">Forgot Password !</h1>
			<!--end::Title-->
		</div>
		<!--begin::Heading-->
		<div class="fv-row mb-10">
			<label class="form-label fs-6 fw-bolder text-dark">Email</label>
			<input class="form-control form-control-lg form-control-solid" type="email" name="email" autocomplete="off" placeholder="Enter your registered email address" />
		</div>
		<!--begin::Actions-->
		<div class="text-center">
			<!--begin::Submit button-->
			<button type="submit" class="btn btn-lg btn-primary w-100 mb-5">
				<span class="indicator-label">Send Password Reset Link</span>
			</button>

			<div class="text-center text-muted text-uppercase fw-bolder mb-5">or</div>

			<div class="text-gray-400 fw-bold fs-4">
				<a href="{{ route('login') }}" class="link-primary fw-bolder">Log In ?</a>
			</div>
		</div>
		<!--end::Actions-->
	</form>
	<!--end::Form-->
</div>
<!--end::Wrapper-->

@endsection

@push('js')
<script>
	$(document).ready(function () {

		function validate_form() {
		    var validator = $("#myForm").validate({
		        errorClass: "is-invalid",
		        validClass: "is-valid",
		        rules: {
		            email: {
		                required: true,
		                email: true,
		            }
		        },
		        messages: {
		            email:{
		            	required:"Please enter email.",
		            },
		        },
		    });
		    return validator;
		}

		$(document).on("submit", "#myForm", function (e) {
	        e.preventDefault();
	        var validator = validate_form();
	        if (validator.form() != false) {
	        	$('[type="submit"]').prop('disabled', true);
	            $.ajax({
	                url: '{{ route('forgot-password.email') }}',
	                type: "POST",
	                data: new FormData($("#myForm")[0]),
	                processData: false,
	                contentType: false,
	                success: function (data) {
	                    if (data.status) {
	                        toast_success(data.message);
	                        setTimeout(function(){
		                        window.location.href = "{{route('login')}}"
		                    },1500)
	                    } else {
	                        toast_error(data.message);
	                        $('[type="submit"]').prop('disabled', false);
	                    }
	                },
	                error: function () {
	                    toast_error("Something went wrong !");
	                    $('[type="submit"]').prop('disabled', false);
	                },
	            });
	        }
	    });

	});
</script>
@endpush

