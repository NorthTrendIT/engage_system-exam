@extends('layouts.master')

@section('title','Pending Orders')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Pending Order</h1>
      </div>

      <!--begin::Actions-->
      <div class="d-flex align-items-center py-1">

        <a href="{{ route('draft-order.index') }}" class="btn btn-sm btn-primary">Back</a>
        <!--end::Button-->
      </div>
      <!--end::Actions-->

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

                    <!--begin::Content-->
                    <div class="flex-lg-row-fluid me-xl-18 mb-10 mb-xl-0">
                      <!--begin::Invoice 2 content-->
                      <div class="mt-n1">

                        <!--begin::Wrapper-->
                        <div class="m-0">

                          <div class="row g-5 mb-11">
                            <div class="col-sm-4">
                              <div class="fw-bold fs-7 text-gray-600 mb-1">Customer Name</div>
                              <div class="fw-bolder fs-6 text-gray-800">{{ $data->customer->card_name ?? '-' }}</div>
                            </div>

                            <div class="col-sm-3">
                              <div class="fw-bold fs-7 text-gray-600 mb-1">Order Date:</div>
                              <div class="fw-bolder fs-6 text-gray-800">{{  date('F d, Y',strtotime($data->created_at))  }}</div>
                            </div>

                          </div>
                          <!--end::Row-->
                          <!--begin::Content-->
                          <div class="flex-grow-1">
                            <!--begin::Table-->
                            <div class="table-responsive border-bottom mb-9">
                              <table class="table mb-3">
                                <thead>
                                  <tr class="border-bottom fs-6 fw-bolder text-muted">
                                    <th class="min-w-175px pb-2">Product</th>
                                    <th class="min-w-70px text-end pb-2">Quantity</th>
                                    <th class="min-w-80px text-end pb-2">Price</th>
                                    <th class="min-w-80px text-end pb-2">Discount</th>
                                    <th class="min-w-100px text-end pb-2">Amount</th>
                                  </tr>
                                </thead>
                                <tbody>
                                    @foreach($data->items as $value)
                                    <tr class="fw-bolder text-gray-700 fs-5 text-end">
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
                            <!--end::Table-->
                            <!--begin::Container-->
                            <div class="d-flex justify-content-end">
                              <!--begin::Section-->
                              <div class="mw-300px">
                                <!--begin::Item-->
                                <div class="d-flex flex-stack mb-3">
                                  <!--begin::Accountname-->
                                  <div class="fw-bold pe-10 text-gray-600 fs-7">Subtotal:</div>
                                  <!--end::Accountname-->
                                  <!--begin::Label-->
                                  <div class="text-end fw-bolder fs-6 text-gray-700">₱ {{ $data->items()->sum('total'); }}</div>
                                  <!--end::Label-->
                                </div>
                                <!--end::Item-->
                                <!--begin::Item-->
                                <div class="d-flex flex-stack mb-3">
                                  <!--begin::Accountname-->
                                  <div class="fw-bold pe-10 text-gray-600 fs-7">Discount:</div>
                                  <!--end::Accountname-->
                                  <!--begin::Label-->
                                  <div class="text-end fw-bolder fs-6 text-gray-700">- ₱ 0.00</div>
                                  <!--end::Label-->
                                </div>
                                <!--end::Item-->

                                <!--begin::Item-->
                                <div class="d-flex flex-stack">
                                  <!--begin::Code-->
                                  <div class="fw-bold pe-10 text-gray-600 fs-7 ">Total:</div>
                                  <!--end::Code-->
                                  <!--begin::Label-->
                                  <div class="text-end fw-bolder fs-6 fw-boldest">₱ {{ $data->items()->sum('total'); }}</div>
                                  <!--end::Label-->
                                </div>
                                <!--end::Item-->
                              </div>
                              <!--end::Section-->
                            </div>
                            <!--end::Container-->
                          </div>
                          <!--end::Content-->
                        </div>
                        <!--end::Wrapper-->
                      </div>
                      <!--end::Invoice 2 content-->
                    </div>
                    <!--end::Content-->

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
