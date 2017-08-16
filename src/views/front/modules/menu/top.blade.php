<nav class="uk-navbar uk-hidden-small" id="{{ $menu->first()->type }}_menu_block">
    <ul class="uk-navbar-nav">
        @foreach($menu as $data_item)
            <li @if($data_item->selected) class="uk-active" @endif>
                <a href="{{ $data_item->url }}">{{ $data_item->title }}</a>
            </li>
        @endforeach
    </ul>
</nav>