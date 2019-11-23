<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <title>Fuzzy Search</title>
</head>
<body>
<div class="container mt-5 col-md-3">
    <div class="container">
        <form id="form" class="form-inline" method="GET" action="{{url('airportSearch')}}">
            <div class="form-group">

                <label for="airport">Введите название</label>
                <input type="text" id="airport" name="airport" class="form-control mx-sm-3" pattern="[A-Za-zА-Яа-яЁё -]+" required>
                <input type="submit" class="btn btn-info" value="Найти">
            </div>
        </form>
    </div>
</div>


<div id="resultBlock" class="container mt-5 col-md-4" style="display: none">
    <div class="container">
        <table class="table">
            <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Город</th>
                <th scope="col">City</th>
                <th scope="col">Airport name</th>
                <th scope="col">Country</th>
                <th scope="col">timezone</th>
            </tr>
            </thead>
            <tbody>
            <tr id="closest">

            </tr>
            </tbody>
        </table>
    </div>

    <div class="container">
        <p>Возможно вы искали:</p>
        <table class="table">
            <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Город</th>
                <th scope="col">City</th>
                <th scope="col">Airport name</th>
                <th scope="col">Country</th>
                <th scope="col">timezone</th>
            </tr>
            </thead>
            <tbody id="allSimilar">

            </tbody>

        </table>
    </div>

</div>
<div id="alertInfo" class="container mt-5 col-md-4" style="display: none">
    <div  class="alert alert-info" role="alert">
        Совпадений не найдено :(
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

<script>

    function updateAirport(data)
    {
        $('#closest').html(`
            <td>${data.closest.id}</td>
            <td>${data.closest.cityName.ru}</td>
            <td>${data.closest.cityName.en}</td>
            <td>${isset(data.airportName) ? data.airportName.ru : '-'}</td>
            <td>${data.closest.country}</td>
            <td>${data.closest.timezone}</td>
        `);
    }
    function updateAllSimilar(data){
        $('#allSimilar').html('');
        if(isset(data.all))
        {
            data.all.forEach(function (airport, index) {
                $('#allSimilar').append(`<tr>
            <td>${airport.id}</td>
            <td>${airport.cityName.ru}</td>
            <td>${airport.cityName.en}</td>
            <td>${isset(airport.airportName) ? airport.airportName.ru : '-'}</td>
            <td>${airport.country}</td>
            <td>${airport.timezone}</td>
        </tr>`);
            })
        }
    }
    $('#form').submit(function (e) {
        e.preventDefault();
        let airportName = $(this).serialize();
        $.ajax({
            url: "/airportSearch",
            data: airportName,
            success: function( result ) {
                if(isset(JSON.parse(result).closest))
                {
                    $('#resultBlock').show();
                    $('#alertInfo').hide();
                    updateAirport(JSON.parse(result));
                    updateAllSimilar(JSON.parse(result));
                }
                else {
                    $('#resultBlock').hide();
                    $('#alertInfo').show();
                }
            }
        });
    })

    function isset(r) {
        return typeof r !== 'undefined';
    }
</script>

</body>
</html>
