@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchases/address.css') }}">
@endsection

@section('content')

<div class="address-form">
    <div class="address-form__content">
        <div class="address-form__heading">
            <h1>住所の変更</h1>
        </div>
        <form class="form" action="{{ route('purchase.updateAddress', $item_id) }}" method="post">
            @csrf
            @method('PATCH')

            <input type="hidden" name="item_id" value="{{ $item_id }}">

            <div class="form-group">
                <label for="shipping_postal_code" class="form-label">郵便番号</label>
                <input type="text" id="shipping_postal_code" name="shipping_postal_code"
                    value="{{ old('shipping_postal_code', $postal_code) }}">
                @error('shipping_postal_code')
                    <div class="input-error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="shipping_address_line" class="form-label">住所</label>
                <input type="text" id="shipping_address_line" name="shipping_address_line"
                value="{{ old('shipping_address_line', $address_line)}}">
                @error('shipping_address_line')
                    <div class="input-error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="shipping_building" class="form-label">建物名</label>
                <input type="text" id="shipping_building" name="shipping_building"
                    value="{{ old('shipping_building', $building) }}">
            </div>

            <div class="form-btn">
                <button class="submit-btn" type="submit">更新する</button>
            </div>
        </form>
    </div>
</div>

@endsection
