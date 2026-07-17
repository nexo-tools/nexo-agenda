<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class HelpController extends Controller
{
    public function __invoke(): View
    {
        return view('help.index');
    }
}
