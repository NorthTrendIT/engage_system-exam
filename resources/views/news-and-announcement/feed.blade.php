@if($notifications->count() > 0)
    @foreach($notifications as $data)
    <div class="mt-5 border border-bottom-5 p-3">
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
                        <td class="text-center" colspan="2"><p class="lead"><h3 class="display-6">{{ @$data->title ?? "" }}</h3></p></td>
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
            <div class="row mb-5">
                <h1 class="d-block mb-5">Documents</h1>
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
                    @else
                        <div class="col-md-4">
                            <video width="320" height="240" class="img-thumbnail" controls>
                                <source src="{{ asset('sitebucket/news-and-announcement/'.$item->file) }}" type="video/{{$ext}}">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
        <div class="row">
            <div class="d-flex justify-content-end">
                <p class="">Posted: <i class="text-info">{{$data->created_at->toDayDateTimeString()}}</i></p>
            </div>
        </div>
    </div>
    @endforeach
@else
    <div class="row mt-10 bg-light p-5"><h1 class="notif-contents-end text-center">No more posts to show.</h1></div>
@endif   <!-- end of count -->