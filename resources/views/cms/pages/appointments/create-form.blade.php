@extends('cms.layouts.parent')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Quick Example</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form id="form">
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <label>User</label>
                                    <select class="form-control " id="user-id">
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}"> {{ $user->name }}</option>
                                        @endforeach

                                    </select>
                                </div>
                                {{-- <div class="form-group">
                                         <label>Status</label>
                                           <select id="user-id" class="form-control">
                                           <option>option 1</option>
                                           <option>option 2</option>
                                           <option>option 3</option>
                                           <option>option 4</option>
                                           <option>option 5</option>
                                           </select>
                                         </div> --}}
                                <div class="form-group">
                                    <label for="title-input">Title</label>
                                    <input type="text" class="form-control" id="title-input" placeholder="Enter title"
                                        name="title">
                                </div>
                                <div class="form-group">
                                    <label for="description-input">Description</label>
                                    <input type="text" class="form-control" id="description-input"
                                        placeholder="Enter description" name="description">
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputFile">Image </label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="image">
                                            <label class="custom-file-label" for="image">Choose file</label>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                            <!-- /.card-body -->

                            <div class="card-footer">
                                <button type="button" onclick="store()" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                    <!-- /.card -->
                </div>
                <!--/.col (left) -->
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>
@endsection


@section('scripts')
<script src="{{asset('cms/plugins/bs-custom-file-input/bs-custom-file-input.min.js')}}"></script>
<script>
$(function () {
  bsCustomFileInput.init();
});
</script>
    <script>
        function store() {
            //Promise - ASYNC
            axios.post('{{ route('tickets.store') }}', {
                title: document.getElementById('title-input').value,
                description: document.getElementById('description-input').value,
                // status: document.getElementById('password-input').value,
                user_id: document.getElementById('user-id').value,
            }).then(function(response) {
                // 2.x.x
                console.log(response);
                toastr.success(response.data.message);
                document.getElementById('form').reset();
            }).catch(function(error) {
                // 4.x.x/5.x.x
                console.log(error);
                toastr.error(error.response.data.message);
            })
        }
    </script>
@endsection
