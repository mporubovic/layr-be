<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;

use Illuminate\Auth\Notifications\ResetPassword;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Relation::morphMap([
            
            // CONTENT

            'image' => 'App\Models\Content\Image',
            'video' => 'App\Models\Content\Video',
            'url' => 'App\Models\Content\Url',

            //
            
            // INTERPRETERS SINGLE
            
            'imageviewer' => 'App\Models\Interpreters\ImageViewer',
            'videoplayer' => 'App\Models\Interpreters\VideoPlayer',

            //
            
            // INTERPRETERS MANY

            'list' => 'App\Models\Interpreters\List',
            'tiles' => 'App\Models\Interpreters\Tiles',
            'gallery' => 'App\Models\Interpreters\Gallery',

            'file' => 'App\Models\File',

            //

        ]);
        
        ResetPassword::createUrlUsing(function ($notifiable, $token) {
            return "https://mylayr.com/reset-password/{$token}";
        });

    }
}
