<div class="col-md-4 col-xl-4 col-sm-6 product-grid-outer">
  <div class="product-grid">
    <div class="product-image">
      <a href="{{ route('customer-promotion.product-detail',['id' => @$promotion_type_product->id, 'promotion_id' => $promotion_id]) }}" target="_blank" class="image">
        @if(@$product->product_images && count(@$product->product_images))
          
          @php
            $image = @$product->product_images->first();
            $discount_fix_amount = false;
          @endphp

          @if($image->image && get_valid_file_url('sitebucket/products',$image->image))
            <img class="pic-1" src="{{ get_valid_file_url('sitebucket/products',$image->image) }}">
          @else
           <img class="pic-1" src="{{ asset('assets') }}/assets/media/img-1.jpg">
          @endif

        @else

          <img class="pic-1" src="{{ asset('assets') }}/assets/media/img-1.jpg">

        @endif

        @if(isset($promotion_type_product))
          <span class="product-discount-label">-
            @if(@$promotion_type_product->promotion_type->scope == "P")
              {{ @$promotion_type_product->promotion_type->percentage }}

              @php
                $discount_percentage = @$promotion_type_product->promotion_type->percentage;
              @endphp

            @elseif(@$promotion_type_product->promotion_type->scope == "U")
              {{ @$promotion_type_product->promotion_type->percentage }}

              @php
                $discount_percentage = @$promotion_type_product->promotion_type->percentage;
                $discount_fix_amount = @$promotion_type_product->promotion_type->fixed_price;
              @endphp

            @elseif(@$promotion_type_product->promotion_type->scope == "R")
              {{ @$promotion_type_product->discount_percentage }}

              @php
                $discount_percentage = @$promotion_type_product->discount_percentage;
              @endphp

            @endif%
          </span>
        @endif

      </a>
      <ul class="product-links">
        
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
        <a href="{{ route('customer-promotion.product-detail',['id' => @$promotion_type_product->id, 'promotion_id' => $promotion_id]) }}" target="_blank">{{ @$product->item_name ?? "-" }}</a>
      </h3>
      
      @if(@$discount_percentage)
        <div class="price">₱ 
          @if(get_product_customer_price(@$product->item_prices,@Auth::user()->customer->price_list_num) > 0)
            <span>{{ get_product_customer_price(@$product->item_prices,@Auth::user()->customer->price_list_num) }}</span> 
          @endif

          {{ get_product_customer_price(@$product->item_prices,@Auth::user()->customer->price_list_num,@$discount_percentage,@$discount_fix_amount) }}
        </div>
      @else
        <div class="price">₱ {{ get_product_customer_price(@$product->item_prices,@Auth::user()->customer->price_list_num) }}</div>
      @endif

      <a class="add-to-cart" href="{{ route('customer-promotion.product-detail',['id' => @$promotion_type_product->id, 'promotion_id' => $promotion_id]) }}" target="_blank">View more</a>
    </div>
  </div>
</div>