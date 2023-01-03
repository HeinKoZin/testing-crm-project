@extends('layouts.mainlayout')
@section('title', 'Member Show')
@section('links')
@endsection
@section('content')
    <div class="d-flex align-items-center justify-content-between">
        <div class="pagetitle">
            <h1>Member Detail Page</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Member</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        {{-- <a class="btn btn-warning float-end" href="{{ route('members.export') }}">Export member Data</a> --}}
        <a href="{{ route('members') }}" class="d-flex align-items-center btn btn-primary">
            <i class="bi bi-arrow-left-circle"></i> &nbsp; Back
        </a>
    </div>

    <section class="section">
        <div class="d-flex justify-content-center align-items-center" style="height: 100%">
            <div class="card p-4">
                <div class="row">
                    <div class="col-md-6">
                        <img src="{{ asset($member->profile ? $member->profile : 'assets/img/images.jpg') }}"
                            class="img-fluid" alt="Profile Image">

                    </div>
                    <div class="col-md-6">
                        <h5>{{ $member->name }} </h5>
                        <p><i class="bi bi-envelope"></i>&nbsp; {{ $member->email }}</p>
                        @if ($member->phone)
                            <p><i class="bi bi-telephone"></i>&nbsp; {{ $member->phone }}</p>
                        @endif
                        @if ($member->gender)
                            <span class="badge rounded-pill text-bg-primary">{{ $member->gender }}</span>
                        @endif
                        <br>
                        <button type="button" class="btn btn-outline-success btn-sm mt-4" data-bs-toggle="modal"
                            data-bs-target="#exampleModal">
                            Qr Scan
                        </button>
                        <!-- Modal -->
                        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="exampleModalLabel">Profile Qr Code</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="d-flex justify-content-center">
                                            {!! QrCode::size(200)->generate(
                                                '[' . $member->name . ', ' . $member->email . ', ' . $member->phone . ', ' . $member->gender . ']',
                                            ) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('script')

@endsection
