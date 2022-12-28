@extends('layouts.mainlayout')
@section('title', 'Create Role')
@section('links')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.3.2/css/buttons.dataTables.min.css" />
@endsection
@section('content')
    <div class="d-flex align-items-center justify-content-between">
        <div class="pagetitle">
            <h1>Create Role Page</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Roles</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->
        <a href="{{ route('roles') }}" class="d-flex align-items-center btn btn-primary">
            <i class="bi bi-arrow-left-circle"></i> &nbsp; Back
        </a>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Create Role </h5>
                        <form action="{{ route('roles.save') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group pb-4">
                                        <label for="name"><strong>Role Name:</strong></label>
                                        <input type="text" class="@error('name') is-invalid @enderror form-control"
                                            name="name" value="{{ old('name') }}">
                                        @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="permission"><strong>Role Permission:</strong></label><br>
                                        @foreach ($permission as $value)
                                            <input type="checkbox" id="permission" name="permission[]"
                                                value="{{ $value->id }}">
                                            <label for="permission">{{ $value->name }}</label><br>
                                        @endforeach
                                    </div>
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
@endsection
