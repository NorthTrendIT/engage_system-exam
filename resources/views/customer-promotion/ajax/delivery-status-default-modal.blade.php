<div class="d-flex justify-content-between">
    <h5>{{$product}}<h5>
    <h5>Total Qty Ordered: {{$orderd_quantity}}</h5>
</div> 
<div class="hh-grayBox pb20">
  <div class="row justify-content-center">
    <div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 col-12">
      <h6>Invoice No</h6>
    </div> 
     <div class="col-xl-1 col-lg-1 col-md-1 col-sm-12 col-12">
       <h6>Qty</h6>
    </div>
    <div class="col-xl-9 col-lg-9 col-md-9 col-sm-12 col-12 d-flex justify-content-center">
      <h6>Status</h6>
    </div> 
  </div>
  <div class="row justify-content-center mb-10 text-center">
    
  </div>
  @if($status == "Cancelled")
  <div class="row justify-content-center">
    <div class="order-tracking completed">
      <span class="is-complete"><img src="{{ asset('assets/assets/media/svg/order/pending.svg') }}"></span>
      <p>Pending
        @if(@$date_array['Pending'])
          <br><span class="delivery-date">{{ date('F d, Y', strtotime(@$date_array['Pending']))}}</span>
        @endif
      </p>
    </div>

    <div class="order-tracking completed cancelled">
      <span class="is-cancelled">
        <img src="{{ asset('assets/assets/media/svg/order/cancelled.svg') }}">
      </span>
      <p>Cancelled
        @if(@$date_array['Cancelled'])
          <br><span class="delivery-date">{{ date('F d, Y', strtotime(@$date_array['Cancelled']))}}</span>
        @endif
      </p>
    </div>
  </div>
  @else
  <div class="row justify-content-center">
    <div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 col-12">
      <p>No Invoice Yet</p>
    </div>
    <div class="col-xl-1 col-lg-1 col-md-1 col-sm-12 col-12">
       <p>0</p>
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
        <div class="order-tracking">
          <span class="is-complete"><img src="{{ asset('assets/assets/media/svg/order/for-delivery.svg') }}"></span>
          <p>For Delivery
            
          </p>
        </div>
        <div class="order-tracking">
          <span class="is-complete"><img src="{{ asset('assets/assets/media/svg/order/delivered.svg') }}"></span>
          <p>Delivered
            
          </p>
        </div>
        <div class="order-tracking">
          <span class="is-complete">
            <img src="{{ asset('assets/assets/media/svg/order/completed.svg') }}">
          </span>
          <p>Completed
           
          </p>
        </div>
      </div>
    </div>
  </div>
  @endif
</div>
<div class="d-flex justify-content-between">
    <h5>Total Delivered Qty: 0</h5>
</div> 