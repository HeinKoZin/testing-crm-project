@extends('layouts.mainlayout')
@section('title', 'Edit User')
@section('links')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.3.2/css/buttons.dataTables.min.css" />
@endsection
@section('content')
    <div class="d-flex align-items-center justify-content-between">
        <div class="pagetitle">
            <h1>Edit User Page</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Users</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->
        <a href="{{ route('users') }}" class="d-flex align-items-center btn btn-primary">
            <i class="bi bi-arrow-left-circle"></i> &nbsp; Back
        </a>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Edit User </h5>
                        <form action="{{ route('users.update', ['id' => $user->id]) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label>User Profile:</label>
                                    <br>
                                    <label for="imgInp">
                                        <img id="blah"
                                            src="{{ asset($user->profile ? $user->profile : 'assets/img/images.jpg') }}"
                                            class="rounded shadow-sm p-1"
                                            style="transition: 0.4s; height: 100px; width: 100px" />
                                    </label>
                                    <input hidden accept="image/*" name="profile" type='file' id="imgInp"
                                        class="mx-2" />
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group pb-4">
                                        <label for="name"><strong>User Name <span style="color: red">*</span>
                                                :</strong></label>
                                        <input type="text" class="@error('name') is-invalid @enderror form-control"
                                            name="name" value="{{ $user->name }}">
                                        @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="permission"><strong>Role Permission <span style="color: red">*</span>
                                                :</strong></label><br>
                                        <select class="form-select @error('role_id') is-invalid @enderror form-control"
                                            aria-label="Default select example" name="role_id">
                                            <option>Select Role</option>
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->id }}"
                                                    @isset($userRole->id)
                                                    {{ $userRole->id == $role->id ? 'selected' : '' }}
                                                    @endisset>
                                                    {{ $role->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('role_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group pb-4">
                                        <label for="email"><strong>Email <span style="color: red">*</span>
                                                :</strong></label>
                                        <input type="email" class="@error('email') is-invalid @enderror form-control"
                                            name="email" value="{{ $user->email }}">
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group pb-4">
                                        <label for="phone"><strong>Phone :</strong></label>
                                        <input type="text" class="@error('phone') is-invalid @enderror form-control"
                                            name="phone" value="{{ $user->phone }}">
                                        @error('phone')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group pb-4">
                                        <label for="phone"><strong>Gender :</strong></label>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="gender" id="inlineRadio1"
                                                value="male" {{ $user->gender == 'male' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="inlineRadio1">Male</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="gender" id="inlineRadio2"
                                                value="female" {{ $user->gender == 'female' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="inlineRadio2">Female</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="gender" id="inlineRadio2"
                                                value="other" {{ $user->gender == 'other' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="inlineRadio2">Other</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group pb-4">
                                        <label for="address"><strong>Address:</strong></label>
                                        <textarea name="addresss" class="form-control">{{ $user->address }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="yourPassword" class="form-label"><strong>Password <span
                                                style="color: red">*</span> :</strong></label>
                                    <input id="password" type="password"
                                        class="form-control @error('password') is-invalid @enderror" name="password"
                                        autocomplete="new-password">

                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="yourPassword" class="form-label"><strong>Confirm Password <span
                                                style="color: red">*</span> :</strong></label>
                                    <input id="password-confirm" type="password" class="form-control"
                                        name="password_confirmation" autocomplete="new-password">
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary mt-4">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </section>
@endsection
@section('script')
    <script>
        imgInp.onchange = evt => {
            const [file] = imgInp.files
            if (file) {
                blah.src = URL.createObjectURL(file)
            }
        }
    </script>
@endsection
