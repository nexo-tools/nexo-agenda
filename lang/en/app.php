<?php

// Fragments that are lookups rather than sentences, so they are keyed by name
// instead of by their English text (the generator treats a bare word as a
// lang-file key).
return [
    // Carbon format string for translatedFormat(): the month and day NAMES come
    // from the Carbon locale, the ORDER and the connecting words come from here.
    // This replaces isoFormat('dddd D [de] MMMM YYYY'), whose bracketed "de" was
    // the Spanish preposition hardcoded inside every locale — an English reader
    // was getting "Monday 4 de August 2026".
    'datetime' => 'l j F Y, H:i',

    // Same idea without the time, for a day-only line.
    'date' => 'l j F Y',
];
