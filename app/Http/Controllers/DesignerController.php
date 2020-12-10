<?php


namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;

use App\Http\Resources\Page as PageResource;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactRequest;
use Illuminate\Support\Facades\Cache;
use App\Assets\ViberApi;
use MetaTag;



class DesignerController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     *
     * get active page items
     */
    public function view() {
        $arrBreadCrumbs = array();
        $arrBreadCrumbs[0] = 'designers@'.__('page.all_designers');

        $data = [
            'brands' => \App\Designer::orderBy('name')->get(),
            'breadcrumbs' => $arrBreadCrumbs
        ];

        return view('designer.view', compact('data'));        
    }

    
    public function sendQuickRequest(Request $request) {
        $orderInfo = $request->all();

        $viberApi = new ViberApi();
        $viberApi->viberBroadcastQuickRequest($orderInfo);

        $response = [
            'success' => true,
            'message' => 'Ваш запит успішно надіслано'
        ];

        return response()->json($response,200);
    }


    
}
