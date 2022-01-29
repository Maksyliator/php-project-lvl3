<!DOCTYPE html>
<html lang="en">
    @include('page_elements/head')
    <body class="vh-100 d-flex flex-column">
    <header class="flex-shrink-0">
        @include('page_elements/nav')
    </header>
    <main class="flex-grow-1">
        <div class="container-lg">
            <h1 class="mt-5 mb-3">Сайт: {{ $urlData->name }}</h1>
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-nowrap">
                    <tr>
                        <td>{{ 'ID'}}</td>
                        <td>{{ $urlData->id }}</td>
                    </tr>
                    <tr>
                        <td>{{ 'Имя' }}</td>
                        <td>{{ $urlData->name }}</td>
                    </tr>
                    <tr>
                        <td>{{ 'Дата создания' }}</td>
                        <td>{{ $urlData->created_at }}</td>
                    </tr>
                </table>
                <h2 class="mt-5 mb-3">Проверки</h2>
                <form method="post" action="{{ route('urls.checks', ['id' => $urlData->id]) }}">
                    @csrf
                    <input type="submit" class="btn btn-primary" value="Запустить проверку">
                </form>
                <table class="table table-bordered mt-3 table-hover text-nowrap">
                    <tbody>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Код ответа</th>
                        <th scope="col">h1</th>
                        <th scope="col">title</th>
                        <th scope="col">description</th>
                        <th scope="col">Дата создания</th>
                    </tr>
                    <tr>
                    @foreach ($checkData as $url)
                        <tr>
                            <td>{{ $url->id }}</td>
                            <td>{{ $url->status_code }}</td>
                            <td>{{ Str::limit($url->h1, 9, ' ...') }}</td>
                            <td>{{ Str::limit($url->title, 30, ' ...') }}</td>
                            <td>{{ Str::limit($url->description, 30, ' ...') }}</td>
                            <td>{{ $url->created_at }}</td>
                        </tr>
                        @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    @include('page_elements/footer')
    </body>
</html>
