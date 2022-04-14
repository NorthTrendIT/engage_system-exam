<!--begin::Header-->
<div id="kt_header" style="" class="header align-items-stretch">
   <!--begin::Container-->
   <div class="container-fluid d-flex align-items-stretch justify-content-between">
      <!--begin::Aside mobile toggle-->
      <div class="d-flex align-items-center d-lg-none ms-n3 me-1" title="Show aside menu">
         <div class="btn btn-icon btn-active-light-primary w-30px h-30px w-md-40px h-md-40px" id="kt_aside_mobile_toggle">
            <!--begin::Svg Icon | path: icons/duotune/abstract/abs015.svg-->
            <span class="svg-icon svg-icon-2x mt-1">
               <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                  <path d="M21 7H3C2.4 7 2 6.6 2 6V4C2 3.4 2.4 3 3 3H21C21.6 3 22 3.4 22 4V6C22 6.6 21.6 7 21 7Z" fill="black" />
                  <path opacity="0.3" d="M21 14H3C2.4 14 2 13.6 2 13V11C2 10.4 2.4 10 3 10H21C21.6 10 22 10.4 22 11V13C22 13.6 21.6 14 21 14ZM22 20V18C22 17.4 21.6 17 21 17H3C2.4 17 2 17.4 2 18V20C2 20.6 2.4 21 3 21H21C21.6 21 22 20.6 22 20Z" fill="black" />
               </svg>
            </span>
            <!--end::Svg Icon-->
         </div>
      </div>
      <!--end::Aside mobile toggle-->
      <!--begin::Mobile logo-->
      <div class="d-flex align-items-center flex-grow-1 flex-lg-grow-0">
         <a href="{{ route('home') }}" class="d-lg-none">
            <img alt="Logo" src="{{ asset('assets') }}/assets/media/logo-full.png" class="h-30px" />
         </a>
      </div>
      <!--end::Mobile logo-->
      <!--begin::Wrapper-->
      <div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1">
         <!--begin::Navbar-->
         <div class="d-flex align-items-stretch" id="kt_header_nav">
            <!--begin::Menu wrapper-->

            <!--end::Menu wrapper-->
         </div>
         <!--end::Navbar-->
         <!--begin::Topbar-->
         <div class="d-flex align-items-stretch flex-shrink-0">
            <!-- Notification -->
            @if(Auth::user()->role_id != 1)
                @php 
                  $notification = getMyNotifications(); 
                @endphp
               <div class="d-flex align-items-stretch flex-shrink-0">
                  <!--begin::User-->
                  <div class="d-flex align-items-center ms-1 ms-lg-3" id="kt_header_user_menu_toggle">
                     <!--begin::Menu wrapper-->
                     <div class="cursor-pointer btn btn-icon btn-clean btn-lg mr-1" data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                        <i class="fa fa-bell" style="font-size: 20px"></i>
                        @if(count($notification) > 0)
                        <span class="top-0 start-0 translate-middle badge badge-circle badge-danger">{{ count($notification) }}</span>
                        @elseif(count($notification) > 9)
                        <span class="top-0 start-0 translate-middle badge badge-circle badge-danger">9+</span>
                        @endif
                     </div>
                     <!--begin::Menu-->
                     <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-primary fw-bold py-4 fs-6 w-350px" data-kt-menu="true">
                        @if(isset($notification) && count($notification) > 0)
                        @foreach($notification as $item)
                        <div class="menu-item px-3">
                           <div class="menu-content d-flex align-items-center @if($item->is_important) bg-light-danger @else bg-light-success @endif px-3">
                              <div class="symbol symbol-50px me-5">
                                <span class="svg-icon @if($item->is_important) svg-icon-danger @else svg-icon-success @endif me-5">
                                    <span class="svg-icon svg-icon-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path opacity="0.3" d="M21.25 18.525L13.05 21.825C12.35 22.125 11.65 22.125 10.95 21.825L2.75 18.525C1.75 18.125 1.75 16.725 2.75 16.325L4.04999 15.825L10.25 18.325C10.85 18.525 11.45 18.625 12.05 18.625C12.65 18.625 13.25 18.525 13.85 18.325L20.05 15.825L21.35 16.325C22.35 16.725 22.35 18.125 21.25 18.525ZM13.05 16.425L21.25 13.125C22.25 12.725 22.25 11.325 21.25 10.925L13.05 7.62502C12.35 7.32502 11.65 7.32502 10.95 7.62502L2.75 10.925C1.75 11.325 1.75 12.725 2.75 13.125L10.95 16.425C11.65 16.725 12.45 16.725 13.05 16.425Z" fill="black" />
                                            <path d="M11.05 11.025L2.84998 7.725C1.84998 7.325 1.84998 5.925 2.84998 5.525L11.05 2.225C11.75 1.925 12.45 1.925 13.15 2.225L21.35 5.525C22.35 5.925 22.35 7.325 21.35 7.725L13.05 11.025C12.45 11.325 11.65 11.325 11.05 11.025Z" fill="black" />
                                        </svg>
                                    </span>
                                </span>
                              </div>
                              <div class="d-flex flex-column">
                                 <div class="fw-bolder d-flex align-items-center fs-5">
                                    <a href="{{ route('news-and-announcement.show',$item->id) }}" class="fw-bolder text-gray-800 text-hover-primary fs-6">{{ $item->title }}</a>
                                 </div>
                                 <span class="text-muted fw-bold d-block">{{ getNotificationType($item->type) }}</span>
                              </div>
                           </div>
                        </div>
                        <div class="separator my-2"></div>
                        @endforeach

                        <div class="menu-item px-5">
                            <div class="flex-grow-1 me-2" style="text-align: center">
                                <a href="{{ route('news-and-announcement.index') }}" class="btn btn-sm btn-light-primary font-weight-bold mr-2 px-5">View All</a>
                            </div>
                        </div>
                        @else
                        <div class="menu-item px-5">
                            <div class="flex-grow-1 me-2" style="text-align: center">
                                <span class="text-muted fw-bold d-block">No new Notification.</span>
                            </div>
                        </div>
                        @endif
                     </div>
                  </div>
                  <!--end::Heaeder menu toggle-->
               </div>
            @endif
            @if(Auth::user()->role_id == 4)
            <div class="d-flex align-items-center ms-1 ms-lg-3">
                  <!--begin::Menu wrapper-->
                  <div class="cursor-pointer btn btn-icon btn-clean btn-lg mr-1">
                        <a href="{{ route('cart.index') }}" class="btn btn-icon btn-clean btn-lg mr-1 btn-active-color-primary">
                           <i class="fa fa-shopping-cart" style="font-size: 20px;"></i>
                            @if(getCartCount() > 0)
                            <span class="top-0 start-0 translate-middle badge badge-circle badge-danger cartCount">{{ getCartCount() }}</span>
                            @elseif(getCartCount() > 9)
                            <span class="top-0 start-0 translate-middle badge badge-circle badge-danger cartCount">9+</span>
                            @else
                            <span class="top-0 start-0 translate-middle badge badge-circle badge-danger cartCount" style="display:none"></span>
                            @endif
                        </a>
                  </div>
               </div>
            @endif
            <!--begin::Toolbar wrapper-->
            <div class="d-flex align-items-stretch flex-shrink-0">
               <!--begin::User-->
               <div class="d-flex align-items-center ms-1 ms-lg-3" id="kt_header_user_menu_toggle">
                  <!--begin::Menu wrapper-->
                  <div class="cursor-pointer symbol symbol-30px symbol-md-40px" data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                     @if(get_login_user_profile())
                     <img src="{{ get_login_user_profile() }}" alt="user" />
                     @else
                     <div class="symbol-label fs-3" style="color:{{ convert_hex_to_rgba(Auth::user()->default_profile_color) }};background-color:{{ convert_hex_to_rgba(Auth::user()->default_profile_color,0.5) }};"><b>{{ get_sort_char(Auth::user()->sales_specialist_name) }}</b></div>
                     {{-- <img src="{{ asset('assets') }}/assets/media/default_user.png" alt="user" /> --}}
                     @endif
                  </div>
                  <!--begin::Menu-->
                  <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-primary fw-bold py-4 fs-6 w-275px" data-kt-menu="true">
                     <!--begin::Menu item-->
                     <div class="menu-item px-3">
                        <div class="menu-content d-flex align-items-center px-3">
                           <!--begin::Avatar-->
                           <div class="symbol symbol-50px me-5">
                              @if(get_login_user_profile())
                              <img src="{{ get_login_user_profile() }}" alt="user" />
                              @else
                              {{-- <img src="{{ asset('assets') }}/assets/media/default_user.png" alt="user" /> --}}
                              <div class="symbol-label fs-3" style="color:{{ convert_hex_to_rgba(Auth::user()->default_profile_color) }};background-color:{{ convert_hex_to_rgba(Auth::user()->default_profile_color,0.5) }};"><b>{{ get_sort_char(Auth::user()->sales_specialist_name) }}</b></div>
                              @endif
                           </div>
                           <!--end::Avatar-->
                           <!--begin::Username-->
                           <div class="d-flex flex-column align-items-left">
                              <div class="fw-bolder d-flex align-items-center fs-5" style="max-width: 170px;word-break: break-word;">{{ @Auth::user()->first_name ?? "" }} {{ @Auth::user()->last_name ?? "" }}
                              </div>
                              <span class="badge badge-light-success fw-bolder fs-8 px-3 py-1 mt-1 mb-1" style="max-width: 170px;word-break: break-word;white-space: normal;line-height: 1.1;">{{ @Auth::user()->role->name ?? "" }}</span>
                              <a href="javascript:" class="fw-bold text-muted text-hover-primary fs-7" style="max-width: 170px;word-break: break-word;">{{ @Auth::user()->email ?? "" }}</a>
                           </div>
                           <!--end::Username-->
                        </div>
                     </div>
                     <!--end::Menu item-->
                     <!--begin::Menu separator-->
                     <div class="separator my-2"></div>
                     <!--end::Menu separator-->

                     <!--begin::Menu item-->
                     <div class="menu-item px-5">
                        <a href="{{ route('profile.index') }}" class="menu-link px-5"><i class="fa fa-user text-info mr-10"></i> <span>My Profile</span></a>
                     </div>
                     <!--end::Menu item-->

                     <!--begin::Menu item-->
                     <div class="menu-item px-5">
                        <a href="{{ route('profile.change-password.index') }}" class="menu-link px-5"><i class="fa fa-lock text-primary mr-10"></i> <span>Change Password</span></a>
                     </div>
                     <!--end::Menu item-->

                     @if(userrole() == 1)
                     <!--begin::Menu item-->
                     <div class="menu-item px-5">
                        <a href="{{ asset('assets/files/OMS_DOCUMENT.pdf') }}" class="menu-link px-5" target="_blank"><i class="fa fa-file text-success mr-10"></i> <span>Documentation</span></a>
                     </div>
                     <!--end::Menu item-->
                     @endif

                     <!--begin::Menu item-->
                     <div class="menu-item px-5">
                        <a href="{{ route('logout') }}" class="menu-link px-5"><i class="fa fa-sign-out-alt text-danger mr-10"></i> Sign Out</a>
                     </div>
                     <!--end::Menu item-->

                  </div>
                  <!--end::Menu-->
                  <!--end::Menu wrapper-->
               </div>
               <!--end::Heaeder menu toggle-->
            </div>
            <!--end::Toolbar wrapper-->
         </div>
         <!--end::Topbar-->
      </div>
      <!--end::Wrapper-->
   </div>
   <!--end::Container-->
</div>
<!--end::Header-->
