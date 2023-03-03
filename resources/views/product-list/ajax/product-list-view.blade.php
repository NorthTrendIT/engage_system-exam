<div class="col-md-12 col-xl-12 col-sm-12 product-grid-outer">
    <div class="product-grid row">
        <div class="col-md-6 col-xl-6 col-sm-12">
        <div class="product-image">
            <a href="{{ route('product-list.show',@$product->id) }}" class="image">
                @if(@$product->product_images && count(@$product->product_images))
                @php
                    $image = @$product->product_images->first();
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
                {{-- <li>
                <a href="{{ route('product-list.show',@$product->id) }}" data-tip="Add to Wishlist">
                    <i class="fas fa-heart"></i>
                </a>
                </li> --}}
                <li>
                <a href="{{ route('product-list.show',@$product->id) }}" data-tip="Quick View">
                    <i class="fa fa-search"></i>
                </a>
                </li>
            </ul>
        </div>
        </div>

        <div class="col-md-6 col-xl-6 col-sm-12">
        <div class="product-content">
            <h3 class="title">
                <a href="{{ route('product-list.show',@$product->id) }}">{{ @$product->item_name ?? "-" }}</a>
            </h3>

            <div class="price">₱ {{ get_product_customer_price(@$product->item_prices,@$customer->price_list_num) }}</div>
            @if(userdepartment() != 1)
            @if(is_in_cart(@$product->id) == 1)
                <a class="add-to-cart" href="{{ route('cart.index') }}">Go to cart</a>
            @else
                <a href="javascript:;" class="add-to-cart addToCart" data-url="{{ route('cart.add',@$product->id) }}">Add to Cart</a>
                <a class="add-to-cart goToCart" href="{{ route('cart.index') }}" style="display:none">Go to cart</a>
            @endif
            @endif
        </div>
        </div>
    </div>
</div>
