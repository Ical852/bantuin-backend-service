@extends('cms.main')
@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Helper</h1>
                    </div>
                    <div class="col-sm-6">

                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">DataTable with default features</h3>
                            </div>
                            <div class="card-body">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>No. </th>
                                            <th>Gambar</th>
                                            <th>Nama</th>
                                            <th>Email</th>
                                            <th>User Balance</th>
                                            <th>Helper Balance</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($data as $item)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td><img src="{{ $item->user->image }}" alt=""></td>
                                                <td>{{ $item->user->full_name }}</td>
                                                <td>{{ $item->user->email }}</td>
                                                <td>{{ $item->user->balance }}</td>
                                                <td>{{ $item->helper_balance }}</td>
                                                <td>{{ $item->status }}</td>
                                                <td>
                                                    @if ($item->status == 'pending')
                                                        <div class="form-inline">
                                                            <form action="/cms/helper/accept" method="post"
                                                                class='form-inline'>
                                                                @csrf
                                                                <input type="hidden" name="id" id="id"
                                                                    value="{{ $item->id }}">
                                                                <input type="hidden" name="email" id="email"
                                                                    value="{{ $item->user->email }}">
                                                                <input type="hidden" name="device" id="device"
                                                                    value="{{ $item->user->user_device->device_id }}">
                                                                <button class="btn btn-success" type="submit"
                                                                    onclick="return confirm('Accept?')">Accept</button>
                                                            </form>
                                                            <form action="/cms/helper/deny" method="post"
                                                                class='form-inline ml-1'>
                                                                @csrf
                                                                <input type="hidden" name="id" id="id"
                                                                    value="{{ $item->id }}">
                                                                <input type="hidden" name="email" id="email"
                                                                    value="{{ $item->user->email }}">
                                                                <input type="hidden" name="device" id="device"
                                                                    value="{{ $item->user->user_device->device_id }}">
                                                                <button class="btn btn-danger" type="submit"
                                                                    onclick="return confirm('Deny?')">Deny</button>
                                                            </form>
                                                        </div>
                                                    @elseif ($item->status == 'denied' || $item->status == 'stopped')
                                                        <form action="/cms/helper/activate" method="post"
                                                            class='form-inline'>
                                                            @csrf
                                                            <input type="hidden" name="id" id="id"
                                                                value="{{ $item->id }}">
                                                            <input type="hidden" name="email" id="email"
                                                                value="{{ $item->user->email }}">
                                                            <input type="hidden" name="device" id="device"
                                                                value="{{ $item->user->user_device->device_id }}">
                                                            <button class="btn btn-primary" type="submit"
                                                                onclick="return confirm('Activate?')">Activate</button>
                                                        </form>
                                                    @else
                                                        <form action="/cms/helper/stop" method="post">
                                                            @csrf
                                                            <input type="hidden" name="id" id="id"
                                                                value="{{ $item->id }}">
                                                            <input type="hidden" name="email" id="email"
                                                                value="{{ $item->user->email }}">
                                                            <input type="hidden" name="device" id="device"
                                                                value="{{ $item->user->user_device->device_id }}">
                                                            <button class="btn btn-warning text-white"
                                                                onclick="return confirm('Stop?')">Stop</button>
                                                        </form>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>
@endsection
