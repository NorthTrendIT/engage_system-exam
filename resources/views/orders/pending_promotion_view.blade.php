@extends('layouts.master')

@section('title','Pending Promotions')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Claimed Promotions</h1>
      </div>

      <!--begin::Actions-->
      <div class="d-flex align-items-center py-1">

        <a href="{{ route('customer-promotion.order.index') }}" class="btn btn-sm btn-primary">Back</a>
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
           {{--  <div class="card-header border-0 pt-5">
              <h5>{{ isset($edit) ? "Update" : "Add" }} Details</h5>
            </div> --}}
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
                          <!--begin::Label-->
                          <div class="fw-bolder fs-3 text-gray-800 mb-8">Invoice</div>
                          <!--end::Label-->

                          @if(userrole() == 1)
                          <!--begin::Row-->
                          <div class="row g-5 mb-11">
                            <!--end::Col-->
                            <div class="col-sm-9">
                              <!--end::Label-->
                              <div class="fw-bold fs-7 text-gray-600 mb-1">Customer:</div>
                              <!--end::Label-->
                              <!--end::Col-->
                              <div class="fw-bolder fs-6 text-gray-800">{{ @$data->user->sales_specialist_name ?? "" }}</div>
                              <!--end::Col-->
                            </div>
                            <!--end::Col-->

                            @if($data->status == "approved" && $data->is_sap_pushed == false)
                            <!--end::Col-->
                            <div class="col-sm-3">
                              <div class="fw-bold fs-7 text-gray-600 mb-1">Push Details In SAP:</div>
                              <!--end::Label-->
                              <!--end::Col-->
                              <div class="fw-bolder fs-6 text-gray-800">
                                <a href="javascript:" class="btn btn-sm btn-info btn-inline push-in-sap">Push Details</a>
                              </div>
                              <!--end::Col-->
                            </div>
                            <!--end::Col-->
                            @endif

                          </div>
                          <!--end::Row-->
                          @endif

                          <!--begin::Row-->
                          <div class="row g-5 mb-11">
                            <!--end::Col-->
                            <div class="col-sm-4">
                              <!--end::Label-->
                              <div class="fw-bold fs-7 text-gray-600 mb-1">Claimed Date:</div>
                              <!--end::Label-->
                              <!--end::Col-->
                              <div class="fw-bolder fs-6 text-gray-800">{{ date('F d, Y',strtotime($data->created_at)) }}</div>
                              <!--end::Col-->
                            </div>
                            <!--end::Col-->

                            <!--end::Col-->
                            <div class="col-sm-4">
                              <!--end::Label-->
                              <div class="fw-bold fs-7 text-gray-600 mb-1">Promotion Details:</div>
                              <!--end::Label-->
                              <!--end::Text-->
                              <div class="fw-bolder fs-6 text-gray-800">{{ @$data->promotion->title ?? "-" }}</div>
                              <!--end::Text-->
                            </div>
                            <!--end::Col-->

                            <!--end::Col-->
                            <div class="col-sm-4">
                              <!--end::Label-->
                              <div class="fw-bold fs-7 text-gray-600 mb-1">Status:</div>
                              <!--end::Label-->
                              <!--end::Text-->
                              <div class="fw-bolder fs-6 text-gray-800">

                                @if($data->status == "approved")
                                  <a href="javascript:" class="btn btn-sm btn-success btn-inline ">Approved</a>
                                @elseif($data->status == "pending")
                                  <a href="javascript:" class="btn btn-sm btn-warning btn-inline ">Pending</a>
                                @elseif($data->status == "canceled")
                                  <a href="javascript:" class="btn btn-sm btn-danger btn-inline ">Canceled</a>
                                @endif

                              </div>
                              <!--end::Text-->
                            </div>
                            <!--end::Col-->
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

                                  @if(@$data->products)

                                    @foreach(@$data->products as $p)
                                      <tr class="fw-bolder text-gray-700 fs-5 text-end">
                                        <td class="d-flex align-items-center pt-6">{{ @$p->product->item_name ?? "-" }}</td>
                                        <td class="pt-6">{{ @$p->quantity }}</td>
                                        <td class="pt-6">₱ {{ number_format(@$p->price,2) }}</td>
                                        <td class="pt-6">₱ {{ number_format(@$p->discount,2) }}</td>
                                        <td class="pt-6 text-dark fw-boldest">₱ {{ number_format(@$p->amount,2) }}</td>
                                      </tr>
                                    @endforeach

                                  @endif

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
                                  <div class="text-end fw-bolder fs-6 text-gray-700">₱ {{ number_format(@$data->total_price,2) }}</div>
                                  <!--end::Label-->
                                </div>
                                <!--end::Item-->
                                <!--begin::Item-->
                                <div class="d-flex flex-stack mb-3">
                                  <!--begin::Accountname-->
                                  <div class="fw-bold pe-10 text-gray-600 fs-7">Discount:</div>
                                  <!--end::Accountname-->
                                  <!--begin::Label-->
                                  <div class="text-end fw-bolder fs-6 text-gray-700">- ₱ {{ number_format(@$data->total_discount,2) }}</div>
                                  <!--end::Label-->
                                </div>
                                <!--end::Item-->

                                <!--begin::Item-->
                                <div class="d-flex flex-stack">
                                  <!--begin::Code-->
                                  <div class="fw-bold pe-10 text-gray-600 fs-7 ">Total:</div>
                                  <!--end::Code-->
                                  <!--begin::Label-->
                                  <div class="text-end fw-bolder fs-6 fw-boldest">₱ {{ number_format(@$data->total_amount,2) }}</div>
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


                        <!--begin::Wrapper-->
                        <div class="mt-20">

                          <hr>

                          <!--begin::Label-->
                          <div class="fw-bolder fs-3 text-gray-800 mb-8 mt-2">Delivery details</div>
                          <!--end::Label-->
                          <!--begin::Row-->
                          <div class="row g-5 mb-11">

                            <!--end::Col-->
                            <div class="col-sm-12">
                              <!--end::Label-->
                              <div class="fw-bold fs-7 text-gray-600 mb-1">Address:</div>
                              <!--end::Label-->
                              <!--end::Col-->
                              <div class="fw-bolder fs-6 text-gray-800">{{ @$data->customer_bp_address->address ?? "-" }}</div>

                              <div class="fw-bold fs-7 text-gray-600">{{ @$data->customer_bp_address->street ?? "" }}</div>
                              <!--end::Col-->
                            </div>
                            <!--end::Col-->

                          </div>
                          <!--end::Row-->

                          @if(@$data->products)
                            @foreach(@$data->products as $key => $p)
                            <!--begin::Content-->
                            <div class="flex-grow-1 mt-20">
                              <h5 class="mb-5"> {{ ordinal($key + 1) }} Product: <i>{{ @$p->product->item_name ?? "-" }}</i></h5>
                              <!--begin::Table-->
                              <div class="table-responsive border-bottom mb-9">
                                <table class="table mb-3">
                                  <thead>
                                    <tr class="border-bottom fs-6 fw-bolder text-muted">
                                      <th class="min-w-175px pb-2">Delivery Date</th>
                                      <th class="min-w-70px text-end pb-2">Delivery Quantity</th>
                                    </tr>
                                  </thead>
                                  <tbody>

                                    @foreach(@$p->deliveries as $d)
                                      <tr class="fw-bolder text-gray-700 fs-5 text-end">
                                        <td class="d-flex align-items-center pt-6">{{ date('F d, Y',strtotime($d->delivery_date)) }}</td>
                                        <td class="pt-6">{{ @$d->delivery_quantity }}</td>
                                      </tr>
                                    @endforeach

                                  </tbody>
                                </table>
                              </div>
                              <!--end::Table-->
                            </div>
                            <!--end::Content-->
                            @endforeach
                          @endif

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


    </div>
  </div>
