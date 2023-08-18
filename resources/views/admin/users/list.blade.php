@extends('layouts.admin')
@section('title')
    Istifadəçi Siyahısı
@endsection

@section('css')
    <link href="{{asset('assets/admin/plugins/flatpickr/flatpickr.min.css')}}" rel="stylesheet">
    <link href="{{asset('assets/admin/plugins/select2/css/select2.min.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('assets/front/aos/aos.css')}}">

    <style>
        .table-hover > tbody > tr:hover {
            --bs-table-hover-bg: transparent;
            background: #363638;
            color: white;
        }

        table td {
            vertical-align: middle;
        }
    </style>
@endsection

@section('content')
    <x-bootstrap.card>
        <x-slot:header>
            <h2>Istifadəçi Siyahısı</h2>
        </x-slot:header>
        <x-slot:body>
            <form action="" id="formFilter">
                <div class="row">
                    <div class="col-3 my-2">
                        <select class="form-select" aria-label="Status Seçin" name="status">
                            <option value="{{null}}">Status</option>
                            <option value="0" {{request()->get('status')==="0" ? 'selected' : ''}}>Passiv</option>
                            <option value="1" {{request()->get('status')==="1" ? 'selected' : ''}}>Aktiv</option>
                        </select>
                    </div>

                    <div class="col-3 my-2">
                        <select class="form-select" aria-label="Is Admin" name="is_admin">
                            <option value="{{null}}">User Role</option>
                            <option value="0" {{request()->get('is_admin')==="0" ? 'selected' : ''}}>User</option>
                            <option value="1" {{request()->get('is_admin')==="1" ? 'selected' : ''}}>Admin</option>
                        </select>
                    </div>

                    <div class="col-3 my-2">
                        <input type="text" class="form-control" name="search_text" placeholder="Name, Username, Email"
                               value="{{request()->get('search_text')}}">
                    </div>
                    <hr>
                    <div class="col-6 mb-3 d-flex">
                        <button type="submit" class="btn btn-primary w-50 me-4">Filtrlə</button>
                        <button type="button" class="btn btn-warning w-50 btnClearFilter">Filtri Təmizlə</button>
                    </div>
                    <hr>
                </div>
            </form>
            <x-bootstrap.table
                :class="'table-stripped table-hover'"
                :is-responsive="1"
            >
                <x-slot:columns>
                    <th scope="col">Image</th>
                    <th scope="col">Name</th>
                    <th scope="col">Username</th>
                    <th scope="col">Email</th>
                    <th scope="col">Status</th>
                    <th scope="col">Is Admin</th>
                    <th scope="col">Actions</th>
                </x-slot:columns>

                <x-slot:rows>
                    @foreach($list as $user)

                        <tr id="row-{{$user->id}}">
                            <td>
                                @if(!empty($user->image))
                                    <img src="{{asset($user->image)}}" alt="" height="55" data-aos="flip-right">
                                @endif
                            </td>
                            <td>{{$user->name}}</td>
                            <td>{{$user->username}}</td>
                            <td>{{$user->email}}</td>
                            <td>
                                @if($user->status)
                                    <a href="javascript:void(0)" class="btn btn-success btn-sm btnChangeStatus" data-id="{{$user->id}}">Aktiv</a>
                                @else
                                    <a href="javascript:void(0)" class="btn btn-danger btn-sm btnChangeStatus" data-id="{{$user->id}}">Passiv</a>
                                @endif
                            </td>
                            <td>
                                @if($user->is_admin)
                                    <a href="javascript:void(0)" class="btn btn-primary btn-sm btnChangeIsAdmin" data-id="{{$user->id}}">Admin</a>
                                @else
                                    <a href="javascript:void(0)" class="btn btn-secondary btn-sm btnChangeIsAdmin" data-id="{{$user->id}}">User</a>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex">
                                    <a href="{{route('users.edit',['user' => $user->username])}}" class="btn btn-warning btn-sm"><i
                                            class="material-icons ms-0">edit</i></a>
                                    <a href="javascript:void(0)"
                                       class="btn btn-danger btn-sm btnDelete"
                                       data-id="{{$user->id}}"
                                       data-name="{{$user->name}}">
                                        <i class="material-icons ms-0">delete</i>
                                    </a>
                                   @if($user->deleted_at)
                                        <a href="javascript:void(0)"
                                           class="btn btn-primary btn-sm btnRestore"
                                           data-id="{{$user->id}}"
                                           data-name="{{$user->name}}">
                                            <i class="material-icons ms-0">undo</i>
                                        </a>
                                   @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </x-slot:rows>
            </x-bootstrap.table>
            <div class="d-flex justify-content-center">
                {{--                {{$list->links('vendor.pagination.bootstrap-5')}}--}}
                {{$list->appends(request()->all())->onEachside(1)->links()}}

            </div>
        </x-slot:body>
    </x-bootstrap.card>
@endsection

