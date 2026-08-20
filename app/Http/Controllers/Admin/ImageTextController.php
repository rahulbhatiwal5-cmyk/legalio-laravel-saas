<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ImageTextService;

class ImageTextController extends Controller
{
    protected $imageTextService;

    public function __construct(ImageTextService $imageTextService)
    {
        $this->imageTextService = $imageTextService;
    }

    public function generateSvg()
    {
        try {
            $filePath = $this->imageTextService->addTextToImage("Carta de@Recomendación@Personal@Cartade");

            return response()->file($filePath, ['Content-Type' => 'image/svg+xml']);
            // return $filePath;
        }
        catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

  


}