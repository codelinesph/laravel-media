<?php

namespace Codelines\LaravelMediaController;


use Illuminate\Support\Facades\Request;

interface HasMedia
{
    function directory():string;
    function storageDriver():string;
    function saveMedia(Request $request):bool;
}
