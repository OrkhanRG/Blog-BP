@extends('layouts.admin')
@section('title')
    Email Şablon Siyahısı
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
            <h2>Email Şablon Siyahısı</h2>
        </x-slot:header>
        <x-slot:body>
            <form action="" method="GET" id="formFilter">
                <div class="row">
                    <div class="col-3 my-2">
                        <select class="js-states form-control" id="theme_type" name="theme_type" tabindex="-1" style="display: none; width: 100%">
                            <option value="{{ null }}">Şablon Tipini Seçin</option>
                            <option value="1" {{request()->get('theme_type')==1 ? 'selected' : ''}}>Özüm Email Şablon Hazırlamaq İstəyirəm</option>
                            <option value="2" {{request()->get('theme_type')==2 ? 'selected' : ''}}>Şifrə Sıfırlama Maili</option>
                        </select>
                    </div>
                    <div class="col-3 my-2">
                        <select class="js-states form-control" id="process" name="process" tabindex="-1" style="display: none; width: 100%">
                            <option value="{{ null }}">Əməliyyat Seçin</option>
                            <option value="1" {{request()->get('theme_type')==1 ? 'selected' : ''}}>Email Doğrulama</option>
                            <option value="2" {{request()->get('theme_type')==2 ? 'selected' : ''}}>Şifrə Sıfırlama</option>
                            <option value="3" {{request()->get('theme_type')==3 ? 'selected' : ''}}>Şifrə Sıfırlama Əməliyyatı Bitdikdən Sonra Göndəriləcək Email</option>
                        </select>
                    </div>
                    <div class="col-3 my-2">
                        <input type="text" class="form-control" name="search_text" placeholder="Mail Məzmun"
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
                    <th scope="col">Şablon Adı</th>
                    <th scope="col">Şablon Tipi</th>
                    <th scope="col">Əməliyyat</th>
                    <th scope="col">Məzmun</th>
                    <th scope="col">Status</th>
                    <th scope="col">User</th>
                    <th scope="col">Created Date</th>
                    <th scope="col">Actions</th>
                </x-slot:columns>

                <x-slot:rows>
                    @foreach($list as $email)

                        <tr id="row-{{$email->id}}">
                            <td>{{$email->name}}</td>
                            <td>{{$email->theme_type}}</td>
                            <td>{{$email->process}}</td>
                            <td>
                                <a href="javascript:void(0)"
                                   class="btn btn-info btn-sm btnModalThemeDetail"
                                   data-bs-toggle="modal"
                                   data-bs-target="#contenViewModal"
                                   data-id="{{ $email->id }}"
                                   data-content="{{ $email->body }}"
                                   data-theme-type="{{ $email->getRawOriginal('theme_type') }}"
                                >
                                    <i class="material-icons ms-0">visibility</i>
                                </a>
                            </td>
                            <td>
                                @if($email->status)
                                    <a href="javascript:void(0)" class="btn btn-success btn-sm btnChangeStatus" data-id="{{$email->id}}">Aktiv</a>
                                @else
                                    <a href="javascript:void(0)" class="btn btn-danger btn-sm btnChangeStatus" data-id="{{$email->id}}">Passiv</a>
                                @endif
                            </td>
                            <td>{{$email->user->name}}</td>
                            <td>{{$email->created_at}}</td>
                            <td>
                                <div class="d-flex">
                                    <a href="{{route('admin.email-themes.edit',['id' => $email->id])}}" class="btn btn-warning btn-sm"><i
                                            class="material-icons ms-0">edit</i></a>
                                    <a href="javascript:void(0)"
                                       class="btn btn-danger btn-sm btnDelete"
                                       data-id="{{$email->id}}"
                                       data-name="{{$email->name}}">
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
                {{$list->appends(request()->all())->onEachside(1)->links()}}

            </div>
        </x-slot:body>
    </x-bootstrap.card>

    <div class="modal fade" id="contenViewModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Şablon Məzmunu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalBody">
                    <pre><code class="language-json" id="jsonData"></code></pre>
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

            $('.btnModalThemeDetail').click(function () {
                let content = $(this).data('content');
                let themeType = $(this).data('theme-type');

                if (themeType == 1)
                {
                    $('#jsonData').html(content.replace('"', '').replace('"', ''));
                }
                else
                {
                    $('#jsonData').html(JSON.stringify(content, null, 2));
                    document.querySelectorAll('#jsonData').forEach((block) => {
                        hljs.highlightElement(block);
                    })
                }
            });

            $('.btnChangeStatus').click(function () {
                let themeID = $(this).data('id');
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
                            url: "{{  route('admin.email-themes.changeStatus') }}",
                            data: {
                                themeID: themeID
                            },
                            success: function (data){
                                if(data.theme_status)
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

            $('.btnDelete').click(function () {
                let themeID = $(this).data('id');
                let themeName = $(this).data('name');
                let self = $(this);

                Swal.fire({
                    title: themeName + ' i Silmək istədiyinizə əminsiz?',
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
                           url: "{{route('admin.email-themes.delete')}}",
                           data: {
                               '_method': 'DELETE',
                               themeID: themeID
                           },
                           async: false,
                           success: function (data){
                                $('#row-' + themeID).remove();

                               Swal.fire({
                                   title: 'Uğurlu',
                                   confirmButtonText: 'yaxşı',
                                   text: 'Şablon silindi',
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
