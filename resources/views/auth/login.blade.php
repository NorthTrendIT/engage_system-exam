@extends('layouts.auth')

@section('title','Login')

@section('content')

<!--begin::Wrapper-->
<div class="w-lg-500px bg-body rounded shadow-sm p-10 p-lg-15 mx-auto">
	<!--begin::Form-->
	<form class="form w-100 loginpage-form" id="myForm" method="post">
		@csrf
		<!--begin::Heading-->
		<div class="text-center mb-10">
			<!--begin::Title-->
			{{-- <h1 class="text-dark mb-3">Engage</h1> --}}
			<img src="{{ asset('assets') }}/assets/media/exceltrend-logo.png" class="img-fluid">
			<!--end::Title-->
			<!--begin::Link-->
			<!-- <div class="fw-bold fs-4">
				Ordering Management System
			</div> -->
			<!--end::Link-->
		</div>
		<!--begin::Heading-->
		<div class="fv-row mb-10">
			<label class="form-label fs-6 fw-bolder text-dark">Email</label>
			<input class="form-control form-control-lg form-control-solid" type="email" name="email" autocomplete="off" placeholder="Enter email" />
		</div>
		<div class="fv-row mb-10">
			<label class="form-label fs-6 fw-bolder text-dark">Password</label>
			<div class="input-group input-group-solid">
	            <input class="form-control form-control-lg form-control-solid" type="password" name="password" autocomplete="off" placeholder="********" />
	            <div class="input-group-append password_icon_div cursor-pointer pt-2">
	              <span class="input-group-text">
	                <i class="fas fa-eye-slash password_icon"></i>
	              </span>
	            </div>
	        </div>
			
		</div>
		<!--end::Input group-->
		<div class="fv-row mb-10 text-left">
			<a href="{{ route('forgot-password.index') }}" class="link-primary fs-6 fw-bolder forgot-link">Forgot Password ?</a>

			<a href="https://www.youtube.com/watch?v=Dr63PvezjN4" class="link-primary fs-6 fw-bolder login-link">View how to log-in <u>here</u></a>
		</div>
		<!--begin::Actions-->
		<div class="text-center">
			<!--begin::Submit button-->
			<button type="submit" class="btn btn-lg btn-primary w-100 mb-5">
				<span class="indicator-label">Log In</span>
			</button>
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
		            },
		            password: {
		                required: true,
		            },
		        },
		        messages: {
		            email:{
		            	required:"Please enter email.",
		            },
		            password:{
		            	required:"Please enter password.",
		            },
		        },
		    });
		    return validator;
		}

		$(document).on("submit", "#myForm", function (e) {
	        e.preventDefault();
	        var validator = validate_form();
	        if (validator.form() != false) {
	            $.ajax({
	                url: '{{ route('check-login') }}',
	                type: "POST",
	                data: new FormData($("#myForm")[0]),
	                processData: false,
	                contentType: false,
	                success: function (data) {
	                    if (data.status) {
	                        toast_success(data.message);
	                        setTimeout(function(){
		                        window.location.href = "{{route('home')}}"
		                    },500)
	                    } else {
	                        toast_error(data.message);
	                    }
	                },
	                error: function () {
	                    toast_error("Something went wrong !");
	                },
	            });
	        }
	    });

	});
</script>
@endpush

