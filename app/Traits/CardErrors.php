<?php

namespace App\Traits;


trait CardErrors
{
    public function cardNotFoundError()
    {

        abort(403, 'The authenticated user does not have access to the requested card.');
    }

    public function cardNoPermissionError()
    {

        abort(403, 'The authenticated user does not have access to the requested card.');
    }

    public function cardNoFileUploadedError()
    {

        abort(400, 'No file was included with the request');
    }

    public function cardFileUploadError()
    {

        abort(400, 'There was an error with the file upload.');
    }
}
