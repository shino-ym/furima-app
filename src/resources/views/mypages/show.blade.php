@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{asset('css/mypages/show.css')}}">
@endsection


@section('content')

<div class="all-content">
    <div class="mypage-container">
        <div class="img-group">
            <div class="img-container">
                    <img id="preview"
                    src="{{ $user->profile_image && file_exists(storage_path('app/public/' . $user->profile_image))
                            ? asset('storage/' . $user->profile_image)
                            : asset('images/profile_images/default-profile.png') }}"
                    class="preview-image">
            </div>
            <h1>{{ $user->name }}</h1>
            <a class="profile-edit-link" href="{{route('mypage.profile.edit')}}">プロフィールを編集</a>
        </div>
        {{-- タブメニュー --}}
        <div class="tab-menu">
            <a href="{{ route('mypage.index', ['tab' => 'sell']) }}"
                class="tab-link {{ $tab === 'sell' ? 'active' : '' }}">
                出品した商品
            </a>
            <a href="{{ route('mypage.index', ['tab' => 'buy']) }}"
                class="tab-link {{ $tab === 'buy' ? 'active' : '' }}">
                購入した商品
            </a>
        </div>

        <hr class="separator">

        <div class="tab-content">
            <ul class="item-list">
                @foreach($items as $item)
                    <li class="item-card">
                        <img src="{{ asset('storage/' . $item->item_image) }}" alt="{{ $item->name }}" class="item-image">
                        <p class="item-name">{{ $item->name }}</p>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>

@endsection