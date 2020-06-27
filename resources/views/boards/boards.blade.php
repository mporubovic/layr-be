<h2>User boards</h2>

@foreach ($boards as $board)

    <h3><a href="/boards/{{ $board->id}}">{{ $board->title }}</a></h3>

@endforeach

<h2>Create a new board</h2>
<form method="POST" action="/boards">
    @csrf <!-- Cross site request forgery -->
    

    <label class="label" for="title">Title </label>
    <input class="input" type="text" name="title" id="title">
    <button class="button" type="submit">Create</button>
</form>