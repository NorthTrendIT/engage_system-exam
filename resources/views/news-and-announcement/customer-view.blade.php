@extends('layouts.master')

@section('title','Notification')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="toolbar" id="kt_toolbar">
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
        <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
            <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Notification</h1>
        </div>

        <div class="d-flex align-items-center py-1">
            <a href="{{ route('news-and-announcement.index') }}" class="btn btn-sm btn-primary sync-products">Back</a>
        </div>

        </div>
    </div>

    <div class="post d-flex flex-column-fluid detail-view-table" id="kt_post">
        <div id="kt_content_container" class="container-xxl">
            <div class="row gy-5 g-xl-8">
                <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
                <div class="card card-xl-stretch mb-5 mb-xl-8">

                    <div class="card-body">
                        <div class="notif-contents">
                            @if($notifications->count() > 0)
                                @foreach($notifications as $data)
                                    <div class="mt-5 border border-5 border-secondary p-3">
                                        <div class="row message-notif" data-id="{{$data->id}}">
                                            <div class="col-md-12">
                                            <div class="form-group">
                                                <!--begin::Table container-->
                                                <div class="table-responsive">
                                                <!--begin::Table-->
                                                <table class="table table-bordered" id="myTable">
                                                    <!--begin::Table head-->
                                                    <thead>
                                                        <tr>
                                                        {{-- <th> </th> --}}
                                                        <td class="text-center" colspan="2">
                                                            <p class="lead"><h3 class="display-6">{{ @$data->title ?? "" }}</h3></p>
                                                            <p class="">Posted: <i class="text-info">{{$data->created_at->toDayDateTimeString()}}</i></p>
                                                        </td>
                                                        </tr>

                                                        <tr>
                                                        {{-- <th></th> --}}
                                                        <td colspan="2">{!! @$data->message ?? "" !!}</td>
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
                                        @if(!empty($data->documents) && count($data->documents) > 0)
                                            <div class="row">
                                                <h1 class="d-block mb-5 text-center">Attached Documents</h1>
                                                @foreach($data->documents as $item)
                                                @php
                                                    $temp = explode('.', $item->file);
                                                    $ext = strtolower(end($temp));
                                                @endphp
                                                    @if(in_array($ext, ['jpeg', 'jpg', 'png', 'bmp', 'tif', 'tiff', 'webp']))
                                                        <div class="col-md-4">
                                                            <a href="{{ asset('sitebucket/news-and-announcement/'.$item->file) }}" class="fancybox">
                                                                <img class="img-thumbnail" src="{{ asset('sitebucket/news-and-announcement/'.$item->file) }}" height="240px" width="320px"/>
                                                            </a>
                                                        </div>
                                                    @elseif(in_array($ext, ['mp4']))
                                                        <div class="col-md-4">
                                                            <video width="320" height="240" class="img-thumbnail" controls>
                                                                <source src="{{ asset('sitebucket/news-and-announcement/'.$item->file) }}" type="video/{{$ext}}">
                                                                Your browser does not support the video tag.
                                                            </video>
                                                        </div>
                                                    @else
                                                        <div class="col-md-4">
                                                            <iframe src='https://docs.google.com/viewer?url={{ asset('sitebucket/news-and-announcement/'.$item->file) }}&embedded=true' frameborder='0'></iframe>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @endif
                                        {{-- <div class="row">
                                            <div class="d-flex justify-content-end">
                                                <p class="">Posted: <i class="text-info">{{$data->created_at->toDayDateTimeString()}}</i></p>
                                            </div>
                                        </div> --}}
                                    </div>
                                @endforeach
                            @else
                                <div class="row mt-10 bg-light p-5"><h1 class="notif-contents-end text-center">No more posts to show.</h1></div>
                            @endif   <!-- end of count -->
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
{{-- <script src="{{ asset('assets') }}/assets/plugins/custom/sweetalert2/sweetalert2.all.min.js"></script> --}}
<script>
$(document).ready(function() {
    $(window).scroll(function() {
        if($(window).scrollTop() + $(window).height() == $(document).height()) {
            var notif_id = $(document).find("div.notif-contents .message-notif").last().attr('data-id');

            $.get( "{{ route('news-and-announcement.feed-previous') }}", { notif_id : notif_id},  function(resp) {
                $(document).find('.notif-contents .notif-contents-end').parent().remove();
                $('.notif-contents').append(resp);
            })
            // .done(function() {
            //         alert( "second success" );
            //     })
            .fail(function(xhr, status, error) {
                if(xhr.status === 404){ //previous solution
                    if(!$('.notif-contents .notif-contents-end').length){
                        $('.notif-contents').append('<div class="row mt-10 bg-light p-5"><h1 class="notif-contents-end text-center">No more posts to show.</h1></div>');
                    }
                }else{
                    alert("An AJAX error occured: " + status + "\nError: " + error);
                }
            })
        }
    });
});
</script>
@endpush
