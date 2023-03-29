<!DOCTYPE html>
<html>
<head>
    <title>Sales Personnel upload</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-8 mt-5">
                <h3>Upload Sales Personnel</h3>
   
                @if(Session::has('success'))
                    <div class="alert alert-success">
                        {{ Session::get('success') }}
                        @php
                            Session::forget('success');
                        @endphp
                    </div>
                @endif

                @if($errors->any())
                    {!! implode('', $errors->all('<div class="text-danger"><code>:message</code></div>')) !!}
                @endif
           
                <form method="post" action="/uploadfile" class="mt-4" accept-charset="UTF-8" enctype="multipart/form-data">
          
                    {{ csrf_field() }}
          
                    <div class="form-group mb-3">
                        <label>Select File:</label>
                        <input type="file" name="csv" class="form-control" required="">
                    </div>
           
                    <div class="form-group">
                        <button class="btn btn-success btn-submit btn-sm">Submit</button>
                    </div>
                </form>
            </div>    
        </div>
    </div>
</body>
</html>