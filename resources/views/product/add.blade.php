@extends('layouts.master')

@section('title','Product')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Product</h1>
      </div>

      <!--begin::Actions-->
      <div class="d-flex align-items-center py-1">
        <!--begin::Button-->
        <a href="{{ route('product.index') }}" class="btn btn-sm btn-primary">Back</a>
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
                  
                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Product Name</label>
                      <input type="text" class="form-control form-control-solid" @if(isset($edit)) value="{{ $edit->item_name }}" @endif readonly="" disabled="">
                    </div>
                  </div>

                </div>

                <div class="row mb-5">
                  
                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Technical specifications</label>
                      <textarea class="form-control form-control-solid" placeholder="Enter technical specifications" name="technical_specifications" rows="5">@if(isset($edit)){{ $edit->technical_specifications }}@endif</textarea>
                    </div>
                  </div>

                </div>

                <div class="row mb-5">
                  
                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Features</label>
                      <textarea class="form-control form-control-solid" placeholder="Enter features" name="product_features" rows="5">@if(isset($edit)){{ $edit->product_features }}@endif</textarea>
                    </div>
                  </div>

                </div>

                <div class="row mb-5">

                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Advantages & Benefits</label>
                      <textarea class="form-control form-control-solid" placeholder="Enter advantages & benefits" name="product_benefits" rows="5">@if(isset($edit)){{ $edit->product_benefits }}@endif</textarea>
                    </div>
                  </div>

                </div>


                <div class="row mb-5">

                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Sell Sheets</label>
                      <textarea class="form-control form-control-solid" placeholder="Enter sell sheets" name="product_sell_sheets" rows="5">@if(isset($edit)){{ $edit->product_sell_sheets }}@endif</textarea>
                    </div>
                  </div>

                </div>

                <div class="row mb-5 mt-10">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Upload Product Image<span class="asterisk">*</span></label>
                    </div>
                  </div>
                </div>

                <div data-repeater-list="product_images">
                  
                  @if(isset($edit->product_images) && count($edit->product_images) > 0)
                    @foreach($edit->product_images as $key => $image)
                      <div class="row mb-5" data-repeater-item>
                        <input type="hidden" name="id" value="{{$image->id}}">

                        <div class="col-md-6">
                          <div class="form-group">
                            <input type="hidden" name="image" value="{{$image->image}}" class="product_images_value">
                            <input type="file" class="dropify form-control form-control-solid product_images_image" name="image" accept="image/*" data-allowed-file-extensions="jpeg jpg png eps bmp tif tiff webp" data-max-file-size-preview="10M">
                          </div>
                        </div>

                        @if($image->image && get_valid_file_url('sitebucket/products',$image->image))
                          <div class="col-md-3 image_preview">
                            <div class="form-group">
                              <a href="{{ get_valid_file_url('sitebucket/products',$image->image) }}" class="fancybox"><img src="{{ get_valid_file_url('sitebucket/products',$image->image) }}" height="100" width="100" class=""></a>
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
                          <input type="file" class="dropify form-control form-control-solid product_images_image" name="image" accept="image/*" data-allowed-file-extensions="jpeg jpg png eps bmp tif tiff webp" data-max-file-size-preview="10M">
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

    CKEDITOR.replace( 'technical_specifications',{
      removePlugins: ['image', 'uploadimage']
    });
    CKEDITOR.replace( 'product_features' ,{
      removePlugins: ['image', 'uploadimage']
    });
    CKEDITOR.replace( 'product_benefits' ,{
      removePlugins: ['image', 'uploadimage']
    });
    CKEDITOR.replace( 'product_sell_sheets' ,{
      removePlugins: ['image', 'uploadimage']
    });


    // $('.edit_dropify').dropify();

    $('body').on("submit", "#myForm", function (e) {
      e.preventDefault();
      var validator = validate_form();
      
      if (validator.form() != false) {
        $('[type="submit"]').prop('disabled', true);
        $.ajax({
          url: "{{route('product.store')}}",
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
                  window.location.href = '{{ route('product.index') }}';
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
            
          },
          messages: {
            
          },
      });

      $('.product_images_image').each(function() {
        var pre_image = $(this).prev('.product_images_value').val();

        $(this).rules('add', {
          required: function () {
                      if(!pre_image){
                          return true;
                      }else{
                          return false;
                      }
                  },
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
        $(this).find('.product_images_value').remove();
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