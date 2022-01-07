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
                                    <div class="tab-pane {{ $key == 0 ? "active" : "" }}" id="pic-{{ $key }}"><img src="{{ get_valid_file_url('sitebucket/products',$image->image) }}" /></div>
                                  @else
                                   <div class="tab-pane {{ $key == 0 ? "active" : "" }}" id="pic-{{ $key }}"><img src="{{ asset('assets') }}/assets/media/product_default.jpg" /></div>
                                  @endif
                                @endforeach
                              @else
                                <div class="tab-pane active" id="pic"><img src="{{ asset('assets') }}/assets/media/product_default.jpg" /></div>
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
                         <p><span class="mr-1 price"><strong>₱ {{ get_product_customer_price(@$product->item_prices,@Auth::user()->customer->price_list_num) }}</strong></span></p>
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
                         @if(userdepartment() != 1)
                             <button type="button" class="btn btn-primary btn-md mr-1 mb-2">Buy now</button>
                             @if(is_in_cart(@$product->id) == 1)
                             <a class="btn btn-light btn-md mr-1 mb-2" href="{{ route('cart.index') }}">
                                 <i class="fas fa-shopping-cart pr-2"></i>Go to cart
                             </a>
                             @else
                             <button type="button" class="btn btn-light btn-md mr-1 mb-2 addToCart" data-url="{{ route('cart.add',@$product->id) }}">
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
      </div>
    </div>
  </div>
</div>
@endsection

@push('css')

@endpush

@push('js')

<script>
$(document).ready(function() {
    $(document).on('click', '.addToCart', function(event) {
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
});
</script>
@endpush
