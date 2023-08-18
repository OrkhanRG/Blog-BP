@extends('layouts.admin')
@section('title')
    Kateqoriyalar Siyahısı
@endsection

@section('css')
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
            <h2>Kateqoriya Siyahısı</h2>
        </x-slot:header>
        <x-slot:body>
            <form action="" id="formFilter">
                <div class="row">
                    <div class="col-3 my-2">
                        <input type="text" class="form-control" name="name" placeholder="Name" value="{{request()->get('name')}}">
                    </div>
                    <div class="col-3 my-2">
                        <input type="text" class="form-control" name="slug" placeholder="Slug" value="{{request()->get('slug')}}">
                    </div>
                    <div class="col-3 my-2">
                        <input type="text" class="form-control" name="description" placeholder="Description" value="{{request()->get('description')}}">
                    </div>
                    <div class="col-3 my-2">
                        <input type="number" class="form-control" name="order" placeholder="Sıra" value="{{request()->get('order')}}">
                    </div>
                    <div class="col-3 my-2">
                        <select class="js-states form-control" name="parent_id" tabindex="-1" style="display: none; width: 100%">
                            <option value="{{null}}">Üst Kateqoriya</option>
                            @foreach($parentCategories as $parentCategory)
                                <option value="{{$parentCategory->id}}" {{request()->get('parent_id')==$parentCategory->id ? 'selected' : ''}}>{{$parentCategory->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-3 my-2">
                        <select class="form-select" name="user_id">
                            <option value="{{null}}">User</option>
                            @foreach($users as $user)
                                <option value="{{$user->id}}" {{request()->get('user_id')==$user->id ? 'selected' : ''}}>{{$user->name}}</option>
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
                        <select class="form-select" aria-label="Feature Status Seçin" name="feature_status">
                            <option value="{{null}}">Feature Status</option>
                            <option value="0"  {{request()->get('feature_status')==="0" ? 'selected' : ''}}>Passiv</option>
                            <option value="1"  {{request()->get('feature_status')==="1" ? 'selected' : ''}}>Aktiv</option>
                        </select>
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
                    <th scope="col">Name</th>
                    <th scope="col">Slug</th>
                    <th scope="col">Status</th>
                    <th scope="col">Feature Status</th>
                    <th scope="col">Description</th>
                    <th scope="col">Order</th>
                    <th scope="col">Paren Category</th>
                    <th scope="col">User</th>
                    <th scope="col">Actions</th>
                </x-slot:columns>

                <x-slot:rows>
                    @foreach($list as $category)
                        <tr>
                            <th scope="row">{{$category->name}}</th>
                            <td>{{$category->slug}}</td>
                            <td>
                                @if($category->status)
                                    <a href="javascript:void(0)" data-id="{{$category->id}}"
                                       class="btn btn-success btn-sm btnChangeStatus">Akitv</a>
                                @else
                                    <a href="javascript:void(0)" data-id="{{$category->id}}"
                                       class="btn btn-danger btn-sm btnChangeStatus">Passiv</a>
                                @endif
                            </td>
                            <td>
                                @if($category->feature_status)
                                    <a href="javascript:void(0)" data-id="{{$category->id}}" class="btn btn-success btn-sm btnChangeFeatureStatus">Akitv</a>
                                @else
                                    <a href="javascript:void(0)" data-id="{{$category->id}}" class="btn btn-danger btn-sm btnChangeFeatureStatus">Passiv</a>
                                @endif
                            </td>
                            <td>{{substr($category->description,0,20)}}</td>
                            <td>{{$category->order}}</td>
                            <td>{{$category->parentCategory?->name}}</td>
                            <td>{{$category->user->name}}</td>
                            <td>
                                <div class="d-flex">
                                    <a href="{{route('categories.edit',['id' => $category->id])}}" class="btn btn-warning btn-sm"><i
                                            class="material-icons ms-0">edit</i></a>
                                    <a href="javascript:void(0)"
                                       class="btn btn-danger btn-sm btnDelete"
                                       data-id="{{$category->id}}"
                                       data-name="{{$category->name}}">
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
    <form action="" method="post" id="statusChangeForm">
        @csrf
        <input type="hidden" name="id" id="inputStatus" value="">
    </form>
@endsection

@section('js')
    <script>
        $(Document).ready(function () {

            $('select').select2();

            $('.btnChangeStatus').click(function () {
                let categoryID = $(this).data('id');
                $('#inputStatus').val(categoryID);

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
                        $('#statusChangeForm').attr('action', '{{route('categories.changeStatus')}}')
                        $('#statusChangeForm').submit();
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

            $('.btnChangeFeatureStatus').click(function () {
                let categoryID = $(this).data('id');
                $('#inputStatus').val(categoryID);

                Swal.fire({
                    title: 'Feature Statusu dəyişdirməy istədiyinizə əminsiz?',
                    showDenyButton: true,
                    showCancelButton: true,
                    confirmButtonText: 'Hə',
                    denyButtonText: `Yox`,
                    cancelButtonText: `Ləvğ et`,
                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        $('#statusChangeForm').attr('action', '{{route('categories.changeFeatureStatus')}}')
                        $('#statusChangeForm').submit();
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

            $('.btnDelete').click(function () {
                let categoryID = $(this).data('id');
                let categoryName = $(this).data('name');
                $('#inputStatus').val(categoryID);

                Swal.fire({
                    title: categoryName + ' i Silmək istədiyinizə əminsiz?',
                    showDenyButton: true,
                    showCancelButton: true,
                    confirmButtonText: 'Hə',
                    denyButtonText: `Yox`,
                    cancelButtonText: `Ləvğ et`,
                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        $('#statusChangeForm').attr('action', '{{route('categories.delete')}}')
                        $('#statusChangeForm').submit();
                    } else if (result.isDenied)
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

        });
    </script>

    <script src="{{asset('assets/admin/plugins/select2/js/select2.full.min.js')}}"></script>
    <script src="{{asset('assets/admin/js/pages/select2.js')}}"></script>
@endsection
