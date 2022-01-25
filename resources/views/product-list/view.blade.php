@extends('layouts.master')

@section('title','Product View')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Product View</h1>
      </div>

      <!--begin::Actions-->
      <div class="d-flex align-items-center py-1">
        <!--begin::Button-->
        <a href="{{ route('product-list.index') }}" class="btn btn-sm btn-primary">Back</a>
        <!--end::Button-->
      </div>
      <!--end::Actions-->

    </div>
  </div>

  <div class="post d-flex flex-column-fluid" id="kt_post">
    <div id="kt_content_container" class="container-xxl">
      <div class="row gy-5 g-xl-8">
        <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">

          <!--begin::Tables Widget 9-->
          <div class="card card-xl-stretch mb-5 mb-xl-8 product-detail-outer">
             <!--begin::Body-->
             <div class="card-body py-3 product-detail">
                <!-- product detail -->
                   <!--Section: Block Content-->
                   <section class="mb-5 ">

                      <div class="row">
                         <div class="preview col-md-6">

                           <div class="preview-pic tab-content">
                              @if(isset($product->product_images) && count($product->product_images) > 0)
                                @foreach($product->product_images as $key => $image)
                                  @if($image->image && get_valid_file_url('sitebucket/products',$image->image))
                                    <div class="tab-pane {{ $key == 0 ? "active" : "" }}" id="pic-{{ $key }}"><img class="product-detail-image" src="{{ get_valid_file_url('sitebucket/products',$image->image) }}" /></div>
                                  @else
                                   <div class="tab-pane {{ $key == 0 ? "active" : "" }}" id="pic-{{ $key }}"><img class="product-detail-image" src="{{ asset('assets') }}/assets/media/product_default.jpg" /></div>
                                  @endif
                                @endforeach
                              @else
                                <div class="tab-pane active" id="pic"><img class="product-detail-image" src="{{ asset('assets') }}/assets/media/product_default.jpg" /></div>
                              @endif
                           </div>
                           <ul class="preview-thumbnail nav nav-tabs">

                              @if(isset($product->product_images) && count($product->product_images) > 0)
                                 @foreach($product->product_images as $key => $image)
                                  @if($image->image && get_valid_file_url('sitebucket/products',$image->image))
                                    <li class="{{ $key == 0 ? "active" : "" }}"><a data-target="#pic-{{ $key }}" data-toggle="tab"><img src="{{ get_valid_file_url('sitebucket/products',$image->image) }}" /></a></li>
                                  @else
                                   <li class="{{ $key == 0 ? "active" : "" }}"><a data-target="#pic-{{ $key }}" data-toggle="tab"><img src="{{ asset('assets') }}/assets/media/product_default.jpg" /></a></li>
                                  @endif
                                 @endforeach
                              @else
                                <li class="active"><a data-target="#pic" data-toggle="tab"><img src="{{ asset('assets') }}/assets/media/product_default.jpg" /></a></li>
                              @endif

                           </ul>

                         </div>
                      <div class="col-md-6">

                         <h5>{{ @$product->item_name ?? "" }}</h5>

                         @if($customer)
                         <p><span class="mr-1 price"><strong>₱ {{ get_product_customer_price(@$product->item_prices,@$customer->price_list_num) }}</strong></span></p>
                         @endif
                         <p class="pt-1">{!! @$product->technical_specifications ?? "" !!}</p>
                         <div class="table-responsive">
                            <table class="table table-sm table-borderless mb-0">
                            <tbody>
                               <tr>
                                  <th class="pl-0 w-25" scope="row"><strong>Code</strong></th>
                                  <td>{{ @$product->item_code ?? "" }}</td>
                               </tr>
                               <tr>
                                  <th class="pl-0 w-25" scope="row"><strong>Delivery</strong></th>
                                  <td>-</td>
                               </tr>
                            </tbody>
                            </table>
                         </div>
                         <hr>
                         <!-- <div class="table-responsive mb-2">
                            <table class="table table-sm table-borderless">
                            <tbody>
                               <tr>
                                  <td class="pl-0 pb-0 w-25">Quantity</td>
                               </tr>
                               <tr>
                                  <td class="pl-0">
                                  <div class="def-number-input number-input safari_only mb-0">
                                     <button onclick="this.parentNode.querySelector('input[type=number]').stepDown()"
                                        class="minus"></button>
                                     <input class="quantity" min="0" name="quantity" value="1" type="number">
                                     <button onclick="this.parentNode.querySelector('input[type=number]').stepUp()"
                                        class="plus"></button>
                                  </div>
                                  </td>

                               </tr>
                            </tbody>
                            </table>
                         </div> -->
                         @if(userdepartment() != 1 && $customer)
                             <button type="button" class="btn btn-primary btn-md mr-1 mb-2">Buy now</button>
                             @if(is_in_cart(@$product->id) == 1)
                             <a class="btn btn-light btn-md mr-1 mb-2" href="{{ route('cart.index') }}">
                                 <i class="fas fa-shopping-cart pr-2"></i>Go to cart
                             </a>
                             @else
                             <button type="button" class="btn btn-light btn-md mr-1 mb-2 add_to_cart" data-url="{{ route('cart.add',@$product->id) }}">
                                 <i class="fas fa-shopping-cart pr-2"></i>Add to cart
                             </button>
                             @endif
                        @endif
                      </div>
                      </div>

                      <!-- Classic tabs -->
                      <div class=" product-desc classic-tabs border rounded px-4 pt-1">

                         <ul class="nav tabs-primary nav-justified" id="advancedTab" role="tablist">

                         <li class="nav-item">
                            <a class="nav-link active show" id="info-tab" data-toggle="tab" href="#info" role="tab" aria-controls="info" aria-selected="false">Information</a>
                         </li>

                         </ul>
                         <div class="tab-content" id="advancedTabContent">

                         <div class="tab-pane fade show active" id="info" role="tabpanel" aria-labelledby="info-tab">
                            <h6>Additional Information</h6>
                            <table class="table table-striped table-bordered mt-3">
                               <thead>

                               </thead>
                               <tbody>
                                <tr>
                                  <th scope="row" class=" dark-grey-text h6">Technical Specifications:</th>
                                  <td>{!! @$product->technical_specifications ?? "" !!}</td>
                               </tr>
                               <tr>
                                  <th scope="row" class=" dark-grey-text h6">Product Features:</th>
                                  <td>{!! @$product->product_features ?? "" !!}</td>
                               </tr>
                               <tr>
                                  <th scope="row" class=" dark-grey-text h6">Product Benefits:</th>
                                  <td>{!! @$product->product_benefits ?? "" !!}</td>
                               </tr>
                               <tr>
                                  <th scope="row" class=" dark-grey-text h6">Product Sell Sheets:</th>
                                  <td>{!! @$product->product_sell_sheets ?? "" !!}</td>
                               </tr>
                               </tbody>
                            </table>
                         </div>

                         </div>

                      </div>
                      <!-- Classic tabs -->

                   </section>
                   <!--Section: Block Content-->
                <!-- end detail list -->
             </div>
             <!--begin::Body-->
          </div>
          <!--end::Tables Widget 9-->

        </div>

        @if($customer)
        <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
            <div class="card card-xl-stretch mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">
                    <h5>Recommended Products</h5>
                </div>
                <div class="card-body">
                    <div class="row tns tns-default" id="product_list_row">
                        @php $products = getRecommendedProducts(); @endphp
                        @if(!empty($products) && count($products) > 0)
                        <div class="tns-outer my-slider"
                            data-tns="true"
                            data-tns-loop="true"
                            data-tns-swipe-angle="false"
                            data-tns-speed="500"
                            data-tns-autoplay="true"
                            data-tsn-autowidth="false"
                            data-tns-autoplay-timeout="18000"
                            data-tns-controls="true"
                            data-tns-nav="false"
                            data-tns-items="3"
                            data-tns-center="false"
                            data-tns-dots="false"
                            data-tns-prev-button="#kt_team_slider_prev1"
                            data-tns-next-button="#kt_team_slider_next1">
                        @foreach($products as $item)
                            <div class="product-grid-outer px-5 py-5">
                                <div class="product-grid">
                                    <div class="product-image">
                                        <a href="{{ route('product-list.show',@$item->product->id) }}" class="image">
                                            @if(@$item->product->product_images && count(@$item->product->product_images))

                                            @php
                                                $image = @$item->product->product_images->first();
                                            @endphp

                                            @if($image->image && get_valid_file_url('sitebucket/products',$image->image))
                                                <img class="pic-1" src="{{ get_valid_file_url('sitebucket/products',$image->image) }}" >
                                            @else
                                            <img class="pic-1" src="{{ asset('assets') }}/assets/media/product_default.jpg">
                                            @endif

                                            @else

                                            <img class="pic-1" src="{{ asset('assets') }}/assets/media/product_default.jpg">

                                            @endif

                                        </a>
                                        <ul class="product-links">
                                            <li>
                                            <a href="{{ route('product-list.show',@$item->product->id) }}" data-tip="Quick View">
                                                <i class="fa fa-search"></i>
                                            </a>
                                            </li>
                                        </ul>
                                    </div>

                                    <div class="product-content">
                                        <h3 class="title">
                                            <a href="{{ route('product-list.show',@$item->product->id) }}">{{ @$item->product->item_name ?? "-" }}</a>
                                        </h3>
                                        
                                        <div class="price">₱ {{ get_product_customer_price(@$item->product->item_prices,@$customer->price_list_num) }}</div>
                                        @if(userdepartment() != 1)
                                        @if(is_in_cart(@$item->product->id) == 1)
                                            <a class="add-to-cart" href="{{ route('cart.index') }}">Go to cart</a>
                                        @else
                                            <a href="javascript:;" class="add-to-cart addToCart" data-url="{{ route('cart.add',@$item->product->id) }}">Add to Cart</a>
                                            <a class="add-to-cart goToCart" href="{{ route('cart.index') }}" style="display:none">Go to cart</a>
                                        @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        </div>
                        <button class="btn btn-icon btn-active-color-primary" id="kt_team_slider_prev1">
                            <span class="svg-icon svg-icon-3x">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path d="M11.2657 11.4343L15.45 7.25C15.8642 6.83579 15.8642 6.16421 15.45 5.75C15.0358 5.33579 14.3642 5.33579 13.95 5.75L8.40712 11.2929C8.01659 11.6834 8.01659 12.3166 8.40712 12.7071L13.95 18.25C14.3642 18.6642 15.0358 18.6642 15.45 18.25C15.8642 17.8358 15.8642 17.1642 15.45 16.75L11.2657 12.5657C10.9533 12.2533 10.9533 11.7467 11.2657 11.4343Z" fill="black"></path>
                                </svg>
                            </span>
                        </button>

                        <button class="btn btn-icon btn-active-color-primary" id="kt_team_slider_next1">
                            <span class="svg-icon svg-icon-3x">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path d="M12.6343 12.5657L8.45001 16.75C8.0358 17.1642 8.0358 17.8358 8.45001 18.25C8.86423 18.6642 9.5358 18.6642 9.95001 18.25L15.4929 12.7071C15.8834 12.3166 15.8834 11.6834 15.4929 11.2929L9.95001 5.75C9.5358 5.33579 8.86423 5.33579 8.45001 5.75C8.0358 6.16421 8.0358 6.83579 8.45001 7.25L12.6343 11.4343C12.9467 11.7467 12.9467 12.2533 12.6343 12.5657Z" fill="black"></path>
                                </svg>
                            </span>
                        </button>
                        @else
                        <div class='text-center mt-5'><h2>Products Not Found !</h2></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection

