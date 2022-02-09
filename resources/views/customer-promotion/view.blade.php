@extends('layouts.master')

@section('title','My Promotions')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">My Promotions</h1>
      </div>

      <!--begin::Actions-->
      <div class="d-flex align-items-center py-1">
        <!--begin::Button-->
        <a href="{{ route('customer-promotion.index') }}" class="btn btn-sm btn-primary sync-products">Back</a>
        <!--end::Button-->
      </div>
      <!--end::Actions-->
      
    </div>
  </div>
  
  <div class="post d-flex flex-column-fluid detail-view-table" id="kt_post">
    <div id="kt_content_container" class="container-xxl">
      <div class="row gy-5 g-xl-8">
        <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
          <div class="card card-xl-stretch mb-5 mb-xl-8">
            <div class="card-header border-0 pt-5 min-0">
              <h5>View Details</h5>

              <a href="javascript:" data-href="{{ route('customer-promotion.order.create',$data->id) }}" class="btn btn-success claim-now-btn" @if(userrole() != 4) style="display:none;" @endif>Claim Now</a>
            </div>
            <div class="card-body">
              
              <div class="row mb-5">
                <div class="col-md-12">
                  <div class="form-group">
                    <!--begin::Table container-->
                    <div class="table-responsive">
                       <!--begin::Table-->
                       <table class="table table-bordered" id="myTable">
                          <!--begin::Table head-->
                          <thead>
                            <tr>
                              <th> <b>Title:</b> </th>
                              <td>{{ @$data->title ?? "" }}</td>
                            </tr>
                            <tr>
                              <th> <b>Description:</b> </th>
                              <td>{{ @$data->description ?? "" }}</td>
                            </tr>
                            <tr>
                              <th> <b>Discount:</b> </th>
                              <td>
                                <b class="text-success">
                                  @if(@$data->promotion_type->scope == "R")
                                    {{ @$data->promotion_type->min_percentage }} % - {{ @$data->promotion_type->max_percentage }} %
                                  @elseif(@$data->promotion_type->scope == "P")
                                    {{ @$data->promotion_type->percentage }} %
                                  @elseif(@$data->promotion_type->scope == "U")
                                    {{ @$data->promotion_type->percentage }} %, Maximum discount upto ₱ {{ @$data->promotion_type->fixed_price }}/- only. 
                                  @endif
                                  
                                </b>

                              </td>
                            </tr>
                            <tr>
                              <th> <b>Delivery:</b> </th>
                              <td>
                                @if(@$data->promotion_type)
                                  @if(is_null($data->promotion_type->number_of_delivery))
                                  No Limit
                                  @else
                                  Fixed ({{ $data->promotion_type->number_of_delivery }})
                                  @endif
                                @endif
                              </td>
                            </tr>
                            <tr>
                              <th> <b>Quantity:</b> </th>
                              <td>
                                @if(@$data->promotion_type)
                                  @if($data->promotion_type->is_fixed_quantity == false)
                                  No Limit
                                  @else
                                  Fixed ({{ $data->promotion_type->is_total_fixed_quantity ? "Total Fixed Quantity" : "Fixed Quantity Per Product" }})
                                  @endif
                                @endif

                              </td>
                            </tr>

                            <tr>
                              <th> <b>Valid from:</b> </th>
                              <td>
                                <b class="text-danger">
                                  {{ date('M d, Y',strtotime(@$data->promotion_start_date)) }} to {{ date('M d, Y',strtotime(@$data->promotion_end_date)) }}
                                </b>
                              </td>
                            </tr>

                          </thead>
                          <!--end::Table head-->
                          <!--begin::Table body-->
                          <tbody>
                            
                          </tbody>
                          <!--end::Table body-->
                       </table>
                       <!--end::Table-->
                    </div>
                    <!--end::Table container-->

                  </div>

                </div>
              </div>

            </div>
          </div>
        </div>
      </div>

      <div class="row gy-5 g-xl-8 customer-product-list">
        <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
          <div class="card card-xl-stretch mb-5 mb-xl-8">
            <div class="card-header border-0 pt-5 min-0">
              <h5>List of Products</h5>
            </div>
            <div class="card-body">

              @if(userrole() != 4) {{-- If its not a customer --}}
                <div class="row mb-5">
                  <div class="col-md-8">
                    <div class="form-group">
                      <label>Customer<span class="asterisk">*</span></label>
                      <select class="form-control form-control-lg form-control-solid" name="customer_id" data-control="select2" data-hide-search="false" data-placeholder="Select Customer" data-allow-clear="false">
                        <option value=""></option>
                      </select>
                    </div>
                  </div>
                </div>
              @endif

              <div class="row mb-5 mt-10" id="product_list_row">
                
              </div>

              <div class="row mt-10">
                <div class="col-md-12 d-flex justify-content-center" id="view_more_col">

                </div>
              </div>
              
              <div class="row mt-5">
                <div class="col-md-12 d-flex justify-content-center">
                  <a href="javascript:" data-href="{{ route('customer-promotion.order.create',$data->id) }}" class="btn btn-success claim-now-btn" @if(userrole() != 4) style="display:none;" @endif>Claim Now</a>
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
<script>
$(document).ready(function() {
  
  @if(userrole() == 4) // If its a customer
  getProductList();
  @endif

  function getProductList($id = ""){
    $('#view_more_btn').remove();
    $.ajax({
      url: '{{ route('customer-promotion.get-all-product-list') }}',
      type: 'POST',
      dataType:'json',
      data: {
              promotion_type_id: '{{ @$data->promotion_type_id }}',
              promotion_id: '{{ @$data->id }}',
              id: $id,
              _token:'{{ csrf_token() }}',
              
              @if(userrole() != 4) // If its a customer
              customer_id:$('[name="customer_id"]').val(),
              @endif
            },
    })
    .done(function(data) {
      $('#product_list_row').append(data.output);
      $('#view_more_col').html(data.button);
    })
    .fail(function() {
      toast_error("error");
    });
  }

  $(document).on('click', '#view_more_btn', function(event) {
    event.preventDefault();
    $id = $(this).attr('data-id');
    getProductList($id);
  });

  $('[name="customer_id"]').select2({
    ajax: {
      url: "{{route('customer-promotion.get-customer')}}",
      type: "post",
      dataType: 'json',
      delay: 250,
      data: function (params) {
          return {
              _token: "{{ csrf_token() }}",
              search: params.term
          };
      },
      processResults: function (response) {
        return {
          results:  $.map(response, function (item) {
                        return {
                          text: item.card_name,
                          id: item.id
                        }
                    })
        };
      },
      cache: true,
    },
  });

  $(document).on('change', '[name="customer_id"]', function(event) {
    event.preventDefault();
    $('#product_list_row').html("");
    $('.claim-now-btn').show();
    getProductList();
  });

  $(document).on('click', '.view-product-a', function(event) {
    event.preventDefault();
    var url = $(this).data('href');

    @if(userrole() != 4) // If its not a customer
      url += "/" + $('[name="customer_id"]').val(); 
    @endif

    window.open(url, '_blank');
  });


  $(document).on('click', '.claim-now-btn', function(event) {
    event.preventDefault();
    var url = $(this).data('href');

    @if(userrole() != 4) // If its not a customer
      url += "/" + $('[name="customer_id"]').val(); 
    @endif
    window.location.href = url;
  });

});
</script>
@endpush