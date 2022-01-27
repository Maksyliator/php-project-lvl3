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

    public function testUrlsRoot(): void
    {
        $response = $this->get(route('main'));
        $response->assertOk();
    }

    public function testUrlsIndex(): void
    {
        $response = $this->get(route('browsing.sites'));
        $response->assertOk();
    }

    public function testUrlsShow(): void
    {
        $urlDataTest = [
            'id' => $this->id,
            'name' => 'https://www.test.com',
        ];
        $response = $this->get(route('site.analysis', $this->id));
        $response->assertOk();
        $this->assertDatabaseHas('urls', $urlDataTest);
    }

    public function testInvalidUrlShow(): void
    {
        $invalidId = [1000];
        $response = $this->get(route('site.analysis', $invalidId));
        $response->assertNotFound();
    }

    public function testUrlsStore(): void
    {
        $urlData = [
            'name' => 'https://www.test.com',
        ];
        $response = $this->post(route('saving.site', ['url' => $urlData]));
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
        $this->post(route('saving.site', ['url' => $newData]));
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
            'title' => "Main page",
            'description' => "Форум PHP программистов, док?..."
        ];
        Http::fake(fn ($request) => Http::response($body, 200));

        $response = $this->post(route('checks', [$this->id]));
        $response->assertSessionHasNoErrors();
        $response->assertRedirect()->assertStatus(302);
        $this->assertDatabaseHas('url_checks', $checkData);
    }
}
