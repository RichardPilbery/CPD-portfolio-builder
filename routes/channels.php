<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Portfolio;
use App\Models\Audit;
use Illuminate\Support\Facades\Log;

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

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('portfolios.{portfolioId}', function ($user, $portfolioId) {
    return $user->id === Portfolio::findOrNew($portfolioId)->user_id;
});

Broadcast::channel('user.cpdprofile.{userId}', function ($user, $userId) {
    // if((int) $user->id === (int) $userId) {
    //     Log::debug('Broadcast match');
    // } else {
    //     Log::debug('Broadcast no match');
    // }
    
    return (int) $user->id === (int) $userId;
});

Broadcast::channel('user.auditlog.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

Broadcast::channel('user.airwaylog.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

Broadcast::channel('audits.{auditId}', function ($user, $auditId) {
    return $user->id === Audit::findOrNew($auditId)->user_id;
});



