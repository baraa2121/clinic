@extends('cms.layouts.parent')

@section('content')
@section('title', 'Read Doctors')

<section class="content pt-3">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-10">

                <div class="card shadow-sm border-0 rounded-4">
                    
                    <div class="card-header bg-white border-0 pt-4">
                        <h3 class="card-title fw-bold">Doctor Details</h3>
                    </div>

                    <div class="card-body">

                        <div class="row">

                            <!-- Left Side -->
                            <div class="col-md-6 text-center">

                                <!-- Image -->
                                <div class="form-group mb-4">
                                    <img src="{{ asset('storage/' . $doctor->image) }}"
                                         width="120"
                                         height="120"
                                         class="rounded-circle border"
                                         style="object-fit: cover;">
                                </div>

                                <div class="form-group mb-3">
                                    <label class="fw-bold">Doctor Name</label>
                                    <p>{{ $doctor->name }}</p>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="fw-bold">Email</label>
                                    <p>{{ $doctor->email }}</p>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="fw-bold">Experience</label>
                                    <p>{{ $doctor->experience_years }} years</p>
                                </div>

                            </div>

                            <!-- Right Side -->
                            <div class="col-md-6">

                                <div class="form-group mb-3 mt-md-3">
                                    <label class="fw-bold">Specialty</label>
                                    <p>{{ $doctor->specialization }}</p>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="fw-bold">Address</label>
                                    <p>
                                        {{ $doctor->address1 }} <br>
                                    </p>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="fw-bold">About</label>
                                    <p class="text-muted">
                                        {{ $doctor->bio }}
                                    </p>
                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="card-footer bg-white border-0 pb-4">
                        <a href="{{ route('doctors.index') }}"
                           class="btn btn-secondary px-4 rounded-pill">
                            Back
                        </a>
                    </div>

                </div>

            </div>
        </div>
    </div>
</section>
@endsection