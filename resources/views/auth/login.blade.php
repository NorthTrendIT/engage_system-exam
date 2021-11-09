@extends('layouts.auth')

@section('title','Login')

@section('content')

<!--begin::Wrapper-->
<div class="w-lg-500px bg-body rounded shadow-sm p-10 p-lg-15 mx-auto">
	<!--begin::Form-->
	<form class="form w-100" id="myForm" method="post">
		@csrf
		<!--begin::Heading-->
		<div class="text-center mb-10">
			<!--begin::Title-->
			<h1 class="text-dark mb-3">Log In to B2B CRM</h1>
			<!--end::Title-->
			<!--begin::Link-->
			<div class="text-gray-400 fw-bold fs-4">
				Customer Order Managment
			</div>
			<!--end::Link-->
		</div>
		<!--begin::Heading-->
		<div class="fv-row mb-10">
			<label class="form-label fs-6 fw-bolder text-dark">Email</label>
			<input class="form-control form-control-lg form-control-solid" type="email" name="email" autocomplete="off" />
		</div>
		<div class="fv-row mb-10">
			<div class="d-flex flex-stack mb-2">
				<label class="form-label fw-bolder text-dark fs-6 mb-0">Password</label>
				<a href="{{ route('forgot-password.index') }}" class="link-primary fs-6 fw-bolder">Forgot Password ?</a>
			</div>
			<input class="form-control form-control-lg form-control-solid" type="password" name="password" autocomplete="off" />
		</div>
		<!--end::Input group-->
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

