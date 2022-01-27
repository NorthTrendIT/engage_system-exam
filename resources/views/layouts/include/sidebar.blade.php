@php
   $access = get_user_role_module_access(Auth::user()->role_id);
@endphp

<!--begin::Aside-->
<div id="kt_aside" class="aside aside-dark aside-hoverable" data-kt-drawer="true" data-kt-drawer-name="aside" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'200px', '300px': '250px'}" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_aside_mobile_toggle">
   <!--begin::Brand-->
   <div class="aside-logo flex-column-auto" id="kt_aside_logo">
      <!--begin::Logo-->
      <a href="{{ route('home') }}">
      {{-- <img alt="Logo" src="{{ asset('assets') }}/assets/media/logo-full.png" class="h-25px logo"> --}}
      <span class="text-white logo" style="font-size: 20px;">Exceltrend OMS</span>
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


            <div class="menu-item">
               <a class="menu-link {{ (in_array(request()->route()->getName(), ['news-and-announcement.index', 'news-and-announcement.edit', 'news-and-announcement.create'])) ? 'active' : '' }}" href="{{ route('news-and-announcement.index') }}">
                  <span class="menu-icon">
                     <span class="svg-icon svg-icon-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                           <rect x="2" y="2" width="9" height="9" rx="2" fill="black"></rect>
                           <rect opacity="0.3" x="13" y="2" width="9" height="9" rx="2" fill="black"></rect>
                           <rect opacity="0.3" x="13" y="13" width="9" height="9" rx="2" fill="black"></rect>
                           <rect opacity="0.3" x="2" y="13" width="9" height="9" rx="2" fill="black"></rect>
                        </svg>
                     </span>
                  </span>
                  <span class="menu-title">News & Announcement</span>
               </a>
            </div>

            {{-- Product Management --}}
            @if(Auth::user()->role_id == 1 || (isset($access['view-product']) && $access['view-product'] == 1) || (isset($access['view-product-group']) && $access['view-product-group'] == 1))
            <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ (in_array(request()->route()->getName(), ['product-group.index','product-group.create','product-group.edit','product-group.show','product.index','product.create','product.edit','product.show'])) ? 'hover show' : '' }}">

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
                  <span class="menu-title">Product Management</span>
                  <span class="menu-arrow"></span>
               </span>
               <div class="menu-sub menu-sub-accordion">

                  @if(Auth::user()->role_id == 1 || (isset($access['view-product-group']) && $access['view-product-group'] == 1))
                  <div class="menu-item">
                     <a class="menu-link {{ (in_array(request()->route()->getName(), ['product.index','product.create','product.edit','product.show'])) ? 'active' : '' }}" href="{{ route('product.index') }}">
                        <span class="menu-bullet">
                        <span class="bullet bullet-dot"></span>
                        </span>
                        <span class="menu-title">Products</span>
                     </a>
                  </div>
                  @endif

                  @if(Auth::user()->role_id == 1 || (isset($access['view-product']) && $access['view-product'] == 1))
                  <div class="menu-item">
                     <a class="menu-link {{ (in_array(request()->route()->getName(), ['product-group.index','product-group.create','product-group.edit','product-group.show'])) ? 'active' : '' }}" href="{{ route('product-group.index') }}" >
                        <span class="menu-bullet">
                        <span class="bullet bullet-dot"></span>
                        </span>
                        <span class="menu-title">Brands</span>
                     </a>
                  </div>
                  @endif

               </div>
            </div>
            @endif


            {{-- Promotions Management --}}
            @if(Auth::user()->role_id == 1)
            <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ (in_array(request()->route()->getName(), ['promotion-type.index','promotion-type.create','promotion-type.edit','promotion-type.show','promotion.index','promotion.create','promotion.edit','promotion.show','customer-promotion.order.index','customer-promotion.order.create','customer-promotion.order.show'])) ? 'hover show' : '' }}">

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
                  <span class="menu-title">Promotion Management</span>
                  <span class="menu-arrow"></span>
               </span>
               <div class="menu-sub menu-sub-accordion">

                  <div class="menu-item">
                     <a class="menu-link {{ (in_array(request()->route()->getName(), ['promotion.index','promotion.create','promotion.edit','promotion.show'])) ? 'active' : '' }}" href="{{ route('promotion.index') }}">
                        <span class="menu-bullet">
                        <span class="bullet bullet-dot"></span>
                        </span>
                        <span class="menu-title">Promotions</span>
                     </a>
                  </div>

                  <div class="menu-item">
                     <a class="menu-link {{ (in_array(request()->route()->getName(), ['promotion-type.index','promotion-type.create','promotion-type.edit','promotion-type.show'])) ? 'active' : '' }}" href="{{ route('promotion-type.index') }}" >
                        <span class="menu-bullet">
                        <span class="bullet bullet-dot"></span>
                        </span>
                        <span class="menu-title">Promotion Types</span>
                     </a>
                  </div>

                  <div class="menu-item">
                     <a class="menu-link {{ (in_array(request()->route()->getName(), ['customer-promotion.order.index','customer-promotion.order.create','customer-promotion.order.show'])) ? 'active' : '' }}" href="{{ route('customer-promotion.order.index') }}">
                        <span class="menu-bullet">
                        <span class="bullet bullet-dot"></span>
                        </span>
                        <span class="menu-title">Claimed Promotions</span>
                     </a>
                  </div>

               </div>
            </div>
            @endif


            {{-- Orders --}}
            @if(Auth::user()->role_id == 1 || (isset($access['view-order']) && $access['view-order'] == 1))
            <div class="menu-item">
               <a class="menu-link {{ (in_array(request()->route()->getName(), ['orders.index','orders.show'])) ? 'active' : '' }}" href="{{ route('orders.index') }}">
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
                  <span class="menu-title">Orders</span>
               </a>
            </div>
            @endif

            {{-- Customer Management --}}
            @if(Auth::user()->role_id == 1 || ( (isset($access['view-customer-group']) && $access['view-customer-group'] == 1) || (isset($access['view-customer']) && $access['view-customer'] == 1) || (isset($access['view-class']) && $access['view-class'] == 1) ) )
            <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ (in_array(request()->route()->getName(), ['customer.index','customer.show','customer-group.index','class.index','class.show', 'customers-sales-specialist.index', 'customers-sales-specialist.create', 'customers-sales-specialist.edit', 'customers-sales-specialist.show', 'customer-delivery-schedule.index', 'customer-delivery-schedule.create', 'customer-delivery-schedule.edit', 'customer-delivery-schedule.show', 'customers-sales-specialist.import.index'])) ? 'hover show' : '' }}">
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
                  <span class="menu-title">Customer Management</span>
                  <span class="menu-arrow"></span>
               </span>
               <div class="menu-sub menu-sub-accordion">

                  @if(Auth::user()->role_id == 1 || (isset($access['view-customer']) && $access['view-customer'] == 1))
                  <div class="menu-item">
                     <a class="menu-link {{ (in_array(request()->route()->getName(), ['customer.index','customer.show'])) ? 'active' : '' }}" href="{{ route('customer.index') }}" >
                        <span class="menu-bullet">
                        <span class="bullet bullet-dot"></span>
                        </span>
                        <span class="menu-title">Customers</span>
                     </a>
                  </div>
                  @endif

                  @if(Auth::user()->role_id == 1 || (isset($access['view-class']) && $access['view-class'] == 1))
                  <div class="menu-item">
                     <a class="menu-link {{ (in_array(request()->route()->getName(), ['class.index','class.show'])) ? 'active' : '' }}" href="{{ route('class.index') }}" >
                        <span class="menu-bullet">
                        <span class="bullet bullet-dot"></span>
                        </span>
                        <span class="menu-title">Class</span>
                     </a>
                  </div>
                  @endif


                  @if(Auth::user()->role_id == 1 || (isset($access['view-customer-group']) && $access['view-customer-group'] == 1))
                  <div class="menu-item">
                     <a class="menu-link {{ (in_array(request()->route()->getName(), ['customer-group.index'])) ? 'active' : '' }}" href="{{ route('customer-group.index') }}">
                        <span class="menu-bullet">
                        <span class="bullet bullet-dot"></span>
                        </span>
                        <span class="menu-title">Groups</span>
                     </a>
                  </div>
                  @endif

                  @if(Auth::user()->role_id == 1)
                  <div class="menu-item">
                     <a class="menu-link {{ (in_array(request()->route()->getName(), ['customer-delivery-schedule.index', 'customer-delivery-schedule.create', 'customer-delivery-schedule.edit', 'customer-delivery-schedule.show'])) ? 'active' : '' }}" href="{{ route('customer-delivery-schedule.index') }}">
                        <span class="menu-bullet">
                        <span class="bullet bullet-dot"></span>
                        </span>
                        <span class="menu-title">Delivery Schedule</span>
                     </a>
                  </div>

                  <div class="menu-item">
                     <a class="menu-link {{ (in_array(request()->route()->getName(), ['customers-sales-specialist.index', 'customers-sales-specialist.create', 'customers-sales-specialist.edit', 'customers-sales-specialist.show', 'customers-sales-specialist.import.index'])) ? 'active' : '' }}" href="{{ route('customers-sales-specialist.index') }}">
                        <span class="menu-bullet">
                        <span class="bullet bullet-dot"></span>
                        </span>
                        <span class="menu-title">Sales Specialist Assignment</span>
                     </a>
                  </div>
                  @endif

               </div>
            </div>
            @endif

            {{-- User Management --}}
            @if(Auth::user()->role_id == 1 || ( (isset($access['view-role']) && $access['view-role'] == 1) || (isset($access['view-location']) && $access['view-location'] == 1) || (isset($access['view-department']) && $access['view-department'] == 1) || (isset($access['view-user']) && $access['view-user'] == 1) ) )
            <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ (in_array(request()->route()->getName(), ['location.index','location.create','location.edit','role.index','role.create','role.edit','department.index','department.create','department.edit','user.index','user.create','user.edit','user.show','organisation.index','role.chart','department.show', 'territory.index','territory-sales-specialist.index','territory-sales-specialist.create','territory-sales-specialist.edit'])) ? 'hover show' : '' }}">

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
                  <span class="menu-title">User Management</span>
                  <span class="menu-arrow"></span>
               </span>
               <div class="menu-sub menu-sub-accordion">
                  {{-- @if(Auth::user()->role_id == 1 || (isset($access['view-location']) && $access['view-location'] == 1))
                  <div class="menu-item">
                     <a class="menu-link {{ (in_array(request()->route()->getName(), ['location.index','location.create','location.edit'])) ? 'active' : '' }}" href="{{ route('location.index') }}" >
                        <span class="menu-bullet">
                        <span class="bullet bullet-dot"></span>
                        </span>
                        <span class="menu-title">Locations</span>
                     </a>
                  </div>
                  @endif --}}

                  @if(Auth::user()->role_id == 1 || (isset($access['view-user']) && $access['view-user'] == 1))
                  <div class="menu-item">
                     <a class="menu-link {{ (in_array(request()->route()->getName(), ['user.index','user.create','user.edit','user.show'])) ? 'active' : '' }}" href="{{ route('user.index') }}">
                        <span class="menu-bullet">
                        <span class="bullet bullet-dot"></span>
                        </span>
                        <span class="menu-title">Users</span>
                     </a>
                  </div>
                  @endif

                  @if(Auth::user()->role_id == 1 || (isset($access['view-role']) && $access['view-role'] == 1))
                  <div class="menu-item">
                     <a class="menu-link {{ (in_array(request()->route()->getName(), ['role.index','role.create','role.edit','role.chart'])) ? 'active' : '' }}" href="{{ route('role.index') }}">
                        <span class="menu-bullet">
                        <span class="bullet bullet-dot"></span>
                        </span>
                        <span class="menu-title">Roles</span>
                     </a>
                  </div>
                  @endif

                  @if(Auth::user()->role_id == 1 || (isset($access['view-department']) && $access['view-department'] == 1))
                  <div class="menu-item">
                     <a class="menu-link {{ (in_array(request()->route()->getName(), ['department.index','department.create','department.edit','department.show'])) ? 'active' : '' }}" href="{{ route('department.index') }}" >
                        <span class="menu-bullet">
                        <span class="bullet bullet-dot"></span>
                        </span>
                        <span class="menu-title">Departments</span>
                     </a>
                  </div>
                  @endif

                  @if(Auth::user()->role_id == 1)
                  <div class="menu-item">
                     <a class="menu-link {{ (in_array(request()->route()->getName(), ['territory.index'])) ? 'active' : '' }}" href="{{ route('territory.index') }}">
                        <span class="menu-bullet">
                        <span class="bullet bullet-dot"></span>
                        </span>
                        <span class="menu-title">Territories</span>
                     </a>
                  </div>
                  @endif

                  @if(Auth::user()->role_id == 1)
                  <div class="menu-item">
                     <a class="menu-link {{ (in_array(request()->route()->getName(), ['territory-sales-specialist.index','territory-sales-specialist.create','territory-sales-specialist.edit'])) ? 'active' : '' }}" href="{{ route('territory-sales-specialist.index') }}">
                        <span class="menu-bullet">
                        <span class="bullet bullet-dot"></span>
                        </span>
                        <span class="menu-title">Territory Assignment</span>
                     </a>
                  </div>
                  @endif

                  @if(Auth::user()->role_id == 1)
                  <div class="menu-item">
                     <a class="menu-link {{ (in_array(request()->route()->getName(), ['organisation.index'])) ? 'active' : '' }}" href="{{ route('organisation.index') }}">
                        <span class="menu-bullet">
                        <span class="bullet bullet-dot"></span>
                        </span>
                        <span class="menu-title">Organization Chart</span>
                     </a>
                  </div>
                  @endif

               </div>
            </div>
            @endif

            {{-- Product List --}}
            @if((isset($access['view-product-list']) && $access['view-product-list'] == 1))
            <div class="menu-item">
               <a class="menu-link {{ (in_array(request()->route()->getName(), ['product-list.index','product-list.show'])) ? 'active' : '' }}" href="{{ route('product-list.index') }}">
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
                  <span class="menu-title">Product List</span>
               </a>
            </div>
            @endif


            {{-- My Promotions --}}
            @if((isset($access['view-my-promotions']) && $access['view-my-promotions'] == 1))
            <div class="menu-item">
               <a class="menu-link {{ (in_array(request()->route()->getName(), ['customer-promotion.index','customer-promotion.show','customer-promotion.order.index','customer-promotion.order.create','customer-promotion.order.show','customer-promotion.order.edit','customer-promotion.get-interest', 'customer-promotion.product-detail'])) ? 'active' : '' }}" href="{{ route('customer-promotion.index') }}">
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
                  <span class="menu-title">My Promotions </span>
               </a>
            </div>
            @endif


            {{-- Place Order For Customer --}}
            @if(Auth::user()->role_id == 2)
            <div class="menu-item">
               <a class="menu-link {{ (in_array(request()->route()->getName(), ['sales-specialist-orders.index','sales-specialist-orders.create','sales-specialist-orders.edit'])) ? 'active' : '' }}" href="{{ route('sales-specialist-orders.index') }}">
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
                  <span class="menu-title">Place Order For Customer</span>
               </a>
            </div>
            @endif


            {{-- Draft Orders --}}
            @if(Auth::user()->role_id == 4)
            <div class="menu-item">
               <a class="menu-link {{ (in_array(request()->route()->getName(), ['draft-order.index','draft-order.show', 'draft-order.edit'])) ? 'active' : '' }}" href="{{ route('draft-order.index') }}">
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
                  <span class="menu-title">Draft Orders</span>
               </a>
            </div>
            @endif


            {{-- Help Desk --}}
            <div class="menu-item">
               <a class="menu-link {{ (in_array(request()->route()->getName(), ['help-desk.index','help-desk.edit','help-desk.show'])) ? 'active' : '' }}" href="{{ route('help-desk.index') }}">
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
                  <span class="menu-title">Help Desk</span>
               </a>
            </div>

            @if(Auth::user()->role_id != 1)
            <div class="menu-item">
               <a class="menu-link {{ (in_array(request()->route()->getName(), ['conversation.index','conversation.create','conversation.show'])) ? 'active' : '' }}" href="{{ route('conversation.index') }}">
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
                  <span class="menu-title">Live Chat</span>

                  <i class="fa fa-circle new-message text-primary" aria-hidden="true" style="display: none;"></i>
               </a>
            </div>
            @endif

            {{-- SAP API Connection --}}
            @if(Auth::user()->role_id == 1)
            <div class="menu-item">
               <a class="menu-link {{ (in_array(request()->route()->getName(), ['sap-connection.index', 'sap-connection.edit', 'sap-connection.create'])) ? 'active' : '' }}" href="{{ route('sap-connection.index') }}">
                  <span class="menu-icon">
                     <span class="svg-icon svg-icon-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                           <rect x="2" y="2" width="9" height="9" rx="2" fill="black"></rect>
                           <rect opacity="0.3" x="13" y="2" width="9" height="9" rx="2" fill="black"></rect>
                           <rect opacity="0.3" x="13" y="13" width="9" height="9" rx="2" fill="black"></rect>
                           <rect opacity="0.3" x="2" y="13" width="9" height="9" rx="2" fill="black"></rect>
                        </svg>
                     </span>
                  </span>
                  <span class="menu-title">SAP API Connection</span>
               </a>
            </div>
            @endif



            {{-- Activity Log --}}
            @if(Auth::user()->role_id == 1)
            <div class="menu-item">
               <a class="menu-link {{ (in_array(request()->route()->getName(), ['activitylog.index'])) ? 'active' : '' }}" href="{{ route('activitylog.index') }}">
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
                  <span class="menu-title">Activity Log</span>
               </a>
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
