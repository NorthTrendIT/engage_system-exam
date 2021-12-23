@extends('layouts.master')

@section('title','News & Announcement')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">News & Announcement</h1>
      </div>

      <!--begin::Actions-->
      <div class="d-flex align-items-center py-1">
        <!--begin::Button-->
        <a href="{{ route('news-and-announcement.index') }}" class="btn btn-sm btn-primary">Back</a>
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
              <h1 class="text-dark fw-bolder fs-3 my-1">{{ isset($edit) ? "Update" : "Add" }} Details</h1>
            </div>
            <div class="card-body">
              <form method="post" id="myForm">
                @csrf

                @if(isset($edit))
                  <input type="hidden" name="id" value="{{ $edit->id }}">
                @endif

                <div class="row mb-5">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Title<span class="asterisk">*</span></label>
                      <input type="text" class="form-control form-control-solid" placeholder="Enter Title" name="title" @if(isset($edit)) value="{{ $edit->title }}" @endif>
                    </div>
                  </div>

                  <div class="col-md-6">
                        <div class="form-group">
                            <label>Select Type<span class="asterisk">*</span></label>
                            <select class="form-select form-select-solid" data-control="select2" data-hide-search="false" name="type">
                                <option value="">Select Type</option>
                                <option value="A" @if(isset($edit) && $edit->type == 'A') selected @endif>Announcement</option>
                                <option value="N" @if(isset($edit) && $edit->type == 'N') selected @endif>News</option>
                            </select>
                        </div>
                  </div>
                </div>

                <div class="row mb-5">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Message<span class="asterisk">*</span></label>
                            <textarea class="form-control form-control-solid" placeholder="Enter your message" name="message" rows="5">@if(isset($edit)){{ $edit->message }}@endif</textarea>
                        </div>
                    </div>
                </div>

                <div class="row mb-5">
                    <div class="col-md-6">
                            <div class="form-group">
                                <label>Select Module<span class="asterisk">*</span></label>
                                <select class="form-select form-select-solid" data-control="select2" id="selectModule" data-hide-search="false" name="module">
                                    <option value="">Select Module</option>
                                    <option value="role" @if(isset($edit->connection) && $edit->connection->module == 'role') selected @endif>Role</option>
                                </select>
                            </div>
                    </div>
                    <!-- Roles -->
                    <div class="col-md-6 roles" style="display:none">
                        <div class="form-group">
                            <label>Select Role<span class="asterisk">*</span></label>
                            <select class="form-select form-select-solid" data-control="select2" id="selectRole" data-hide-search="false" name="record_id">
                                <option value="">Select Role</option>
                            </select>
                        </div>
                    </div>
                </div>


                <div class="row mb-5 mt-10">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Upload Image and Documents</label>
                    </div>
                  </div>
                </div>

                <div data-repeater-list="documents">

                  @if(isset($edit->documents) && count($edit->documents) > 0)
                    @foreach($edit->documents as $key => $doc)
                      <div class="row mb-5" data-repeater-item>
                        <input type="hidden" name="id" value="{{$doc->id}}">

                        <div class="col-md-6">
                          <div class="form-group">
                            <input type="hidden" name="file" value="{{$doc->file}}">
                            <input type="file" class="dropify form-control form-control-solid product_images_image" name="file" accept="" data-allowed-file-extensions="jpeg jpg png eps bmp tif tiff webp pdf doc docx xls xlsx ppt pptx odt ods" data-max-file-size-preview="10M">
                          </div>
                        </div>

                        @if($doc->file && get_valid_file_url('sitebucket/news-and-announcement',$doc->file))
                          <div class="col-md-3 image_preview">
                            <div class="form-group">
                              <img src="{{ get_valid_file_url('sitebucket/news-and-announcement',$doc->file) }}" height="100" width="100" class="">
                            </div>
                          </div>
                        @endif

                        <div class="col-md-2">
                          <div class="form-group">
                            <a href="javascript:" class="btn btn-icon btn-bg-light btn-active-color-primary btn-md btn-color-danger" data-repeater-delete><i class="fa fa-trash"></i></a>
                          </div>
                        </div>

                      </div>
                    @endforeach

                  @else
                    <div class="row mb-5" data-repeater-item>
                      <div class="col-md-6">
                        <div class="form-group">
                          <input type="file" class="dropify form-control form-control-solid product_images_image" name="file" accept="image/*" data-allowed-file-extensions="jpeg jpg png eps bmp tif tiff webp" data-max-file-size-preview="10M">
                        </div>
                      </div>

                      <div class="col-md-6">
                        <div class="form-group">
                          <a href="javascript:" class="btn btn-icon btn-bg-light btn-active-color-primary btn-md btn-color-danger" data-repeater-delete><i class="fa fa-trash"></i></a>
                        </div>
                      </div>
                    </div>
                  @endif

                </div>

                <div class="row mb-5">

                  <div class="col-md-6">
                    <div class="form-group">
                      <a href="javascript:" class="btn btn-success btn-sm" data-repeater-create >Add more</a>
                    </div>
                  </div>

                </div>


                <div class="row mb-5">
                  <div class="col-md-12">
                    <div class="form-group">
                      <input type="submit" value="Save and Send Push" class="btn btn-primary">
                    </div>
                  </div>
                </div>

              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection


