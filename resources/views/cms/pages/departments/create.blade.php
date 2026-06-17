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
                                    <label for="description-input">Description</label>
                                    <input type="text" class="form-control" id="description-input" placeholder="Enter description"
                                        name="description">
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
            axios.post('{{route('departments.store')}}', {
                name: document.getElementById('name-input').value,
                description: document.getElementById('description-input').value,
            }).then(function (response) {
                // handle success   
                 console.log(response);
                toastr.success(response.data.message);
                    window.location.href = '/cms/departments';
                document.getElementById('form').reset();
            
            }).catch(function (error) {
                // handle error
                 console.log(error);
                toastr.error(error.response.data.message);
            });
        }
  </script>
@endsection
