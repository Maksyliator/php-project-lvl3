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

    public function getFixtureFullPath(string $fixtureName): string
    {
        $parts = [__DIR__, '../fixtures', $fixtureName];
        $path = realpath(implode(DIRECTORY_SEPARATOR, $parts));
        if ($path == false) {
            throw new \Exception("'/tests/Feature/UrlsChecksTest.php'  Error: path to fextures not found");
        }
        return $path;
    }

    public function testUrlChecks(): void
    {
        $pathToFixtures = $this->getFixtureFullPath('htmlTest.html');
        $body = (string)(file_get_contents($pathToFixtures));
        if ($body == false) {
            throw new \Exception("'/tests/Feature/UrlsChecksTest.php'  Error: the file is damaged or missing ");
        }
        $checkData = [
            'url_id' => $this->id,
            'status_code' => '200',
            'h1' => 'Новости',
            'title' => "Форум PHP программистов.",
            'description' => "Форум PHP программистов."
        ];
        Http::fake(fn ($request) => Http::response($body, 200));

        $response = $this->post(route('urls.checks', [$this->id]));
        $response->assertSessionHasNoErrors();
        $response->assertRedirect()->assertStatus(302);
        $this->assertDatabaseHas('url_checks', $checkData);
    }
}
