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
                                   <div class="tab-pane {{ $key == 0 ? "active" : "" }}" id="pic-{{ $key }}"><img src="{{ asset('assets') }}/assets/media/img-1.jpg" /></div>
                                  @endif
                                @endforeach
                              @else
                                <div class="tab-pane active" id="pic-"><img src="{{ asset('assets') }}/assets/media/img-1.jpg" /></div>
                              @endif
                            </div>
                            <ul class="preview-thumbnail nav nav-tabs">

                              @if(isset($product->product_images) && count($product->product_images) > 0)
                                @foreach($product->product_images as $key => $image)
                                  @if($image->image && get_valid_file_url('sitebucket/products',$image->image))
                                    <li class="{{ $key == 0 ? "active" : "" }}"><a data-target="#pic-{{ $key }}" data-toggle="tab"><img src="{{ get_valid_file_url('sitebucket/products',$image->image) }}" /></a></li>
                                  @else
                                   <li class="{{ $key == 0 ? "active" : "" }}"><a data-target="#pic-{{ $key }}" data-toggle="tab"><img src="{{ asset('assets') }}/assets/media/img-1.jpg" /></a></li>
                                  @endif
                                @endforeach
                              @else
                               <li class="active"><a data-target="#pic-" data-toggle="tab"><img src="{{ asset('assets') }}/assets/media/img-1.jpg" /></a></li>
                              @endif

                            </ul>
                            
                         </div>
                      <div class="col-md-6">
                   
                         <h5>{{ @$product->item_name ?? "" }}</h5>

                        @php
                          if(isset($promotion)){
                            $discount_fix_amount = false;

                            if(@$promotion->promotion_type->scope == "P"){

                              $discount_percentage = @$promotion->promotion_type->percentage;

                            }else if(@$promotion->promotion_type->scope == "U"){

                              $discount_percentage = @$promotion->promotion_type->percentage;
                              $discount_fix_amount = @$promotion->promotion_type->fixed_price;

                            }else if(@$promotion->promotion_type->scope == "R"){

                              $discount_percentage = @$data->discount_percentage;
                            }
                          }
                        @endphp

                          <p>
                            @if(@$discount_percentage)
                                @if(get_product_customer_price(@$product->item_prices,@Auth::user()->customer->price_list_num) > 0)
                                  <span class="mr-3 text-muted">(₱ <strike> <b>{{ get_product_customer_price(@$product->item_prices,@Auth::user()->customer->price_list_num) }}</strike></b>)</span>
                                @endif

                                <span class="mr-1 price"><strong>₱ {{ get_product_customer_price(@$product->item_prices,@Auth::user()->customer->price_list_num,@$discount_percentage,@$discount_fix_amount) }}</strong></span>
                            @else
                              <span class="mr-1 price"><strong>₱ {{ get_product_customer_price(@$product->item_prices,@Auth::user()->customer->price_list_num) }}</strong></span>
                            @endif
                          </p>
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
                                  <td>
                                    @if(@$promotion->promotion_type)
                                      @if(is_null($promotion->promotion_type->number_of_delivery))
                                      No Limit
                                      @else
                                      Fixed ({{ $promotion->promotion_type->number_of_delivery }})
                                      @endif
                                    @endif
                                  </td>
                                </tr>
                                <tr>
                                  <th class="pl-0 w-25" scope="row"><strong>Quantity</strong></th>
                                  <td>
                                    @if($promotion->promotion_type->is_fixed_quantity == false)
                                      No Limit
                                      @else

                                      @if($promotion->promotion_type->is_total_fixed_quantity)
                                        {{ $promotion->promotion_type->total_fixed_quantity }}
                                      @else
                                        {{ $data->fixed_quantity }}
                                      @endif
                                    @endif
                                  </td>
                                </tr>
                            </tbody>
                            </table>
                         </div>

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

<!--begin::Page Custom Javascript(used by this page)-->
<script src="{{ asset('assets') }}/assets/js/popper.min.js"></script>
<script src="{{ asset('assets') }}/assets/js/bootstrap.js"></script>
<!--end::Page Custom Javascript-->


<script>

</script>
@endpush