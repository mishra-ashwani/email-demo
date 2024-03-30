@extends('layouts.master')

@section('content')
<div id="layoutSidenav">

    @include('user.sidebar')
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <h1 class="mt-4">Dashboard</h1>
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>


                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-table me-1"></i>
                        Recipients Lists
                    </div>
                    <div class="card-body">

                        <table id="datatablesSimple">
                            <thead>
                                <tr>
                                    <tr>
                                        <th>Group Name</th>
                                        <th>File</th>
                                        <th>Action</th>
                                    </tr>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recipients as $recipient)
                                <tr>
                                    <td>{{ $recipient->recipient_list_name }}</td>
                                    <td><a href="{{ $recipient->recipient_file_path }}" target="_blank"><i class="fa fa-file-excel-o" aria-hidden="true"></i></a></td>
                                    <td>
                                        @if (is_null(Auth::user()->parent_id) || Auth::user()->validatePermission('recipients-permission'))
                                        <button type="button" class="btnDeleteRecipientsList" data-id="{{$recipient->id}}" name="btnDeleteRecipientsList"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Group Name</th>
                                    <th>File</th>
                                    <th>Action</th>
                                </tr>
                            </tfoot>

                        </table>
                    </div>
                </div>
            </div>
        </main>
        @include('user.footer')
    </div>
</div>
@endsection
