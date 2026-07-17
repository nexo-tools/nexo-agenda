<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Symfony test requests default to Accept-Language: en-us; our tests
        // assert against the Spanish base locale unless they say otherwise.
        $this->withHeader('Accept-Language', 'es');
    }
}
