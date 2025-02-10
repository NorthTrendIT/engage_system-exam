@if(in_array(userrole(),[1,10,11]))
  <select name="order_approval" class="form-select" id="orderApproval">
    <option value="Pending">Pending</option>
    <option value="Approve">Approve</option>
    <option value="Reject" @if($data->approval === 'Rejected') selected @endif>{{($data->approval === 'Rejected')? $data->approval : 'Reject'}}</option>
  </select>
@else

@php
  $orderStatBg = '';
  switch ($data->approval) {
    case 'Rejected':
        $orderStatBg = 'bg-danger';
        break;
    default:
        $orderStatBg = 'bg-dark';
        break;
  }
@endphp

<span class="badge {{$orderStatBg}} fs-6">{{$data->approval}}</span>

@endif