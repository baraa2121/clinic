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
                        <form >
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="name-input">Name</label>
                                    <input type="text" class="form-control" id="name-input" placeholder="Enter name"
                                        name="name" >
                                </div>
                                <div class="form-group">
                                    <label for="email-input">Email address</label>
                                    <input type="email" class="form-control" id="email-input" placeholder="Enter email"
                                        name="email">
                                </div>
                                <div class="form-group">
                                    <label for="password-input">Password</label>
                                    <input type="password" class="form-control" id="password-input" placeholder="Password"
                                        name="password"    >
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputFile">Image</label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="image" name="image">
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
  <script>
        function store(){
            axios.post('{{route('admins.store')}}', {
                name: document.getElementById('name-input').value,
                email: document.getElementById('email-input').value,
                password: document.getElementById('password-input').value,
            }).then(function (response) {
                // handle success   
                 console.log(response);
                toastr.success(response.data.message);
                    window.location.href = '/cms/admins';
                document.getElementById('form').reset();
            
            }).catch(function (error) {
                // handle error
                 console.log(error);
                toastr.error(error.response.data.message);
            });
        }
  </script>
@endsection
