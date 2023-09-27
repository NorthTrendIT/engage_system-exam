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

              <form method="post" id="myForm">
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
                        <select class="form-control form-control-lg form-control-solid" name="filter_customer" data-control="select2" data-hide-search="false" data-placeholder="Select" data-allow-clear="true">
                            <option value=""></option>
                        </select>
                    </div>             
                </div>
                <div class="row mt-2">
                  @php
                    $year1 = date("Y")+1;
                    $endyear = $year1-10;
                  @endphp
                    <label class="col-sm-2 col-form-label col-form-label-lg" for="">Year</label>
                    <div class="col-sm-3">
                      <select class="form-control form-control-lg form-control-solid" name="year" data-control="select2" data-hide-search="false" data-placeholder="Select" data-allow-clear="true">
                          <option value=""></option>
                          @for ($year = $year1; $year >= $endyear; $year--)
                              <option value="{{$year}}">{{ $year }}</option>
                          @endfor 
                      </select>
                    </div>
                    <div class="col-sm-3">
                        <button class="btn btn-primary search_it"><i class="fas fa-search"></i>Search</button>
                    </div>
                </div>
                <div class="row mt-5">
                    <div class="col-md-12">
                        <div class="table-responsive text-nowrap repeater">
                        <table class="table table-bordered table-hover" id="cutomer_target_tbl" data-paging='false'>
                            <thead class="bg-dark text-white">
                             <tr>
                                <th class="bg-dark text-white">#</th>
                                <th class="min-w-150px bg-dark text-white">Brand</th>
                                <th class="min-w-150px bg-dark text-white">Category</th>
                                @for($x = 1; $x <= 12; $x++)
                                 <th class="min-w-80px">{{ date("F", strtotime("$x/12/1997")) }}</th>
                                @endfor
                                <th class="min-w-80px bg-dark text-white">Action</th>
                             </tr>
                            </thead>
                            <tbody data-repeater-list="target">
                                <tr data-repeater-item name="items">
                                    <td>1</td>
                                    <td>
                                        <select class="form-control form-control-sm form-control-solid border border-dark select_brand " name="filter_brand" data-control="select2" data-hide-search="false" data-placeholder="Select" data-allow-clear="true">
                                            <option value=""></option>
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-control form-control-sm form-control-solid border border-dark select_category" name="filter_category" data-control="select2" data-hide-search="false" data-placeholder="Select" data-allow-clear="true">
                                            <option value=""></option>
                                        </select>
                                    </td>
                                    @for($x = 1; $x <= 12; $x++)
                                        <td><input type="number" name="month_target" value="0" class="form-control form-control-sm form-control-solid border border-dark months" data-month="{{$x}}"></td>
                                    @endfor
                                    <td><button type="button" class="btn btn-danger btn-sm" data-repeater-delete><span class="fa fa-trash"></span></button></td>
                                </tr>
                            </tbody>
                        </table>
                            <button type="button" class="btn btn-success btn-sm" data-repeater-create><span class="fa fa-plus"></span></button>
                        </div>
                    </div>
                </div>
              </form>

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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/additional-methods.min.js"></script>
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
                                    right: 1
                                  },
                                  initComplete: function(settings, json) {
                                      $('body').find('.dataTables_scrollBody').addClass("scrollbar");
                                  },
                              });
    
    
    function validate_form(){
      var validator = $("#myForm").validate({
          errorClass: "is-invalid",
          validClass: "is-valid",
          rules: {
              filter_company:{
                  required: true,
              },
              filter_customer:{
                  required: true,
              },
              year:{
                  required: true,
                  // min: 2023,
              },
              month_target: {

              }
          },
          messages: {
              filter_company:{
                  required: "Please select company.",
              },
              filter_customer:{
                  required: "Please select customer.",
              },
              year:{
                  required: "Please input year.",
              }
          },
          errorPlacement: function (error, element) {
              if (element.hasClass('.select2').length) {
                  error.insertAfter(element.parent());
              } else {
                  error.insertAfter(element);
              }
          }
      });

      // $(".select_brand").each(function() {
      //     $(this).rules('add', {
      //         required: true,
      //     });
      // });

      // $(".quantity").each(function() {
      //     $(this).rules('add', {
      //         required: true,
      //         min: 1,
      //     });
      // });
      
      // $('.months').each(function(){
      //     $(this).rules('add', {
      //         required: true,
      //         min: 0,
      //     });
      // });
      // $.validator.addClassRules("months", {
      //               required: true,
      //               min: 1
      //           });
      return validator;
  }


    


    $('.search_it').on('click', function(e){
      e.preventDefault();
      var validator = validate_form();
      if (validator.form() != false) {
        $.ajax({
              url: "{{ route('customer-target.fetch') }}",
              method: "GET",
              data: {
                  sap_connection_id: $('[name="filter_company"]').val(),
                  customer_id: $('[name="filter_customer"]').val(),
                  year: $('[name="year"]').val()
              }
          })
          .done(function(result) { 
              if(result.status == false){
                  toast_error(result.message);
              }else{
                  var html = '';
                  if(result.data.length > 0){
                      $.each(result.data, function( index, value ) {

                        html += '<tr data-repeater-item name="items" class="odd">';
                            html += '<td class="dtfc-fixed-left" style="left: 0px; position: sticky;">'+(index+1)+'</td>';
                            html += '<td class="dtfc-fixed-left" style="left: 21.6719px; position: sticky;">'+
                                        '<select class="form-control form-control-sm form-control-solid border border-dark select_brand " name="filter_brand" current-brand="'+value.product_group.id+'" data-control="select2" data-hide-search="false" data-placeholder="Select" data-allow-clear="true">'+
                                            '<option value="'+value.product_group.id+'">'+value.product_group.group_name+'</option>'+
                                        '</select>'+
                                    '</td>';
                            html += '<td class="dtfc-fixed-left" style="left: 191.172px; position: sticky;">'+
                                        '<select class="form-control form-control-sm form-control-solid border border-dark select_category" name="filter_category" current-category="'+value.product_category.id+'" data-control="select2" data-hide-search="false" data-placeholder="Select" data-allow-clear="true">'+
                                            '<option value="'+value.product_category.id+'">'+value.product_category.u_tires+'</option>'+
                                        '</select>'+
                                    '</td>';
                            @for($x = 1; $x <= 12; $x++)
                            @php 
                              $month = date("F", strtotime("$x/12/1997"));
                            @endphp

                            html += '<td><input type="number" name="month_target" value="'+value['{{strtolower($month)}}']+'" class="form-control form-control-sm form-control-solid border border-dark months" data-month="{{$x}}"></td>';
                            @endfor
                            html += '<td class="dtfc-fixed-right" style="position: sticky; right: 0px;">'+
                                      '<button type="button" class="btn btn-primary btn-sm update_target"><span class="fa fa-save"></span></button>'+
                                      '<button type="button" class="btn btn-danger btn-sm" data-repeater-delete><span class="fa fa-trash"></span></button>'+
                                      '</td>';
                            html += '</tr>';

                      });
                  }else{
                      //else html
                      // $('.btn-success[data-repeater-create]').get(0).click();
                    html += '<tr data-repeater-item name="items" class="odd">'+
                                    '<td class="dtfc-fixed-left" style="left: 0px; position: sticky;">1</td>'+
                                    '<td class="dtfc-fixed-left" style="left: 21.6719px; position: sticky;">'+
                                        '<select class="form-control form-control-sm form-control-solid border border-dark select_brand " name="filter_brand" data-control="select2" data-hide-search="false" data-placeholder="Select" data-allow-clear="true">'+
                                            '<option value=""></option>'+
                                        '</select>'+
                                    '</td>'+
                                    '<td class="dtfc-fixed-left" style="left: 191.172px; position: sticky;">'+
                                        '<select class="form-control form-control-sm form-control-solid border border-dark select_category" name="filter_category" data-control="select2" data-hide-search="false" data-placeholder="Select" data-allow-clear="true">'+
                                            '<option value=""></option>'+
                                        '</select>'+
                                    '</td>';
                                    @for($x = 1; $x <= 12; $x++)
                                    html += '<td><input type="number" name="month_target" value="0" class="form-control form-control-sm form-control-solid border border-dark months" data-month="{{$x}}"></td>';
                                    @endfor
                                    html += '<td class="dtfc-fixed-right" style="position: sticky; right: 0px;"><button type="button" class="btn btn-danger btn-sm" data-repeater-delete><span class="fa fa-trash"></span></button></td>'+
                              '</tr>';
                  }

                  $('#cutomer_target_tbl tbody').html(html);
                  select2_brand();
                  select2_category();

              }
          })
          .fail(function() {
              toast_error("error");
          });

      }
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
          var validator = validate_form();
          var has_prev = $(this).prev().find('td').length; 
          var has_update = $(this).prev().find('.update_target').length;
          var has_brand = false;
          var has_category = false;
          var has_target = false;
          var has_duplicate = false;
          var check_duplicate = [];
          var brand = 0;
          var category = 0;
          var monthly_target = {};

          $(this).prev().find('.select_brand').each(function(){
              if(this.value){
                brand = this.value;
                has_brand = true;
              }
          });

          $(this).prev().find('.select_category').each(function(){
              if(this.value){
                category = this.value;
                has_category = true;
              }
          });

          $(document).find("tr[data-repeater-item]").each(function(){
              var data = $(this).find('.select_brand').val()+','+$(this).find('.select_category').val();

              if(check_duplicate.indexOf(data) == -1){
                check_duplicate.push(data)
              }else{
                has_duplicate = true;
                return false; //stops loop
              }
          });

          $(this).prev().find('.months').each(function(){
              if(this.value != 0 && this.value >= 1){
                has_target = true;
              }
              if(this.value != 0 && this.value < 1){
                has_target = false;
                return false; //stops loop
              }
              monthly_target[$(this).attr('data-month')] = this.value;
          });


          if(has_prev != 0){ // to avoid pop up if previous row not found.
            if(validator.form() != false && has_target == false){
              Swal.fire('Please input a valid monthly target', '', 'error');
            }
            if(validator.form() != false && has_duplicate == true){
              Swal.fire('Brand & Category must be unique.', '', 'error');
            }
            if(validator.form() != false && has_category == false){
              Swal.fire('Please select category.', '', 'error');
            }
            if(validator.form() != false && has_brand == false){
              Swal.fire('Please select brand', '', 'error');
            }
          } 

          if (validator.form() != false && (has_brand != false && has_category != false && has_duplicate == false && has_target != false && has_update == 0)) {
            $(this).prev().find('.btn-danger').parent().prepend('<button type="button" class="btn btn-primary btn-sm update_target"><span class="fa fa-save"></span></button>');
            $.ajax({
                url: "{{ route('customer-target.add') }}",
                method: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    sap_connection_id: $('[name="filter_company"]').val(),
                    customer_id: $('[name="filter_customer"]').val(),
                    year: $('[name="year"]').val(),
                    brand: brand,
                    category: category,
                    monthly_target: monthly_target
                }
            })
            .done(function(result, status, xhr) { 
                if(result.status == false){
                    toast_error(result.message);
                    $('.search_it').trigger('click');
                }else{
                    //success
                    // $('[name="year"]').trigger('focusout');
                    var prev_row = $(document).find("tr[data-repeater-item]").last().prev();
                
                    $(prev_row).find('.select_brand').attr("current-brand", result.data.brand_id);
                    $(prev_row).find('.select_category').attr("current-category", result.data.category_id);
                    toast_success(result.message);
                }
            })
            .fail(function() {
                toast_error("error");
            });

              $(this).slideDown();
              $(this).find('input').val(0);

              // var category_ids = [];
              // $('.repeater').find('.select_category').each(function(){
              //     if(this.value){
              //       category_ids.push(this.value);
              //     }
              // });

              $('.repeater').find('.select_brand').next('.select2-container').remove();
              $('.repeater').find('.select_category').next('.select2-container').remove();
              
              select2_brand();
              select2_category();

              var counter = $("tr[data-repeater-item]").length;
              $(this).find('td:first-child').text(counter);
            
          }else{
            if( has_duplicate == false && (has_update == 1 || has_prev == 0) ){
              $(this).slideDown();
              $(this).find('input').val(0);

              $('.repeater').find('.select_brand').next('.select2-container').remove();
              $('.repeater').find('.select_category').next('.select2-container').remove();
              
              select2_brand();
              select2_category();

              var counter = $("tr[data-repeater-item]").length;
              $(this).find('td:first-child').text(counter);
            }else{
              $(this).remove(); //need to remove so it will read freshly created attr.
            }
          }
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
                  if($(this).find('.update_target').length == 1){
                    $.ajax({
                        url: "{{ route('customer-target.delete') }}",
                        method: "POST",
                        data: {
                            _token: '{{ csrf_token() }}',
                            sap_connection_id: $('[name="filter_company"]').val(),
                            customer_id: $('[name="filter_customer"]').val(),
                            year: $('[name="year"]').val(),
                            brand: $(this).find('.select_brand').val(),
                            category: $(this).find('.select_category').val()
                        }
                    })
                    .done(function(result) { 
                        if(result.status == false){
                            toast_error(result.message);
                        }else{
                            //success
                            toast_success(result.message);
                        }
                    })
                    .fail(function() {
                        toast_error("error");
                    });
                  }

                  $(this).slideUp(deleteElement);
                  $(this).remove();

                  new_index = 1;
                  $(document).find("tr[data-repeater-item]").each(function(){
                      $(this).find('td:first-child').text(new_index);
                      new_index++;
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

    $('[name="filter_company"]').on('change', function(){
      $('[name="filter_customer"]').val('').trigger('change');
      // $('.search_it').trigger('click');
    });

    
    $('[name="filter_customer"]').select2({
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
    
    select2_brand();
    select2_category();

    function select2_brand(){
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
    }

    function select2_category(){
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
    }


    $(document).on('click', '.update_target', function(){

      var $self = $(this).closest('tr');
      var validator = validate_form();
      var has_brand = false;
      var has_category = false;
      var has_target = false;
      var has_duplicate = false;
      var check_duplicate = [];
      var brand = 0;
      var category = 0;
      var monthly_target = {};

      $($self).find('.select_brand').each(function(){
          if(this.value){
            brand = this.value;
            has_brand = true;
          }
      });

      $($self).find('.select_category').each(function(){
          if(this.value){
            category = this.value;
            has_category = true;
          }
      });

      $(document).find("tr[data-repeater-item]").each(function(){
          var data = $(this).find('.select_brand').val()+','+$(this).find('.select_category').val();

          if(check_duplicate.indexOf(data) == -1){
            check_duplicate.push(data)
          }else{
            has_duplicate = true;
            return false; //stops loop
          }
      });

      $($self).find('.months').each(function(){
          if(this.value != 0 && this.value >= 1){
            has_target = true;
          }
          if(this.value != 0 && this.value < 1){
            has_target = false;
            return false; //stops loop
          }
          monthly_target[$(this).attr('data-month')] = this.value;
      });


      if(validator.form() != false && has_target == false){
        Swal.fire('Please input a valid monthly target', '', 'error');
      }
      if(validator.form() != false && has_duplicate == true){
        Swal.fire('Brand & Category must be unique.', '', 'error');
      }
      if(validator.form() != false && has_category == false){
        Swal.fire('Please select category.', '', 'error');
      }
      if(validator.form() != false && has_brand == false){
        Swal.fire('Please select brand', '', 'error');
      }

      if (validator.form() != false && (has_brand != false && has_category != false && has_duplicate == false && has_target != false)) {
        Swal.fire({
                title: 'Are you sure you want to update this record?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, do it!'
            }).then((result) => {
                if(result.isConfirmed) {
                  $.ajax({
                      url: "{{ route('customer-target.update') }}",
                      method: "POST",
                      data: {
                          _token: '{{ csrf_token() }}',
                          sap_connection_id: $('[name="filter_company"]').val(),
                          customer_id: $('[name="filter_customer"]').val(),
                          year: $('[name="year"]').val(),
                          brand: $($self).find('.select_brand').attr('current-brand'),
                          category: $($self).find('.select_category').attr('current-category'),
                          new_brand: brand,
                          new_category: category,
                          monthly_target: monthly_target
                      }
                  })
                  .done(function(result) { 
                      if(result.status == false){
                          toast_error(result.message);
                      }else{
                          //success
                          toast_success(result.message);
                      }
                      $('.search_it').trigger('click');
                  })
                  .fail(function() {
                      toast_error("error");
                  });
                } //closing tag of isConfirmed
        });
      }
    })

    



  })
</script>
@endpush
