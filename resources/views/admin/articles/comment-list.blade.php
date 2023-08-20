@extends('layouts.admin')
@section('title')
    @if($page == 'commentList')
        Kommentlər Siyahısı
    @else
        Təsdiq Gözləyən Komment Siyahısı
    @endif
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
            <h2>
                @if($page == 'commentList')
                    Kommentlər Siyahısı
                @else
                    Təsdiq Gözləyən Komment Siyahısı
                @endif
            </h2>
        </x-slot:header>
        <x-slot:body>
            <form action="{{$page=='commentList' ? route('article.comment.list') : route('article.pending-approval')}}" id="formFilter">
                <div class="row">
                    <div class="col-3 my-2">
                        <select class="form-select" name="user_id">
                            <option value="{{null}}">User</option>
                            @foreach($users as $user)
                                <option
                                    value="{{$user->id}}" {{request()->get('user_id')==$user->id ? 'selected' : ''}}>{{$user->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    @if($page == 'commentList')
                        <div class="col-3 my-2">
                            <select class="form-select" aria-label="Status Seçin" name="status">
                                <option value="{{null}}">Status</option>
                                <option value="0" {{request()->get('status')==="0" ? 'selected' : ''}}>Passiv</option>
                                <option value="1" {{request()->get('status')==="1" ? 'selected' : ''}}>Aktiv</option>
                            </select>
                        </div>
                    @endif
                    <div class="col-3 my-2">
                        <input class="form-control form-control-solid-bordered m-b-sm"
                               type="text"
                               id="created_at"
                               name="created_at"
                               value="{{request()->get('created_at')}}"
                               placeholder="Komment Tarixi">
                    </div>
                    <div class="col-3 my-2">
                        <input type="text" class="form-control" name="search_text" placeholder="Comment, Name, Email"
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
                    <th scope="col">Məqalə Link</th>
                    <th scope="col">User Name</th>
                    <th scope="col">Name</th>
                    <th scope="col">Email</th>
                    <th scope="col">IP</th>
                    @if(isset($page) && $page == 'approval')
                        <th scope="col">Approve Status</th>
                    @else
                        <th scope="col">Status</th>
                    @endif
                    <th scope="col">Comment</th>
                    <th scope="col">Created Date</th>
                    <th scope="col">Actions</th>
                </x-slot:columns>

                <x-slot:rows>
                    @foreach($comments as $comment)

                        <tr id="row-{{$comment->id}}">
                            <td>
                                <a target="_blank" href="{{route('front.articleDetail', [
                                'user' => $comment->article->user->username,
                                'article' => $comment->article->slug
                                ])}}">
                                    <span class="material-icons-outlined"> visibility</span>
                                </a>
                            </td>
                            <td>{{$comment->user?->name}}</td>
                            <td>{{$comment->name}}</td>
                            <td>{{$comment->email}}</td>
                            <td>{{$comment->ip}}</td>
                            <td>
                            @if(isset($page) && $page!='commentList')
                                    @if($comment->approve_status)
                                        <a href="javascript:void(0)" class="btn btn-success btn-sm btnChangeStatus"
                                           data-id="{{$comment->id}}">Aktiv</a>
                                    @else
                                        <a href="javascript:void(0)" class="btn btn-danger btn-sm btnChangeStatus"
                                           data-id="{{$comment->id}}">Passiv</a>
                                    @endif
                            @else
                                    @if($comment->status)
                                        <a href="javascript:void(0)" class="btn btn-success btn-sm btnChangeStatus"
                                           data-id="{{$comment->id}}">Aktiv</a>
                                    @else
                                        <a href="javascript:void(0)" class="btn btn-danger btn-sm btnChangeStatus"
                                           data-id="{{$comment->id}}">Passiv</a>
                                    @endif
                            @endif

                            </td>
                            <td>
                                {{--<span data-bs-container="body" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{substr($comment->comment, 0, 200)}}">
                                    {{ substr($comment->comment, 0, 10) }}
                                </span>--}}

                                <button type="button" class="btn btn-primary lookComment btn-sm p-0 px-2"
                                        data-comment="{{$comment->comment}}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#exampleModal">
                                    <span data-bs-toggle="tooltip" data-bs-placement="top"
                                          data-bs-title="{{substr($comment->comment, 0, 200)}}"
                                          class="material-icons-outlined" style="line-height: unset; font-size: 20px"> visibility</span>
                                </button>
                            </td>
                            <td>{{\Carbon\Carbon::parse($comment->created_at)->translatedFormat('d F Y H:i:s')}}</td>
                            <td>
                                <div class="d-flex">
                                    <a href="javascript:void(0)"
                                       class="btn btn-danger btn-sm btnDelete"
                                       data-id="{{$comment->id}}"
                                       data-name="{{$comment->id}}">
                                        <i class="material-icons ms-0">delete</i>
                                    </a>
                                    @if($comment->deleted_at)
                                        <a href="javascript:void(0)"
                                           class="btn btn-primary btn-sm btnRestore"
                                           data-id="{{$comment->id}}"
                                           data-name="{{$comment->name}}"
                                           title="geri al">
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
                {{$comments->appends(request()->all())->onEachside(1)->links()}}

            </div>
        </x-slot:body>
    </x-bootstrap.card>
    {{--Modal--}}
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Komment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalBody">
                    ...
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
        $(Document).ready(function ()
        {
            @if(isset($page) && $page=='approval')
            $('.btnChangeStatus').click(function () {
                let id = $(this).data('id');
                let self = $(this);
                Swal.fire({
                    title: 'Təsdiq etmək istədiyinizə əminsiz?',
                    showDenyButton: true,
                    showCancelButton: true,
                    confirmButtonText: 'Hə',
                    denyButtonText: `Yox`,
                    cancelButtonText: `Ləvğ et`,
                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        $.ajax({
                            method: "POST",
                            url: "{{  route('article.pending-approval.changeStatus') }}",
                            data: {
                                id: id,
                                page: "{{ $page }}"
                            },
                            success: function (data) {
                                $('#row-'+id).remove();

                                Swal.fire({
                                    title: 'Uğurlu',
                                    confirmButtonText: 'yaxşı',
                                    text: 'Təsdiq olundu',
                                    icon: 'success',
                                });
                            },
                            error: function () {
                                console.log('ERRORRRR');
                            }
                        })
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
            @else
            $('.btnChangeStatus').click(function () {
                let id = $(this).data('id');
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
                    if (result.isConfirmed) {
                        $.ajax({
                            method: "POST",
                            url: "{{  route('article.pending-approval.changeStatus') }}",
                            data: {
                                id: id
                            },
                            success: function (data) {
                                if (data.comment_status) {
                                    self.removeClass('btn-danger');
                                    self.addClass('btn-success');
                                    self.text('Aktiv');
                                    Swal.fire({
                                        title: 'Uğurlu',
                                        confirmButtonText: 'yaxşı',
                                        text: 'Kommnet statusu aktiv olaraq dəyişdirildi',
                                        icon: 'success',
                                    });
                                }
                                else
                                {
                                    self.removeClass('btn-success');
                                    self.addClass('btn-danger');
                                    self.text('Passiv');
                                    Swal.fire({
                                        title: "Uğurlu",
                                        text: "Kommnet statusu passiv olaraq dəyişdirildi",
                                        confirmButtonText: 'yaxşı',
                                        icon: "success"
                                    });
                                }


                            },
                            error: function () {
                                console.log('ERRORRRR');
                            }
                        })
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
            @endif

            $('select').select2();

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
                            url: "{{route('article.pending-approval.delete')}}",
                            data: {
                                '_method': 'DELETE',
                                id: id
                            },
                            async: false,
                            success: function (data) {
                                $('#row-' + id).remove();

                                Swal.fire({
                                    title: 'Uğurlu',
                                    confirmButtonText: 'yaxşı',
                                    text: 'Komment silindi',
                                    icon: 'success',
                                });
                            },
                            error: function () {
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
                let id = $(this).data('id');
                let commentName = $(this).data('name');
                let self = $(this);

                Swal.fire({
                    title: commentName + ' i Geri gətirmək istədiyinizə əminsiz?',
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
                            url: "{{route('article.comment.restore')}}",
                            data: {
                                id: id
                            },
                            async: false,
                            success: function (data) {
                                self.remove();

                                Swal.fire({
                                    title: 'Uğurlu',
                                    confirmButtonText: 'yaxşı',
                                    text: 'Komment geri gətirildi',
                                    icon: 'success',
                                });
                            },
                            error: function () {
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

            $("#created_at").flatpickr({
                dateFormat: "Y-m-d",
            });

            $('.lookComment').click(function () {
                let comment = $(this).data('comment');
                $('#modalBody').text(comment);
            });
        });
    </script>
    <script src="{{asset('assets/admin/plugins/flatpickr/flatpickr.js')}}"></script>
    <script src="{{asset('assets/admin/js/pages/datepickers.js')}}"></script>
    <script src="{{asset('assets/admin/plugins/select2/js/select2.full.min.js')}}"></script>
    <script src="{{asset('assets/admin/js/pages/select2.js')}}"></script>
    <script src="{{asset('assets/admin/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('assets/admin/plugins/bootstrap/js/popper.min.js')}}"></script>

    <script>

        const popover = new bootstrap.Popover('.example-popover', {
            container: 'body'
        })

    </script>
@endsection
