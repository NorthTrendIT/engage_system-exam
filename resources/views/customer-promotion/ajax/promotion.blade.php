<div class="col-md-4 col-xl-4 col-sm-6 product-grid-outer">
  <div class="product-grid">
  	<div class="p-2">

      @if(!in_array(userrole(), [2]))
        @php
          $interest = @$promotion->promotion_interests->firstWhere('user_id' , Auth::id());
        @endphp

        @if(@$interest->is_interested == 1)
    		  {{-- <a href="javascript:" data-value="0" data-id="{{ @$promotion->id }}" class="btn btn-light-danger btn_interest">Not Interested</a> --}}
          <a href="javascript:" class="btn btn-light-success"><i class="fa fa-check"></i> Interested</a>
        @else
    		  <a href="javascript:" data-value="1" data-id="{{ @$promotion->id }}" class="btn btn-light-success btn_interest">Interested</a>
          <a href="javascript:" class="btn btn-light-success interested_text_a" style="display:none;"><i class="fa fa-check"></i> Interested</a>
          <a href="javascript:" data-value="0" data-id="{{ @$promotion->id }}" class="btn btn-light-danger btn_interest">Not Interested</a>
        @endif
      @endif

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