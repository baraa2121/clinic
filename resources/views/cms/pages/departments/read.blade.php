@extends('cms.layouts.parent')

@section('content')

<section class="content pt-3">
    <div class="container-fluid">

        <div class="row justify-content-center">
            <div class="col-md-10">

                <div class="card shadow-sm border-0 rounded-4">

                    <!-- Header -->
                    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                        <h3 class="card-title fw-bold mb-0">Departments</h3>

                        <a href="{{ route('departments.create') }}" class="btn btn-primary rounded-pill px-4">
                            + Add Department
                        </a>
                    </div>

                    <!-- Table -->
                    <div class="card-body">

                        <table class="table table-hover align-middle text-center">

                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Department Name</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($data as $department)
                                    <tr>
                                        <td>{{ $department->id }}</td>
                                        <td>{{ $department->name }}</td>
                                        <td>{{ $department->created_at->format('Y-m-d') }}</td>

                                        <td>

                                            <!-- Edit -->
                                            <a href="{{ route('departments.edit', $department->id) }}"
                                               class="btn btn-sm btn-warning rounded-pill px-3">
                                                Edit
                                            </a>

                                            <!-- Delete -->
                                            <button class="btn btn-sm btn-danger rounded-pill px-3"
                                                onclick="deleteDepartment({{ $department->id }})">
                                                Delete
                                            </button>

                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-muted">
                                            No departments found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>

                        </table>

                    </div>

                    <!-- Pagination -->
                    <div class="card-footer bg-white border-0">
                        {{ $data->links() }}
                    </div>

                </div>

            </div>
        </div>

    </div>
</section>

@endsection


@section('scripts')

<script>
    function deleteDepartment(id) {

        axios.delete('/cms/departments/' + id)

            .then(function (response) {

                toastr.success(response.data.message);

                location.reload();

            })
            .catch(function (error) {

                toastr.error(error.response.data.message);

            });
    }
</script>

@endsection