@section('js')
    <script>
        $(Document).ready(function () {

            $('select').select2();

            $('.btnChangeStatus').click(function (){
                let userID = $(this).data('id');
                let self = $(this);
                Swal.fire({
                    title: 'Statusu dəyişdirməy istədiyinizə əminsiz?',
                    showDenyButton: true,
                    showCancelButton: true,
                    confirmButtonText: 'Hə',
                    denyButtonText: `Yox`,
                    cancelButtonText: `Ləvğ et`,
                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed)
                    {
                        $.ajax({
                           method: "POST",
                            url: "{{  route('users.changeStatus') }}",
                            data: {
                                id: userID
                            },
                            success: function (data){
                                if(data.user_status)
                                {
                                    self.removeClass('btn-danger');
                                    self.addClass('btn-success');
                                    self.text('Aktiv');
                                }
                                else
                                {
                                    self.removeClass('btn-success');
                                    self.addClass('btn-danger');
                                    self.text('Passiv');
                                }

                                Swal.fire({
                                    title: 'Uğurlu',
                                    confirmButtonText: 'yaxşı',
                                    text: 'Status dəyişdirildi',
                                    icon: 'success',
                                });
                            },
                            error: function () {
                                console.log('ERRORRRR');
                            }
                        })
                    }
                    else if (result.isDenied)
                    {
                        Swal.fire({
                            title: 'Info',
                            confirmButtonText: 'yaxşı',
                            text: 'Heçbir dəyişiklik edilmədi',
                            icon: 'info',
                        });
                    }
                })

            });

            $('.btnChangeIsAdmin').click(function (){
                let userID = $(this).data('id');
                let self = $(this);
                Swal.fire({
                    title: 'Admin statusunu dəyişdirməy istədiyinizə əminsiz?',
                    showDenyButton: true,
                    showCancelButton: true,
                    confirmButtonText: 'Hə',
                    denyButtonText: `Yox`,
                    cancelButtonText: `Ləvğ et`,
                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed)
                    {
                        $.ajax({
                           method: "POST",
                            url: "{{  route('users.changeIsAdmin') }}",
                            data: {
                                id: userID
                            },
                            success: function (data){
                                if(data.user_is_admin)
                                {
                                    self.removeClass('btn-secondary');
                                    self.addClass('btn-primary');
                                    self.text('Admin');
                                }
                                else
                                {
                                    self.removeClass('btn-primary');
                                    self.addClass('btn-secondary');
                                    self.text('User');
                                }

                                Swal.fire({
                                    title: 'Uğurlu',
                                    confirmButtonText: 'yaxşı',
                                    text: 'Is Admin dəyişdirildi',
                                    icon: 'success',
                                });
                            },
                            error: function () {
                                console.log('ERRORRRR');
                            }
                        })
                    }
                    else if (result.isDenied)
                    {
                        Swal.fire({
                            title: 'Info',
                            confirmButtonText: 'yaxşı',
                            text: 'Heçbir dəyişiklik edilmədi',
                            icon: 'info',
                        });
                    }
                })

            });

            $('.btnDelete').click(function () {
                let userID = $(this).data('id');
                let userName = $(this).data('name');
                let self = $(this);

                Swal.fire({
                    title: userName + ' i Silmək istədiyinizə əminsiz?',
                    showDenyButton: true,
                    showCancelButton: true,
                    confirmButtonText: 'Hə',
                    denyButtonText: `Yox`,
                    cancelButtonText: `Ləvğ et`,
                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        $.ajax({
                            method: 'POST',
                            url: "{{route('users.delete')}}",
                            data: {
                                '_method': 'DELETE',
                                id: userID
                            },
                            async: false,
                            success: function (data){
                                $('#row-' + userID).remove();

                                Swal.fire({
                                    title: 'Uğurlu',
                                    confirmButtonText: 'yaxşı',
                                    text: 'İstifadəçi silindi',
                                    icon: 'success',
                                });
                            },
                            error: function (){
                                console.log('ERRORRRR');
                            }
                        });

                    } else if (result.isDenied) {
                        Swal.fire({
                            title: 'Info',
                            confirmButtonText: 'yaxşı',
                            text: 'Heçbir dəyişiklik edilmədi',
                            icon: 'info',
                        });
                    }
                })

            });

            $('.btnRestore').click(function () {
                let userID = $(this).data('id');
                let userName = $(this).data('name');
                let self = $(this);

                Swal.fire({
                    title: userName + ' i Geri gətirmək istədiyinizə əminsiz?',
                    showDenyButton: true,
                    showCancelButton: true,
                    confirmButtonText: 'Hə',
                    denyButtonText: `Yox`,
                    cancelButtonText: `Ləvğ et`,
                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        $.ajax({
                            method: 'POST',
                            url: "{{route('users.restore')}}",
                            data: {
                                id: userID
                            },
                            async: false,
                            success: function (data){
                                self.remove();

                                Swal.fire({
                                    title: 'Uğurlu',
                                    confirmButtonText: 'yaxşı',
                                    text: 'İstifadəçi geri gətirildi',
                                    icon: 'success',
                                });
                            },
                            error: function (){
                                console.log('ERRORRRR');
                            }
                        });

                    } else if (result.isDenied) {
                        Swal.fire({
                            title: 'Info',
                            confirmButtonText: 'yaxşı',
                            text: 'Heçbir dəyişiklik edilmədi',
                            icon: 'info',
                        });
                    }
                })

            });

        });
    </script>
    <script src="{{asset('assets/admin/plugins/flatpickr/flatpickr.js')}}"></script>
    <script src="{{asset('assets/admin/js/pages/datepickers.js')}}"></script>
    <script src="{{asset('assets/admin/plugins/select2/js/select2.full.min.js')}}"></script>
    <script src="{{asset('assets/admin/js/pages/select2.js')}}"></script>
    <script src="{{asset('assets/admin/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('assets/admin/plugins/bootstrap/js/popper.min.js')}}"></script>
    <script src="{{asset('assets/front/aos/aos.js')}}"></script>

    <script>
        AOS.init();

        $("#publish_date").flatpickr({
            enableTime: true,
            dateFormat: "Y-m-d H:i",
        });

        const popover = new bootstrap.Popover('.example-popover', {
            container: 'body'
        })

    </script>
@endsection
