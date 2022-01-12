@extends('layouts.master')

@section('title','Help Desk')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Help Desk</h1>
      </div>

      <!--begin::Actions-->
      <div class="d-flex align-items-center py-1">
        <!--begin::Button-->
        <a href="{{ route('help-desk.index') }}" class="btn btn-sm btn-primary">Back</a>
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
                      <label>Name<span class="asterisk">*</span></label>
                      <input type="text" class="form-control form-control-solid" value="{{ @Auth::user()->first_name." ".@Auth::user()->last_name }}" readonly="" disabled="">
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Email<span class="asterisk">*</span></label>
                      <input type="text" class="form-control form-control-solid" value="{{ @Auth::user()->email }}" readonly="" disabled="">
                    </div>
                  </div>

                </div>

                <div class="row mb-5">
                  
                  {{-- <div class="col-md-6">
                    <div class="form-group">
                      <label>Department<span class="asterisk">*</span></label>
                      <select class="form-control form-control-lg form-control-solid" name="department_id" data-control="select2" data-hide-search="false" data-placeholder="Select a department" data-allow-clear="true">
                        <option value=""></option>
                        @foreach($departments as $d)
                        <option value="{{ $d->id }}">{{ $d->name }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div> --}}

                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Urgency<span class="asterisk">*</span></label>
                      <select class="form-control form-control-lg form-control-solid" name="help_desk_urgency_id" data-control="select2" data-hide-search="true" data-placeholder="Select a urgency" data-allow-clear="true">
                        <option value=""></option>
                        @foreach($urgencies as $u)
                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>

                </div>


                <div class="row mb-5">
                  
                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Subject<span class="asterisk">*</span></label>
                      <input type="text" class="form-control form-control-solid" name="subject" placeholder="Enter subject">
                    </div>
                  </div>

                </div>


                <div class="row mb-5">
                  
                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Message<span class="asterisk">*</span></label>
                      <textarea class="form-control form-control-solid" placeholder="Enter message" name="message" rows="5"></textarea>
                    </div>
                  </div>

                </div>

                <div class="row mb-5 mt-10">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Upload Image</label>
                      <input type="file" class="dropify form-control form-control-solid" name="images[]" accept="image/*" multiple>
                    </div>
                  </div>
                </div>

                {{-- <div data-repeater-list="help_images">
                  <div class="row mb-5" data-repeater-item>
                    <div class="col-md-6">
                      <div class="form-group">
                        <input type="file" class="dropify form-control form-control-solid help_images_image" name="image" accept="image/*" data-allowed-file-extensions="jpeg jpg png eps bmp tif tiff webp" data-max-file-size-preview="10M">
                      </div>
                    </div>

                    <div class="col-md-6">
                      <div class="form-group">
                        <a href="javascript:" class="btn btn-icon btn-bg-light btn-active-color-primary btn-md btn-color-danger" data-repeater-delete><i class="fa fa-trash"></i></a>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row mb-5">
                  
                  <div class="col-md-6">
                    <div class="form-group">
                      <a href="javascript:" class="btn btn-success btn-sm" data-repeater-create >Add more</a>
                    </div>
                  </div>

                </div> --}}

                <div class="row mb-5 mt-10">
                  <div class="col-md-12">
                    <div class="form-group">
                      <input type="submit" value="{{ isset($edit) ? "Update" : "Save" }}" class="btn btn-primary">
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

    $('body').on("submit", "#myForm", function (e) {
      e.preventDefault();
      var validator = validate_form();
      
      if (validator.form() != false) {
        $('[type="submit"]').prop('disabled', true);
        $.ajax({
          url: "{{route('help-desk.store')}}",
          type: "POST",
          data: new FormData($("#myForm")[0]),
          async: false,
          processData: false,
          contentType: false,
          success: function (data) {
            if (data.status) {
              toast_success(data.message)
              setTimeout(function(){

                @if(isset($edit->id))
                  window.location.reload(); 
                @else
                  window.location.href = '{{ route('help-desk.index') }}';
                @endif

              },500)
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
            department_id:{
              required:true,
            },
            help_desk_urgency_id:{
              required:true,
            },
            subject:{
              required:true,
              maxlength:185,
            },
          },
          messages: {
            
          },
      });

      $('.help_images_image').each(function() {
        $(this).rules('add', {
          required:false,
          messages: {
            accept : "Allow only .jpeg .jpg .png .eps .bmp .tif .tiff .webp files."
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
  
  });
</script>
@endpush