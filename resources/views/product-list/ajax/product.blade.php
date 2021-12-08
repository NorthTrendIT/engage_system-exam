<div class="col-md-4 col-xl-4 col-sm-6 product-grid-outer">
  <div class="product-grid">
    <div class="product-image">
      <a href="{{ route('product-list.show',@$product->id) }}" class="image">
        @if(@$product->product_images && count(@$product->product_images))
          
          @php
            $image = @$product->product_images->first();
          @endphp

          @if($image->image && get_valid_file_url('sitebucket/products',$image->image))
            <img class="pic-1" src="{{ get_valid_file_url('sitebucket/products',$image->image) }}">
          @else
           <img class="pic-1" src="{{ asset('assets') }}/assets/media/img-1.jpg">
          @endif

        @else

          <img class="pic-1" src="{{ asset('assets') }}/assets/media/img-1.jpg">

        @endif

      </a>
      <ul class="product-links">
        <li>
          <a href="{{ route('product-list.show',@$product->id) }}" data-tip="Add to Wishlist">
            <i class="fas fa-heart"></i>
          </a>
        </li>
        <li>
          <a href="{{ route('product-list.show',@$product->id) }}" data-tip="Quick View">
            <i class="fa fa-search"></i>
          </a>
        </li>
      </ul>
    </div>
           
    <div class="product-content">
      <ul class="rating">
        <li class="fas fa-star"></li>
        <li class="fas fa-star"></li>
        <li class="fas fa-star"></li>
        <li class="far fa-star"></li>
        <li class="far fa-star"></li>
      </ul>
                  
      <h3 class="title">
        <a href="{{ route('product-list.show',@$product->id) }}">{{ @$product->item_name ?? "-" }}</a>
      </h3>
                  
      <div class="price">$66.00</div>
      <a class="add-to-cart" href="{{ route('product-list.show',@$product->id) }}">add to cart</a>
    </div>
  </div>
</div>