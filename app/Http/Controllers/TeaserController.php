<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Resources\Teaser as TeaserResource;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;

class TeaserController extends Controller
{
    //
    public function fetchTeasers() {

        $teasers = \App\Teaser::where('active', 1)->orderBy('order', 'asc')->get();

        // Return collection of product as a resources
        return TeaserResource::collection($teasers);
    }
}
