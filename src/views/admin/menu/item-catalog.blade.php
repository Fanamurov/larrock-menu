<tr>
    <td>
        @if($data->level > 1)
            <div style="padding-left: {{ $data->level*15 }}px">
        @endif
        <a class="h4" href="/admin/{{ $app->name }}/{{ $data->id }}/edit">{{ $data->title }}</a>
        <br/>
        <a class="link-to-front" target="_blank" href="{{ $data->full_url }}" title="ссылка на элемент на сайте">
            {{ str_limit($data->full_url, 35, '...') }}
        </a>
        @if($data->level > 1)
            </div>
        @endif
    </td>
    <td>
        <a href="/admin/category/{{ $data->id }}">{{ $data->title }}</a>
    </td>
    @include('larrock::admin.admin-builder.additional-rows-td', ['data_value' => $data])
</tr>
@if(isset($data->children))
    @foreach($data->children as $child)
        @include('larrock::admin.menu.item-catalog', ['data' => $child])
    @endforeach
@endif