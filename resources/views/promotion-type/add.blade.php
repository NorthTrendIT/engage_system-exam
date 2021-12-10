@extends('layouts.master')

@section('title','Promotion Type')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Promotion Type</h1>
      </div>

      <!--begin::Actions-->
      <div class="d-flex align-items-center py-1">
        <!--begin::Button-->
        <a href="{{ route('promotion-type.index') }}" class="btn btn-sm btn-primary">Back</a>
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
                      <label>Promotion Type Title<span class="asterisk">*</span></label>
                      <input type="text" class="form-control form-control-solid" name="title" placeholder="Enter promotion type title" @if(isset($edit)) value="{{ $edit->title }}" @endif>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Criteria<span class="asterisk">*</span></label>
                      <select class="form-control form-control-lg form-control-solid" name="scope" data-control="select2" data-hide-search="true" data-placeholder="Select a criteria" data-allow-clear="true">
                        <option value=""></option>
                        <option value="P" @if(isset($edit) && $edit->scope == "P") selected="" @endif >Discount in Percentage</option>
                        <option value="R" @if(isset($edit) && $edit->scope == "R") selected="" @endif >Discount Percentage Range</option>
                        <option value="U" @if(isset($edit) && $edit->scope == "U") selected="" @endif >Percentage discount + Up to amount limit</option>
                      </select>

                    </div>
                  </div>

                </div>

                <div class="row mb-5">
                  
                  <div class="col-md-6 scope_p_div" style="display: none;">
                    <div class="form-group">
                      <label>Discount Percentage<span class="asterisk">*</span></label>
                      <input type="number" step=".01" class="form-control form-control-solid" placeholder="Enter discount percentage" name="percentage" @if(isset($edit)) value="{{ $edit->percentage }}" @endif>
                    </div>
                  </div>

                  <div class="col-md-6 scope_u_div" style="display: none;">
                    <div class="form-group">
                      <label>Fixed Price<span class="asterisk">*</span></label>
                      <input type="number" step=".01" class="form-control form-control-solid" placeholder="Enter fixed price" name="fixed_price" @if(isset($edit)) value="{{ $edit->fixed_price }}" @endif>
                    </div>
                  </div>

                </div>


                <div class="row mb-5 scope_r_div" style="display: none;">
                  
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Minimum Percentage<span class="asterisk">*</span></label>
                      <input type="number" step=".01" class="form-control form-control-solid" placeholder="Enter minimum percentage" name="min_percentage" @if(isset($edit)) value="{{ $edit->min_percentage }}" @endif id="min_percentage">
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Maximum Percentage<span class="asterisk">*</span></label>
                      <input type="number" step=".01" class="form-control form-control-solid" placeholder="Enter maximum percentage" name="max_percentage" @if(isset($edit)) value="{{ $edit->max_percentage }}" @endif id="max_percentage">
                    </div>
                  </div>

                </div>

                <div class="row mb-5">
                  
                  {{-- <div class="col-md-6">
                    <div class="form-group">
                      <label>Fixed Quantity</label>
                      <input type="number" class="form-control form-control-solid" placeholder="Enter fixed quantity" name="fixed_quantity" @if(isset($edit)) value="{{ $edit->fixed_quantity }}" @endif>
                    </div>
                  </div> --}}

                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Number Of Delivery<span class="asterisk">*</span></label>
                      <input type="number" class="form-control form-control-solid" placeholder="Enter number of delivery" name="number_of_delivery" @if(isset($edit)) value="{{ $edit->number_of_delivery }}" @endif>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Has Fixed Quantity ?<span class="asterisk">*</span></label>
                      <select class="form-control form-control-lg form-control-solid" name="is_fixed_quantity" data-control="select2" data-hide-search="true" data-placeholder="Select a this" data-allow-clear="false">
                        <option value="1" @if(isset($edit) && $edit->is_fixed_quantity == "1") selected="" @endif >Yes</option>
                        <option value="0" @if(isset($edit) && $edit->is_fixed_quantity == "0") selected="" @endif >No</option>
                      </select>
                    </div>
                  </div>

                </div>


                <div class="row mb-5">
                  
                  <div class="col-md-6 is_total_fixed_quantity_div">
                    <div class="form-group">
                      <label>Is Total Fixed Quantity ?<span class="asterisk">*</span></label>
                      <select class="form-control form-control-lg form-control-solid" name="is_total_fixed_quantity" data-control="select2" data-hide-search="true" data-placeholder="Select Fixed Quantity Option" data-allow-clear="false">
                        <option value="0" @if(isset($edit) && $edit->is_total_fixed_quantity == "0") selected="" @endif >Fixed Quantity Per Product</option>
                        <option value="1" @if(isset($edit) && $edit->is_total_fixed_quantity == "1") selected="" @endif >Total Fixed Quantity</option>
                      </select>
                    </div>
                  </div>

                  <div class="col-md-6 total_fixed_quantity_div" style="display: none;">
                    <div class="form-group">
                      <label>Total Fixed Quantity<span class="asterisk">*</span></label>
                      <input type="number" class="form-control form-control-solid" placeholder="Enter total fixed quantity" name="total_fixed_quantity" @if(isset($edit)) value="{{ $edit->total_fixed_quantity }}" @endif>
                    </div>
                  </div>

                </div>


                {{-- <div class="row mb-5 only_products_div" style="display: none;">
                  
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Products<span class="asterisk">*</span></label>
                      <select class="form-control form-control-lg form-control-solid" id="products" name="products[]" multiple="">
                        <option value=""></option>
                      </select>
                    </div>
                  </div>

                </div> --}}


                <div class="mt-15 multi_product_div" >

                  <div data-repeater-list="product_list">

                    @if(isset($edit) && count($edit->products) > 0 )

                      @foreach($edit->products as $key => $p)

                        <div class="row mb-5" data-repeater-item>
                          <div class="col-md-3">
                            <div class="form-group">
                              <label>Product<span class="asterisk">*</span></label>
                              <select class="form-control form-control-lg form-control-solid product_id" name="product_id">
                                <option value=""></option>
                                <option value="{{ @$p->product->id }}" selected="">{{ @$p->product->item_name }}</option>
                              </select>
                            </div>
                          </div>

                          <div class="col-md-3 product_fixed_quantity_div">
                            <div class="form-group">
                              <label>Fixed Quantity<span class="asterisk">*</span></label>
                              <input type="number" class="form-control form-control-solid fixed_quantity" placeholder="Enter fixed quantity" name="fixed_quantity" value="{{ $p->fixed_quantity }}">
                            </div>
                          </div>

                          <div class="col-md-3 product_discount_div" style="display: none;">
                            <div class="form-group">
                              <label>Discount Percentage<span class="asterisk">*</span></label>
                              <input type="number" step=".01" class="form-control form-control-solid discount_percentage" placeholder="Enter discount percentage" name="discount_percentage" value="{{ $p->discount_percentage }}">
                            </div>
                          </div>

                          <div class="col-md-3 delete_btn_div" @if($key == 0) style="display: none;" @endif>
                            <div class="form-group">
                              <a href="javascript:" class="btn btn-icon btn-bg-light btn-active-color-primary btn-md btn-color-danger mt-6 " data-repeater-delete ><i class="fa fa-trash"></i></a>
                            </div>
                          </div>

                        </div>

                      @endforeach

                    @else
                      <div class="row mb-5" data-repeater-item>
                        <div class="col-md-3">
                          <div class="form-group">
                            <label>Product<span class="asterisk">*</span></label>
                            <select class="form-control form-control-lg form-control-solid product_id" name="product_id">
                              <option value=""></option>
                            </select>
                          </div>
                        </div>

                        <div class="col-md-3 product_fixed_quantity_div">
                          <div class="form-group">
                            <label>Fixed Quantity<span class="asterisk">*</span></label>
                            <input type="number" class="form-control form-control-solid fixed_quantity" placeholder="Enter fixed quantity" name="fixed_quantity">
                          </div>
                        </div>

                        <div class="col-md-3 product_discount_div" style="display: none;">
                          <div class="form-group">
                            <label>Discount Percentage<span class="asterisk">*</span></label>
                            <input type="number" step=".01" class="form-control form-control-solid discount_percentage" placeholder="Enter discount percentage" name="discount_percentage">
                          </div>
                        </div>

                        <div class="col-md-3">
                          <div class="form-group">
                            <a href="javascript:" class="btn btn-icon btn-bg-light btn-active-color-primary btn-md btn-color-danger mt-6" data-repeater-delete><i class="fa fa-trash"></i></a>
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

    @if(isset($edit) && $edit->scope == "P")
      $('.scope_p_div').show();
      //$('.only_products_div').show();
      $('.product_discount_div').hide();
    @endif

    @if(isset($edit) && $edit->scope == "R")
      $('.scope_r_div').show();
      //$('.multi_product_div').show();
      $('.product_discount_div').show();
    @endif

    @if(isset($edit) && $edit->scope == "U")
      $('.scope_p_div').show();
      $('.scope_u_div').show();
      //$('.only_products_div').show();
      $('.product_discount_div').hide();
    @endif

    @if(isset($edit) && $edit->is_total_fixed_quantity == "1")
      $('.product_fixed_quantity_div').hide();
      $('.total_fixed_quantity_div').show();
    @endif

    @if(isset($edit) && $edit->is_total_fixed_quantity == "0" && $edit->is_fixed_quantity == "1")
      $('.product_fixed_quantity_div').show();
      $('.total_fixed_quantity_div').hide();
    @endif
    
    @if(isset($edit) && $edit->is_fixed_quantity == "0")
      $('.product_fixed_quantity_div').hide();
      $('.is_total_fixed_quantity_div').hide();
      $('.total_fixed_quantity_div').hide();
    @endif




    $(document).on('change', '[name="scope"]', function(){
      $value = $('[name="scope"]').find('option:selected').val();
      $('.scope_r_div').hide();
      $('.scope_p_div').hide();
      $('.scope_u_div').hide();
      //$('.only_products_div').hide();
      // $('.multi_product_div').hide();
      $('.product_discount_div').hide();

      if($value == "P"){
        $('.scope_p_div').show();
        //$('.only_products_div').show();
      }else if($value == "R"){
        $('.scope_r_div').show();
        //$('.multi_product_div').show();
        $('.product_discount_div').show();
      }else if($value == "U"){
        $('.scope_p_div').show();
        $('.scope_u_div').show();
        //$('.only_products_div').show();
      }
    });

    $(document).on('change', '[name="is_fixed_quantity"]', function(){
      $value = $(this).find('option:selected').val();
      $('.is_total_fixed_quantity_div').hide();
      $('.total_fixed_quantity_div').hide();
      $('.product_fixed_quantity_div').hide();

      if($value == "0"){
        $('.is_total_fixed_quantity_div').hide();
        // $('[name="is_total_fixed_quantity"]').val(1).trigger('change');
        // $('.product_fixed_quantity_div').hide();
      }else{
        $('.is_total_fixed_quantity_div').show();
        // $('.product_fixed_quantity_div').show();
        $('[name="is_total_fixed_quantity"]').val(0).trigger('change');
      }
    });

    $(document).on('change', '[name="is_total_fixed_quantity"]', function(){
      $value = $(this).find('option:selected').val();
      $('.total_fixed_quantity_div').hide();
      $('.product_fixed_quantity_div').hide();

      if($value == "0"){
        $('.total_fixed_quantity_div').hide();
        $('.product_fixed_quantity_div').show();
      }else{
        $('.total_fixed_quantity_div').show();
        $('.product_fixed_quantity_div').hide();
      }

    });

    {{-- $initialProducts = [];

    @if(isset($edit) && $edit->scope != "R")
        @foreach ($edit->products as $p)
          @if (@$p->product->id)
            var initialOption = {
                id: {{ $p->product->id }},
                text: '{!! $p->product->item_name !!}',
                selected: true
            }
            $initialProducts.push(initialOption);
          @endif
        @endforeach
    @endif

    $('#products').select2({
      ajax: {
        url: "{{route('promotion-type.get-products')}}",
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
            results:  $.map(response, function (item) {
                          return {
                            text: item.item_name,
                            id: item.id
                          }
                      })
          };
        },
        cache: true
      },
      placeholder: 'Select products',
      allowClear: true,
      multiple: true,
      @if(isset($edit))
      data:$initialProducts,
      @endif
    });--}}

    $('.product_id').select2({
      ajax: {
        url: "{{route('promotion-type.get-products')}}",
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
            results:  $.map(response, function (item) {
                          return {
                            text: item.item_name,
                            id: item.id
                          }
                      })
          };
        },
        cache: true
      },
      placeholder: 'Select a product',
      allowClear: true,
      multiple: false,
    });

    $('body').on("submit", "#myForm", function (e) {
      e.preventDefault();
      var validator = validate_form();
      
      if (validator.form() != false) {
        $('[type="submit"]').prop('disabled', true);
        $.ajax({
          url: "{{route('promotion-type.store')}}",
          type: "POST",
          data: new FormData($("#myForm")[0]),
          async: false,
          processData: false,
          contentType: false,
          success: function (data) {
            if (data.status) {
              toast_success(data.message)
              setTimeout(function(){
                // window.location.reload();
                window.location.href = '{{ route('promotion-type.index') }}';
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
            title:{
              required:true,
              maxlength:185,
            },
            scope:{
              required:true,
            },
            percentage:{
              min:0,
              max:100,
              number: true,
              required: function () {
                        if($('[name="scope"]').find('option:selected').val() == 'P' || $('[name="scope"]').find('option:selected').val() == "U"){
                            return true;
                        }else{
                            return false;
                        }
                    },
            },
            fixed_price:{
              min:0,
              number: true,
              required: function () {
                        if($('[name="scope"]').find('option:selected').val() == "U"){
                            return true;
                        }else{
                            return false;
                        }
                    },
            },
            min_percentage:{
              min:0,
              max:100,
              number: true,
              required: function () {
                        if($('[name="scope"]').find('option:selected').val() == "R"){
                            return true;
                        }else{
                            return false;
                        }
                    },
            },
            max_percentage:{
              min:$('#min_percentage').val(),
              max:100,
              number: true,
              required: function () {
                        if($('[name="scope"]').find('option:selected').val() == "R"){
                            return true;
                        }else{
                            return false;
                        }
                    },
            },
            'products[]':{
              required: function () {
                        if($('[name="scope"]').find('option:selected').val() == 'P' || $('[name="scope"]').find('option:selected').val() == "U"){
                            return true;
                        }else{
                            return false;
                        }
                    },
            },
            // fixed_quantity:{
            //   min:0,
            //   digits: true
            // },
            total_fixed_quantity:{
              min:0,
              digits: true,
              required: function () {
                        if($('[name="is_total_fixed_quantity"]').find('option:selected').val() == "1"){
                          return true;
                        }else{
                          return false;
                        }
                    },
            },
            number_of_delivery:{
              min:1,
              digits: true,
              required: true,
            },
            is_fixed_quantity:{
              required:true
            }
          },
          messages: {
            
          },
      });

      $('.product_id').each(function() {
        $(this).rules('add', {
          required:true,
        });
      });

      $('.discount_percentage').each(function() {
        $(this).rules('add', {
          required:true,
          min:$('#min_percentage').val(),
          max:$('#max_percentage').val(),
          number: true
        });
      });

      $('.fixed_quantity').each(function() {
        $(this).rules('add', {
          min:1,
          required:true,
          digits: true,
        });
      });

      return validator;
    }

    $('#myForm').repeater({
      initEmpty: false,
      show: function () {
        
        $(this).slideDown();
        $(this).find('.product_discount_div').hide();
        $(this).find('.delete_btn_div').show();

        if($('[name="scope"]').find('option:selected').val() == "R"){
          $(this).find('.product_discount_div').show();
        }

        if($('[name="is_fixed_quantity"]').find('option:selected').val() == "0" || $('[name="is_total_fixed_quantity"]').find('option:selected').val() == "1"){
          $('.product_fixed_quantity_div').hide();
        }else{
          $('.product_fixed_quantity_div').show();
        }


        $('.multi_product_div').find('.select2-container').remove();

        $('.product_id').select2({
          ajax: {
            url: "{{route('promotion-type.get-products')}}",
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
                results:  $.map(response, function (item) {
                              return {
                                text: item.item_name,
                                id: item.id
                              }
                          })
              };
            },
            cache: true
          },
          placeholder: 'Select a product',
          allowClear: true,
          multiple: false,
        });

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