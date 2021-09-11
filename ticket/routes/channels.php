<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/
Broadcast::channel('testchannel', function ($user) {
    return true;
}); 

//DASHBOARD ADMIN
Broadcast::channel('eventChannel', function ($test) {
    return true;
}); 

Broadcast::channel('orderChannel', function ($test) {
    return true;
}); 

Broadcast::channel('adminDashboardChannel',function ($test) {
    return true;
});

Broadcast::channel('adminDashboardChannel',function ($test) {
    return true;
});

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
