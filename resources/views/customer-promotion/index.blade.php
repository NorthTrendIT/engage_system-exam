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
        <a href="{{ route('customer-promotion.get-interest') }}" class="btn btn-sm btn-primary mr-10">Interested Promotions</a>

        <a href="{{ route('customer-promotion.order.index') }}" class="btn btn-sm btn-primary mr-10">Claimed Promotions</a>
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
              <h5>List of Promotions</h5>
            </div>
            <div class="card-body">

              <div class="row mb-5 mt-10" id="promotion_list_row">
                
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
@endsection

@push('css')

@endpush

@push('js')

<script>

  $(document).ready(function() {

    getCommentList();

    function getCommentList($id = ""){
      $('#view_more_btn').remove();

      $.ajax({
        url: '{{ route('customer-promotion.get-all') }}',
        type: 'POST',
        dataType:'json',
        data: {
                id: $id,
                _token:'{{ csrf_token() }}',
              },
      })
      .done(function(data) {
        $('#promotion_list_row').append(data.output);
        $('#view_more_col').html(data.button);
        toast_success("Promotions List Updated Successfully !");
      })
      .fail(function() {
        toast_error("error");
      });
    }

    $(document).on('click', '#view_more_btn', function(event) {
      event.preventDefault();
      $id = $(this).attr('data-id');
      getCommentList($id);
    });

    $(document).on('click', '.btn_interest', function(event) {
      event.preventDefault();
      $id = $(this).attr('data-id');
      $value = $(this).attr('data-value');
      $this = $(this);

      Swal.fire({
        title: 'Are you sure want to do this ?',
        //text: "Once deleted, you will not be able to recover this record!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, do it!'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: '{{ route('customer-promotion.store-interest') }}',
            method: "POST",
            data: {
                    promotion_id:$id,
                    is_interested:$value,
                    _token:'{{ csrf_token() }}'
                  }
          })
          .done(function(result) {
            if(result.status == false){
              toast_error(result.message);
            }else{

              if($value == 1){
                $($this).remove();
              }else{
                $($this).closest('.product-grid-outer').remove();
              }

              toast_success(result.message);
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