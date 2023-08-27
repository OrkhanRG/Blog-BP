@extends('layouts.admin')
@section('title')
    Email Aktiv Şablon Siyahısı
@endsection

@section('css')
    <link href="{{asset('assets/admin/plugins/flatpickr/flatpickr.min.css')}}" rel="stylesheet">
    <link href="{{asset('assets/admin/plugins/select2/css/select2.min.css')}}" rel="stylesheet">
    <style>
        .table-hover > tbody > tr:hover {
            --bs-table-hover-bg: transparent;
            background: #363638;
            color: white;
        }
    </style>
@endsection

@section('content')
    <x-bootstrap.card>
        <x-slot:header>
            <h2>Email Aktiv Şablon Siyahısı</h2>
        </x-slot:header>
        <x-slot:body>
            <x-bootstrap.table
                :class="'table-stripped table-hover'"
                :is-responsive="1"
            >
                <x-slot:columns>
                    <th scope="col">Şablon Adı</th>
                    <th scope="col">Mail Tipi</th>
                    <th scope="col">Məzmun</th>
                    <th scope="col">User</th>
                    <th scope="col">Created Date</th>
                    <th scope="col">Actions</th>
                </x-slot:columns>

                <x-slot:rows>
                    @foreach($list as $email)
                        <tr id="row-{{$email->theme_type_id}}">
                            <td>{{$email->theme->name}}</td>
                            <td>{{ $process[$email->process_id] }}</td>
                            <td>
                                <a href="javascript:void(0)"
                                   class="btn btn-info btn-sm btnShowMailContent"
                                   data-bs-toggle="modal"
                                   data-bs-target="#contenViewModal"
                                   data-id="{{ $email->theme->id }}"
                                >
                                    <i class="material-icons ms-0">visibility</i>
                                </a>
                            </td>
                            <td>{{$email->user->name}}</td>
                            <td>{{$email->created_at}}</td>
                            <td>
                                <div class="d-flex">
                                    <a href="javascript:void(0)"
                                       class="btn btn-danger btn-sm btnDelete"
                                       data-id="{{$email->theme_type_id}}"
                                       data-name="{{$email->theme->name}}">
                                        <i class="material-icons ms-0">delete</i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </x-slot:rows>
            </x-bootstrap.table>
            <div class="d-flex justify-content-center">
                {{--                {{$list->links('vendor.pagination.bootstrap-5')}}--}}
{{--                {{$list->appends(request()->all())->onEachside(1)->links()}}--}}

            </div>
        </x-slot:body>
    </x-bootstrap.card>

    <div class="modal fade" id="contenViewModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Şablon Məzmunu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalBody">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bağla</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(Document).ready(function () {

            $('.btnShowMailContent').click(function () {
                let themeID = $(this).data('id');
                let self = $(this);

                $.ajax({
                    method: "get",
                    url: "{{ route('admin.email-themes.assign.show.email') }}",
                    data: {
                        themeID: themeID
                    },
                    success: function (data){
                        $('#modalBody').html(data);
                    },
                    error: function () {
                        console.log('ERRORRRR');
                    }
                })

            });

            $('.btnDelete').click(function () {
                let id = $(this).data('id');
                let articleName = $(this).data('name');
                let self = $(this);

                Swal.fire({
                    title: articleName + ' i Silmək istədiyinizə əminsiz?',
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
                           url: "{{route('admin.email-themes.assign.delete')}}",
                           data: {
                               '_method': 'DELETE',
                               id: id
                           },
                           async: false,
                           success: function (data){
                                $('#row-' + id).remove();

                               Swal.fire({
                                   title: 'Uğurlu',
                                   confirmButtonText: 'yaxşı',
                                   text: 'Seçim silindi',
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

            $('#theme-type').select2();
            $('#process').select2();
        });
    </script>
    <script src="{{asset('assets/admin/plugins/flatpickr/flatpickr.js')}}"></script>
    <script src="{{asset('assets/admin/js/pages/datepickers.js')}}"></script>
    <script src="{{asset('assets/admin/plugins/select2/js/select2.full.min.js')}}"></script>
    <script src="{{asset('assets/admin/js/pages/select2.js')}}"></script>
    <script src="{{asset('assets/admin/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('assets/admin/plugins/bootstrap/js/popper.min.js')}}"></script>
@endsection
@push('javascript')
    <script src="{{ asset('assets/front/js/highlight.min.js') }}"></script>
    <script>
        hljs.highlightAll();
    </script>
@endpush
@push('style')
    <link rel="stylesheet" href="{{ asset('assets/plugins/highlight/styles/androidstudio.css') }}">
@endpush
