<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment('Built for what comes next.');
})->purpose('Display an inspiring quote');
