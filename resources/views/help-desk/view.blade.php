@extends('layouts.master')

@section('title','Help Desk')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Help Desk</h1>
      </div>

      <!--begin::Actions-->
      <div class="d-flex align-items-center py-1">
        <!--begin::Button-->
        <a href="{{ route('help-desk.index') }}" class="btn btn-sm btn-primary sync-products">Back</a>
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
                              <th> <b>Ticket number:</b> </th>
                              <td>{{ @$data->ticket_number ?? "" }}</td>
                            </tr>

                            <tr>
                              <th> <b>Name:</b> </th>
                              <td>{{ @$data->user->first_name ?? "" }} {{ @$data->user->last_name ?? "" }}</td>
                            </tr>

                            <tr>
                              <th> <b>Email:</b> </th>
                              <td>{{ @$data->user->email ?? "" }}</td>
                            </tr>

                            <tr>
                              <th> <b>Created date:</b> </th>
                              <td>{{ date('M d, Y h:i A',strtotime(@$data->created_at)) }}</td>
                            </tr>

                            <tr>
                              <th> <b> Last updated at:</b> </th>
                              <td>{{ date('M d, Y h:i A',strtotime(@$data->updated_at)) }}</td>
                            </tr>

                            {{-- <tr>
                              <th> <b>Department:</b> </th>
                              <td>{{ @$data->department->name ?? "" }}</td>
                            </tr> --}}
                            
                            <tr>
                              <th> <b>Urgency:</b> </th>
                              <td><b style="color: {{ @$data->urgency->color_code ??  "-" }}">{{ @$data->urgency->name ??  "-" }}</b></td>
                            </tr>

                            <tr>
                              <th> <b>Status:</b> </th>
                              <td><b style="color: {{ @$data->status->color_code ??  "-" }}">{{ @$data->status->name ??  "-" }}</b></td>
                            </tr>

                            <tr>
                              <th> <b>Subject:</b> </th>
                              <td>{!! @$data->subject ?? "" !!}</td>
                            </tr>

                            <tr>
                              <th> <b>Message:</b> </th>
                              <td>{!! @$data->message ?? "" !!}</td>
                            </tr>

                            <tr>
                              <th> <b>Images:</b> </th>
                              <td>
                                @if(isset($data->files) && count($data->files) > 0)
                                  @foreach($data->files as $key => $image)

                                    @if($image->filename && get_valid_file_url('sitebucket/help-desk',$image->filename))
                                      <img src="{{ get_valid_file_url('sitebucket/help-desk',$image->filename) }}" height="100" width="100" class="mr-10">
                                    @endif

                                  @endforeach
                                @endif
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

      <!-- Access only for admin and support department-->
      @if(userrole() == 1 || userdepartment() == 1)
      <div class="row gy-5 g-xl-8">
        <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
          <div class="card card-xl-stretch mb-5 mb-xl-8">
            <div class="card-header border-0 pt-5 min-0">
              <h5>Update Status</h5>
            </div>
            <div class="card-body">
              <div class="row mb-5">
                <div class="col-md-4">
                  <div class="form-group">
                    <select class="form-control form-control-lg form-control-solid" name="status" data-control="select2" data-hide-search="false" data-placeholder="Select a status" data-allow-clear="false">
                      <option value=""></option>
                      @foreach($status as $s)
                      <option value="{{ $s->id }}" @if($data->help_desk_status_id == $s->id) selected="" @endif>{{ $s->name }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group">
                    <a href="javascript:" class="btn btn-success  update_btn">Update</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      @endif


      <div class="row gy-5 g-xl-8">
        <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
          <div class="card card-xl-stretch mb-5 mb-xl-8">
            <div class="card-header border-0 pt-5 min-0">
              <h5>Comments</h5>
            </div>
            <div class="card-body">
              <form id="myForm" method="post">
                @csrf
                <input type="hidden" name="help_desk_id" value="{{ $data->id }}">
                <div class="row mb-5">
                  <div class="col-md-12">
                    <div class="form-group">
                      <textarea class="form-control form-control-solid" placeholder="add your comments here..." name="comment" rows="5"></textarea>
                    </div>
                  </div>
                </div>

                <div class="row mb-5 mt-10">
                  <div class="col-md-12">
                    <div class="form-group">
                      <input type="submit" value="Post Comment" class="btn btn-primary">
                    </div>
                  </div>
                </div>
              </form>

              <hr>

              <div class="row mb-5 mt-10" id="comment_list_row">
                
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/additional-methods.min.js"></script>

<script src="https://cdn.ckeditor.com/4.17.1/standard/ckeditor.js"></script>
<script src="{{ asset('assets') }}/assets/plugins/custom/sweetalert2/sweetalert2.all.min.js"></script>
<script>

  $(document).ready(function() {

    $(document).on('focus', '[name="comment"]', function(event) {
      event.preventDefault();
      CKEDITOR.replace( 'comment',{
        removePlugins: ['image', 'uploadimage'],
      });
    });




    $('body').on("submit", "#myForm", function (e) {
      e.preventDefault();
      var validator = validate_form();
      
      if (validator.form() != false) {
        $('[type="submit"]').prop('disabled', true);
        $.ajax({
          url: "{{route('help-desk.comment.store')}}",
          type: "POST",
          data: new FormData($("#myForm")[0]),
          async: false,
          processData: false,
          contentType: false,
          success: function (data) {
            if (data.status) {
              toast_success(data.message)
              // setTimeout(function(){
              //   window.location.reload();
              // },500)

              $('[type="submit"]').prop('disabled', false);
              CKEDITOR.instances.comment.setData('');
              $('#comment_list_row').html("");
              $('#view_more_col').html("");
              getCommentList();

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
            comment:{
              required:true,
            }
          },
          messages: {
            
          },
      });
      return validator;
    }

    getCommentList();

    function getCommentList($id = ""){
      $help_desk_id = $('[name="help_desk_id"]').val();

      
      $('#view_more_btn').remove();

      $.ajax({
        url: '{{ route('help-desk.comment.get-all') }}',
        type: 'POST',
        dataType:'json',
        data: {
                help_desk_id: $help_desk_id,
                id: $id,
                _token:'{{ csrf_token() }}',
              },
      })
      .done(function(data) {
        $('#comment_list_row').append(data.output);
        $('#view_more_col').html(data.button);
        // toast_success("Comment List Updated Successfully !");
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
  
    @if(userrole() == 1 || userdepartment() == 1)
      $(document).on('click', '.update_btn', function(event) {
        event.preventDefault();
        $status = $('[name="status"]').find('option:selected').val();

        Swal.fire({
          title: 'Are you sure want to change status?',
          //text: "Once deleted, you will not be able to recover this record!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, update it!'
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              url: '{{ route('help-desk.status') }}',
              method: "POST",
              data: {
                      _token:'{{ csrf_token() }}',
                      id:'{{ $data->id }}',
                      status:$status,
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
          }
        })
      });
    @endif
  
  });

</script>
@endpush