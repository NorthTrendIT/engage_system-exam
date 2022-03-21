<div class="hh-grayBox pt45 pb20">
  @if($status == "Cancelled")
  <div class="row justify-content-center">
    <div class="order-tracking completed">
      <span class="is-complete"><img src="{{ asset('assets/assets/media/svg/order/pending.svg') }}"></span>
      <p>Pending</p>
    </div>

    <div class="order-tracking completed cancelled">
      <span class="is-cancelled">
        <img src="{{ asset('assets/assets/media/svg/order/cancelled.svg') }}">
      </span>
      <p>Cancelled</p>
    </div>
  </div>
  @else
  <div class="row justify-content-between">
    <div class="order-tracking {{ in_array('Pending',getOrderStatusProcessArray($status)) ? "completed" : "" }}">
      <span class="is-complete"><img src="{{ asset('assets/assets/media/svg/order/pending.svg') }}"></span>
      <p>Pending</p>
    </div>
    <div class="order-tracking {{ in_array('On Process',getOrderStatusProcessArray($status)) ? "completed" : "" }}">
      <span class="is-complete"><img src="{{ asset('assets/assets/media/svg/order/on-process.svg') }}"></span>
      <p>On Process</p>
    </div>
    <div class="order-tracking {{ in_array('For Delivery',getOrderStatusProcessArray($status)) ? "completed" : "" }}">
      <span class="is-complete"><img src="{{ asset('assets/assets/media/svg/order/for-delivery.svg') }}"></span>
      <p>For Delivery</p>
    </div>
    <div class="order-tracking {{ in_array('Delivered',getOrderStatusProcessArray($status)) ? "completed" : "" }}">
      <span class="is-complete"><img src="{{ asset('assets/assets/media/svg/order/delivered.svg') }}"></span>
      <p>Delivered</p>
    </div>
    <div class="order-tracking {{ in_array('Completed',getOrderStatusProcessArray($status)) ? "completed" : "" }}">
      <span class="is-complete">
        <img src="{{ asset('assets/assets/media/svg/order/completed.svg') }}">
      </span>
      <p>Completed</p>
    </div>
  </div>
  @endif
  <div class="row justify-content-center mt-10 text-center">
    <span>Delivery Status</span>
  </div>
</div>