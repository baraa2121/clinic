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
                                        name="name" value="{{ $doctor->name }}">
                                </div>
                                <div class="form-group">
                                    <label for="email-input">Email address</label>
                                    <input type="email" class="form-control" id="email-input" placeholder="Enter email"
                                        name="email" value="{{ $doctor->email }}">
                                </div>
                            </div>
                            <!-- /.card-body -->

                            <div class="card-footer">
                                <button type="button" onclick="update('{{ $doctor->id }}')"
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
            axios.put(`/cms/doctors/${id}`, {
                name: document.getElementById('name-input').value,
                email: document.getElementById('email-input').value,
            }).then(function(response) {
                //
                toastr.success(response.data.message);
                window.location.href = '{{ route('doctors.index') }}';
            }).catch(function(error) {
                //
                toastr.error(error.response.data.message);
            });
        }
    </script>
@endsection
