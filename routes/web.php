<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use DiDom\Document;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;

Route::get('/', function () {
    return view('main');
})->name('main');

// ПОЛУЧЕНИЕ И ОБРАБОТКА АДРЕСА
Route::post('/urls', function (Request $request) {
    $url = $request->input('url');
    $rules = ['name' => ['max:255', 'url']];
    $messages = [
        'max' => 'Длина URL не должна превышать 255 символов',
        'url' => 'Некорректный URL'
    ];
    $validator = Validator::make($url, $rules, $messages);
    if ($validator->fails()) {
        return redirect()
            ->route('main')
            ->withErrors($validator)
            ->withInput();
    }
    $parsedName = parse_url(strtolower($url['name']));
    $nameUrl = $parsedName['scheme'] . '://' . $parsedName['host'];
    $savedUrl = DB::table('urls')->where('name', $nameUrl)->first();
    if (!is_null($savedUrl)) {
        $id = $savedUrl -> id;
        flash('Страница уже существует')->success();
    } else {
        $created = Carbon::now();
        $id = DB::table('urls')->insertGetId(
            ['name' => $nameUrl, 'created_at' => $created]
        );
        flash('Старница успешно добавлена')->success();
    }
    return redirect()->route('urls.show', ['id' => $id]);
})->name('urls.store');

// ВЫВОД СТАНИЦЫ НА ПРОВЕРКУ
Route::get('/urls/{id}', function ($id) {
    $url = DB::table('urls')->find($id);
    abort_unless($url, 404);
    $checks = DB::table('url_checks')
        ->where('url_id', $id)
        ->orderBy('created_at', 'desc')
        ->get();
    return view('analysis', compact('url', 'checks'));
})->name('urls.show');

// ВЫВОД ВСЕХ САЙТОВ
Route::get('/urls', function () {
    $urls = DB::table('urls')->orderBy('id')->paginate();
    $lastChecks = DB::table('url_checks')
        ->distinct('url_id')
        ->orderBy('url_id')
        ->latest()
        ->get()
        ->keyBy('url_id');
    return view('sites', compact('urls', 'lastChecks'));
})->name('urls.index');

// ВЫПОЛНЕНИЕ ПРОВЕРКИ
Route::post('/urls/{id}/checks', function ($id) {
    $name = DB::table('urls')->where('id', $id)->value('name');
    abort_unless($name, 404);
    try {
        $response = Http::get($name);
        $document = new Document($response->body());
        $statusCode = $response->status();
        $h1 = optional($document->first('h1'))->text();
        $title = optional($document->first('title'))->text();
        $created = Carbon::now();
        $description = optional($document->first('meta[name=description]'))->getAttribute('content');
        DB::table('url_checks')->insert(
            [
            'url_id' => $id,
            'status_code' => $statusCode,
            'h1' => $h1,
            'title' => $title,
            'description' => $description,
            'created_at' => $created
            ]
        );
        flash('Страница успешно проверена')->success();
    } catch (RequestException | ConnectionException $e) {
        flash("Exception: {$e->getMessage()}")->error();
    }
    return redirect()->route('urls.show', ['id' => $id]);
})->name('urls.checks');
