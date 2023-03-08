<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">

        <title>Wally's Widgets</title>
    </head>
    <body>
        <div class="d-flex justify-content-center flex-nowrap mt-5">
            <div>
                <h1 class="text-center mb-3">Wally's Widgets</h1>
                @foreach ($errors->all() as $error)
                    <div class="alert alert-danger mb-3 text-center" role="alert">
                        {{ $error }}
                    </div>
                @endforeach
                <form>
                    <div class="input-group">
                        <input type="text" name="order-quantity" class="form-control" placeholder="Widgets Ordered" aria-label="Widgets Ordered" aria-describedby="button-addon2">
                        <button class="btn btn-primary" type="submit" id="button-addon2">Calculate</button>
                    </div>
                </form>
                @isset($orderQuantity)
                    <div class="text-center mt-5">
                        <div class="mb-1">Packs for <b>{{ $orderQuantity }}</b> widgets:</div>
                        @foreach ($minPacksRequired as $packSize => $packQuantity)
                            <span class="badge text-bg-primary">{{ $packSize }} <span class="ms-1 badge text-bg-light">x{{ $packQuantity }}</span></span>
                        @endforeach
                    </div>
                @endisset
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
    </body>
</html>
