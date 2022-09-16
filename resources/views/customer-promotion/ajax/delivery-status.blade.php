<div class="hh-grayBox pb20">
  <div class="row justify-content-center mb-10 text-center">
    <h2><u>Delivery Status</u></h2>
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
  <div class="row justify-content-between">
    <div class="order-tracking {{ in_array('Pending',getOrderStatusProcessArray($status)) ? "completed" : "" }}">
      <span class="is-complete"><img src="{{ asset('assets/assets/media/svg/order/pending.svg') }}"></span>
      <p>Pending
        @if(@$date_array['Pending'])
          <br><span class="delivery-date">{{ date('F d, Y', strtotime(@$date_array['Pending']))}}</span>
        @endif
      </p>
    </div>
    <div class="order-tracking {{ in_array('On Process',getOrderStatusProcessArray($status)) ? "completed" : "" }}">
      <span class="is-complete"><img src="{{ asset('assets/assets/media/svg/order/on-process.svg') }}"></span>
      <p>On Process
        @if(@$date_array['On Process'])
          <br><span class="delivery-date">{{ date('F d, Y', strtotime(@$date_array['On Process']))}}</span>
        @endif
      </p>
    </div>
    <div class="order-tracking {{ in_array('For Delivery',getOrderStatusProcessArray($status)) ? "completed" : "" }}">
      <span class="is-complete"><img src="{{ asset('assets/assets/media/svg/order/for-delivery.svg') }}"></span>
      <p>For Delivery
        @if(@$date_array['For Delivery'] && $status != "On Process")
          <br><span class="delivery-date">{{ date('F d, Y', strtotime(@$date_array['For Delivery']))}}</span>
        @endif
      </p>
    </div>
    <div class="order-tracking {{ in_array('Delivered',getOrderStatusProcessArray($status)) ? "completed" : "" }}">
      <span class="is-complete"><img src="{{ asset('assets/assets/media/svg/order/delivered.svg') }}"></span>
      <p>Delivered
        @if(@$date_array['Delivered'] && $status != "On Process")
          <br><span class="delivery-date">{{ date('F d, Y', strtotime(@$date_array['Delivered']))}}</span>
        @endif
      </p>
    </div>
    <div class="order-tracking {{ in_array('Completed',getOrderStatusProcessArray($status)) ? "completed" : "" }}">
      <span class="is-complete">
        <img src="{{ asset('assets/assets/media/svg/order/completed.svg') }}">
      </span>
      <p>Completed
        @if(@$date_array['Completed'] && $status != "On Process")
          <br><span class="delivery-date">{{ date('F d, Y', strtotime(@$date_array['Completed']))}}</span>
        @endif
      </p>
    </div>
  </div>
  @endif
</div>