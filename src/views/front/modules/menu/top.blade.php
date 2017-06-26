<nav class="uk-navbar uk-hidden-small" id="top_menu_block">
    <ul class="uk-navbar-nav">
        @foreach($menu as $data_item)
            <li @if(
        (ends_with($data_item->url, Route::getCurrentRoute()->parameter('url')))
        OR (Route::current()->uri() === '/' AND $data_item->url === '/')
        OR (starts_with(Route::getCurrentRoute()->uri(), $data_item->connect))
        ) class="uk-active" @endif>
                <a href="{{ $data_item->url }}">{{ $data_item->title }}</a>
            </li>
        @endforeach
    </ul>
</nav>