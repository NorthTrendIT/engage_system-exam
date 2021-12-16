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
            @if(Auth::user()->role_id == 4)
                <a href="{{ route('cart.index') }}" class="btn btn-icon btn-clean btn-lg mr-1">
                    <span class="svg-icon svg-icon-xl svg-icon-primary">
                        <!--begin::Svg Icon | path:/metronic/theme/html/demo1/dist/assets/media/svg/icons/Shopping/Cart3.svg-->
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24"></rect>
                                <path d="M12,4.56204994 L7.76822128,9.6401844 C7.4146572,10.0644613 6.7840925,10.1217854 6.3598156,9.76822128 C5.9355387,9.4146572 5.87821464,8.7840925 6.23177872,8.3598156 L11.2317787,2.3598156 C11.6315738,1.88006147 12.3684262,1.88006147 12.7682213,2.3598156 L17.7682213,8.3598156 C18.1217854,8.7840925 18.0644613,9.4146572 17.6401844,9.76822128 C17.2159075,10.1217854 16.5853428,10.0644613 16.2317787,9.6401844 L12,4.56204994 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                                <path d="M3.5,9 L20.5,9 C21.0522847,9 21.5,9.44771525 21.5,10 C21.5,10.132026 21.4738562,10.2627452 21.4230769,10.3846154 L17.7692308,19.1538462 C17.3034221,20.271787 16.2111026,21 15,21 L9,21 C7.78889745,21 6.6965779,20.271787 6.23076923,19.1538462 L2.57692308,10.3846154 C2.36450587,9.87481408 2.60558331,9.28934029 3.11538462,9.07692308 C3.23725479,9.02614384 3.36797398,9 3.5,9 Z M12,17 C13.1045695,17 14,16.1045695 14,15 C14,13.8954305 13.1045695,13 12,13 C10.8954305,13 10,13.8954305 10,15 C10,16.1045695 10.8954305,17 12,17 Z" fill="#000000"></path>
                            </g>
                        </svg>
                        <!--end::Svg Icon-->
                    </span>
                </a>
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
                     <img src="{{ asset('assets') }}/assets/media/default_user.png" alt="user" />
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
                              <img src="{{ asset('assets') }}/assets/media/default_user.png" alt="user" />
                              @endif
                           </div>
                           <!--end::Avatar-->
                           <!--begin::Username-->
                           <div class="d-flex flex-column">
                              <div class="fw-bolder d-flex align-items-center fs-5">{{ @Auth::user()->first_name ?? "" }} {{ @Auth::user()->last_name ?? "" }}
                              </div>
                              <span class="badge badge-light-success fw-bolder fs-8 px-2 py-1 m-1">{{ @Auth::user()->role->name ?? "" }}</span>
                              <a href="javascript:" class="fw-bold text-muted text-hover-primary fs-7">{{ @Auth::user()->email ?? "" }}</a>
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

                     <!--begin::Menu item-->
                     <div class="menu-item px-5">
                        <a href="{{ route('logout') }}" class="menu-link px-5"><i class="fa fa-sign-out-alt text-danger mr-10"></i> Sign Out</a>
                     </div>
                     <!--end::Menu item-->

                  </div>
                  <!--end::Menu-->
                  <!--end::Menu wrapper-->
               </div>
               <!--end::User -->
               <!--begin::Heaeder menu toggle-->
               <div class="d-flex align-items-center d-lg-none ms-2 me-n3" title="Show header menu">
                  <div class="btn btn-icon btn-active-light-primary w-30px h-30px w-md-40px h-md-40px" id="kt_header_menu_mobile_toggle">
                     <!--begin::Svg Icon | path: icons/duotune/text/txt001.svg-->
                     <span class="svg-icon svg-icon-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                           <path d="M13 11H3C2.4 11 2 10.6 2 10V9C2 8.4 2.4 8 3 8H13C13.6 8 14 8.4 14 9V10C14 10.6 13.6 11 13 11ZM22 5V4C22 3.4 21.6 3 21 3H3C2.4 3 2 3.4 2 4V5C2 5.6 2.4 6 3 6H21C21.6 6 22 5.6 22 5Z" fill="black" />
                           <path opacity="0.3" d="M21 16H3C2.4 16 2 15.6 2 15V14C2 13.4 2.4 13 3 13H21C21.6 13 22 13.4 22 14V15C22 15.6 21.6 16 21 16ZM14 20V19C14 18.4 13.6 18 13 18H3C2.4 18 2 18.4 2 19V20C2 20.6 2.4 21 3 21H13C13.6 21 14 20.6 14 20Z" fill="black" />
                        </svg>
                     </span>
                     <!--end::Svg Icon-->
                  </div>
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
