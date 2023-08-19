@extends('layouts.master')

@section('title','Customer Target')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Customer Target</h1>
      </div>
      <div class="d-flex align-items-center py-1">
        <a href="/" class="btn btn-sm btn-primary">Back</a>
      </div>
    </div>
  </div>

  <div class="post d-flex flex-column-fluid" id="kt_post">
    <div id="kt_content_container" class="container-xxl">
      <div class="row gy-5 g-xl-8">
        <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
          <div class="card card-xl-stretch mb-5 mb-xl-8">
            <div class="card-body">

                <div class="row">
                    <label class="col-sm-2 col-form-label col-form-label-lg" for="">Company</label>
                    <div class="col-sm-3">
                        <select class="form-control form-control-lg form-control-solid" data-control="select2" data-hide-search="false" name="filter_company" data-allow-clear="true" data-placeholder="Select">
                            <option value=""></option>
                            @foreach($company as $c)
                            <option value="{{ $c->id }}">{{ $c->company_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row mt-2">
                    <label class="col-sm-2 col-form-label col-form-label-lg" for="">Customer</label>
                    <div class="col-sm-8">
                        <select class="form-control form-control-lg form-control-solid" name="filter_search" data-control="select2" data-hide-search="false" data-placeholder="Select" data-allow-clear="true">
                            <option value=""></option>
                        </select>
                    </div>             
                </div>
                <div class="row mt-2">
                    <label class="col-sm-2 col-form-label col-form-label-lg" for="">Year</label>
                    <div class="col-sm-3">
                        <input type="number" name="year" class="form-control" id="">
                    </div>
                </div>
                <div class="row mt-5">
                    <div class="col-md-12">
                        <div class="table-responsive text-nowrap repeater">
                        <table class="table table-bordered table-striped table-hover" id="cutomer_target_tbl" data-paging='false'>
                            <thead class="bg-dark text-white">
                             <tr>
                                <th>#</th>
                                <th class="min-w-150px">Brand</th>
                                <th class="min-w-150px">Category</th>
                                @for($x = 1; $x <= 12; $x++)
                                 <th class="min-w-80px">{{ date("F", strtotime("$x/12/1997")) }}</th>
                                @endfor
                                <th class="min-w-80px">Action</th>
                             </tr>
                            </thead>
                            <tbody data-repeater-list="target">
                                <tr data-repeater-item name="items">
                                    <td>1</td>
                                    <td>
                                        <select class="form-control form-control-sm form-control-solid border border-dark select_brand col-s," name="filter_brand" data-control="select2" data-hide-search="false" data-placeholder="Select" data-allow-clear="true">
                                            <option value=""></option>
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-control form-control-sm form-control-solid border border-dark select_category" name="filter_category" data-control="select2" data-hide-search="false" data-placeholder="Select" data-allow-clear="true">
                                            <option value=""></option>
                                        </select>
                                    </td>
                                    @for($x = 1; $x <= 12; $x++)
                                        <td><input type="number" name="month_target" value="0" class="form-control form-control-sm form-control-solid border border-dark" data-month=""></td>
                                    @endfor
                                    <td><button type="button" class="btn btn-danger btn-sm" data-repeater-delete><span class="fa fa-trash"></span></button></td>
                                </tr>
                            </tbody>
                        </table>
                            <button type="button" class="btn btn-success btn-sm" data-repeater-create><span class="fa fa-plus"></span></button>
                        </div>
                    </div>
                </div>

                {{-- <form class="repeater">
                    <!--
                        The value given to the data-repeater-list attribute will be used as the
                        base of rewritten name attributes.  In this example, the first
                        data-repeater-item's name attribute would become group-a[0][text-input],
                        and the second data-repeater-item would become group-a[1][text-input]
                    -->
                    <div data-repeater-list="group-a">
                      <div data-repeater-item>
                        <input type="text" name="text-input" value="A"/>
                        <input data-repeater-delete type="button" value="Delete"/>
                      </div>
                      <div data-repeater-item>
                        <input type="text" name="text-input" value="B"/>
                        <input data-repeater-delete type="button" value="Delete"/>
                      </div>
                    </div>
                    <input data-repeater-create type="button" value="Add"/>
                </form> --}}

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('css')
<link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/fixedcolumns/4.0.1/css/fixedColumns.dataTables.min.css">
<style>
  .scrollbar::-webkit-scrollbar
{
    /* width: 6px;
    background-color: #000000; */
}
 
.scrollbar::-webkit-scrollbar-thumb
{
    /* border-radius: 10px;
    -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,.3); */
    background-color: #181c32;
}
</style>

@endpush

@push('js')
<script type="text/javascript" src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/fixedcolumns/4.0.1/js/dataTables.fixedColumns.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.repeater/1.2.1/jquery.repeater.min.js"></script>

<script>
  $(document).ready(function() {

    var cutomer_target_tbl = $('#cutomer_target_tbl').DataTable({
                                  dom: 'Bfrtip',
                                  bFilter: false, bInfo: false, bPaginate: false, bSort: false,
                                  scrollX: true,
                                  scrollY: "800px",
                                  scrollCollapse: true,
                                  paging: true,
                                  fixedColumns:   {
                                    left: 3,
                                    right: 0
                                  },
                                  initComplete: function(settings, json) {
                                      $('body').find('.dataTables_scrollBody').addClass("scrollbar");
                                  },
                              });


    $('[name="year"]').focusout(function(){
      console.log('fetch to server');
      
      // $.ajax({
      //       url: "{{ route('reports.fetch-top-products') }}",
      //       method: "GET",
      //       data: filter_datas
      //   })
      //   .done(function(result) {  
      //       if(result.status == false){
      //           toast_error(result.message);
      //       }else{
      //           var html = '';
      //           if(result.data.length > 0){
      //               $.each(result.data, function( index, value ) {
      //                   top_products_per_quantity.row.add([(index+1), value.card_name, value.item_code, value.item_description, (value.total_order).toLocaleString()]);
      //               });
      //           }
      //           top_products_per_quantity.draw();
      //       }
      //   })
      //   .fail(function() {
      //       toast_error("error");
      //   });

    });

    $('.repeater').repeater({
        // (Optional)
        // start with an empty list of repeaters. Set your first (and only)
        // "data-repeater-item" with style="display:none;" and pass the
        // following configuration flag
        initEmpty: false,
        // (Optional)
        // "defaultValues" sets the values of added items.  The keys of
        // defaultValues refer to the value of the input's name attribute.
        // If a default value is not specified for an input, then it will
        // have its value cleared.
        defaultValues: {
            // 'month_target': '0'
        },
        // (Optional)
        // "show" is called just after an item is added.  The item is hidden
        // at this point.  If a show callback is not given the item will
        // have $(this).show() called on it.
        show: function () {
            $(this).slideDown();
            $(this).find('input').val(0);

            $('.repeater').find('.select_brand').next('.select2-container').remove();
            $('.repeater').find(".select_brand").select2({
              ajax: {
                  url: "{{route('customers-sales-specialist.get-product-brand')}}",
                  type: "post",
                  dataType: 'json',
                  delay: 250,
                  data: function (params) {
                    
                    return {
                      _token: "{{ csrf_token() }}",
                      search: params.term,
                      sap_connection_id: $('[name="filter_company"]').val()
                    };
                  },
                  processResults: function (response) {
                      return {
                          results: response
                      };
                  },
                  cache: true
              },
              placeholder: 'Select Brand',
              // multiple: true,
            });

            $('.repeater').find('.select_category').next('.select2-container').remove();
            $('.repeater').find(".select_category").select2({
              ajax: {
                  url: "{{route('customers-sales-specialist.get-product-category')}}",
                  type: "post",
                  dataType: 'json',
                  delay: 250,
                  data: function (params) {
                    
                    return {
                      _token: "{{ csrf_token() }}",
                      search: params.term,
                      sap_connection_id: $('[name="filter_company"]').val()
                    };
                  },
                  processResults: function (response) {
                      return {
                          results: response
                      };
                  },
                  cache: true
              },
              placeholder: 'Select Category',
            });

            var counter = $("tr[data-repeater-item]").length;
            $(this).find('td:first-child').text(counter);
        },
        // (Optional)
        // "hide" is called when a user clicks on a data-repeater-delete
        // element.  The item is still visible.  "hide" is passed a function
        // as its first argument which will properly remove the item.
        // "hide" allows for a confirmation step, to send a delete request
        // to the server, etc.  If a hide callback is not given the item
        // will be deleted.
        hide: function (deleteElement) {
            Swal.fire({
                    title: 'Are you sure you want to delete this element?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, do it!'
                }).then((result) => {
                    if(result.isConfirmed) {
                      $(this).slideUp(deleteElement);

                      var deleted = $(this).find('td:first-child').text();
                      $(document).find("tr[data-repeater-item]").each(function(){
                          var counter = $(this).find('td:first-child').text();
                          var new_index = counter;
                          if(deleted !== counter && deleted < counter){
                              new_index = counter - 1; 
                          }
                          $(this).find('td:first-child').text(new_index);
                      });
                    }
                });


        },
        // (Optional)
        // You can use this if you need to manually re-index the list
        // for example if you are using a drag and drop library to reorder
        // list items.
        ready: function (setIndexes) {
            // $dragAndDrop.on('drop', setIndexes);
        },
        // (Optional)
        // Removes the delete button from the first list item,
        // defaults to false.
        isFirstItemUndeletable: false
    });

    
    $('[name="filter_search"]').select2({
      ajax: {
        url: "{{ route('customer-promotion.get-customer') }}",
        type: "post",
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
              _token: "{{ csrf_token() }}",
              search: params.term,
              sap_connection_id: $('[name="filter_company"]').find('option:selected').val(),
            };
        },
        processResults: function (response) {
          return {
            results:  $.map(response, function (item) {
                          return {
                            text: item.card_name + " (Code: " + item.card_code + ")",
                            id: item.id
                          }
                      })
          };
        },
        cache: true
      },
    //   tags: true,
    //   minimumInputLength: 2,
    });
    

    $(document).find(".select_brand").select2({
      ajax: {
          url: "{{route('customers-sales-specialist.get-product-brand')}}",
          type: "post",
          dataType: 'json',
          delay: 250,
          data: function (params) {
            
            return {
              _token: "{{ csrf_token() }}",
              search: params.term,
              sap_connection_id: $('[name="filter_company"]').val()
            };
          },
          processResults: function (response) {
              return {
                  results: response
              };
          },
          cache: true
      },
      placeholder: 'Select Brand',
      // multiple: true,
    });


    $(document).find(".select_category").select2({
      ajax: {
          url: "{{route('customers-sales-specialist.get-product-category')}}",
          type: "post",
          dataType: 'json',
          delay: 250,
          data: function (params) {
            
            return {
              _token: "{{ csrf_token() }}",
              search: params.term,
              sap_connection_id: $('[name="filter_company"]').val()
            };
          },
          processResults: function (response) {
              return {
                  results: response
              };
          },
          cache: true
      },
      placeholder: 'Select Category',
    });

    



  })
</script>
@endpush
