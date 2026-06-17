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
                                                    <option value="{{$user->id}}" @selected($ticket->user_id == $user->id)> {{$user->name}}</option>
                                                @endforeach
                                           
                                           </select>
                                         </div>

                                          <div class="form-group">
                                         <label>Status</label>
                                           <select class="form-control " id="status">
                                                    <option value="Pending"@selected($ticket->status ==='Pending')>Pending</option>
                                                    <option value="Started"@selected($ticket->status ==='Started')>Started</option>
                                                    <option value="Canceled"@selected($ticket->status ==='Canceled')>Canceled</option>
                                                    <option value="Closed"@selected($ticket->status ==='Closed')>Closed</option>
                                                    <option value="Rejected"@selected($ticket->status ==='Rejected')>Rejected</option>
                                          </select>
                                         </div>
                                        
                                <div class="form-group">
                                    <label for="title-input">Title</label>
                                    <input type="text" class="form-control" id="title-input" placeholder="Enter title"
                                        name="title" value="{{$ticket->title}}">
                                </div>
                                <div class="form-group">
                                    <label for="description-input">Description</label>
                                    <input type="text" class="form-control" id="description-input" placeholder="Enter description"
                                        name="description" value="ticket->description">
                                </div>
                            </div>
                            <!-- /.card-body -->

                            <div class="card-footer">
                                <button type="button" onclick="update('{{$ticket->id }}')" class="btn btn-primary">Submit</button>
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
            //Promise - ASYNC
            axios.put(`/cms/tickets/${id}`, {
                title: document.getElementById('title-input').value,
                description: document.getElementById('description-input').value,
                user_id: document.getElementById('user-id').value,
                status: document.getElementById('status').value,
            }).then(function(response) {
                // 2.x.x
                console.log(response);
                toastr.success(response.data.message);
                window.location.href ='/cms/tickets';
            }).catch(function(error) {
                // 4.x.x/5.x.x
                console.log(error);
                toastr.error(error.response.data.message);
            })
        }
    </script>
@endsection
