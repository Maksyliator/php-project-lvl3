<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@include('page_elements/head')
    <body class="min-vh-100 d-flex flex-column">
        @include('page_elements/nav')
        <main class="flex-grow-1">
            <div class="container-lg">
                <h1 class="mt-5 mb-3">Сайты</h1>
                <div class="table-responsive">
                    <table class="table table-bordered text-nowrap">
                        <tbody>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Имя</th>
                                <th scope="col">Последняя проверка</th>
                                <th scope="col">Код ответа</th>
                            </tr>
                            @foreach ($urls as $url)
                            <tr>
                                <th scope="row">{{ $url->id }}</th>
                                <td><a href="{{ route('urls.show', ['id' => $url->id]) }}">{{ $url->name }}</a></td>
                                <td>{{ $lastChecks[$url->id]->created_at ?? null }}</td>
                                <td>{{ $lastChecks[$url->id]->status_code ?? null}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <nav aria-label="navigation">
                    {{ $urls->links("pagination::bootstrap-4") }}
                </nav>
            </div>
        </main>
    @include('page_elements/footer')
    </body>
</html>
