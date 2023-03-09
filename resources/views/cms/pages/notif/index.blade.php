@extends('cms.main')
@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Notifications</h1>
                    </div>
                </div>

                <div class="row my-3">
                    <div class="col-sm-6">
                        <button class="btn btn-success" id="btn-create">
                            Create Notification
                        </button>
                    </div>
                </div>

                <div class="row d-none mb-3" id="create-content">
                    <div class="col-sm-6">
                        <form action="/cms/notif/create" method="POST">
                            @csrf
                            <div class="form-group">
                              <label for="exampleInputEmail1">User Id</label>
                              <input type="text" class="form-control" id="exampleInputEmail1" name="user_id" placeholder="User Id" required>
                            </div>
                            <div class="form-group">
                              <label for="exampleInputPassword1">Title</label>
                              <input type="text" class="form-control" id="exampleInputPassword1" name="title" placeholder="Title" required>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputPassword1">Message</label>
                                <input type="text" class="form-control" id="exampleInputPassword1" name="message" placeholder="Message" required>
                              </div>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </form>
                    </div>
                </div>

                <div class="row">
                    @forelse ($notif as $item)
                        <div class="card col-3 mr-3">
                            <div class="card-body">
                                <h5 class="card-title">{{ $item->title }}</h5>
                                <p class="card-text">{{ $item->message }}</p>
                                <form action="/cms/notif/delete/{{ $item->id }}" method="post">
                                    @csrf
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <h1>No Notifications Data Yet</h1>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

<script type="text/javascript">
    const createBtn = document.getElementById('btn-create')
    const createContent = document.getElementById('create-content')
    createBtn.addEventListener('click', () => {
        createContent.classList.toggle('d-none')
    });
</script>
@endsection
