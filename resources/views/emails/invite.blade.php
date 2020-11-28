@component('mail::message')
# Hi, {{ explode(" ", $student->name)[0] }}

Your tutor has invited you to join their online teaching space, where you can
- see all of your learning resources
- upload and work on your assignments
- organize your own notes
- ...and much more!

@component('mail::button', ['url' => 'https://app.mylayr.net/invite/' . $token, 'color' => 'primary'])
Click here to accept
@endcomponent

@endcomponent