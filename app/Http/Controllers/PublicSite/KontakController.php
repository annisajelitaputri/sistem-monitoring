<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\Controller;

class KontakController extends Controller
{
    public function index()
    {
        // Data kontak & peta diatur di config/sekolah.php
        return view('pages.public.kontak');
    }
}
