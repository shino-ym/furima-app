@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{asset('css/auth/verify-email.css')}}">
@endsection

@section('content')
<div class="verify-container">
    <div class="verify-form">
        <div class="verify-message">
            <p>登録していただいたメールアドレスに認証メールを送付しました。</p>
            <p>メール認証を完了してください。</p>
        </div>
        {{-- 開発環境のみ：Mailhog を開くボタン --}}
            @if(app()->environment('local'))
            <a href="http://localhost:8025/" target="_blank" class="approve-btn">
                承認はこちら
            </a>
            @endif

        <form method="post" action="{{route('verification.send')}}" >
            @csrf
            <button type="submit" class="mail-submit">認証メールを再送する</button>
        </form>
    </div>
</div>

@endsection