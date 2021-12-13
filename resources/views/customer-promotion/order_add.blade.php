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

                <input type="hidden" name="promotion_id" value="{{ $promotion->id }}">

                <div class="row mb-5">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Promotion Title</label>
                      <input type="text" class="form-control form-control-solid" value="{{ $promotion->title }}" readonly="" disabled="">
                    </div>
                  </div>
                </div>

                @if(@$promotion->promotion_type->is_total_fixed_quantity)
                <div class="row mb-5">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Total Fixed Quantity</label>
                      <input type="text" class="form-control form-control-solid total_fixed_quantity" value="{{ @$promotion->promotion_type->total_fixed_quantity }}" readonly="" disabled="">
                    </div>
                  </div>
                </div>
                @endif

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

                  <div class="product_list">
                    @php
                      $discount_percentage = 0;
                      $quantity = false;

                      if(@$promotion->promotion_type->scope == "P"){
                        $discount_percentage = @$promotion->promotion_type->percentage;
                      }elseif(@$promotion->promotion_type->scope == "R"){
                        $discount_percentage = @$p->discount_percentage;
                      }

                      if(@$promotion->promotion_type->is_fixed_quantity){

                        if(!@$promotion->promotion_type->is_total_fixed_quantity){
                          $quantity = @$p->fixed_quantity;
                        }
                      }

                      $amount = get_product_customer_price(@$p->product->item_prices,@Auth::user()->customer->price_list_num);
                      $total_amount = $discount_amount = get_product_customer_price(@$p->product->item_prices,@Auth::user()->customer->price_list_num,$discount_percentage);

                      $discount_amount = $amount - $discount_amount;

                      if($quantity){
                        if($total_amount > 0){
                          $total_amount = floatval($total_amount) * floatval($quantity);
                        }
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
                          <input type="number" class="form-control form-control-solid quantity" placeholder="Enter quantity" name="products[{{ @$p->product->id }}][quantity]" @if($quantity) readonly="" value="{{ $quantity }}" @else value="1" @endif min="1">
                        </div>
                      </div>

                      <div class="col-md-3">
                        <div class="form-group">
                          <label>Unit Price</label>
                          <input type="text" class="form-control form-control-solid unit_price" readonly="" disabled="" value="{{ $amount}}" name="unit_price" >
                        </div>
                      </div>

                      <div class="col-md-3">
                        <div class="form-group">
                          <label>Discount</label>
                          <input type="text" class="form-control form-control-solid discount_amount" readonly="" disabled="" value="{{ $discount_amount }}" data-value="{{ $discount_percentage }}" name="discount_amount">
                        </div>
                      </div>

                      <div class="col-md-3">
                        <div class="form-group">
                          <label>Amount</label>
                          <input type="text" class="form-control form-control-solid amount" readonly="" disabled="" value="{{ $total_amount }}" name="amount">
                        </div>
                      </div>

                    </div>

                    <div class="row mt-8">
                      <div class="col-md-12">
                      </div>
                    </div>

                    @if(@$promotion->promotion_type->number_of_delivery > 0)
                      @for($i=1;$i<=$promotion->promotion_type->number_of_delivery;$i++)
                      <div class="row">
                        <div class="col-md-3 mt-5">
                          <div class="form-group">
                            <label>{{ ordinal($i) }} Delivery Date</label>
                            <input type="text" class="form-control form-control-solid delivery_date" placeholder="Select {{ ordinal($i) }} Delivery Date" name="products[{{ @$p->product->id }}][delivery_date][{{ $i }}]" readonly="">
                          </div>
                        </div>

                        @if(@$promotion->promotion_type->number_of_delivery > 1)
                        <div class="col-md-3 mt-5">
                          <div class="form-group">
                            <label>{{ ordinal($i) }} Delivery Quantity</label>
                            <input type="number" class="form-control form-control-solid delivery_quantity {{ ($promotion->promotion_type->number_of_delivery - 1 == $i) ? "2nd_last_delivery_quantity" : "" }}" placeholder="Enter {{ ordinal($i) }} Delivery Quantity" name="products[{{ @$p->product->id }}][delivery_quantity][{{ $i }}]" min="1">
                          </div>
                        </div>
                        @endif

                      </div>
                      @endfor
                    @endif

                    <div class="row mb-5 mt-5">
                      <div class="col-md-12">
                        <span class="is-invalid quantity_error_span" style="display: none;">Oops! the total of delivery quantity is not the same as the quantity.</span>
                        <hr>
                      </div>
                    </div>
                  </div>
                  @endforeach
                @endif

                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Total Quantity</label>
                      <input type="number" class="form-control form-control-solid total_quantity" readonly="" disabled="">
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Total Discount</label>
                      <input type="number" class="form-control form-control-solid total_discount" readonly="" disabled="">
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Total Amount</label>
                      <input type="number" class="form-control form-control-solid total_amount" readonly="" disabled="">
                    </div>
                  </div>
                </div>

                <div class="row mb-5 mt-5 total_quantity_error_div" style="display: none;">
                  <div class="col-md-12">
                    <span class="is-invalid" >Oops! the total quantity is not the same as the total fix quantity.</span>
                    <hr>
                  </div>
                </div>

                <div class="row mb-5 mt-5 total_delivery_quantity_error_div" style="display: none;">
                  <div class="col-md-12">
                    <span class="is-invalid" >Oops! the total of quantity is not the same as the total of delivery quantity.</span>
                    <hr>
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

@push('css')
  <link rel="stylesheet" href="{{ asset('assets') }}/assets/css/datepicker.css" class="href">
@endpush

@push('js')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/additional-methods.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js" ></script>

<script>
  $(document).ready(function() {

    $('.delivery_date').datepicker({
        format: 'dd/mm/yyyy',
        todayHighlight: true,
        orientation: "bottom left",
        startDate:'{{ date('d/m/Y',strtotime($promotion->promotion_end_date)) }}',
        autoclose: true,
    });

    $(document).on('change', '.delivery_quantity', function(event) {
      event.preventDefault();

      // set zero last quantiy value
      if($(this).hasClass('2nd_last_delivery_quantity')){
        $(this).closest('.product_list').find('.delivery_quantity:eq(-1)').val(0);
      }

      var sum = 0;
      $(this).closest('.product_list').find('.delivery_quantity').each(function(){
        if(this.value != ""){
          sum += parseFloat(this.value);
        }
      });

      var quantity = $(this).closest('.product_list').find('.quantity').val();

      // Auto complete last quantiy value
      if($(this).hasClass('2nd_last_delivery_quantity')){
        var remain = parseInt(quantity) - parseInt(sum);
        if(remain < 0){
          remain = 0;
        }
        $(this).closest('.product_list').find('.delivery_quantity:eq(-1)').val(remain);

        sum += remain;
      }
        
      // Show error message
      if(sum != quantity){
        $(this).closest('.product_list').find('.quantity_error_span').show();
        $('[type="submit"]').prop('disabled', true);
      }else{
        $(this).closest('.product_list').find('.quantity_error_span').hide();
        $('[type="submit"]').prop('disabled', false);
      }

    });
    
    $(document).on('change', '.quantity', function(event) {
      event.preventDefault();

      var quantity = $(this).val();
      var unit_price = $(this).closest('.product_list').find('.unit_price').val();
      var discount_percentage = $(this).closest('.product_list').find('.discount_amount').attr('data-value');

      amount = (unit_price * quantity);

      discount_amount = (amount * discount_percentage) / 100;

      amount = ( amount - discount_amount );

      $(this).closest('.product_list').find('.discount_amount').val(discount_amount);

      $(this).closest('.product_list').find('.amount').val(amount);

      total_details_update();

      $(this).closest('.product_list').find('.delivery_quantity').val(0);

    });

    total_details_update();
    function total_details_update() {
      var sum = 0;
      $('.quantity').each(function(){
        if(this.value != ""){
          sum += parseFloat(this.value);
        }
      });
      $('.total_quantity').val(sum);

      var sum = 0;
      $('.discount_amount').each(function(){
        if(this.value != ""){
          sum += parseFloat(this.value);
        }
      });
      $('.total_discount').val(sum);

      var sum = 0;
      $('.amount').each(function(){
        if(this.value != ""){
          sum += parseFloat(this.value);
        }
      });
      $('.total_amount').val(sum);


      // Show error message
      @if(@$promotion->promotion_type->is_total_fixed_quantity)
        
        var total_fixed_quantity = parseInt($('.total_fixed_quantity').val());
        var total_quantity = parseInt($('.total_quantity').val());

        if(total_quantity != total_fixed_quantity){
          $('.total_quantity_error_div').show();
          $('[type="submit"]').prop('disabled', true);
        }else{
          $('.total_quantity_error_div').hide();
          $('[type="submit"]').prop('disabled', false);
        }
      @endif
    }

    // check quantity and delivery quantity
    function check_quantity_and_delivery_quantity() {
      var quantity = 0;
      $('.quantity').each(function(){
        if(this.value != ""){
          quantity += parseFloat(this.value);
        }
      }); 

      var delivery_quantity = 0;
      $('.delivery_quantity').each(function(){
        if(this.value != ""){
          delivery_quantity += parseFloat(this.value);
        }
      });

      // show error message
      if(quantity != delivery_quantity){
        //$('.total_delivery_quantity_error_div').show();
        //$('[type="submit"]').prop('disabled', true);
        return false;
      }else{
        //$('.total_delivery_quantity_error_div').hide();
        //$('[type="submit"]').prop('disabled', false);
        return true;
      }
    }

    $('body').on("submit", "#myForm", function (e) {
      e.preventDefault();
      var validator = validate_form();
      
      if(!check_quantity_and_delivery_quantity()){
        toast_error("Oops! the total of quantity is not the same as the total of delivery quantity.");
        return false;
      }

      if (validator.form() != false) {

        $('[type="submit"]').prop('disabled', true);
        $.ajax({
          url: "{{route('customer-promotion.order.store')}}",
          type: "POST",
          data: new FormData($("#myForm")[0]),
          async: false,
          processData: false,
          contentType: false,
          success: function (data) {
            if (data.status) {
              toast_success(data.message)
              setTimeout(function(){
                window.location.href = '{{ route('customer-promotion.order.index') }}';
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


      $('.delivery_quantity').each(function() {
        $(this).rules('add', {
          required:true,
          min:1,
          // max:$(this).closest('.product_list').find('.quantity').val(),
          digits: true
        });
      });

      $('.delivery_date').each(function() {
        $(this).rules('add', {
          required:true,
        });
      });

      return validator;
    }
  
  });
</script>
@endpush