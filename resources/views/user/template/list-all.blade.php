@extends('layouts.master')

@section('content')
<div id="layoutSidenav">

    @include('user.sidebar')
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <h1 class="mt-4">Templates</h1>

                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-table me-1"></i>
                        Email Template List
                    </div>
                    <div class="card-body">

                        <table id="datatablesSimple">
                            <thead>
                                <tr>
                                    <th>Template Title</th>
                                    <th>Last Update</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($emailTemplates as $emailTemplate)

                                <tr>
                                    <td>{{ $emailTemplate->template_title }}</td>
                                    <td>{{ $emailTemplate->updated_at }}</td>
                                    <td>
                                        @if (is_null(Auth::user()->parent_id) || Auth::user()->validatePermission('templates-permission'))
                                            <button type="button" class="btnShowTemplate" data-t="{{$emailTemplate->id}}"><i class="fa fa-eye" aria-hidden="true"></i></button>
                                        @endif
                                        @if (is_null(Auth::user()->parent_id) || Auth::user()->validatePermission('templates-permission'))
                                            <a href="{{ route('edit-template',['id'=>$emailTemplate->id])}}"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                                        @endif
                                        @if (is_null(Auth::user()->parent_id) || Auth::user()->validatePermission('templates-permission'))
                                            <button type="button" class="btnDeleteTemplate" data-id="{{$emailTemplate->id}}" name="btnDeleteTemplate"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                        @endif
                                    </td>
                                </tr>
                                {{-- <div class="modal fade" id="emailTemplate_{{$emailTemplate->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                      <div class="modal-content">
                                        <div class="modal-header">
                                          <h5 class="modal-title">{{ $emailTemplate->template_title }}</h5>

                                        </div>
                                        <div class="modal-body">
                                            {!! $emailTemplate->template_body !!}
                                        </div>

                                      </div>
                                    </div>
                                </div> --}}
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Template Title</th>
                                    <th>Template Content</th>
                                    <th>Action</th>
                                </tr>
                            </tfoot>

                        </table>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="emailTemplate" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title"></h5>

                    </div>
                    <div class="modal-body">

                    </div>

                  </div>
                </div>
            </div>
        </main>
        @include('user.footer')
    </div>
</div>
@endsection
