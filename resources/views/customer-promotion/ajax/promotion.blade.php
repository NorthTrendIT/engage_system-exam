<div class="col-md-4 col-xl-4 col-sm-6 product-grid-outer">
  <div class="product-grid">
  	<div class="p-2">
  		<a href="" class="btn btn-light-success">Interested</a>
  		<a href="" class="btn btn-light-danger">Not Interested</a>
  	</div>

    <div class="product-image">
      	<a href="{{ route('customer-promotion.show',@$promotion->id) }}" class="image">

          @if($promotion->promo_image && get_valid_file_url('sitebucket/promotion',$promotion->promo_image))
            <img class="pic-1" src="{{ get_valid_file_url('sitebucket/promotion',$promotion->promo_image) }}">
          @else
           <img class="pic-1" src="{{ asset('assets') }}/assets/media/img-1.jpg">
          @endif
      	</a>
    </div>
           
    <div class="product-content">
                  
      	<h3 class="title">
        	<a href="{{ route('customer-promotion.show',@$promotion->id) }}">{{ @$promotion->title ?? "-" }}</a>
      	</h3>

      	<a href="{{ route('customer-promotion.show',@$promotion->id) }}" class="btn btn-success">Learn more</a>
    </div>

  </div>
</div>