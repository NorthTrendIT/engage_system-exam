@extends('layouts.master')

@section('title','Order Details')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Order Details</h1>
      </div>

      <div class="d-flex align-items-center py-1">
        <a href="{{ route('sales-specialist-orders.index') }}" class="btn btn-sm btn-primary">Back</a>
      </div>

    </div>
  </div>

  <div class="post d-flex flex-column-fluid" id="kt_post">
    <div id="kt_content_container" class="container-xxl">
      <div class="row gy-5 g-xl-8">
        <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
          <div class="card card-xl-stretch mb-5 mb-xl-8">

            <div class="card-body">

              <div class="row mb-5 mt-5">
                <div class="col-md-12">
                  <div class="form-group">

                    <div class="flex-lg-row-fluid me-xl-18 mb-10 mb-xl-0">
                      <div class="mt-n1">

                        <div class="m-0">

                            <div class="row g-5 mb-11">
                                <div class="col-sm-5">
                                    <div class="fw-bold fs-7 text-gray-600 mb-1">Customer Name:</div>
                                    <div class="fw-bolder fs-6 text-gray-800">{{ $data->customer->card_name ?? '-' }} (Code: {{ $data->customer->card_code ?? '-' }})</div>
                                </div>

                                <div class="col-sm-2">
                                    <div class="fw-bold fs-7 text-gray-600 mb-1">Customer Branch:</div>
                                    <div class="fw-bolder fs-6 text-gray-800">{{ $data->customer->group->name ?? '-' }}</div>
                                </div>

                                <div class="col-sm-5">
                                    <div class="fw-bold fs-7 text-gray-600 mb-1">Delivery Address:</div>
                                    <div class="fw-bolder fs-6 text-gray-800">{{ $data->address->address ?? '-' }}</div>
                                </div>
                            </div>

                            <div class="row g-5 mb-11">
                                <div class="col-sm-5">
                                    <div class="fw-bold fs-7 text-gray-600 mb-1">Sales Specialist:</div>
                                    <div class="fw-bolder fs-6 text-gray-800">{{ @$data->sales_specialist->sales_specialist_name ?? "-" }}</div>
                                </div>

                                <div class="col-sm-2">
                                    <div class="fw-bold fs-7 text-gray-600 mb-1">Order #:</div>
                                    <div class="fw-bolder fs-6 text-gray-800">{{ @$data->doc_entry ?? "-"  }}</div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="fw-bold fs-7 text-gray-600 mb-1">Order Date:</div>
                                    <div class="fw-bolder fs-6 text-gray-800">{{ date('F d, Y',strtotime($data->doc_date)) }} {{ $data->doc_time ? date('H:i A',strtotime($data->doc_time)) : "" }}</div>
                                </div>

                                <div class="col-sm-2">
                                    <div class="fw-bold fs-7 text-gray-600 mb-1">Status:</div>
                                    <div class="fw-bolder fs-6 text-gray-800">
                                        <span>{{ !empty($data->doc_entry) ? getOrderStatusByDocEntry($data->doc_entry) : 'Pending' }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-5 mb-11">
                                <div class="col-sm-12">
                                    <div class="fw-bold fs-7 text-gray-600 mb-1">Remarks:</div>
                                    <div class="fw-bolder fs-6 text-gray-800">{{ @$data->u_remarks ?? "-" }}</div>
                                </div>
                            </div>

                            <div class="flex-grow-1">
                                <div class="table-responsive border-bottom mb-9">
                                    <table class="table mb-3">
                                        <thead>
                                        <tr class="border-bottom fs-6 fw-bolder text-muted">
                                            <th class="pb-2">No</th>
                                            <th class="min-w-175px pb-2">Product</th>
                                            <th class="min-w-70px text-end pb-2">Quantity</th>
                                            <th class="min-w-80px text-end pb-2">Price</th>
                                            <th class="min-w-80px text-end pb-2">Discount</th>
                                            <th class="min-w-100px text-end pb-2">Amount</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($data->items as $key => $value)
                                            <tr class="fw-bolder text-gray-700 fs-5 text-end">
                                                <td class="pt-6" style="text-align: initial !important;">{{ $key+1 }}</td>
                                                <td class="d-flex align-items-center pt-6">{{ $value->product->item_name ?? '-' }}</td>
                                                <td class="pt-6">{{ $value->quantity ?? '-' }}</td>
                                                <td class="pt-6">₱ {{ $value->price ?? '-' }}</td>
                                                <td class="pt-6">₱ 0.00 </td>
                                                <td class="pt-6 text-dark fw-boldest">₱ {{ $value->total ?? '-' }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <div class="mw-300px">
                                        <div class="d-flex flex-stack mb-3">
                                            <div class="fw-bold pe-10 text-gray-600 fs-7">Subtotal:</div>
                                            <div class="text-end fw-bolder fs-6 text-gray-700">₱ {{ $data->items()->sum('total'); }}</div>
                                        </div>

                                        <div class="d-flex flex-stack mb-3">
                                            <div class="fw-bold pe-10 text-gray-600 fs-7">Discount:</div>
                                            <div class="text-end fw-bolder fs-6 text-gray-700">- ₱ 0.00</div>
                                        </div>

                                        <div class="d-flex flex-stack">
                                            <div class="fw-bold pe-10 text-gray-600 fs-7 ">Total:</div>
                                            <div class="text-end fw-bolder fs-6 fw-boldest">₱ {{ $data->items()->sum('total'); }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                      </div>
                    </div>

                  </div>

                </div>
              </div>



            </div>
          </div>
        </div>
      </div>


      <!-- Access only for admin -->

    </div>
  </div>
</div>
@endsection

@push('css')

@endpush

@push('js')
<script>
</script>
@endpush
