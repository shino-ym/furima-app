@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchases/create.css') }}">
@endsection

@section('content')
<div class="all-contents">
    <div class="left-container">
        <div class="image-detail">
            <div class="item-content">
                <img src="{{ asset('storage/' . $item->item_image) }}" alt="商品画像" class="item-image" />
            </div>

            <div class="detail-content">
                <h1>{{ $item->name }}</h1>
                <p class="item-price">￥{{ number_format($item->price) }} </p>
            </div>
        </div>

        <hr class="separator">

        <div class="payment-method">
            <h2>支払い方法</h2>
            <div class="select-wrapper">
                <select name="payment" id="payment-select" class="payment-select">
                    <option data-display="選択してください" class="select-text" value="" hidden selected>選択してください</option>
                    <option data-display="コンビニ払い" class="option-text" value="konbini">コンビニ払い</option>
                    <option data-display="カード払い" class="option-text" value="card">カード支払い</option>
                </select>
            </div>
        </div>

        <hr class="separator">

        <div class="address-box">
            <div class="address-info">
                <h2>配送先</h2>
                <a href="{{ route('purchase.address', ['item_id' => $item_id]) }}" class="input-address">変更する</a>
            </div>

            @php
                $postal_code = session('shipping_postal_code', $user->default_postal_code);
                $address_line = session('shipping_address_line', $user->default_address_line);
                $building = session('shipping_building', $user->default_building);
            @endphp

            <div class="default-address">
                <p>〒{{ $postal_code }}</p>
                <p>{{ $address_line }} {{ $building ?: '' }}</p>
            </div>
        </div>

        <hr class="separator">
    </div>

    <div class="right-container">
        <div class="two-rows">
            <div class="box">
                <label class="box-name" for="">商品代金</label>
                <p class="item-price">￥{{ number_format($item->price) }} </p>
            </div>
            <div class="box">
                <label class="box-name" for="item-price">支払い方法</label>
                <p id="payment-box" class="payment-item"></p>
            </div>

            @if ($is_sold)
                <button disabled class="sold-button">sold</button>
            @else
                <form id="stripe-form" action="{{ route('purchase.checkout') }}" method="POST">
                    @csrf
                    {{-- 商品ID --}}
                    <input type="hidden" name="item_id" value="{{ $item->id }}">
                    {{-- 支払い方法 --}}
                    <input type="hidden" name="payment_method" id="hidden-payment-method">
                    <button type="submit" class="buy-button">購入する</button>
                </form>
            @endif
        </div>
    </div>
</div>

<script>
const select = document.getElementById('payment-select');
const box = document.getElementById('payment-box');
const hiddenPayment = document.getElementById('hidden-payment-method');

select.addEventListener('change', () => {
    const selectedText = select.options[select.selectedIndex].text;
    box.textContent = selectedText;
    hiddenPayment.value = select.value;
});
</script>
@endsection
