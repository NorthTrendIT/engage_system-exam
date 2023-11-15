@extends('layouts.master')

@section('title','Product Benefits')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Product Benefits</h1>
      </div>
      <!--begin::Actions-->
      <div class="d-flex align-items-center py-1">
        <a href="{{ route('benefits.assignment') }}" class="btn btn-sm btn-primary">Back</a>
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
              <div class="row">

              </div>
              <div class="row mb-5 mt-5">
                <div class="col-md-12">
                  <div class="table-responsive">
                    <table class="table" id="product_benefits_tbl">
                      <thead class="bg-dark text-white">
                        <tr>
                          <th>#</th>
                          {{-- <th>ID</th> --}}
                          <th>Code</th>
                          <th>Description</th>
                          <th>Icon</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($benefits as $key => $b)
                            <tr>
                                <td>{{ $key+1 }}</td>
                                {{-- <td>{{ $b->id }}</td> --}}
                                <td>{{ $b->code }}</td>
                                <td>{{ $b->name }}</td>
                                <td>
                                  @if (Storage::disk('public')->exists('products/benefits/'.$b->icon.'') && $b->icon != "")
                                    <img src="{{ asset('storage/products/benefits/'.$b->icon.'')}}" alt="" class="img-fluid img-fluid" width="50" height="50" />
                                  @endif
                                </td>
                                <td><button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#benefitsModal" benefit-id="{{ $b->id }}"><span class="fa fa-edit"></span></button></td>
                            </tr>
                        @endforeach
                      </tbody>
                    </table>
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

<!-- Modal -->
<div class="modal fade" id="benefitsModal" tabindex="-1" aria-labelledby="benefitsModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="benefitsModalLabel">Update</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        
        <form action="{{route('product.benefits.update')}}" method="post" enctype="multipart/form-data" id="benefitForm">
          @csrf
          <input type="hidden" name="id">
          <div class="mb-3">
            <label for="bnfCode" class="form-label">Code</label>
            <input name="code" type="text" class="form-control" id="bnfCode" aria-describedby="emailHelp">
            {{-- <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div> --}}
          </div>
          <div class="mb-3">
            <label for="bnfDescription" class="form-label">Description</label>
            <input name="description" type="text" class="form-control" id="bnfDescription">
          </div>
          <div class="mb-3">
            <label for="formFile" class="form-label">Icon</label>
            <input name="icon" class="form-control" type="file" id="formFile">
          </div>
        </form>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary btn-sm updateBenefit">Save changes</button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('css')
<link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<style>
  #product_benefits_tbl .custom_width{
      max-width: 250px !important;
      white-space: nowrap; 
      width: 50px; 
      overflow: hidden;
      text-overflow: ellipsis;
  }  
</style>
@endpush

@push('js')
<script type="text/javascript" src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
  $(document).ready(function() {
    var table = [];
    render_table();

    $('.search').on('click', function(){
      render_table();
    });

    $('.clear-search').on('click', function(){
      $('[name="filter_company"]').val('').trigger('change');
      $('[name="filter_search"]').val('');
      render_table();
    })

    function render_table(){
      table = $("#product_benefits_tbl").DataTable({
                  aaSorting: [[0, 'asc']],
                  aoColumnDefs: [
                                //  { "bVisible": false, "aTargets": [1] },
                                 {bSortable: false, aTargets: [ -1, -2 ] }
                                ],
              });
    }


    $(document).on('click', '.delete', function(event) {
      event.preventDefault();
      $url = $(this).attr('data-url');

      Swal.fire({
        title: 'Are you sure?',
        text: "Once deleted, you will not be able to recover this record!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: $url,
            method: "DELETE",
            data: {
                    _token:'{{ csrf_token() }}'
                  }
          })
          .done(function(result) {
            if(result.status == false){
              toast_error(result.message);
            }else{
              console.log(result);
              toast_success(result.message);
              render_table();
            }
          })
          .fail(function() {
            toast_error("error");
          });
        }
      })
    });

    table.on('click', 'button', function (e) {
        let data = table.row(e.target.closest('tr')).data();

        $('[name="id"]').val($(this).attr('benefit-id'));
        $('[name="code"]').val(data[1]);
        $('[name="description"]').val(data[2]);
    });

    $('.updateBenefit').on('click', function(e){
      $('#benefitForm').submit();
    })

    @if ($message = session()->get('success'))
      toast_success('{{$message}}');
    @endif

    @if (count($errors) > 0)
      @foreach ($errors->all() as $error)
        toast_error('{{ $error }}');
      @endforeach
    @endif
  })
</script>
@endpush
