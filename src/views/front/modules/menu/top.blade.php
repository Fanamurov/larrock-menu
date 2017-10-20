<button class="uk-button uk-button-large uk-button-primary uk-width-1-1 uk-hidden-medium uk-hidden-large"
        data-uk-toggle="{target:'#{{ $menu->first()->type }}_menu_block', cls:'uk-hidden-small'}" onclick="$(this).hide()">Меню</button>
<nav class="uk-navbar uk-hidden-small" id="{{ $menu->first()->type }}_menu_block">
    <ul class="uk-navbar-nav">
        @foreach($menu as $data_item)
            @if(count($data_item->get_childActive) > 0)
                <li class="uk-parent @if($data_item->selected) uk-active @endif uk-open2" data-uk-dropdown="{hoverDelayIdle:0; remaintime:0}">
                    <a href="" onclick="return false">{{ $data_item->title }} <i class="uk-icon-caret-down"></i></a>
                    <div class="uk-dropdown uk-dropdown-bottom">
                        <ul class="uk-nav">
                            @foreach($data_item->get_childActive as $child)
                                <li @if($child->selected) class="uk-active" @endif><a href="{{ $child->url }}">{{ $child->title }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                </li>
            @else
                <li @if($data_item->selected) class="uk-active" @endif>
                    <a href="{{ $data_item->url }}">{{ $data_item->title }}</a>
                </li>
            @endif
        @endforeach
    </ul>
</nav>