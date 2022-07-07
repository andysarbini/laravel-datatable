<a href="/users/{{$user->id}}/edit" class="btn btn-primary btn-sm">Edit</a>
<form action="/users/{{$user->id}}" class="d-inline" method="post">
    @method("DELETE")
    @csrf

    <input type="hidden" value="{{$user->id}}">
    <button class="btn btn-danger btn-sm"> Hapus </button>
</form>