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
        <a href="{{ route('conversation.index') }}" class="btn btn-sm btn-primary">Back</a>
        <!--end::Button-->
      </div>
      <!--end::Actions-->
    </div>
  </div>
  
  <div class="post d-flex flex-column-fluid" id="kt_post">
    <div id="kt_content_container" class="container-xxl">
      <div class="row gy-5 g-xl-8">
        <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
          <div class="card card-xl-stretch mb-5 mb-xl-8">
            <div class="card-header border-bottom pt-5">
              <h1 class="text-dark fw-bolder fs-3 my-1">Search Users</h1>
            </div>
            <div class="card-body">
              <form method="post" id="myForm">
                @csrf
                <div class="row mb-5 d-flex justify-content-between">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Search by department:</label>
                      <select class="form-control form-control-lg form-control-solid" name="department" data-control="select2" data-hide-search="false" data-allow-clear="true" data-placeholder="Select department">
                        <option value=""></option>

                        @foreach($departments as $d)
                          <option value="{{ $d->id }}">{{ $d->name }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <div class="form-group">
                      <label>Search by:</label>
                      <input type="text" class="form-control form-control-solid" placeholder="Search by name or email..." name="search">
                    </div>
                  </div>
                  
                </div>

                <div class="row mb-10 mt-5">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Search by category:</label>
                        <input type="hidden" name="category" value="">
                        @if(userrole() == 4)
                          {{-- Is Customer --}}
                          <a href="javascript:" class="btn btn-dark btn-sm category_btn" data-value="self-users">Self Users</a>
                          <a href="javascript:" class="btn btn-dark btn-sm category_btn" data-value="sales-specialist">Sales Specialist</a>

                        @elseif(userrole() == 2)
                          {{-- Is SS --}}
                          <a href="javascript:" class="btn btn-dark btn-sm category_btn" data-value="customers">Customers</a>
                          <a href="javascript:" class="btn btn-dark btn-sm category_btn" data-value="parent-users">Parent Users</a>

                        @elseif(@Auth::user()->created_by && @Auth::user()->created_by_user->customer_id)
                          {{-- Is Customer User --}}
                          <a href="javascript:" class="btn btn-dark btn-sm category_btn" data-value="sales-specialist">Sales Specialist</a>
                          <a href="javascript:" class="btn btn-dark btn-sm category_btn" data-value="parent-customer">Parent Customer</a>
                          <a href="javascript:" class="btn btn-dark btn-sm category_btn" data-value="parent-user">Parent User</a>

                        @else
                          {{-- Other User --}}
                          <a href="javascript:" class="btn btn-dark btn-sm category_btn" data-value="parent-user">Parent User</a>

                        @endif
                      </select>
                    </div>
                  </div>
                </div>

                <div class="row mb-5 d-flex justify-content-between">
                  <div class="col-md-4">
                    <div class="form-group">
                      <button type="submit" class="btn btn-primary px-6 font-weight-bold">Search</button> 
                      <a href="javascript:" class="btn btn-light-dark font-weight-bold clear-search">Clear</a>
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>


      <div class="row gy-5 g-xl-8" id="search_result_div" style="display:none;">
        <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
          <div class="card card-xl-stretch mb-5 mb-xl-8">
            <div class="card-header border-bottom pt-5">
              <h1 class="text-dark fw-bolder fs-3 my-1">Search Result</h1>
            </div>
            <div class="card-body">
              <!--begin::List-->
              <div class="scroll-y me-n5 pe-5 h-200px h-lg-auto" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_header, #kt_toolbar, #kt_footer, #kt_chat_contacts_header" data-kt-scroll-wrappers="#kt_content, #kt_chat_contacts_body" data-kt-scroll-offset="0px" id="search_user_list_div">
                


              </div>
              <!--end::List-->
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
@endsection


@push('js')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/additional-methods.min.js"></script>

<script>
  $(document).ready(function() {
    
    $(document).on('click', '.category_btn', function(event) {
      var $this = $(this);
      $('[name="category"]').val("");
      $('.category_btn').removeClass('btn-info');
      $('.category_btn').addClass('btn-dark');

      // For Active Class
      if($($this).hasClass('active')){
        $($this).removeClass('active');
      }else{
        $('.category_btn').removeClass('active');
        $($this).addClass('active');
        $('[name="category"]').val($(this).data('value'));
      }

      // For Button Class
      if($($this).hasClass('active')){
        $($this).removeClass('btn-dark');
        $($this).addClass('btn-info');
      }else{
        $($this).removeClass('btn-info');
        $($this).addClass('btn-dark');
      }
    })

    $(document).on('click', '.clear-search', function(event) {
      $('[name="search"]').val('');
      $('[name="category"]').val('').trigger('change');
      $('[name="department"]').val('').trigger('change');

      $('[name="category"]').val("");
      $('.category_btn').removeClass('btn-info');
      $('.category_btn').removeClass('active');
      $('.category_btn').addClass('btn-dark');

      $('#search_result_div').hide();
      $('#search_user_list_div').html("");
    })

    $('body').on("submit", "#myForm", function (e) {
      e.preventDefault();
      
      $('#search_user_list_div').html("");
      $('#search_result_div').show();
      $.ajax({
        url: "{{route('conversation.search-new-user')}}",
        type: "POST",
        data: new FormData($("#myForm")[0]),
        async: false,
        processData: false,
        contentType: false,
        success: function (data) {
          if (data.html) {
            $('#search_user_list_div').html(data.html);
          }
        },
        error: function () {
          toast_error("Something went to wrong !");
        },
      });

    });

    $(document).on('click', '.create_conversation', function(event) {
      event.preventDefault();
      $user_id = $(this).attr('data-id');

      Swal.fire({
        title: 'Are you sure want to start conversation?',
        //text: "Once deleted, you will not be able to recover this record!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, do it!'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: '{{ route('conversation.store') }}',
            method: "POST",
            data: {
                    _token:'{{ csrf_token() }}',
                    user_id: $user_id
                  }
          })
          .done(function(result) {
            if(result.status == false){
              toast_error(result.message);
            }else{
              toast_success(result.message);
              setTimeout(function(){
                window.location.href = '{{ route('conversation.index') }}?id='+result.id;
              },1500)
            }
          })
          .fail(function() {
            toast_error("error");
          });
        }
      })
    });

  });
</script>
@endpush