@extends('layouts.master')

@section('title','Conversation')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Conversation</h1>
      </div>

      <!--begin::Actions-->
      <div class="d-flex align-items-center py-1">
        <!--begin::Button-->
        <a href="{{ route('conversation.create') }}" class="btn btn-sm btn-primary sync-products">New Conversation</a>
        <!--end::Button-->
      </div>
      <!--end::Actions-->
      
    </div>
  </div>
  
  
  <div class="post d-flex flex-column-fluid" id="kt_post">
    <!--begin::Container-->
    <div id="kt_content_container" class="container-xxl">
      <!--begin::Layout-->
      <div class="d-flex flex-column flex-lg-row">
        <!--begin::Sidebar-->
        <div class="flex-column flex-lg-row-auto w-100 w-lg-300px w-xl-400px mb-10 mb-lg-0">
          <!--begin::Contacts-->
          <div class="card card-flush">
            <!--begin::Card header-->
            <div class="card-header pt-7" id="kt_chat_contacts_header">
              <!--begin::Form-->
              <form class="w-100 position-relative" autocomplete="off">
                <!--begin::Icon-->
                <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                <span class="svg-icon svg-icon-2 svg-icon-lg-1 svg-icon-gray-500 position-absolute top-50 ms-5 translate-middle-y">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                    <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                  </svg>
                </span>
                <!--end::Svg Icon-->
                <!--end::Icon-->
                <!--begin::Input-->
                <input type="text" class="form-control form-control-solid px-15" name="search" value="" placeholder="Search by username or email..." />
                <!--end::Input-->
              </form>
              <!--end::Form-->
            </div>
            <!--end::Card header-->
            <!--begin::Card body-->
            <div class="card-body pt-5" id="kt_chat_contacts_body">
              <!--begin::List-->
              <div class="scroll-y me-n5 pe-5 h-200px h-lg-auto" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_header, #kt_toolbar, #kt_footer, #kt_chat_contacts_header" data-kt-scroll-wrappers="#kt_content, #kt_chat_contacts_body" data-kt-scroll-offset="0px">

                <!--begin::User-->
                <div class="d-flex flex-stack py-4">
                  <!--begin::Details-->
                  <div class="d-flex align-items-center">
                    <!--begin::Avatar-->
                    <div class="symbol symbol-45px symbol-circle">
                      <span class="symbol-label bg-light-danger text-danger fs-6 fw-bolder">M</span>
                      <div class="symbol-badge bg-success start-100 top-100 border-4 h-15px w-15px ms-n2 mt-n2"></div>
                    </div>
                    <!--end::Avatar-->
                    <!--begin::Details-->
                    <div class="ms-5">
                      <a href="#" class="fs-5 fw-bolder text-gray-900 text-hover-primary mb-2">Melody Macy</a>
                      <div class="fw-bold text-muted">melody@altbox.com</div>
                    </div>
                    <!--end::Details-->
                  </div>
                  <!--end::Details-->
                  <!--begin::Lat seen-->
                  <div class="d-flex flex-column align-items-end ms-2">
                    <span class="text-muted fs-7 mb-1">1 day</span>
                    <span class="badge badge-sm badge-circle badge-light-success">2</span>
                  </div>
                  <!--end::Lat seen-->
                </div>
                <!--end::User-->
                <!--begin::Separator-->
                <div class="separator separator-dashed d-none"></div>
                <!--end::Separator-->
                <!--begin::User-->
                <div class="d-flex flex-stack py-4">
                  <!--begin::Details-->
                  <div class="d-flex align-items-center">
                    <!--begin::Avatar-->
                    <div class="symbol symbol-45px symbol-circle">
                      <img alt="Pic" src="{{ asset('assets') }}/assets/media/avatars/150-26.jpg" />
                      <div class="symbol-badge bg-success start-100 top-100 border-4 h-15px w-15px ms-n2 mt-n2"></div>
                    </div>
                    <!--end::Avatar-->
                    <!--begin::Details-->
                    <div class="ms-5">
                      <a href="#" class="fs-5 fw-bolder text-gray-900 text-hover-primary mb-2">Max Smith</a>
                      <div class="fw-bold text-muted">max@kt.com</div>
                    </div>
                    <!--end::Details-->
                  </div>
                  <!--end::Details-->
                  <!--begin::Lat seen-->
                  <div class="d-flex flex-column align-items-end ms-2">
                    <span class="text-muted fs-7 mb-1">1 week</span>
                    <span class="badge badge-sm badge-circle badge-light-success">6</span>
                  </div>
                  <!--end::Lat seen-->
                </div>
                <!--end::User-->
                
              </div>
              <!--end::List-->
            </div>
            <!--end::Card body-->
          </div>
          <!--end::Contacts-->
        </div>
        <!--end::Sidebar-->
        <!--begin::Content-->
        <div class="flex-lg-row-fluid ms-lg-7 ms-xl-10">
          <!--begin::Messenger-->
          <div class="card" id="kt_chat_messenger">
            <!--begin::Card header-->
            <div class="card-header" id="kt_chat_messenger_header">
              <!--begin::Title-->
              <div class="card-title">
                <!--begin::User-->
                <div class="d-flex justify-content-center flex-column me-3">
                  <a href="#" class="fs-4 fw-bolder text-gray-900 text-hover-primary me-1 mb-2 lh-1">Brian Cox</a>
                  <!--begin::Info-->
                  <div class="mb-0 lh-1">
                    <span class="badge badge-success badge-circle w-10px h-10px me-1"></span>
                    <span class="fs-7 fw-bold text-muted">Active</span>
                  </div>
                  <!--end::Info-->
                </div>
                <!--end::User-->
              </div>
              <!--end::Title-->
              <!--begin::Card toolbar-->
              <div class="card-toolbar">
                <!--begin::Menu-->
                <div class="me-n3">
                  <button class="btn btn-sm btn-icon btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                    <i class="fa fa-trash" style="font-size: 20px;"></i>
                  </button>
                  
                </div>
                <!--end::Menu-->
              </div>
              <!--end::Card toolbar-->
            </div>
            <!--end::Card header-->
            <!--begin::Card body-->
            <div class="card-body" id="kt_chat_messenger_body">
              <!--begin::Messages-->
              <div class="scroll-y me-n5 pe-5 h-300px h-lg-auto" data-kt-element="messages" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_header, #kt_toolbar, #kt_footer, #kt_chat_messenger_header, #kt_chat_messenger_footer" data-kt-scroll-wrappers="#kt_content, #kt_chat_messenger_body" data-kt-scroll-offset="-2px">
                <!--begin::Message(in)-->
                <div class="d-flex justify-content-start mb-10">
                  <!--begin::Wrapper-->
                  <div class="d-flex flex-column align-items-start">
                    <!--begin::User-->
                    <div class="d-flex align-items-center mb-2">
                      <!--begin::Avatar-->
                      <div class="symbol symbol-35px symbol-circle">
                        <img alt="Pic" src="{{ asset('assets') }}/assets/media/avatars/150-15.jpg" />
                      </div>
                      <!--end::Avatar-->
                      <!--begin::Details-->
                      <div class="ms-3">
                        <a href="#" class="fs-5 fw-bolder text-gray-900 text-hover-primary me-1">Brian Cox</a>
                        <span class="text-muted fs-7 mb-1">2 mins</span>
                      </div>
                      <!--end::Details-->
                    </div>
                    <!--end::User-->
                    <!--begin::Text-->
                    <div class="p-5 rounded bg-light-info text-dark fw-bold mw-lg-400px text-start" data-kt-element="message-text">How likely are you to recommend our company to your friends and family ?</div>
                    <!--end::Text-->
                  </div>
                  <!--end::Wrapper-->
                </div>
                <!--end::Message(in)-->
                <!--begin::Message(out)-->
                <div class="d-flex justify-content-end mb-10">
                  <!--begin::Wrapper-->
                  <div class="d-flex flex-column align-items-end">
                    <!--begin::User-->
                    <div class="d-flex align-items-center mb-2">
                      <!--begin::Details-->
                      <div class="me-3">
                        <span class="text-muted fs-7 mb-1">5 mins</span>
                        <a href="#" class="fs-5 fw-bolder text-gray-900 text-hover-primary ms-1">You</a>
                      </div>
                      <!--end::Details-->
                      <!--begin::Avatar-->
                      <div class="symbol symbol-35px symbol-circle">
                        <img alt="Pic" src="{{ asset('assets') }}/assets/media/avatars/150-26.jpg" />
                      </div>
                      <!--end::Avatar-->
                    </div>
                    <!--end::User-->
                    <!--begin::Text-->
                    <div class="p-5 rounded bg-light-primary text-dark fw-bold mw-lg-400px text-end" data-kt-element="message-text">Hey there, we’re just writing to let you know that you’ve been subscribed to a repository on GitHub.</div>
                    <!--end::Text-->
                  </div>
                  <!--end::Wrapper-->
                </div>
                <!--end::Message(out)-->
                <!--begin::Message(in)-->
                <div class="d-flex justify-content-start mb-10">
                  <!--begin::Wrapper-->
                  <div class="d-flex flex-column align-items-start">
                    <!--begin::User-->
                    <div class="d-flex align-items-center mb-2">
                      <!--begin::Avatar-->
                      <div class="symbol symbol-35px symbol-circle">
                        <img alt="Pic" src="{{ asset('assets') }}/assets/media/avatars/150-15.jpg" />
                      </div>
                      <!--end::Avatar-->
                      <!--begin::Details-->
                      <div class="ms-3">
                        <a href="#" class="fs-5 fw-bolder text-gray-900 text-hover-primary me-1">Brian Cox</a>
                        <span class="text-muted fs-7 mb-1">1 Hour</span>
                      </div>
                      <!--end::Details-->
                    </div>
                    <!--end::User-->
                    <!--begin::Text-->
                    <div class="p-5 rounded bg-light-info text-dark fw-bold mw-lg-400px text-start" data-kt-element="message-text">Ok, Understood!</div>
                    <!--end::Text-->
                  </div>
                  <!--end::Wrapper-->
                </div>
                <!--end::Message(in)-->
                <!--begin::Message(out)-->
                <div class="d-flex justify-content-end mb-10">
                  <!--begin::Wrapper-->
                  <div class="d-flex flex-column align-items-end">
                    <!--begin::User-->
                    <div class="d-flex align-items-center mb-2">
                      <!--begin::Details-->
                      <div class="me-3">
                        <span class="text-muted fs-7 mb-1">2 Hours</span>
                        <a href="#" class="fs-5 fw-bolder text-gray-900 text-hover-primary ms-1">You</a>
                      </div>
                      <!--end::Details-->
                      <!--begin::Avatar-->
                      <div class="symbol symbol-35px symbol-circle">
                        <img alt="Pic" src="{{ asset('assets') }}/assets/media/avatars/150-26.jpg" />
                      </div>
                      <!--end::Avatar-->
                    </div>
                    <!--end::User-->
                    <!--begin::Text-->
                    <div class="p-5 rounded bg-light-primary text-dark fw-bold mw-lg-400px text-end" data-kt-element="message-text">You’ll receive notifications for all issues, pull requests!</div>
                    <!--end::Text-->
                  </div>
                  <!--end::Wrapper-->
                </div>
                <!--end::Message(out)-->
                <!--begin::Message(in)-->
                <div class="d-flex justify-content-start mb-10">
                  <!--begin::Wrapper-->
                  <div class="d-flex flex-column align-items-start">
                    <!--begin::User-->
                    <div class="d-flex align-items-center mb-2">
                      <!--begin::Avatar-->
                      <div class="symbol symbol-35px symbol-circle">
                        <img alt="Pic" src="{{ asset('assets') }}/assets/media/avatars/150-15.jpg" />
                      </div>
                      <!--end::Avatar-->
                      <!--begin::Details-->
                      <div class="ms-3">
                        <a href="#" class="fs-5 fw-bolder text-gray-900 text-hover-primary me-1">Brian Cox</a>
                        <span class="text-muted fs-7 mb-1">3 Hours</span>
                      </div>
                      <!--end::Details-->
                    </div>
                    <!--end::User-->
                    <!--begin::Text-->
                    <div class="p-5 rounded bg-light-info text-dark fw-bold mw-lg-400px text-start" data-kt-element="message-text">You can unwatch this repository immediately by clicking here:
                    <a href="https://keenthemes.com">Keenthemes.com</a></div>
                    <!--end::Text-->
                  </div>
                  <!--end::Wrapper-->
                </div>
                <!--end::Message(in)-->
                <!--begin::Message(out)-->
                <div class="d-flex justify-content-end mb-10">
                  <!--begin::Wrapper-->
                  <div class="d-flex flex-column align-items-end">
                    <!--begin::User-->
                    <div class="d-flex align-items-center mb-2">
                      <!--begin::Details-->
                      <div class="me-3">
                        <span class="text-muted fs-7 mb-1">4 Hours</span>
                        <a href="#" class="fs-5 fw-bolder text-gray-900 text-hover-primary ms-1">You</a>
                      </div>
                      <!--end::Details-->
                      <!--begin::Avatar-->
                      <div class="symbol symbol-35px symbol-circle">
                        <img alt="Pic" src="{{ asset('assets') }}/assets/media/avatars/150-26.jpg" />
                      </div>
                      <!--end::Avatar-->
                    </div>
                    <!--end::User-->
                    <!--begin::Text-->
                    <div class="p-5 rounded bg-light-primary text-dark fw-bold mw-lg-400px text-end" data-kt-element="message-text">Most purchased Business courses during this sale!</div>
                    <!--end::Text-->
                  </div>
                  <!--end::Wrapper-->
                </div>
                <!--end::Message(out)-->
                <!--begin::Message(in)-->
                <div class="d-flex justify-content-start mb-10">
                  <!--begin::Wrapper-->
                  <div class="d-flex flex-column align-items-start">
                    <!--begin::User-->
                    <div class="d-flex align-items-center mb-2">
                      <!--begin::Avatar-->
                      <div class="symbol symbol-35px symbol-circle">
                        <img alt="Pic" src="{{ asset('assets') }}/assets/media/avatars/150-15.jpg" />
                      </div>
                      <!--end::Avatar-->
                      <!--begin::Details-->
                      <div class="ms-3">
                        <a href="#" class="fs-5 fw-bolder text-gray-900 text-hover-primary me-1">Brian Cox</a>
                        <span class="text-muted fs-7 mb-1">5 Hours</span>
                      </div>
                      <!--end::Details-->
                    </div>
                    <!--end::User-->
                    <!--begin::Text-->
                    <div class="p-5 rounded bg-light-info text-dark fw-bold mw-lg-400px text-start" data-kt-element="message-text">Company BBQ to celebrate the last quater achievements and goals. Food and drinks provided</div>
                    <!--end::Text-->
                  </div>
                  <!--end::Wrapper-->
                </div>
                <!--end::Message(in)-->
                <!--begin::Message(template for out)-->
                <div class="d-flex justify-content-end mb-10 d-none" data-kt-element="template-out">
                  <!--begin::Wrapper-->
                  <div class="d-flex flex-column align-items-end">
                    <!--begin::User-->
                    <div class="d-flex align-items-center mb-2">
                      <!--begin::Details-->
                      <div class="me-3">
                        <span class="text-muted fs-7 mb-1">Just now</span>
                        <a href="#" class="fs-5 fw-bolder text-gray-900 text-hover-primary ms-1">You</a>
                      </div>
                      <!--end::Details-->
                      <!--begin::Avatar-->
                      <div class="symbol symbol-35px symbol-circle">
                        <img alt="Pic" src="{{ asset('assets') }}/assets/media/avatars/150-26.jpg" />
                      </div>
                      <!--end::Avatar-->
                    </div>
                    <!--end::User-->
                    <!--begin::Text-->
                    <div class="p-5 rounded bg-light-primary text-dark fw-bold mw-lg-400px text-end" data-kt-element="message-text"></div>
                    <!--end::Text-->
                  </div>
                  <!--end::Wrapper-->
                </div>
                <!--end::Message(template for out)-->
                <!--begin::Message(template for in)-->
                <div class="d-flex justify-content-start mb-10 d-none" data-kt-element="template-in">
                  <!--begin::Wrapper-->
                  <div class="d-flex flex-column align-items-start">
                    <!--begin::User-->
                    <div class="d-flex align-items-center mb-2">
                      <!--begin::Avatar-->
                      <div class="symbol symbol-35px symbol-circle">
                        <img alt="Pic" src="{{ asset('assets') }}/assets/media/avatars/150-15.jpg" />
                      </div>
                      <!--end::Avatar-->
                      <!--begin::Details-->
                      <div class="ms-3">
                        <a href="#" class="fs-5 fw-bolder text-gray-900 text-hover-primary me-1">Brian Cox</a>
                        <span class="text-muted fs-7 mb-1">Just now</span>
                      </div>
                      <!--end::Details-->
                    </div>
                    <!--end::User-->
                    <!--begin::Text-->
                    <div class="p-5 rounded bg-light-info text-dark fw-bold mw-lg-400px text-start" data-kt-element="message-text">Right before vacation season we have the next Big Deal for you.</div>
                    <!--end::Text-->
                  </div>
                  <!--end::Wrapper-->
                </div>
                <!--end::Message(template for in)-->
              </div>
              <!--end::Messages-->
            </div>
            <!--end::Card body-->
            <!--begin::Card footer-->
            <div class="card-footer pt-4" id="kt_chat_messenger_footer">
              
              <div class="">
                <textarea class="form-control form-control-flush mb-3" rows="1" data-kt-element="input" placeholder="Type a message"></textarea>
              </div>
              
              <button class="btn btn-primary" type="button" data-kt-element="send">Send</button>


            </div>
            <!--end::Card footer-->
          </div>
          <!--end::Messenger-->
        </div>
        <!--end::Content-->
      </div>
      <!--end::Layout-->
      
    </div>
    <!--end::Container-->
  </div>



</div>
@endsection

@push('css')

@endpush

@push('js')

@endpush