@extends('layouts.master')

@section('title','Product List')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Product List</h1>
      </div>

    </div>
  </div>

  <div class="post d-flex flex-column-fluid" id="kt_post">
    <div id="kt_content_container" class="container-xxl">
      <div class="row gy-5 g-xl-8">
        <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
          <div class="card card-xl-stretch mb-5 mb-xl-8">
            {{-- <div class="card-header border-0 pt-5 min-0">
              <h5>View Details</h5>
            </div> --}}
            <div class="card-body">

              <div class="row mb-5">
                <div class="col-md-12">
                  <div class="form-group">

                    <!-- product list -->
                    <div class="row" id="product_list_row">

                    </div>

                    <div class="row mt-10">
                      <div class="col-md-12 d-flex justify-content-center" id="view_more_col">
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
@endsection

@push('css')

@endpush

@push('js')
<script>
$(document).ready(function() {
  getProductList();

  function getProductList($id = ""){
    $('#view_more_btn').remove();
    $.ajax({
      url: '{{ route('product-list.get-all') }}',
      type: 'POST',
      dataType:'json',
      data: {
              id: $id,
              _token:'{{ csrf_token() }}',
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

    $(document).on('click', '.addToCart', function(event) {
      event.preventDefault();
      $url = $(this).attr('data-url');
        $.ajax({
            url: $url,
            method: "POST",
            data: {
                    _token:'{{ csrf_token() }}'
                    }
            })
            .done(function(result) {
                if(result.status == false){
                    toast_error(result.message);
                }else{
                    toast_success(result.message);
                    setTimeout(function(){
                        window.location.reload();
                    },1500)
                }
            })
            .fail(function() {
                toast_error("error");
            });
    });
});
</script>
@endpush
