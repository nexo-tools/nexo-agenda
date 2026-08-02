<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

// Help center. FAQ items are translatable: the list lives in lang/<locale>/help.php
// as `faqs => [['q' => ..., 'a' => ...], ...]`.
//
// Contact target: unlike the tools that hand out a mailto, this one has a real
// feedback form (/contacto) — a business owner writing about a booking should
// not have to open a mail client. The canonical shape is what changes here: the
// view used to read __('help.faqs') itself and hardcode the contact route, so
// the scaffold's contract existed on paper and nothing filled it.
class HelpController extends Controller
{
    public function __invoke(): View
    {
        return view('help.index', [
            'faqs' => (array) __('help.faqs'),
            'contactUrl' => route('contact'),
        ]);
    }
}
