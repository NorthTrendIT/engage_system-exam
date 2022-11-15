@extends('layouts.master')

@section('title','Order Details')
<style type="text/css">
  .order_class td{min-width: 150px;}
  .order_class td:first-child{min-width: 80px;} 
  .order_class td:nth-child(4){min-width: 80px;}
  .order_class td:nth-child(5){min-width: 80px;}
  .order_class td:nth-child(2){min-width: 280px;}
  .order_class td:nth-child(3){min-width: 80px;}
  .order_class th.min-w-175px{min-width: auto !important;}
  .order_class th{text-align: center !important;}

  .order_class td {
      border-left: 2px solid #eff2f5 !important;
      padding: 5px 10px !important;
      
  }
</style>
@section('content')
@php
  $status = getOrderStatusByQuotation(@$data, true);
  $date_array = @$status['date_array'];
  $status = @$status['status'];
  use App\Models\SapConnectionApiFieldValue;

@endphp

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Order Details</h1>
      </div>

      <!--begin::Actions-->
      <div class="d-flex align-items-center py-1">

        @if($status != "Cancelled")
        <a href="javascript:" class="btn btn-sm btn-primary sync-details mr-10">Sync Details</a>
        @endif

        <a href="{{ route('orders.index') }}" class="btn btn-sm btn-primary">Back</a>
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
                          <!--begin::Label-->
                          <!-- <div class="fw-bolder fs-3 text-gray-800 mb-8">Order</div> -->
                          <!--end::Label-->



                          <!--begin::Row-->
                          <div class="row g-5 mb-1">
                            <!--end::Col-->
                            <div class="col-md-12">

                              {!! view('customer-promotion.ajax.delivery-status',compact('status', 'date_array')) !!}

                            </div>
                            <!--end::Col-->
                          </div>

                          <!--begin::Row-->


                          <div class="row g-5 mb-11">
                            <!--end::Col-->
                            <div class="col-sm-6">
                              <!--end::Label-->
                              <div class="fw-bold fs-7 text-gray-600 mb-1">Customer Name:</div>
                              <!--end::Label-->
                              <!--end::Text-->
                              <div class="fw-bolder fs-6 text-gray-800">{{ $data->customer->card_name ?? @$data->card_name ?? "-" }} (Code: {{ $data->customer->card_code ?? @$data->card_code ?? '-' }})</div>
                              <!--end::Text-->
                            </div>
                            <!--end::Col-->

                            <!--end::Col-->
                            <div class="col-sm-6">
                              <!--end::Label-->
                              <div class="fw-bold fs-7 text-gray-600 mb-1">Status:</div>
                              <!--end::Label-->
                              <!--end::Text-->
                              <div class="fw-bolder fs-6 text-gray-800">

                                <span class="mr-10">{!! getOrderStatusBtnHtml($status) !!}</span>

                                @if($status == "Pending" && !$data->customer_promotion_id)
                                  <a href="javascript:" class="btn btn-danger btn-sm cancel-order" title="Cancel Order">Cancel Order</a>
                                @else
                                  @if($status != "Cancelled")
                                    <button type="button" class="btn btn-danger btn-sm" title="Cancel Order" disabled>Cancel Order</button>
                                    <a href="javascript:" class="mx-2 text-dark" title="Promotion Orders can not be Cancelled! "><i class="fa fa-info-circle fs-6"></i></a>
                                  @endif
                                @endif

                                {{-- @if($status == "Completed" && !$date_array['Completed'] && in_array(userid(),[@$data->customer->user->id,1]))
                                  <a href="javascript:" class="btn btn-info btn-sm mark-as-completed-order" title="Mark as Completed">Mark as Completed</a>
                                @endif --}}

                              </div>
                              <!--end::Text-->
                            </div>
                            <!--end::Col-->

                            <!--end::Col-->
                            <div class="col-sm-6">
                              <!--end::Label-->
                              <div class="fw-bold fs-7 text-gray-600 mb-1">Sales Specialist:</div>
                              <!--end::Label-->
                              <!--end::Text-->
                              <div class="fw-bolder fs-6 text-gray-800">{{ @$data->sales_specialist->sales_specialist_name ?? "-" }}</div>
                              <!--end::Text-->
                            </div>
                            <!--end::Col-->

                            <!--end::Col-->
                            <div class="col-sm-6">
                              <!--end::Label-->
                              <div class="fw-bold fs-7 text-gray-600 mb-1">Delivery Address:</div>
                              <!--end::Label-->
                              <!--end::Text-->
                              <div class="fw-bolder fs-6 text-gray-800">{{ $data->address ?? '-' }}</div>
                              <!--end::Text-->
                            </div>
                            <!--end::Col-->

                            <!--end::Col-->
                            <div class="col-sm-6">
                              <!--end::Label-->
                              <div class="fw-bold fs-7 text-gray-600 mb-1">Order Type:</div>
                              <!--end::Label-->
                              <!--end::Text-->
                              <div class="fw-bolder fs-6 text-gray-800">{{ (!is_null($data->customer_promotion_id)) ? "Promotion" : "Standard" }}</div>
                              <!--end::Text-->
                            </div>
                            <!--end::Col-->                            

                            <!--end::Col-->
                            <div class="col-sm-6">
                              <!--end::Label-->
                              <div class="fw-bold fs-7 text-gray-600 mb-1">Order Date:</div>
                              <!--end::Label-->
                              <!--end::Col-->
                              <div class="fw-bolder fs-6 text-gray-800">{{ date('F d, Y',strtotime($data->doc_date)) }} {{ $data->doc_time ? date('H:i A',strtotime($data->doc_time)) : "" }}</div>
                              <!--end::Col-->
                            </div>
                            <!--end::Col-->

                            <!--end::Col-->
                            <div class="col-sm-6">
                              <!--end::Label-->
                              <div class="fw-bold fs-7 text-gray-600 mb-1">Customer Branch:</div>
                              <!--end::Label-->
                              <!--end::Text-->
                              <div class="fw-bolder fs-6 text-gray-800">{{ $data->customer->group->name ?? '-' }}</div>
                              <!--end::Text-->
                            </div>
                            <!--end::Col-->

                            <!--end::Col-->
                            <div class="col-sm-6">
                              <!--end::Label-->
                              <div class="fw-bold fs-7 text-gray-600 mb-1">Delivery Date:</div>
                              <!--end::Label-->
                              <!--end::Col-->
                              <div class="fw-bolder fs-6 text-gray-800">{{(isset($data->order->invoice->u_commitment))?date('F d, Y',strtotime(@$data->order->invoice->u_commitment)) : '-'}}</div>
                              <!--end::Col-->
                            </div>
                            <!--end::Col-->

                            <!--end::Col-->
                            <div class="col-sm-6">
                              <!--end::Label-->
                              <div class="fw-bold fs-7 text-gray-600 mb-1">Order #:</div>
                              <!--end::Label-->
                              <!--end::Col-->
                              <div class="fw-bolder fs-6 text-gray-800">{{ '#'.@$data->doc_entry ?? "-"  }}</div>
                              <!--end::Col-->
                            </div>
                            <!--end::Col-->
                            @if(!in_array(userrole(),[4]))
                            <!--end::Col-->
                            <div class="col-sm-6">
                              <!--end::Label-->
                              <div class="fw-bold fs-7 text-gray-600 mb-1">Quotation #:</div>
                              <!--end::Label-->
                              <!--end::Col-->
                              <div class="fw-bolder fs-6 text-gray-800">{{ @$data->doc_num ?? "-"  }}</div>
                              <!--end::Col-->
                            </div>
                            @endif
                            <!--end::Col-->
                          </div>

                          <div class="row g-5 mb-11">

                           
                            <!-- <div class="col-sm-2">
                              <div class="fw-bold fs-7 text-gray-600 mb-1">Reference #:</div>
                             
                              <div class="fw-bolder fs-6 text-gray-800">{{ @$data->num_at_card ?? "-"  }}</div>
                            </div> -->

                            

                          </div>
                          <!--end::Row-->


                          <div class="row g-5 mb-11">
                            

                            @if(!is_null($data->customer_promotion_id))
                            <!--end::Col-->
                            <div class="col-sm-5">
                              <!--end::Label-->
                              <div class="fw-bold fs-7 text-gray-600 mb-1">Promotion Title:</div>
                              <!--end::Label-->
                              <!--end::Text-->
                              <div class="fw-bolder fs-6 text-gray-800">{{ $data->customer_promotion->promotion->title ?? '-' }}</div>
                              <!--end::Text-->
                            </div>
                            <!--end::Col-->

                            <!--end::Col-->
                            <div class="col-sm-5">
                              <!--end::Label-->
                              <div class="fw-bold fs-7 text-gray-600 mb-1">Promotion Code:</div>
                              <!--end::Label-->
                              <!--end::Text-->
                              <div class="fw-bolder fs-6 text-gray-800">{{ $data->customer_promotion->promotion->code ?? '-' }}</div>
                              <!--end::Text-->
                            </div>
                            <!--end::Col-->
                            @endif

                            
                          </div>
                          

                          <hr>

                          <!--begin::Content-->
                          <div class="flex-grow-1 mt-10">
                            <!--begin::Table-->
                            <div class="table-responsive border-bottom mb-9">
                              <table class="table mb-3 order_class">
                                <thead>
                                  <tr class="border-bottom fs-6 fw-bolder text-muted">
                                    <th>#</th>
                                    <th class="min-w-175px pb-2">Product</th>
                                    <th>Unit</th>
                                    <th class="min-w-175px pb-2">Ordered Quantity</th>
                                    <th class="min-w-175px pb-2">Served Quantity</th>
                                    @if($data->order_type == 'Promotion')
                                    <th class="min-w-80px text-end pb-2">Promo Delivery Date</th>
                                    @endif
                                    <th class="min-w-70px text-end pb-2">Invoice</th>
                                    <th class="min-w-80px text-end pb-2">Price</th>
                                    <th class="min-w-80px text-end pb-2">Price After VAT</th>
                                    <th class="min-w-100px text-end pb-2">Amount</th>
                                    <th class="min-w-100px text-end pb-2">Line Status</th>
                                    <th class="min-w-100px text-end pb-2">Line Remarks</th>
                                  </tr>
                                </thead>
                                <tbody>                                    
                                  @foreach($invoiceDetails as $k=>$val)
                                  <tr class="fw-bolder text-gray-700 fs-5">
                                    <td>{{$val['key']}}</td>
                                    <td>{{$val['product']}}</td>
                                    <td>{{$val['unit']}}</td>
                                    <td>{{$val['order_quantity']}}</td>
                                    <td>{{$val['serverd_quantity']}}</td>
                                    @if($data->order_type == 'Promotion')
                                        <td>{{$val['promotion']}}</td>
                                    @endif
                                    <td>{{$val['invoice_num']}}</td>
                                    <td>{{$val['price']}}</td>
                                    <td>{{$val['price_after_vat']}}</td>
                                    <td>{{$val['amount']}}</td>
                                    <td>{{$val['line_status']}}</td>
                                    <td>{{$val['line_remarks']}}</td>
                                  </tr>
                                  @endforeach
                                </tbody>
                              </table>
                            </div>
                            <!--end::Table-->
                            <!--begin::Container-->
                            <div class="row">
                              <div class="col-sm-12 col-md-6 custom_remarks_order">Remark: {{ @$orderRemarks->remarks ?? "-" }}</div>
                              <!-- <div class="col-sm-4 col-md-4 d-flex align-items-center justify-content-center">
                                <p>Note: Prices may be subjected with discount. Final amount of order will reflect on the actual invoice.</p>
                              </div> -->
                              <div class="col-sm-12 col-md-6 d-flex align-items-center justify-content-center">
                                                              
                                  <div class="total">
                                    <div class="d-flex justify-content-end">
                                      <!--begin::Section-->
                                      <div class="mw-300px">
                                        {{-- <!--begin::Item-->
                                        <div class="d-flex flex-stack mb-3">
                                          <!--begin::Accountname-->
                                          <div class="fw-bold pe-10 text-gray-600 fs-7">Subtotal:</div>
                                          <!--end::Accountname-->
                                          <!--begin::Label-->
                                          <div class="text-end fw-bolder fs-6 text-gray-700">₱ {{ number_format_value(@$data->doc_total) }}</div>
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
                                        <!--end::Item--> --}}

                                        <!--begin::Item-->
                                        <div class="d-flex flex-stack">
                                          <!--begin::Code-->
                                          <div class="fw-bold pe-10 text-gray-900 fs-7 ">Total Weight:</div>
                                          <!--end::Code-->
                                          <!--begin::Label-->
                                          <div class="text-end fw-bolder fs-6 fw-boldest">{{ number_format(@$Weight).' Kg'}}</div>
                                          <!--end::Label-->
                                        </div>
                                        <!--end::Item-->

                                        <!--begin::Item-->
                                        <div class="d-flex flex-stack">
                                          <!--begin::Code-->
                                          <div class="fw-bold pe-10 text-gray-900 fs-7 ">Total Volume:</div>
                                          <!--end::Code-->
                                          <!--begin::Label-->
                                          <div class="text-end fw-bolder fs-6 fw-boldest">{{  number_format(@$Volume) }}</div>
                                          <!--end::Label-->
                                        </div>
                                        <!--end::Item-->

                                        <!--begin::Item-->
                                        <div class="d-flex flex-stack">
                                          <!--begin::Code-->
                                          <div class="fw-bold pe-10 text-gray-900 fs-4 ">Total:</div>
                                          <!--end::Code-->
                                          <!--begin::Label-->
                                          <div class="text-end fw-bolder fs-4 fw-boldest">₱ {{ number_format_value(round(@$data->doc_total,1)) }}</div>
                                          <!--end::Label-->
                                        </div>
                                        <!--end::Item-->
                                      </div>
                                      <!--end::Section-->
                                    </div>
                                  </div>                              
                              </div>
                            <!--end::Container-->
                          </div>
                          <br>
                          <div class="row">
                            <div class="col-md-6"></div>
                            <div class="col-md-6 d-flex align-items-center justify-content-center">
                              <p>Note: Prices may be subjected with discount. Final <br>amount of order will reflect on the actual invoice.</p>
                            </div>
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

      @if(!empty(@$data->order->invoice->completed_date) && userrole() == 1)
      <div class="row gy-5 g-xl-8">
        <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
          <div class="card card-xl-stretch mb-5 mb-xl-8">
            <div class="card-header border-0 pt-5 min-0">
              <h5 class="text-info">Customer Order Remarks</h5>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <p>{{ @$data->order->invoice->completed_remarks }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      @endif
      
      <!-- Access only for admin-->
      @if($status == "Completed" && !$date_array['Completed'] && in_array(userid(),[@$data->customer->user->id]) && @$data->order->invoice->id && empty(@$data->order->invoice->completed_date))
      <div class="row gy-5 g-xl-8">
        <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
          <div class="card card-xl-stretch mb-5 mb-xl-8">
            <div class="card-header border-0 pt-5 min-0">
              <h5 class="text-info">Complete Order</h5>
            </div>
            <div class="card-body">
              <form id="myForm" method="post">
                @csrf
                <input type="hidden" name="id" value="{{ @$data->id }}">
                <div class="row">
                  <div class="col-md-6 mt-5">
                    <div class="form-group">
                      <label><input type="checkbox" name="is_accept" class="form-check-input mr-10" value="1" title="Mark the Order as Completed" checked>Mark the Order as Completed.<span class="asterisk">*</span></label>
                    </div>
                  </div>
                  
                </div>

                <div class="row">
                  <div class="col-md-6 mt-5">
                    <div class="form-group">
                      <label>Remarks</label>
                      <textarea class="form-control form-control-lg form-control-solid" name="remarks" placeholder="Enter your remarks"></textarea>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-6 mt-5">
                    <div class="form-group">
                      <button type="submit" class="btn btn-success mt-6">Save</button>
                    </div>
                  </div>
                </div>

              </form>
            </div>
          </div>
        </div>
      </div>
      @endif

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
                    id:'',
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


    $(document).on('click', '.sync-details', function(event) {
      event.preventDefault();

      Swal.fire({
        title: 'Are you sure want to sync details?',
        text: "It may take some time to sync details.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, do it!'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: '{{ route('orders.sync-specific-orders') }}',
            method: "POST",
            data: {
                    _token:'{{ csrf_token() }}',
                    id:'{{ $data->id }}'
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

    @if($status == "Pending" && !$data->customer_promotion_id)
      $(document).on('click', '.cancel-order', function(event) {
        event.preventDefault();

        Swal.fire({
          title: 'Are you sure want to cancel order ?',
          text: "Once canceled, you will not be able to recover this record!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, do it!'
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              url: '{{ route('orders.cancel-order') }}',
              method: "POST",
              data: {
                      _token:'{{ csrf_token() }}',
                      id:'{{ $data->id }}'
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
    @endif

    @if($status == "Completed" && !$date_array['Completed'] && in_array(userid(),[@$data->customer->user->id,1]) && @$data->order->invoice->id && empty(@$data->order->invoice->completed_date))
      /*$(document).on('click', '.mark-as-completed-order', function(event) {
        event.preventDefault();

        Swal.fire({
          title: 'Are you sure want to complete order ?',
          text: "Once completed, you will not be able to recover this record!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, do it!'
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              url: '{{--{{ route('orders.complete-order') }} --}}',
              method: "POST",
              data: {
                      _token:'{{--{{ csrf_token() }} --}}',
                      id:'{{--{{ $data->id }} --}}'
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
      });*/

      $('body').on("submit", "#myForm", function (e) {
        e.preventDefault();
        var validator = validate_form();
        
        if (validator.form() != false) {

          Swal.fire({
            title: 'Are you sure want to complete order ?',
            text: "Once completed, you will not be able to recover this record!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, update it!'
          }).then((result) => {
            if (result.isConfirmed) {
              $('[type="submit"]').prop('disabled', true);
              $.ajax({
                url: "{{route('orders.complete-order')}}",
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
        }
      });

      function validate_form(){
        var validator = $("#myForm").validate({
            errorClass: "is-invalid",
            validClass: "is-valid",
            rules: {
              department_id:{
                required:true,
              },
              remarks:{
                maxlength:300,
              },
            },
            messages: {
              
            },
        });
        return validator;
      }

    @endif

  });

</script>
@endpush
