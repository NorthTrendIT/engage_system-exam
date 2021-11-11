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

<script src="{{ asset('assets') }}/assets/js/common/common_function.js"></script>

@stack('js')