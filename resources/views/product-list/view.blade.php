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
                                   <div class="tab-pane {{ $key == 0 ? "active" : "" }}" id="pic-{{ $key }}"><img src="{{ asset('assets') }}/assets/media/img-1.jpg" /></div>
                                  @endif
                                @endforeach
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
                              @endif

                            </ul>
                            
                         </div>
                      <div class="col-md-6">
                   
                         <h5>{{ @$product->item_name ?? "" }}</h5>
                         <ul class="rating">
                            <li>
                            <i class="fas fa-star fa-sm text-primary"></i>
                            </li>
                            <li>
                            <i class="fas fa-star fa-sm text-primary"></i>
                            </li>
                            <li>
                            <i class="fas fa-star fa-sm text-primary"></i>
                            </li>
                            <li>
                            <i class="fas fa-star fa-sm text-primary"></i>
                            </li>
                            <li>
                            <i class="far fa-star fa-sm text-primary"></i>
                            </li>
                         </ul>
                         <p><span class="mr-1 price"><strong>$66.00</strong></span></p>
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
                         <div class="table-responsive mb-2">
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
                         </div>
                         <button type="button" class="btn btn-primary btn-md mr-1 mb-2">Buy now</button>
                         <button type="button" class="btn btn-light btn-md mr-1 mb-2"><i
                            class="fas fa-shopping-cart pr-2"></i>Add to cart</button>
                      </div>
                      </div>

                      <!-- Classic tabs -->
                      <div class=" product-desc classic-tabs border rounded px-4 pt-1">

                         <ul class="nav tabs-primary nav-justified" id="advancedTab" role="tablist">
                        
                         <li class="nav-item">
                            <a class="nav-link active show" id="info-tab" data-toggle="tab" href="#info" role="tab" aria-controls="info" aria-selected="false">Information</a>
                         </li>
                         <li class="nav-item">
                            <a class="nav-link" id="reviews-tab" data-toggle="tab" href="#reviews" role="tab" aria-controls="reviews" aria-selected="false">Reviews (1)</a>
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
                         <div class="tab-pane fade" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">
                            <h6><span>1</span> review for <span>{{ @$product->name ?? ""}}</span></h6>
                            <div class="media mt-3 mb-4">
                               <img class="d-flex mr-3 z-depth-1" src="{{ asset('assets') }}/assets/media/img-1.jpg" width="62" alt="Generic placeholder image">
                               <div class="media-body">
                               <div class="d-sm-flex justify-content-between">
                                  <p class="mt-1 mb-2">
                                     <strong>Marthasteward </strong>
                                     <span>– </span><span>January 28, 2020</span>
                                  </p>
                                  <ul class="rating mb-sm-0">
                                     <li>
                                     <i class="fas fa-star fa-sm text-primary"></i>
                                     </li>
                                     <li>
                                     <i class="fas fa-star fa-sm text-primary"></i>
                                     </li>
                                     <li>
                                     <i class="fas fa-star fa-sm text-primary"></i>
                                     </li>
                                     <li>
                                     <i class="fas fa-star fa-sm text-primary"></i>
                                     </li>
                                     <li>
                                     <i class="far fa-star fa-sm text-primary"></i>
                                     </li>
                                  </ul>
                               </div>
                               <p class="mb-0">Nice one, love it!</p>
                               </div>
                            </div>
                            <hr>
                            <h6 class="mt-4">Add a review</h6>
                            <p>Your email address will not be published.</p>
                            <div class="my-3">
                               <ul class="rating mb-0">
                               <li>
                                  <a href="#!">
                                     <i class="fas fa-star fa-sm text-primary"></i>
                                  </a>
                               </li>
                               <li>
                                  <a href="#!">
                                     <i class="fas fa-star fa-sm text-primary"></i>
                                  </a>
                               </li>
                               <li>
                                  <a href="#!">
                                     <i class="fas fa-star fa-sm text-primary"></i>
                                  </a>
                               </li>
                               <li>
                                  <a href="#!">
                                     <i class="fas fa-star fa-sm text-primary"></i>
                                  </a>
                               </li>
                               <li>
                                  <a href="#!">
                                     <i class="far fa-star fa-sm text-primary"></i>
                                  </a>
                               </li>
                               </ul>
                            </div>
                            <div>
                               <!-- Your review -->
                               <div class="md-form md-outline">
                               <textarea class="md-textarea form-control pr-6" rows="4" placeholder="Your review"></textarea>
                               
                               </div>
                               <!-- Name -->
                               <div class="md-form md-outline">
                               <input type="text" class="form-control pr-6" placeholder="Name">
                               
                               </div>
                               <!-- Email -->
                               <div class="md-form md-outline">
                               <input type="email"  class="form-control pr-6" placeholder="Email">
                               
                               </div>
                               <div class="text-right pb-2">
                               <button type="button" class="btn btn-primary">Add a review</button>
                               </div>
                            </div>
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