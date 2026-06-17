@extends('cms.layouts.parent')

@section('content')
    <section class="content pt-3">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-10">

                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-header bg-white border-0 pt-4">
                            <h3 class="card-title fw-bold">Add Doctor</h3>
                        </div>

                        <form id="form">
                            @csrf

                            <div class="card-body">

                                <div class="row">

                                    <!-- Left Side -->
                                    <div class="col-md-6">

                                        <!-- Upload Image -->
                                        <div class="form-group mb-4 text-center">
                                            <label for="image-input" class="d-block mb-2">
                                                <img src="{{ asset('cms/dist/img/user2-160x160.jpg') }}" width="90"
                                                    height="90" class="rounded-circle border"
                                                    style="object-fit: cover; cursor:pointer;">
                                            </label>

                                            <input type="file" id="image-input" class="d-none" name="image">
                                            <p class="text-muted">Upload doctor picture</p>
                                        </div>

                                        <!-- Doctor Name -->
                                        <div class="form-group mb-3">
                                            <label for="name-input">Doctor name</label>
                                            <input type="text" class="form-control rounded-pill" id="name-input"
                                                placeholder="Name" name="name">
                                        </div>

                                        <!-- Doctor Email -->
                                        <div class="form-group mb-3">
                                            <label for="email-input">Doctor Email</label>
                                            <input type="email" class="form-control rounded-pill" id="email-input"
                                                placeholder="Your email" name="email">
                                        </div>

                                        <!-- Password -->
                                        <div class="form-group mb-3">
                                            <label for="password-input">Doctor Password</label>
                                            <input type="password" class="form-control rounded-pill" id="password-input"
                                                placeholder="Password" name="password">
                                        </div>

                                        <!-- Experience -->
                                        <div class="form-group mb-3">
                                            <label for="experience-input">Experience</label>

                                            <select class="form-control rounded-pill" id="experience-input"
                                                name="experience_years">

                                                <option value="1">1 year</option>
                                                <option value="2">2 years</option>
                                                <option value="3">3 years</option>
                                                <option value="5">5 years</option>
                                                <option value="10">10 years</option>

                                            </select>
                                        </div>

                                    </div>

                                    <!-- Right Side -->
                                    <div class="col-md-6">

                                        <!-- Specialty -->
                                        <div class="form-group mb-3 mt-md-5">
                                            <label for="specialization-input">Specialization</label>

                                            <select class="form-control rounded-pill" id="specialization-input"
                                                name="specialization">

                                                <option value="General physician">General physician</option>
                                                <option value="Pediatrician">Pediatrician</option>
                                                <option value="Cardiologist">Cardiologist</option>
                                                <option value="Education">Education</option>

                                            </select>
                                        </div>

                                        <!-- Address -->
                                        <div class="form-group mb-3">
                                            <label for="address-input">Address</label>

                                            <input type="text" class="form-control rounded-pill mb-2" id="address-input"
                                                placeholder="Address 1" name="address">


                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="department-input">Department</label>

                                            <select class="form-control rounded-pill" id="department-input"
                                                name="department_id">

                                                @foreach ($data as $department)
                                                    <option value="{{ $department->id }}">
                                                        {{ $department->name }}
                                                    </option>
                                                @endforeach

                                            </select>
                                        </div>

                                    </div>

                                </div>



                            </div>

                            <!-- Footer -->
                            <div class="card-footer bg-white border-0 pb-4">
                                <button type="button" onclick="store()" class="btn btn-primary px-5 rounded-pill">

                                    Add Doctor
                                </button>
                            </div>

                        </form>
                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection


@section('scripts')
    <script>
        function store() {

            let formData = new FormData();

            formData.append('image', document.getElementById('image-input').files[0]);
            formData.append('name', document.getElementById('name-input').value);
            formData.append('email', document.getElementById('email-input').value);
            formData.append('password', document.getElementById('password-input').value);
            formData.append('experience_years', document.getElementById('experience-input').value);
            formData.append('specialization', document.getElementById('specialization-input').value);
            formData.append('address', document.getElementById('address-input').value);
            formData.append('department_id', document.getElementById('department-input').value);

            axios.post('{{ route('doctors.store') }}', formData)

                .then(function(response) {

                    toastr.success(response.data.message);

                    window.location.href = '/cms/doctors';

                }).catch(function(error) {

                    toastr.error(error.response.data.message);

                });
        }
    </script>
@endsection
