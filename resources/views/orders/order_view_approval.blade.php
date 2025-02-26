@extends('layouts.master')

@section('title','Order Details')
<style type="text/css">
  .order_class td{min-width: 150px;}
  .order_class td:first-child{min-width: 180px;} 
  .order_class td:nth-child(4){min-width: 80px;}
  .order_class td:nth-child(5){min-width: 80px;}
  .order_class td:nth-child(6){min-width: 80px;}
  .order_class td:nth-child(7){min-width: 80px;}
  .order_class td:nth-child(2){min-width: 45px;}
  .order_class td:nth-child(3){min-width: 280px;}
  .order_class th.min-w-175px{min-width: auto !important;}
  .order_class th{text-align: center !important;}

  .order_class td {
      /*border-left: 2px solid #eff2f5 !important;*/
      padding: 5px 10px !important;
      
  }

  .custom_td_order{
    width: 200px;
    height: 50px;
    max-width: 200px;
    min-width: 21px !important;
    max-height: 50px;
    min-height: 50px;
  }

  .table-responsive {
    scrollbar-color: #c1c1c1 transparent;
  }

  @media (min-width: 992px) {
      div, ol, pre, span, ul {
          scrollbar-width: revert !important;
      }
  }


  .tableFixHead          { overflow: auto; height: 378px; }
  .tableFixHead thead th { position: sticky; top: 0; z-index: 1; background-color: white}

  /* Just common table stuff. Really. */
  table  { border-collapse: collapse; width: 100%; }
  th, td { padding: 8px 16px;  vertical-align: middle;  }
  th     { background:#eee; }

  .note_order{
    width: 6% !important;
  }
  @media only screen and (max-width: 944px) {
    .note_order{
      width: 18% !important;
    }
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
                  {{-- <div class="form-group"> --}}

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

                              {!! view('customer-promotion.ajax.delivery-status',compact('status', 'date_array', 'data')) !!}

                            </div>
                            <!--end::Col-->
                          </div>

                          <!--begin::Row-->


                          <div class="row g-5 mb-11">
                            <!--end::Col-->
                            <div class="col-sm-6">
                              <div class="row">
                                <div class="fw-bold fs-7 text-gray-600 mb-1">Customer Name:</div>
                                <div class="fw-bolder fs-6 text-gray-800">{{ $data->customer->card_name ?? @$data->card_name ?? "-" }} (Code: {{ $data->customer->card_code ?? @$data->card_code ?? '-' }})</div>
                              </div>
                              <div class="row mt-5 d-none">
                                <div class="fw-bold fs-7 text-gray-600 mb-1">Sales Specialist:</div>
                                <div class="fw-bolder fs-6 text-gray-800">{{ @$data->sales_specialist->sales_specialist_name ?? "-" }}</div>
                              </div>
                              <div class="row mt-5">
                                <div class="fw-bold fs-7 text-gray-600 mb-1">Order Type:</div>
                                <div class="fw-bolder fs-6 text-gray-800">{{ (!is_null($data->customer_promotion_id)) ? "Promotion" : "Standard" }}</div>
                              </div>
                              <div class="row mt-5">
                                <div class="fw-bold fs-7 text-gray-600 mb-1">Customer Branch:</div>
                                <div class="fw-bolder fs-6 text-gray-800">{{ $data->customer->group->name ?? '-' }}</div>
                              </div>
                              <div class="row mt-5">
                                <div class="fw-bold fs-7 text-gray-600 mb-1">Order #:</div>
                                <div class="fw-bolder fs-6 text-gray-800">-</div>
                              </div>

                              <div class="row mt-5">
                                <div class="fw-bold fs-7 text-gray-600 mb-1">Order Approval</div>
                                <div class="fw-bolder fs-7 text-gray-800 col-md-4">
                                  {!! view('orders.order_status',compact('data')) !!}
                                </div>
                              </div>

                              @if($data->approval === "Rejected" && userrole() != 4 )
                              <div class="row mt-5">
                                <div class="fw-bold fs-7 text-gray-600 mb-1">Reason of Rejection</div>
                                <div class="fw-bolder fs-7 text-gray-800 col-md-4">
                                  {{ $data->disapproval_remarks}}
                                </div>
                              </div>
                              @endif

                            </div>
                            <!--end::Col-->


                            <!--end::Col-->
                            <div class="col-sm-6">
                              <div class="row">
                                <div class="fw-bold fs-7 text-gray-600 mb-1">Delivery Address:</div>
                                <div class="fw-bolder fs-6 text-gray-800">{{ $data->customer->address ?? '-' }}</div>
                              </div>
                              <div class="row mt-5">
                                <div class="col-sm-6">
                                  <div class="fw-bold fs-7 text-gray-600 mb-1">Order Date:</div>
                                  <div class="fw-bolder fs-6 text-gray-800">{{ date('F d, Y',strtotime($data->created_at)) }} {{ $data->doc_time ? date('H:i A',strtotime($data->doc_time)) : "" }}</div>
                                </div>
                                <div class="col-sm-6">
                                  <div class="fw-bold fs-7 text-gray-600 mb-1">Expected Delivery Date:</div>
                                  <div class="fw-bolder fs-6 text-gray-800">{{ $data->due_date ? date('F d, Y',strtotime($data->due_date)) : "" }} </div>
                                </div>
                              </div>
                              <div class="row mt-5">
                                <div class="fw-bold fs-7 text-gray-600 mb-1">Delivery Date:</div>
                                <div class="fw-bolder fs-6 text-gray-800">{{(isset($data->order->invoice->u_commitment))?date('F d, Y',strtotime(@$data->order->invoice->u_commitment)) : '-'}}</div>
                              </div>
                              
                              @if(!in_array(userrole(),[4]))
                              <div class="row mt-5">
                                <div class="fw-bold fs-7 text-gray-600 mb-1">SAP Quotation #:</div>
                                <div class="fw-bolder fs-6 text-gray-800">{{ @$data->doc_num ?? "-"  }}</div>
                              </div>
                              @endif


                            </div>
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
                            <input type="checkbox" name="checked_served_checkbox" id="checked_served_checkbox"> Show Ltrs/Kgs
                            <div class="">
                              <div class="table-responsive border-bottom mb-9 tableFixHead">
                                
                                <table class="table table-striped table-bordered mb-3 order_class align-middle">
                                  <thead>
                                    <tr class="border-bottom fs-6 fw-bolder text-white">
                                      <th style="min" class="bg-dark">Action</th>
                                      <th class="bg-dark">#</th>
                                      <th class="min-w-175px pb-2 product_details bg-dark">Product</th>
                                      <th class="bg-dark">Unit</th>
                                      <th class="min-w-70px text-end pb-2 bg-dark">Invoice</th> 
                                      <th class="min-w-175px pb-2 bg-dark">Ordered Quantity</th>
                                      <th class="min-w-175px pb-2 bg-dark">Served Quantity</th>
                                      <th class="min-w-175px pb-2 ordered_served_class bg-dark" style="display:none;">Ordered Ltr/Kgs</th>
                                      <th class="min-w-175px pb-2 ordered_served_class bg-dark" style="display:none;">Served Ltr/Kgs</th>
                                      @if($data->order_type == 'Promotion')
                                      <th class="min-w-80px text-end pb-2 bg-dark">Promo Delivery Date</th>
                                      @endif
                                      <th class="text-end pb-2 bg-dark" style="text-align: end !important;">Price</th>
                                      {{-- <th class="min-w-80px text-end pb-2">Price After VAT</th> --}}
                                      <th class="text-end pb-2 bg-dark" style="text-align: end !important;">Amount</th>
                                      <th class="min-w-100px text-end pb-2 bg-dark">Line Status</th>
                                      <th class="min-w-100px text-end pb-2 bg-dark">Line Remarks</th>
                                      
                                    </tr>
                                  </thead>
                                  <tbody>   
                                    <?php $total_qty = 0; $total_served = 0; ?>                                 
                                    @foreach($invoiceDetails as $k=>$val)
                                      <?php
                                           $total_qty += (int)$val['order_quantity'];
                                           $total_served += (int)$val['serverd_quantity'];
                                       ?>

                                    <tr class=" text-gray-700 fs-5">
                                      <td class="text-center custom_td_order">
                                        @if($status != 'Pending')
                                        <a class="trackStatus btn btn-primary btn-sm" id="item_{{$val['item_code']}}"> Track</a>
                                        @endif
                                      </td>
                                      <td class="text-center">{{$k+1}}</td>
                                      <td class="product_details">{{$val['product']}}</td>
                                      <td class="text-center">{{$val['unit']}}</td>
                                      <?php
                                        if(@$val['id'] == ""){
                                          $route = '#';
                                        }else{
                                          $route = route('invoices.show',@$val['id']);
                                        }
                                      ?>
                                      <td class="text-end"><a href="{{$route}}" class="text-decoration-underline" target="_blank">{{$val['invoice_num']}}</a></td>
                                      <td class="text-center">{{$val['order_quantity']}}</td>
                                      <td class="text-center">@if(!in_array($status, ['Pending', 'On Process', 'Cancelled'])) {{$val['serverd_quantity']}} @endif</td>
                                      <td class="text-center ordered_served_class" style="display:none;">{{$val['orderd_weight']}}</td>
                                      <td class="text-center ordered_served_class" style="display:none;">{{$val['served_weight']}}</td>
                                      @if($data->order_type == 'Promotion')
                                          <td>{{$val['promotion']}}</td>
                                      @endif 
                                      <td class="text-end">{{$val['price']}}</td>
                                      {{-- <td class="text-end">{{$val['price_after_vat']}}</td> --}}
                                      <td class="text-end">{{$val['amount']}}</td>
                                      <td class="text-center">@if(!in_array($status, ['Pending', 'On Process', 'Cancelled'])) {{$val['line_status']}} @endif</td>
                                      <td class="text-center">{{$val['line_remarks']}}</td>
                                    </tr>
                                    @endforeach
                                  </tbody>
                                </table>
                              </div>
                            </div>
                            <!--end::Table-->
                            <!--begin::Container-->
                            <div class="row">
                              <div class="col-sm-12 col-md-6 custom_remarks_order">Remark: {{ @$data->remarks ?? "-" }}</div>
                              <!-- <div class="col-sm-4 col-md-4 d-flex align-items-center justify-content-center">
                                <p>Note: Prices may be subjected with discount. Final amount of order will reflect on the actual invoice.</p>
                              </div> -->
                              <div class="col-sm-12 col-md-6 d-flex align-items-end justify-content-end">
                                                              
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
                                          <div class="fw-bold pe-10 text-gray-900 fs-7 ">Total Qty:</div>
                                          <!--end::Code-->
                                          <!--begin::Label-->
                                          <div class="text-end fw-bolder fs-6 fw-boldest">{{ number_format($total_qty) }}</div>
                                          <!--end::Label-->
                                        </div>
                                        <!--end::Item-->

                                        <div class="d-flex flex-stack">
                                          <div class="fw-bold pe-10 text-gray-900 fs-7 ">Total Served:</div>
                                          <div class="text-end fw-bolder fs-6 fw-boldest">{{ number_format($total_served) }}</div>
                                        </div>

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
                            <div class="col-md-1 note_order"><p>Note:</p></div>
                            <div class="col-md-11 d-flex align-items-start justify-content-start">
                              <p class="fst-italic text-danger"> The final amount of the order will be reflected on the actual invoice.<br><br> 
                                The ordered quantity is based on SQ and SO, while the served quantity is based on the invoice. 
                                Discrepancies may occur due to returned items, leading to misalignments.</p>
                            </div>
                          </div>
                          <!--end::Content-->
                        </div>
                        <!--end::Wrapper-->
                      </div>
                      <!--end::Invoice 2 content-->
                    </div>
                    <!--end::Content-->

                  {{-- </div> --}}

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

<div class="modal fade" id="myModal" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Delivery Status</h4>
        <button type="button" class="close add_product_close" data-bs-dismiss="modal">&times;</button>
        
      </div>
      <div class="modal-body status_modal_body">
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
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

    $(".trackStatus").on("click",function(){
      var id = $(this).attr('id').split("item_")[1];
      var product = $(this).closest('tr').find("td:eq(2)").text();
      var quantity = $(this).closest('tr').find("td:eq(5)").text();
      var card_code = '{{$data->card_code }}';

      $.ajax({
          url: "{{route('orders.item_status-track')}}",
          method: "POST",
          data: {
                  _token:'{{ csrf_token() }}',
                  id:id,
                  details:"{{@$data->u_omsno}}",
                  product : product,
                  quantity : quantity,
                  card_code : card_code
                }
        })
        .done(function(result) {
          if(result.status == false){
          }else{
            $(".status_modal_body").html(result.data.status_details);
            $("#myModal").modal('toggle'); 
          }
        })
        .fail(function() {
          toast_error("error");
        });        
      });


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

    let previousOrderApprovalValue = $('#orderApproval').val();
    $(document).on('change', '#orderApproval', async function(event) {
      event.preventDefault();
      const selectedValue = $(this).val();

      if (selectedValue !== 'Pending') {
        const result = await Swal.fire({
          title: 'Confirmation',
          text: 'Are you sure you want to ' + selectedValue + ' this order?',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, do it!',
          allowOutsideClick: false,
        });

        if (result.isConfirmed) {
          let reasonIsRequired = false;

          if (selectedValue == 'Reject') {
            reasonIsRequired = true;
          }
            
            const { value: reason, isConfirmed, isDismissed } = await Swal.fire({
              title: 'Enter your reason',
              input: 'textarea',
              inputValue: '',
              inputPlaceholder: "Type your reason here...",
              inputAttributes: {
                "aria-label": "Type your message here"
              },
              showCancelButton: true,
              allowOutsideClick: false,
              inputValidator: (value) => {
                if (reasonIsRequired && !value) {
                  return 'You need to write something!';
                }
              },
            });

            if (isDismissed) {
              $('#orderApproval').val(previousOrderApprovalValue);
            } else if (isConfirmed) {
              confirmationProcess(selectedValue, reason);
            }       
        
        }else{
          $('#orderApproval').val(previousOrderApprovalValue);
        }
      }else{
        $('#orderApproval').val(previousOrderApprovalValue);
      }

      function confirmationProcess(approval, reason = null) {
        $.ajax({
          url: '{{ route('orders.approval') }}',
          method: 'POST',
          data: {
            _token: '{{ csrf_token() }}',
            id: '{{ $data->id }}',
            approval: approval,
            reason: reason,
          },
        })
          .done(function(result) {
            if (result.status == false) {
              toastNotifMsg('Error', result.message);
            } else {
              toastNotifMsg('Success', result.message);
              setTimeout(function() {
                window.location.href = '{{ route('orders.index') }}';
              }, 600);
            }
          })
          .fail(function() {
            toast_error('error');
          });
      }
    });


    

    @if($status == "Completed" && !$date_array['Completed'] && in_array(userid(),[@$data->customer->user->id,1]) && @$data->order->invoice->id && empty(@$data->order->invoice->completed_date))
  

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

  $("#checked_served_checkbox").click(function(){
    if($(this).is(':checked')){
      $(".ordered_served_class").css("display", "table-cell");
    }else{
      $(".ordered_served_class").css("display", "none");
    }
  });

</script>
@endpush
