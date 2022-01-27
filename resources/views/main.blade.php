<!DOCTYPE html>
<html lang="en">
@include('page_elements/head')
<body class="min-vh-100 d-flex flex-column">
<header class="flex-shrink-0">
    @include('page_elements/nav')
</header>
<main class="flex-grow-1">
    <div class="container-lg mt-3">
        <div class="row">
            <div class="col-12 col-md-10 col-lg-8 mx-auto border rounded-3 bg-light p-5">
                <h1 class="display-3">Анализатор страниц</h1>
                <p class="lead">Бесплатно проверяйте сайты на SEO пригодность</p>
                <form action="{{ route('saving.site') }}" method="post" class="d-flex justify-content-center">
                    @csrf
                    <input type="text" name="url[name]" class="form-control form-control-lg"
                           placeholder="https://www.example.com">
                    <input type="submit" value="Проверить">
                </form>
            </div>
        </div>
    </div>
</main>
@include('page_elements/footer')
</body>
</html>
