@extends('layouts.master')

@section('title','Promotion Claim')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Promotion Claim</h1>
      </div>

      <!--begin::Actions-->
      <div class="d-flex align-items-center py-1">
        <!--begin::Button-->
        <a href="{{ route('customer-promotion.show',$promotion->id) }}" class="btn btn-sm btn-primary">Back</a>
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
                      <label>Promotion Title</label>
                      <input type="text" class="form-control form-control-solid" value="{{ $promotion->title }}" readonly="" disabled="">
                    </div>
                  </div>
                </div>

                <div class="row mb-5 mt-10">
                  <div class="col-md-12">
                    <div class="form-group">
                      <h3>Product Details</h3>
                    </div>
                  </div>
                </div>

                <hr>


                @if(!is_null(@$promotion->promotion_type->products))

                  @foreach($promotion->promotion_type->products as $p)

                    @php
                      $discount_percentage = 0;

                      if(@$promotion->promotion_type->scope == "P"){
                        $discount_percentage = @$promotion->promotion_type->percentage;
                      }elseif(@$promotion->promotion_type->scope == "R"){
                        $discount_percentage = @$p->discount_percentage;
                      }

                    @endphp


                    <div class="row mb-5">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label>Product</label>
                          <input type="text" class="form-control form-control-solid" readonly="" disabled="" value="{{ @$p->product->item_name }}" >
                        </div>
                      </div>

                      <div class="col-md-6">
                        <div class="form-group">
                          <label>Brand</label>
                        </div>
                      </div>
                    </div>


                    <div class="row">
                      <div class="col-md-3">
                        <div class="form-group">
                          <label>Quantity</label>
                          <input type="number" class="form-control form-control-solid" placeholder="Enter quantity" name="fixed_quantity" >
                        </div>
                      </div>

                      <div class="col-md-3">
                        <div class="form-group">
                          <label>Unit Price</label>
                          <input type="text" class="form-control form-control-solid" readonly="" disabled="" value="{{ get_product_customer_price(@$p->product->item_prices,@Auth::user()->customer->price_list_num) }}" >
                        </div>
                      </div>

                      <div class="col-md-3">
                        <div class="form-group">
                          <label>Discount</label>
                          <input type="text" class="form-control form-control-solid" readonly="" disabled="" value="{{ $discount_percentage }}%" >
                        </div>
                      </div>

                      <div class="col-md-3">
                        <div class="form-group">
                          <label>Amount</label>
                          <input type="text" class="form-control form-control-solid" readonly="" disabled="" value="{{ get_product_customer_price(@$p->product->item_prices,@Auth::user()->customer->price_list_num,$discount_percentage) }}" >
                        </div>
                      </div>

                    </div>

                    {{-- @if(@$promotion->promotion_type->number_of_delivery > 0)
                      @for($i=1;$i<=$promotion->promotion_type->number_of_delivery;$i++)
                      <div class="row">
                        <div class="col-md-6 mt-5">
                          <div class="form-group">
                            <label>Quantity</label>
                            <input type="number" class="form-control form-control-solid" placeholder="Enter quantity" name="fixed_quantity" >
                          </div>
                        </div>
                      </div>
                      @endfor
                    @endif --}}

                    <div class="row mb-5">
                      <div class="col-md-12">
                        <hr>
                      </div>
                    </div>
                  @endforeach

                @endif

                <div class="row mb-5 mt-10">
                  <div class="col-md-12">
                    <div class="form-group">
                      {{-- <input type="submit" value="{{ isset($edit) ? "Update" : "Save" }}" class="btn btn-primary"> --}}
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/additional-methods.min.js"></script>

<script>
  $(document).ready(function() {

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
            fixed_quantity:{
              min:0,
              digits: true
            },
            number_of_delivery:{
              min:1,
              digits: true,
            },
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

      return validator;
    }
  
  });
</script>
@endpush