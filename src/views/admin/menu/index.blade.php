@extends('larrock::admin.main')
@section('title') {{ $app->name }} admin @endsection

@section('content')
    <div class="uk-margin-large-bottom container-head">
        {!! Breadcrumbs::render('admin.'. $app->name .'.index') !!}
        <p>{{ $app->description }}</p>
        <div class="add-panel">
            <a class="uk-button uk-button-primary uk-float-right" href="/admin/{{ $app->name }}/create">Добавить пункт меню</a>
        </div>
    </div>

    @if(isset($data))
        @foreach($data as $key => $type)
            <p class="uk-h1" id="type-{{ $key }}">{{ $key }}</p>
            <div class="uk-margin-large-bottom">
                <table class="uk-table uk-table-striped uk-form">
                    <thead>
                    <tr>
                        @foreach($app->rows as $rows_name)
                            @if($rows_name->in_table_admin || $rows_name->in_table_admin_ajax_editable)
                                <th style="width: 90px" @if($rows_name->name !== 'active') class="uk-hidden-small" @endif>{{ $rows_name->title }}</th>
                            @endif
                        @endforeach
                        @include('larrock::admin.admin-builder.additional-rows-th')
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($type as $type_menu)
                        @include('larrock::admin.menu.item-default', ['data' => $type_menu])
                    @endforeach
                    </tbody>
                </table>
                @if(count($data) === 0)
                    <div class="uk-alert uk-alert-warning">Данных еще нет</div>
                @endif
            </div>
        @endforeach
    @endif

    @if(isset($catalog))
        <p class="uk-h1">Меню из разделов каталога</p>
        <p class="uk-alert uk-alert-warning">Это алиасы от разделов каталога. Активность элементов управляется только через управление самим разделом</p>
        <div class="uk-margin-large-bottom">
            <table class="uk-table uk-table-striped uk-form">
                <thead>
                <tr>
                    <th>Название пункта</th>
                    <th>Название раздела</th>
                    @include('larrock::admin.admin-builder.additional-rows-th')
                </tr>
                </thead>
                <tbody>
                @foreach($catalog as $type_menu)
                    @include('larrock::admin.menu.item-catalog', ['data' => $type_menu])
                @endforeach
                </tbody>
            </table>
            @if(count($catalog) === 0)
                <div class="uk-alert uk-alert-warning">Данных еще нет</div>
            @endif
        </div>
    @endif
@endsection