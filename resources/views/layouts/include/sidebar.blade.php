@php
   $access = get_user_role_module_access(Auth::user()->role_id);
@endphp

<!--begin::Aside-->
<div id="kt_aside" class="aside aside-dark aside-hoverable" data-kt-drawer="true" data-kt-drawer-name="aside" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'200px', '300px': '250px'}" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_aside_mobile_toggle">
   <!--begin::Brand-->
   <div class="aside-logo flex-column-auto" id="kt_aside_logo">
      <!--begin::Logo-->
      <a href="{{ route('home') }}">
      <img alt="Logo" src="{{ asset('assets') }}/assets/media/logo-full.png" class="h-25px logo">
      </a>
      <!--end::Logo-->
      <!--begin::Aside toggler-->
      <div id="kt_aside_toggle" class="btn btn-icon w-auto px-0 btn-active-color-primary aside-toggle active" data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body" data-kt-toggle-name="aside-minimize">
         <!--begin::Svg Icon | path: icons/duotune/arrows/arr079.svg-->
         <span class="svg-icon svg-icon-1 rotate-180">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
               <path opacity="0.5" d="M14.2657 11.4343L18.45 7.25C18.8642 6.83579 18.8642 6.16421 18.45 5.75C18.0358 5.33579 17.3642 5.33579 16.95 5.75L11.4071 11.2929C11.0166 11.6834 11.0166 12.3166 11.4071 12.7071L16.95 18.25C17.3642 18.6642 18.0358 18.6642 18.45 18.25C18.8642 17.8358 18.8642 17.1642 18.45 16.75L14.2657 12.5657C13.9533 12.2533 13.9533 11.7467 14.2657 11.4343Z" fill="black"></path>
               <path d="M8.2657 11.4343L12.45 7.25C12.8642 6.83579 12.8642 6.16421 12.45 5.75C12.0358 5.33579 11.3642 5.33579 10.95 5.75L5.40712 11.2929C5.01659 11.6834 5.01659 12.3166 5.40712 12.7071L10.95 18.25C11.3642 18.6642 12.0358 18.6642 12.45 18.25C12.8642 17.8358 12.8642 17.1642 12.45 16.75L8.2657 12.5657C7.95328 12.2533 7.95328 11.7467 8.2657 11.4343Z" fill="black"></path>
            </svg>
         </span>
         <!--end::Svg Icon-->
      </div>
      <!--end::Aside toggler-->
   </div>
   <!--end::Brand-->
   <!--begin::Aside menu-->
   <div class="aside-menu flex-column-fluid">
      <!--begin::Aside Menu-->
      <div class="hover-scroll-overlay-y my-5 my-lg-5" id="kt_aside_menu_wrapper" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-height="auto" data-kt-scroll-dependencies="#kt_aside_logo, #kt_aside_footer" data-kt-scroll-wrappers="#kt_aside_menu" data-kt-scroll-offset="0" style="height: 185px;">
         <!--begin::Menu-->
         <div class="menu menu-column menu-title-gray-800 menu-state-title-primary menu-state-icon-primary menu-state-bullet-primary menu-arrow-gray-500" id="#kt_aside_menu" data-kt-menu="true">

            <div class="menu-item">
               <a class="menu-link {{ (in_array(request()->route()->getName(), ['home'])) ? 'active' : '' }}" href="{{ route('home') }}">
                  <span class="menu-icon">
                     <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
                     <span class="svg-icon svg-icon-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                           <rect x="2" y="2" width="9" height="9" rx="2" fill="black"></rect>
                           <rect opacity="0.3" x="13" y="2" width="9" height="9" rx="2" fill="black"></rect>
                           <rect opacity="0.3" x="13" y="13" width="9" height="9" rx="2" fill="black"></rect>
                           <rect opacity="0.3" x="2" y="13" width="9" height="9" rx="2" fill="black"></rect>
                        </svg>
                     </span>
                     <!--end::Svg Icon-->
                  </span>
                  <span class="menu-title">Dashboard</span>
               </a>
            </div>

            @if(Auth::user()->role_id == 1 || (isset($access['customer']) && $access['customer']['view_access'] == 1))
            <div class="menu-item">
               <a class="menu-link {{ (in_array(request()->route()->getName(), ['customer.index'])) ? 'active' : '' }}" href="{{ route('customer.index') }}">
                  <span class="menu-icon">
                     <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
                     <span class="svg-icon svg-icon-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                           <rect x="2" y="2" width="9" height="9" rx="2" fill="black"></rect>
                           <rect opacity="0.3" x="13" y="2" width="9" height="9" rx="2" fill="black"></rect>
                           <rect opacity="0.3" x="13" y="13" width="9" height="9" rx="2" fill="black"></rect>
                           <rect opacity="0.3" x="2" y="13" width="9" height="9" rx="2" fill="black"></rect>
                        </svg>
                     </span>
                     <!--end::Svg Icon-->
                  </span>
                  <span class="menu-title">Customer</span>
               </a>
            </div>
            @endif

            @if(Auth::user()->role_id == 1 || (isset($access['sales-person']) && $access['sales-person']['view_access'] == 1))
            <div class="menu-item">
               <a class="menu-link {{ (in_array(request()->route()->getName(), ['sales-persons.index'])) ? 'active' : '' }}" href="{{ route('sales-persons.index') }}">
                  <span class="menu-icon">
                     <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
                     <span class="svg-icon svg-icon-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                           <rect x="2" y="2" width="9" height="9" rx="2" fill="black"></rect>
                           <rect opacity="0.3" x="13" y="2" width="9" height="9" rx="2" fill="black"></rect>
                           <rect opacity="0.3" x="13" y="13" width="9" height="9" rx="2" fill="black"></rect>
                           <rect opacity="0.3" x="2" y="13" width="9" height="9" rx="2" fill="black"></rect>
                        </svg>
                     </span>
                     <!--end::Svg Icon-->
                  </span>
                  <span class="menu-title">Sales Persons</span>
               </a>
            </div>
            @endif

            @if(Auth::user()->role_id == 1)
            <div class="menu-item">
               <a class="menu-link {{ (in_array(request()->route()->getName(), ['role.index','role.create','role.edit'])) ? 'active' : '' }}" href="{{ route('role.index') }}">
                  <span class="menu-icon">
                     <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
                     <span class="svg-icon svg-icon-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                           <rect x="2" y="2" width="9" height="9" rx="2" fill="black"></rect>
                           <rect opacity="0.3" x="13" y="2" width="9" height="9" rx="2" fill="black"></rect>
                           <rect opacity="0.3" x="13" y="13" width="9" height="9" rx="2" fill="black"></rect>
                           <rect opacity="0.3" x="2" y="13" width="9" height="9" rx="2" fill="black"></rect>
                        </svg>
                     </span>
                     <!--end::Svg Icon-->
                  </span>
                  <span class="menu-title">Role</span>
               </a>
            </div>
            @endif

            @if(Auth::user()->role_id == 1 || (isset($access['user']) && $access['user']['view_access'] == 1))
            <div class="menu-item">
               <a class="menu-link {{ (in_array(request()->route()->getName(), ['user.index','user.create','user.edit'])) ? 'active' : '' }}" href="{{ route('user.index') }}">
                  <span class="menu-icon">
                     <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
                     <span class="svg-icon svg-icon-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                           <rect x="2" y="2" width="9" height="9" rx="2" fill="black"></rect>
                           <rect opacity="0.3" x="13" y="2" width="9" height="9" rx="2" fill="black"></rect>
                           <rect opacity="0.3" x="13" y="13" width="9" height="9" rx="2" fill="black"></rect>
                           <rect opacity="0.3" x="2" y="13" width="9" height="9" rx="2" fill="black"></rect>
                        </svg>
                     </span>
                     <!--end::Svg Icon-->
                  </span>
                  <span class="menu-title">User</span>
               </a>
            </div>
            @endif

            @if(Auth::user()->role_id == 1 || (isset($access['product']) && $access['product']['view_access'] == 1))
            <div class="menu-item">
               <a class="menu-link {{ (in_array(request()->route()->getName(), ['product.index'])) ? 'active' : '' }}" href="{{ route('product.index') }}">
                  <span class="menu-icon">
                     <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
                     <span class="svg-icon svg-icon-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                           <rect x="2" y="2" width="9" height="9" rx="2" fill="black"></rect>
                           <rect opacity="0.3" x="13" y="2" width="9" height="9" rx="2" fill="black"></rect>
                           <rect opacity="0.3" x="13" y="13" width="9" height="9" rx="2" fill="black"></rect>
                           <rect opacity="0.3" x="2" y="13" width="9" height="9" rx="2" fill="black"></rect>
                        </svg>
                     </span>
                     <!--end::Svg Icon-->
                  </span>
                  <span class="menu-title">Product</span>
               </a>
            </div>
            @endif

            @if(Auth::user()->role_id == 1)
            <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ (in_array(request()->route()->getName(), ['productfeatures.index','productfeatures.create','productfeatures.edit','productbenefits.index','productbenefits.create','productbenefits.edit','productsellsheets.index','productsellsheets.create','productsellsheets.edit'])) ? 'hover show' : '' }}">
               <span class="menu-link">
                  <span class="menu-icon">
                     <!--begin::Svg Icon | path: icons/duotune/layouts/lay010.svg-->
                     <span class="svg-icon svg-icon-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                           <path opacity="0.3" d="M20 21H3C2.4 21 2 20.6 2 20V10C2 9.4 2.4 9 3 9H20C20.6 9 21 9.4 21 10V20C21 20.6 20.6 21 20 21Z" fill="black"></path>
                           <path d="M20 7H3C2.4 7 2 6.6 2 6V3C2 2.4 2.4 2 3 2H20C20.6 2 21 2.4 21 3V6C21 6.6 20.6 7 20 7Z" fill="black"></path>
                        </svg>
                     </span>
                     <!--end::Svg Icon-->
                  </span>
                  <span class="menu-title">Product Additional Info</span>
                  <span class="menu-arrow"></span>
               </span>
               <div class="menu-sub menu-sub-accordion">
                  <div class="menu-item">
                     <a class="menu-link {{ (in_array(request()->route()->getName(), ['productfeatures.index','productfeatures.create','productfeatures.edit'])) ? 'active' : '' }}" href="{{ route('productfeatures.index') }}">
                        <span class="menu-bullet">
                        <span class="bullet bullet-dot"></span>
                        </span>
                        <span class="menu-title">Features</span>
                     </a>
                  </div>
                  <div class="menu-item">
                     <a class="menu-link {{ (in_array(request()->route()->getName(), ['productbenefits.index','productbenefits.create','productbenefits.edit'])) ? 'active' : '' }}" href="{{ route('productbenefits.index') }}">
                        <span class="menu-bullet">
                        <span class="bullet bullet-dot"></span>
                        </span>
                        <span class="menu-title">Advantages &amp; Benefits</span>
                     </a>
                  </div>
                  <div class="menu-item">
                     <a class="menu-link {{ (in_array(request()->route()->getName(), ['productsellsheets.index','productsellsheets.create','productsellsheets.edit'])) ? 'active' : '' }}" href="{{ route('productsellsheets.index') }}">
                        <span class="menu-bullet">
                        <span class="bullet bullet-dot"></span>
                        </span>
                        <span class="menu-title">Sell Sheets</span>
                     </a>
                  </div>
               </div>
            </div>
            @endif
            
         </div>
         <!--end::Menu-->
      </div>
      <!--end::Aside Menu-->
   </div>
   <!--end::Aside menu-->
</div>
<!--end::Aside-->