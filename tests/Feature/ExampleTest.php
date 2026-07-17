<?php

it('returns a successful response on the home page', function () {
    $this->get('/')->assertStatus(200);
});
