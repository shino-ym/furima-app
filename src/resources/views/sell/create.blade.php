@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sell/create.css') }}">
@endsection

@section('content')

<div class="sell-form">
    <div class="sell-form__content">
        <div class="sell-form__heading">
            <h1>商品の出品</h1>
        </div>
        <form class="form" method="POST" action="{{ route('sell.store') }}" enctype="multipart/form-data">
            @csrf

        <div class="image-upload">
            <div class="image-frame">
                <button type="button" class="select-btn" onclick="document.getElementById('image').click()">
                    画像を選択する
                </button>
                <input type="file" id="image" name="item_image"class="item-image" accept="image/*" hidden>
            </div>
            @error('item_image')
                <div class="input_error_message">{{ $message }}</div>
            @enderror
        </div>

        <h2>商品の詳細</h2>

            <hr class="separator">

            <div class="form-group">
                <label for="category" class="form-label">カテゴリー</label>
                    <div class="categories">
                        @foreach($categories as $category)
                            <label class="category-label">
                                <input type="checkbox" name="category_id[]" value="{{ $category->id }}" >
                                <span>{{ $category->name }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('category_id')
                        <div class="input_error_message">{{ $message }}</div>
                    @enderror
            </div>
            <div class="form-group">
                <label for="condition" class="form-label">商品の状態</label>
                    <div class="select_wrapper">
                        <select name="condition" id="condition">
                            <option value="" hidden selected>選択してください</option>
                                @foreach(\App\Models\Item::conditions() as $condition)
                                    <option value="{{ $condition }}" {{ old('condition', $item->condition ?? '') == $condition ? 'selected' : '' }}>
                                        {{ $condition }}
                                    </option>
                                @endforeach
                        </select>
                    </div>
                    @error('condition')
                        <div class="input_error_message">{{ $message }}</div>
                    @enderror
            </div>

            <h2>商品名と説明</h2>

            <hr class="separator">

            <div class="form-group">
                <label for="name" class="form-label">商品名</label>
                <input type="text" id="name" name="name" class="input-form" value="{{ old('name') }}"/>
                @error('name')
                    <div class="input_error_message">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="brand_name" class="form-label">ブランド名</label>
                <input type="text" id="brand_name" name="brand_name"class="input-form" value="{{ old('brand_name')}}"/>
            </div>
            <div class="form-group">
                <label for="description" class="form-label">商品の説明</label>
                <textarea name="description"  value="{{ old('description') }}" rows="2"></textarea>
                @error('description')
                    <div class="input_error_message">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="price" class="form-label">販売価格</label>

                <div class="price-input-wrapper">
                    <span class="yen-mark">¥</span>
                    <input type="text" name="price" id="price" step="1" min="0" class="price-input" value="{{ old('price') }}" >
                </div>
                @error('price')
                    <div class="input_error_message">{{ $message }}</div>
                @enderror

            </div>
            <div class="form-btn">
                <button class="submit-btn" type="submit">出品する</button>
            </div>
        </form>
    </div>
</div>

@endsection