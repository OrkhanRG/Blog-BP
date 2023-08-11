@extends('layouts.admin')
@section('title')
    Məqalə Siyahısı
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
            <h2>Məqalə Siyahısı</h2>
        </x-slot:header>
        <x-slot:body>
            <form action="">
                <div class="row">
                    <div class="col-3 my-2">
                        <select class="js-states form-control" name="category_id" tabindex="-1" style="display: none; width: 100%">
                            <option value="{{null}}">Kateqoriya Seçin</option>
                            @foreach($categories as $category)
                                <option
                                    value="{{$category->id}}" {{request()->get('category_id')==$category->id ? 'selected' : ''}}>{{$category->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-3 my-2">
                        <select class="form-select" name="user_id">
                            <option value="{{null}}">User</option>
                            @foreach($users as $user)
                                <option
                                    value="{{$user->id}}" {{request()->get('user_id')==$user->id ? 'selected' : ''}}>{{$user->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-3 my-2">
                        <select class="form-select" aria-label="Status Seçin" name="status">
                            <option value="{{null}}">Status</option>
                            <option value="0" {{request()->get('status')==="0" ? 'selected' : ''}}>Passiv</option>
                            <option value="1" {{request()->get('status')==="1" ? 'selected' : ''}}>Aktiv</option>
                        </select>
                    </div>
                    <div class="col-3 my-2">
                        <input class="form-control form-control-solid-bordered flatpickr2 m-b-sm"
                               type="text"
                               id="publish_date"
                               name="publish_date"
                               value="{{request()->get('publish_date')}}"
                               placeholder="Yayınlanma Vaxtı">
                    </div>
                    <div class="col-3 my-2">
                        <input type="text" class="form-control" name="search_text" placeholder="Title, Slug, Body, Tags"
                               value="{{request()->get('search_text')}}">
                    </div>
                    <div class="col-9 my-2">
                        <div class="row">
                            <div class="col-6">
                                <div class="row">
                                    <div class="col-6">
                                        <input type="number" class="form-control" name="min_view_count" placeholder="Min View Count" value="{{request()->get('min_view_count')}}">
                                    </div>
                                    <div class="col-6">
                                        <input type="number" class="form-control" name="max_view_count" placeholder="Max View Count" value="{{request()->get('max_view_count')}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="row">
                                    <div class="col-6">
                                        <input type="number" class="form-control" name="min_like_count" placeholder="Min like Count" value="{{request()->get('min_like_count')}}">
                                    </div>
                                    <div class="col-6">
                                        <input type="number" class="form-control" name="max_like_count" placeholder="Max like Count" value="{{request()->get('max_like_count')}}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="col-6 mb-3 d-flex">
                        <button type="submit" class="btn btn-primary w-50 me-4">Filtrlə</button>
                        <button type="submit" class="btn btn-warning w-50">Filtri Təmizlə</button>
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
                    <th scope="col">Title</th>
                    <th scope="col">Slug</th>
                    <th scope="col">Status</th>
                    <th scope="col">Body</th>
                    <th scope="col">Tags</th>
                    <th scope="col">View Count</th>
                    <th scope="col">Like Count</th>
                    <th scope="col">Category</th>
                    <th scope="col">Publish Date</th>
                    <th scope="col">User</th>
                    <th scope="col">Actions</th>
                </x-slot:columns>

                <x-slot:rows>
                    @foreach($list as $article)

                        <tr id="row-{{$article->id}}">
                            <td>
                                @if(!empty($article->image))
                                    <img src="{{asset($article->image)}}" alt="" height="100" class="img-fluid">
                                @endif
                            </td>
                            <td>{{$article->title}}</td>
                            <td>{{$article->slug}}</td>
                            <td>
                                @if($article->status)
                                    <a href="javascript:void(0)" class="btn btn-success btn-sm btnChangeStatus" data-id="{{$article->id}}">Aktiv</a>
                                @else
                                    <a href="javascript:void(0)" class="btn btn-danger btn-sm btnChangeStatus" data-id="{{$article->id}}">Passiv</a>
                                @endif
                            </td>
                            <td>
                                <span data-bs-container="body" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{substr($article->body, 0, 200)}}">
                                    {{substr($article->body, 0, 20)}}
                                </span>
                            </td>
                            <td>{{$article->tags}}</td>
                            <td>{{$article->view_count}}</td>
                            <td>{{$article->like_count}}</td>
                            <td>{{$article->category->name}}</td>
                            <td>{{\Carbon\Carbon::parse($article->publish_date)->translatedFormat('d F Y H:i:s')}}</td>
                            <td>{{$article->user->name}}</td>
                            <td>
                                <div class="d-flex">
                                    <a href="{{route('article.edit',['id' => $article->id])}}" class="btn btn-warning btn-sm"><i
                                            class="material-icons ms-0">edit</i></a>
                                    <a href="javascript:void(0)"
                                       class="btn btn-danger btn-sm btnDelete"
                                       data-id="{{$article->id}}"
                                       data-name="{{$article->title}}">
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
@endsection

@section('js')
    <script>
        $(Document).ready(function () {

            $('select').select2();

            $('.btnChangeStatus').click(function () {
                let articleID = $(this).data('id');
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
                            url: "{{  route('article.changeStatus') }}",
                            data: {
                                articleID: articleID
                            },
                            success: function (data){
                                if(data.article_status)
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
                let articleID = $(this).data('id');
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
                           url: "{{route('article.delete')}}",
                           data: {
                               '_method': 'DELETE',
                               articleID: articleID
                           },
                           async: false,
                           success: function (data){
                                $('#row-' + articleID).remove();

                               Swal.fire({
                                   title: 'Uğurlu',
                                   confirmButtonText: 'yaxşı',
                                   text: 'Məqalə silindi',
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

    <script>
        $("#publish_date").flatpickr({
            enableTime: true,
            dateFormat: "Y-m-d H:i",
        });

        const popover = new bootstrap.Popover('.example-popover', {
            container: 'body'
        })

    </script>
@endsection
