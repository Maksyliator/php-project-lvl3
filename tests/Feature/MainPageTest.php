<?php

namespace Tests\Feature;

use Tests\TestCase;

class MainPageTest extends TestCase
{
    public function testUrlsRoot(): void
    {
        $response = $this->get(route('main'));
        $response->assertOk();
    }
}
