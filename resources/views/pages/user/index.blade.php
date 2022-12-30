@extends('layouts.mainlayout')
@section('title', 'Member')
@section('links')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.3.2/css/buttons.dataTables.min.css" />
@endsection
@section('content')
    <div class="d-flex align-items-center justify-content-between">
        <div class="pagetitle">
            <h1>Member Page</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Member</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        {{-- <a class="btn btn-warning float-end" href="{{ route('users.export') }}">Export User Data</a> --}}
        @can('role-create')
            <a href="{{ route('users.create') }}" class="d-flex align-items-center btn btn-primary">
                <i class="bi bi-plus-lg"></i> &nbsp; Create
            </a>
        @endcan
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('users.import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row py-4">
                                <div class="col-md-12">
                                    <h5>Excel Import</h5>
                                </div>
                                <div class="col-md-2">
                                    <input type="file" name="file" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <button class="btn btn-warning">Submit</button>
                                </div>
                            </div>
                        </form>
                        <hr>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="gender" style="font-weight: 700">Gender:</label>
                                    <select id="gender" class="form-select" aria-label="Default select example">
                                        <option value="0">Select Gender</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="role" style="font-weight: 700">Role:</label>
                                    <select id="role" class="form-select" aria-label="Default select example">
                                        <option value="0">Select Role</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="date" style="font-weight: 700">Date Range:</label>
                                    <div
                                        style="display: flex; flex-direction: column; justify-content:end; align-items:start; height: 100%">
                                        <input type="text" class="form-control" name="daterange" id="daterange" />
                                        <div id="from"></div>
                                        <div id="to"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <h5 class="card-title">Member List </h5>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-sm" id="userDataTable"
                                style="width: 100%; height: 100%">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Profile</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Email</th>
                                        <th scope="col">Phone</th>
                                        <th scope="col">Gender</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

    @foreach ($users as $user)
        <div class="modal fade" id="exampleModal{{ $user->id }}" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <form action="{{ route('send.email', ['id' => $user->id]) }}" method="POST">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Send Mail to ({{ $user->name }})</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="Title">Title:</label>
                                <input type="text" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="title">Content:</label>
                                <textarea name="content" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endforeach
@endsection
@section('script')
    <script type="text/javascript">
        $(function() {
            var table = $('#userDataTable').DataTable({
                processing: true,
                serverSide: true,
                dom: 'Bfrtip',
                lengthMenu: [
                    [10, 25, 50, -1],
                    ['10 rows', '25 rows', '50 rows', 'Show all']
                ],
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print', 'pageLength'
                ],
                ajax: {
                    url: "{{ route('getuserlist') }}",
                    data: function(d) {
                        d.gender = $('#gender').val(),
                            d.role = $('#role').val(),
                            d.from_date = $('#from_date').val(),
                            d.to_date = $('#to_date').val(),
                            d.search = $('input[type="search"]').val()
                    }
                },
                columns: [{
                        data: 'id',
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'profile',
                        name: 'profile'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'phone',
                        name: 'phone'
                    },
                    {
                        data: 'gender',
                        name: 'gender'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                    },

                ]
            });
            $('#gender').change(function() {
                table.draw();
            });
            $('#role').change(function() {
                table.draw();
            });
            $('#daterange').change(function() {
                table.draw();
            });
            $('#userDataTable').on('click', 'button.delete', function(e) {
                // console.log(e);
                e.preventDefault();

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You want to delete record",
                    icon: 'warning',
                    showCancelButton: true,
                    timer: 4000,
                    timerProgressBar: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $(e.target).closest('form').submit() // Post the surrounding form

                    }
                })

            });

        });
    </script>
    <script>
        $('input[name="daterange"]').daterangepicker({
            opens: 'left',
            drops: 'buttom',
        }, function(start, end, label) {
            document.getElementById("from").innerHTML =
                `<input name='from_date' id="from_date" type='date' value="${start.format('YYYY-MM-DD') }" hidden />`;
            document.getElementById("to").innerHTML =
                `<input name='to_date' id="to_date" type='date' value="${end.format('YYYY-MM-DD')}" hidden/>`;
            // console.log(start.format('YYYY-MM-DD'));
        });
    </script>
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/select/1.5.0/js/dataTables.select.min.js"></script>

@endsection
