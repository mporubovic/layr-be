<h2>User boards by updated_at, desc</h2>

@foreach ($boards as $board)

    <h3><a href="{{ $board->path() }}">{{ $board->title }}</a></h3>

@endforeach

<h2>Create a new board</h2>
<form method="POST" action="/boards">
    @csrf <!-- Cross site request forgery -->
    

    <label class="label" for="title">Title </label>
    <input class="input" type="text" name="title" id="title" required>
    <input class="input" type="hidden" name="owner" id="owner" value="mawej1">
    <button class="button" type="submit">Create</button>
</form>


<h2>Update a board</h2>

