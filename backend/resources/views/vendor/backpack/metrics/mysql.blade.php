@extends(backpack_view('blank'))

@section('content')
    <table class="table table-striped table-hover nowrap rounded card-table table-vcenter card d-table shadow-xs border-xs dataTable dtr-inline" data-responsive-table="1" data-has-details-row="0" data-has-bulk-actions="0" data-has-line-buttons-as-dropdown="0" cellspacing="0" aria-describedby="crudTable_info">
        <thead>
            <tr>
                <th>Переменная</th>
                <th>Значение</th>
            <tr>
        </thead>
        <tbody>
            @foreach($items as $item)
                <tr>
                    <td>{{$item[0]}}</td>
                    <td>{{$item[1]}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