@push('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tiny-slider/2.9.4/tiny-slider.css">
<!--[if (lt IE 9)]><script src="https://cdnjs.cloudflare.com/ajax/libs/tiny-slider/2.9.4/min/tiny-slider.helper.ie8.js"></script><![endif]-->
@endpush

@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/tiny-slider/2.9.2/min/tiny-slider.js"></script>
<script>
$(document).ready(function() {


    $(document).on('click', '.add_to_cart', function(event) {
        event.preventDefault();
        $url = $(this).attr('data-url');
        $.ajax({
            url: $url,
            method: "POST",
            data: {
                _token:'{{ csrf_token() }}'
                }
        })
        .done(function(result) {
            if(result.status == false){
                toast_error(result.message);
            }else{
                toast_success(result.message);
                setTimeout(function(){
                    window.location.reload();
                },1500)
            }
        })
        .fail(function() {
            toast_error("error");
        });
    });

    $(document).on('click', '.addToCart', function(event) {
      event.preventDefault();
      $url = $(this).attr('data-url');
      $addToCartBtn = $(this);
      $goToCartBtn = $(this).parent().find('.goToCart');
        $.ajax({
            url: $url,
            method: "POST",
            data: {
                    _token:'{{ csrf_token() }}'
                    }
            })
            .done(function(result) {
                if(result.status == false){
                    toast_error(result.message);
                }else{
                    $addToCartBtn.hide();
                    $goToCartBtn.show();
                    toast_success(result.message);
                    // setTimeout(function(){
                    //     window.location.reload();
                    // },1500)
                }
            })
            .fail(function() {
                toast_error("error");
            });
    });
});
</script>
@endpush
