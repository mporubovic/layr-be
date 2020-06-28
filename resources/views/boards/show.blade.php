ID: {{ $board->id }} </br>
Title: {{ $board->title }} </br>
Owner: {{ $board->owner }} </br>
Created at: {{ $board->created_at }} </br>
Updated at: {{ $board->updated_at }}

<a href="/boards/{{ $board->id }}/edit">Edit</a>