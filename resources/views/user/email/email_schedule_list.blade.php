@extends('layouts.master')

@section('content')
<div id="layoutSidenav">

    @include('user.sidebar')
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <h1 class="mt-4">Schedule Emails List</h1>
                <div class="container">
                    <div class="row">

                      <div class="col-sm-12">

                        <div class="recipients-list-box">
                          <table class="table" id="">
                            <thead class="thead-dark">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Subject</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Total Recipent</th>
                                    <th scope="col">Sent Emails</th>
                                    <th scope="col">Failed Emails</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>

                              @php($count=1)
                              @foreach ($schedule_list as $row )
                               <?php
                                    $total = $row->totalEmail()->count();
                                    $sent = $row->sentEmail()->count();
                                    $failed = $row->failedEmail()->count();
                                    $url = route('download-failed-emails')
                                ?>
                                <tr class="row_{{$count}}">
                                    <td scope="row">{{$count}}</td>
                                    <td class="recipient_{{$count}}">{{$row['schedule_date'].' '.$row['schedule_time']}}</td>
                                    <td class="sender_{{$count}}">{{ $row['email_subject'] }}</td>
                                    <td class="status_{{$count}}">{{ $row['status'] }}</td>
                                    <td class="comment_{{$count}}">{{ $total }}</td>
                                    <td class="comment_{{$count}}">{{ $sent }}</td>
                                    <td class="comment_{{$count}}">{{ $failed }}</td>
                                    <td><a href="{{$url.'/'.$row->batch_number }}" class="btn btn-info {{ ($failed == 0) ? "disabled" : "" }}" >Download Failed Emails</a></td>
                                </tr>
                                @php($count++)
                              @endforeach

                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

            </div>
        </main>
        @include('user.footer')
    </div>
</div>

@endsection