@push('js')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.min.css" />

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/additional-methods.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.repeater/1.2.1/jquery.repeater.min.js"></script>
<script src="https://cdn.ckeditor.com/4.17.1/standard/ckeditor.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js" ></script>

<script>
  $(document).ready(function() {

    CKEDITOR.replace( 'message',{
      removePlugins: ['image', 'uploadimage']
    });

    $('body').on('change' ,'#selectModule', function(){
        $module = $('[name="module"]').val();

        $('.roles').hide();

        if($module == "role"){
            $('.roles').show();
        }
    });

    $('body').on("submit", "#myForm", function (e) {
      e.preventDefault();
      var validator = validate_form();

      if (validator.form() != false) {
        $('[type="submit"]').prop('disabled', true);
        $.ajax({
          url: "{{route('news-and-announcement.store')}}",
          type: "POST",
          data: new FormData($("#myForm")[0]),
          async: false,
          processData: false,
          contentType: false,
          success: function (data) {
            if (data.status) {
              toast_success(data.message)
              setTimeout(function(){
                window.location.href = '{{ route('news-and-announcement.index') }}';
              },1500)
            } else {
              toast_error(data.message);
              $('[type="submit"]').prop('disabled', false);
            }
          },
          error: function () {
            toast_error("Something went to wrong !");
            $('[type="submit"]').prop('disabled', false);
          },
        });
      }
    });

    function validate_form(){
      var validator = $("#myForm").validate({
          errorClass: "is-invalid",
          validClass: "is-valid",
          rules: {
            title:{
              required: true,
              maxlength: 185,
            },
            type:{
              required: true,
            },
            message:{
              required:true,
            },
            module:{
              required:true,
            },
          },
          messages: {
            title:{
              required: "Please enter title.",
              maxlength:'Please enter title less than 185 character',
            },
            type:{
              required: "Please select news and announcement type.",
            },
            message:{
              required:"Please enter message.",
            },
            module:{
              required:"Please select Module.",
            },
          },
      });

      $('.product_images_image').each(function() {
        $(this).rules('add', {
          required:false,
          messages: {
            accept : "Allow only .jpeg .jpg .png .eps .bmp .tif .tiff .webp .pdf .doc .docx .xls .xlsx .ppt .pptx .odt .ods files."
          }
        });
      });

      return validator;
    }

    $('#myForm').repeater({
      initEmpty: false,
      show: function () {
        // $(this).find('input[type="file"]').dropify();
        $(this).find('.image_preview').remove();
        $(this).slideDown();
      },
      hide: function (deleteElement) {
        if(confirm('Are you sure you want to delete this element?')) {
          $(this).slideUp(deleteElement);
        }
      },
      ready: function (setIndexes) {
      },
      isFirstItemUndeletable: true,
    });

    $("#selectRole").select2({
        ajax: {
            url: "{{route('news-and-announcement.getRoles')}}",
            type: "post",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    _token: "{{ csrf_token() }}",
                    search: params.term
                };
            },
            processResults: function (response) {
                return {
                    results: response
                };
            },
            cache: true
        },
        placeholder: 'Select Role',
        // minimumInputLength: 1,
        multiple: false,
    });

  });
</script>
@endpush
