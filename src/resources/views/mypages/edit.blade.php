@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{asset('css/mypages/edit.css')}}">
@endsection


@section('content')

<div class="edit-form">
    <div class="edit-form__content">
        <div class="edit-form__heading">
            <h1>プロフィール設定</h1>
        </div>
        <form class="form" method="POST" action="{{ route('mypage.profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="img-group">
                <div class="img-container">
                    <img id="preview"
                    src="{{ $user->profile_image && file_exists(storage_path('app/public/' . $user->profile_image))
                            ? asset('storage/' . $user->profile_image)
                            : asset('images/profile_images/default-profile.png') }}"
                    class="preview-image" alt="プロフィール画像">
                </div>
                <button class="image-button" type="button" onclick="document.getElementById('image').click()">画像を選択する</button>
                <input type="file" id="image" name="profile_image" accept="image/*" onchange="previewImage(event)" hidden>
            </div>
            <div class="form-group">
                <label for="name" class="form-label">ユーザー名</label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"/>
                @error('name')
                    <div class="input-error-message">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="default_postal_code" class="form-label">郵便番号</label>
                <input type="text" id="default_postal_code" name="default_postal_code" value="{{ old('default_postal_code', $user->default_postal_code) }}"/>
                @error('default_postal_code')
                    <div class="input-error-message">{{ $message }}</div>
                @enderror            </div>
            <div class="form-group">
                <label for="default_address_line" class="form-label">住所</label>
                <input type="text" id="default_address_line" name="default_address_line" value="{{ old('default_address_line', $user->default_address_line) }}"/>
                @error('default_address_line')
                    <div class="input-error-message">{{ $message }}</div>
                @enderror

            </div>
            <div class="form-group">
                <label for="default_building" class="form-label">建物名</label>
                <input type="text" id="default_building" name="default_building" value="{{ old('default_building', $user->default_building) }}"/>
            </div>

            <div class="form-btn">
                <button class="submit-btn" type="submit">更新する</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<script>
function previewImage(event) {
    const file = event.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = () => {
        const preview = document.getElementById('preview');
        preview.src = reader.result;
        preview.style.display = 'block';
        document.getElementById('fileName').textContent = file.name;
    };
    reader.readAsDataURL(file);
}

</script>
@endsection


