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
                        <form>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="name-input">Name</label>
                                    <input type="text" class="form-control" id="name-input" placeholder="Enter name"
                                        name="name" value="{{ $user->name }}">
                                </div>
                                <div class="form-group">
                                    <label for="email-input">Email address</label>
                                    <input type="email" class="form-control" id="email-input" placeholder="Enter email"
                                        name="email" value="{{ $user->email }}">
                                </div>
                            </div>
                            <!-- /.card-body -->

                            <div class="card-footer">
                                <button type="button" onclick="update('{{ $user->id }}')"
                                    class="btn btn-primary">Submit</button>
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
        function update(id) {
            axios.put(`/cms/users/${id}`, {
                name: documenet.getElementById('name-input').value,
                email: documenet.getElementById('email-input').value,
            }).then(function(response) {
                //
                toastr.success(response.data.message);
                window.location.href = '{{ route('users.index') }}';
            }).catch(function(error) {
                //
                toastr.error(error.response.data.message);
            });
        }
    </script>
@endsection
