<form method="POST" action="/boards/{{ $board->id }}">
    @csrf <!-- Cross site request forgery -->
    @method('PUT')

    <label class="label" for="title">Title </label>
    <input class="input" type="text" name="title" id="title" value="{{ $board->title }}">
    <input class="input" type="hidden" name="owner" id="owner" value="mawej1">
    <button class="button" type="submit">Update</button>
    @error('title')
        <p>{{ $errors->first('title') }}</p>
    @enderror
</form>