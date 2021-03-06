<tr>
    @foreach($package->rows as $rows_name)
        @if($rows_name->inTableAdminEditable)
            @if($rows_name instanceof \Larrock\Core\Helpers\FormBuilder\FormCheckbox)
                <td class="row-active @if($rows_name->name !== 'active') uk-visible@s @endif">
                    <div class="uk-button-group btn-group_switch_ajax" role="group" style="width: 100px">
                        <button type="button" class="uk-button uk-button-primary uk-button-small @if($data->{$rows_name->name} === 0) uk-button-outline @endif"
                                data-row_where="id" data-value_where="{{ $data->id }}" data-table="{{ $package->table }}"
                                data-row="active" data-value="1" style="width: 50%">on</button>
                        <button type="button" class="uk-button uk-button-danger uk-button-small @if($data->{$rows_name->name} === 1) uk-button-outline @endif"
                                data-row_where="id" data-value_where="{{ $data->id }}" data-table="{{ $package->table }}"
                                data-row="active" data-value="0" style="width: 50%">off</button>
                    </div>
                </td>
            @elseif($rows_name instanceof \Larrock\Core\Helpers\FormBuilder\FormInput)
                <td class="uk-hidden-small">
                    <input type="text" value="{{ $data->{$rows_name->name} }}" name="{{ $rows_name->name }}"
                           class="ajax_edit_row form-control uk-input uk-form-small @if($rows_name->name !== 'active') uk-visible@s @endif"
                           data-row_where="id" data-value_where="{{ $data->id }}" data-table="{{ $package->table }}" style="max-width: 120px;">
                </td>
            @endif
        @endif
        @if($rows_name->inTableAdmin)
            <td class="row-{{ $rows_name->name }}">
                @if($rows_name->name === 'title')
                    @if($data->level > 1)
                        <span style="padding-left: {{ $data->level*15 }}px"></span>
                    @endif
                    <a class="uk-h4" href="/admin/{{ $package->name }}/{{ $data->id }}/edit">{{ $data->{$rows_name->name} }}</a>
                    <br/>
                    <a class="link-to-front" target="_blank" href="{{ $data->url }}" title="ссылка на элемент на сайте">
                        {{ str_limit($data->url, 35, '...') }}
                    </a>
                @else
                    {{ $data->{$rows_name->name} }}
                @endif
            </td>
        @endif
    @endforeach
    @include('larrock::admin.admin-builder.additional-rows-td', ['data_value' => $data])
</tr>
@if(isset($data->children))
    @foreach($data->children as $child)
        @include('larrock::admin.menu.item-default', ['data' => $child])
    @endforeach
@endif