</div>
@endsection

@push('css')

@endpush

@push('js')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/additional-methods.min.js"></script>


<script>

  $(document).ready(function() {
    $('body').on("submit", "#myForm", function (e) {
      e.preventDefault();
      var validator = validate_form();

      if (validator.form() != false) {

        $('[type="submit"]').prop('disabled', true);
        $.ajax({
          url: "{{route('customer-promotion.order.status')}}",
          type: "POST",
          data: new FormData($("#myForm")[0]),
          async: false,
          processData: false,
          contentType: false,
          success: function (data) {
            if (data.status) {
              toast_success(data.message)
              setTimeout(function(){
                window.location.reload();
              },500)
            } else {
              toast_error(data.message);
              $('[type="submit"]').prop('disabled', false);
            }
          },
          error: function () {
            toast_error("Something went to wrong !");
            $('[type="submit"]').prop('disabled', false);
          },
        });
      }
    });


    function validate_form(){
      var validator = $("#myForm").validate({
          errorClass: "is-invalid",
          validClass: "is-valid",
          rules: {
            status:{
              required:true,
            },
            cancel_reason:{
              required: function () {
                        if($('[name="status"]').find('option:selected').val() == 'canceled'){
                          return true;
                        }else{
                          return false;
                        }
                    },
            }
          },
          messages: {

          },
      });
      return validator;
    }

    $(document).on('click', '.push-in-sap', function(event) {
      event.preventDefault();

      Swal.fire({
        title: 'Are you sure want to do this?',
        //text: "Once deleted, you will not be able to recover this record!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, do it!'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: '{{ route('customer-promotion.order.push-in-sap') }}',
            method: "POST",
            data: {
                    _token:'{{ csrf_token() }}',
                    id:'{{ @$data->id }}',
                  }
          })
          .done(function(result) {
            if(result.status == false){
              toast_error(result.message);
            }else{
              toast_success(result.message);
              setTimeout(function(){
                window.location.reload();
              },500)
            }
          })
          .fail(function() {
            toast_error("error");
          });
        }
      })
    });
  });

</script>
@endpush
