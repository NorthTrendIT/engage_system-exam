@if(count($invoice_num) > 0)  
  @foreach($invoice_num as $k=>$val)
  @php
  $track_status = getStatusData(@$val, true);
  $track_date_array = @$track_status['date_array'];
  $track_status = @$track_status['status'];
  @endphp
<div class="hh-grayBox pb20">
  <!-- <div class="row justify-content-center mb-10 text-center">
    <h2><u>Delivery Status</u></h2>
  </div> --> 
  
  <div class="row justify-content-center">
    <div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 col-12">
      <p>{{$invoice_num[$k]->doc_num}}</p>
    </div>
    <div class="col-xl-1 col-lg-1 col-md-1 col-sm-12 col-12">
       <p>{{$invoice_item_details[$k]->quantity}}</p>
    </div>
    <div class="col-xl-9 col-lg-9 col-md-9 col-sm-12 col-12">
      <div class="order_view_status_details">
        <div class="order-tracking {{ in_array('On Process',getOrderStatusProcessArray($status)) ? "completed" : "" }}">
          <span class="is-complete"><img src="{{ asset('assets/assets/media/svg/order/on-process.svg') }}"></span>
          <p>On Process
            @if(@$date_array['On Process'])
              <br><span class="delivery-date">{{ date('F d, Y', strtotime(@$date_array['On Process']))}}</span>
            @endif
          </p>
        </div>
        <div class="order-tracking {{ in_array('For Delivery',getOrderStatusProcessArray($track_status)) ? "completed" : "" }}">
          <span class="is-complete"><img src="{{ asset('assets/assets/media/svg/order/for-delivery.svg') }}"></span>
          <p>For Delivery
            @if(@$track_date_array['For Delivery'] && $track_status != "On Process")
              <br><span class="delivery-date">{{ date('F d, Y', strtotime(@$track_date_array['For Delivery']))}}</span>
            @endif
          </p>
        </div>
        <div class="order-tracking {{ in_array('Delivered',getOrderStatusProcessArray($track_status)) ? "completed" : "" }}">
          <span class="is-complete"><img src="{{ asset('assets/assets/media/svg/order/delivered.svg') }}"></span>
          <p>Delivered
            @if(@$track_date_array['Delivered'] && $track_status != "On Process")
              <br><span class="delivery-date">{{ date('F d, Y', strtotime(@$track_date_array['Delivered']))}}</span>
            @endif
          </p>
        </div>
        <div class="order-tracking {{ in_array('Completed',getOrderStatusProcessArray($track_status)) ? "completed" : "" }}">
          <span class="is-complete">
            <img src="{{ asset('assets/assets/media/svg/order/completed.svg') }}">
          </span>
          <p>Completed
            @if(@$track_date_array['Completed'] && $track_status != "On Process")
              <br><span class="delivery-date">{{ date('F d, Y', strtotime(@$track_date_array['Completed']))}}</span>
            @endif
          </p>
        </div>
      </div>
    </div>
    
  </div>
</div>
@endforeach
@else

@endif