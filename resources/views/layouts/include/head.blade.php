<!--begin::Fonts-->
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
<!--end::Fonts-->
<!--begin::Page Vendor Stylesheets(used by this page)-->
<link href="{{ asset('assets') }}/assets/plugins/custom/fullcalendar/fullcalendar.bundle.css" rel="stylesheet" type="text/css" />
<!--end::Page Vendor Stylesheets-->
<!--begin::Global Stylesheets Bundle(used by all pages)-->
<link href="{{ asset('assets') }}/assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets') }}/assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets') }}/assets/css/custom.css" rel="stylesheet" type="text/css" />
<!--end::Global Stylesheets Bundle-->

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css" />

<link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" />

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.css" />

<style>
	.is-invalid, .asterisk{
		color:red;
	}

	.mr-10{
		margin-right: 10px;
	}

	.dropify-message .span .p::before{
    	font-size: 20px !important;
	}

	.orgchart .node {
	    width: 160px !important;
	}
</style>

@stack('css')
