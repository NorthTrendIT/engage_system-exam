@extends('layouts.auth')

@section('title','Reset Password')

@section('content')

<!--begin::Wrapper-->
<div class="w-lg-500px bg-body rounded shadow-sm p-10 p-lg-15 mx-auto">
	<!--begin::Form-->
	<form class="form w-100" id="myForm" method="post">
		@csrf

		<input type="hidden" name="token" value="{{ $token }}">
    	<input type="hidden" name="email" value="{{ $email }}">

		<!--begin::Heading-->
		<div class="text-center mb-10">
			<!--begin::Title-->
			<h1 class="text-dark mb-3">Reset Password !</h1>
			<!--end::Title-->
		</div>
		<!--begin::Heading-->
		<div class="fv-row mb-10">
			<label class="form-label fs-6 fw-bolder text-dark">Email</label>
			<input class="form-control form-control-lg form-control-solid" type="email" name="email" autocomplete="off" placeholder="Enter your registered email address" disabled="" value="{{ $email }}"/>
		</div>

		<div class="fv-row mb-10">
			<label class="form-label fs-6 fw-bolder text-dark">New Password</label>
			<input type="password" class="form-control form-control-lg form-control-solid" name="new_password" tabindex="1" placeholder="********" autocomplete="new-password"/> 
		</div>

		<div class="fv-row mb-10">
			<label class="form-label fs-6 fw-bolder text-dark">Confirm Password</label>
			<input type="password" class="form-control form-control-lg form-control-solid" name="confirm_password" tabindex="1" placeholder="********" autocomplete="new-password"/>
		</div>

		<!--begin::Actions-->
		<div class="text-center">
			<!--begin::Submit button-->
			<button type="submit" class="btn btn-lg btn-primary w-100 mb-5">
				<span class="indicator-label">Reset Password</span>
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
	                new_password:{
	                    required: true,
	                    maxlength: 52,
	                    minlength:8,
	                },
	                confirm_password:{
	                    required: true,
	                    equalTo: '#myForm input[name="new_password"]',
	                    maxlength: 52,
	                    minlength:8,
	                },
	            },
	            messages: {
	                new_password:{
	                    required: "Please enter your new password.",
	                    maxlength: "Password is too long.",
	                },
	                confirm_password:{
	                    required: "Please enter your confirm password.",
	                    equalTo: "Please enter same password.",
	                    maxlength: "Password is too long.",
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
	                url: '{{ route('forgot-password.reset-password') }}',
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

