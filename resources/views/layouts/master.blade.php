<!DOCTYPE html>
<html lang="en">
   <!--begin::Head-->
   <head>
      <title>@yield('title') | Engage Ordering Management System</title>
      <meta charset="utf-8" />
      <meta name="description" content="" />
      <meta name="keywords" content="M" />
      <meta name="viewport" content="width=device-width, initial-scale=1" />
      <meta property="og:locale" content="en_US" />
      <meta property="og:type" content="article" />
      <link rel="shortcut icon" href="{{ asset('assets') }}/assets/media/logo.png" />

      @include('layouts.include.head')
   </head>
   <!--end::Head-->
   <!--begin::Body-->
   <body id="kt_body" class="header-fixed header-tablet-and-mobile-fixed toolbar-enabled toolbar-fixed aside-enabled aside-fixed" style="--kt-toolbar-height:55px;--kt-toolbar-height-tablet-and-mobile:55px">
      <!--begin::Main-->
      <!--begin::Root-->
      <div class="d-flex flex-column flex-root">
         <!--begin::Page-->
         <div class="page d-flex flex-row flex-column-fluid">

            @include('layouts.include.sidebar')

            <!--begin::Wrapper-->
            <div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">

               @include('layouts.include.header')

               <!--begin::Content-->
                  @yield('content')
               <!--end::Content-->
               <!--begin::Footer-->
                  @include('layouts.include.footer')
               <!--end::Footer-->
            </div>
            <!--end::Wrapper-->
         </div>
         <!--end::Page-->
      </div>
      <!--end::Root-->

      <!--begin::Scrolltop-->
      <div id="kt_scrolltop" class="scrolltop" data-kt-scrolltop="true">
         <!--begin::Svg Icon | path: icons/duotune/arrows/arr066.svg-->
         <span class="svg-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
               <rect opacity="0.5" x="13" y="6" width="13" height="2" rx="1" transform="rotate(90 13 6)" fill="black" />
               <path d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z" fill="black" />
            </svg>
         </span>
         <!--end::Svg Icon-->
      </div>
      <!--end::Scrolltop-->
      <!--end::Main-->
      @include('layouts.include.script')
      <!-- OneSignalJS -->
      {{-- @include('layouts.include.one_signal') --}}
   </body>
   <!--end::Body-->
</html>
