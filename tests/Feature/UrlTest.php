<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class UrlTest extends TestCase
{
    private int $id;

    public function setUp(): void
    {
        parent::setUp();
        $setTime = now();
        $urlData = [
            'name' => 'https://www.test.com',
            'created_at' => $setTime
        ];
        $this->id = DB::table('urls')->insertGetId($urlData);
    }

    public function testUrlsIndex(): void
    {
        $response = $this->get(route('urls.index'));
        $response->assertOk();
    }

    public function testUrlsShow(): void
    {
        $urlDataTest = [
            'id' => $this->id,
            'name' => 'https://www.test.com',
        ];
        $response = $this->get(route('urls.show', $this->id));
        $response->assertOk();
        $this->assertDatabaseHas('urls', $urlDataTest);
    }

    public function testInvalidUrlShow(): void
    {
        $invalidId = [1000];
        $response = $this->get(route('urls.show', $invalidId));
        $response->assertNotFound();
    }

    public function testUrlsStore(): void
    {
        $urlData = [
            'name' => 'https://www.test.com',
        ];
        $response = $this->post(route('urls.store', ['url' => $urlData]));
        $response->assertSessionHasNoErrors();
        $response->assertRedirect()->assertStatus(302);
        $this->assertDatabaseHas('urls', $urlData);
    }

    public function testInvalidUrlsStore(): void
    {
        $newData = [
            'id' => 100,
            'name' => 'https://www.fake.com',
        ];
        $this->post(route('urls.store', ['url' => $newData]));
        $this->assertDatabaseMissing('urls', $newData);
    }
}
