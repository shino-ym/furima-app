@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items/index.css') }}">
@endsection

@section('content')
<div class="item-page">

    {{-- タブメニュー --}}
    <div class="tab-menu">
        <a href="{{ route('index', ['tab' => 'default','keyword'=>request('keyword')]) }}"
            class="tab-link {{ ($tab ?? '') === 'default' ? 'active' : '' }}">
            おすすめ
        </a>
        <a href="{{ route('index', ['tab' => 'mylist','keyword'=>request('keyword')]) }}"
            class="tab-link {{ ($tab ?? '') === 'mylist' ? 'active' : '' }}">
            マイリスト
        </a>
    </div>
    <hr class="separator">

    {{-- 商品一覧 --}}
    <div class="item-container">
        <div class="item-contents">
            @foreach ($items as $item)
                <div class="item-content">
                    <a href="{{ route('items.show', ['id' => $item->id]) }}" class="item-link">
                        <img src="{{ asset('storage/' . $item->item_image) }}" alt="商品画像" class="img-content" />
                    </a>
                    <p class="item-name">
                        {{ $item->name }}
                        @if ($item->is_sold)
                            <span class="sold-badge">Sold</span>
                        @endif
                    </p>
                </div>
            @endforeach
        </div>
    </div>

</div>
@endsection
