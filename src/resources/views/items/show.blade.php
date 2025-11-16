@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items/show.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
@endsection

@section('content')
<div class="item-container">
    <div class="image-detail">

        <div class="item-content">
            <img src="{{asset('storage/' . $item->item_image)}}" alt="商品画像" class="item-image" />
        </div>

        <div class="item-info">
            <div class="detail-content">
                <h1>{{ $item->name }}</h1>
                <p class="item-brand">{{ $item->brand_name }}</p>

                <p class="item-price">¥{{ number_format($item->price) }} <span class="price-tax">（税込）</span></p>
            </div>

            <div class="icon-area">

                {{-- いいね --}}
                <div class="icon-box">
                    <button class="like-button" data-item-id="{{ $item->id }}">
                        <i class="fa-star like-icon {{ $liked ? 'fa-solid liked' : 'fa-regular' }}"></i>
                    </button>
                    <p class="icon-count like-count">{{ $item->likes->count() }}</p>
                </div>

                {{-- コメント --}}
                <div class="icon-box">
                    <i class="fa-regular fa-comment comment-icon"></i>
                    <p class="icon-count" id="commentIconCount">{{ $item->comments->count() }}</p>
                </div>
            </div>

            <a href="{{ route('purchase.create', ['item_id' => $item->id]) }}" class="btn btn-primary">
                購入手続きへ
            </a>

            <h2>商品説明</h2>
            <div class="description-content">
                <p>{!! nl2br(e($item->description)) !!}</p>
            </div>

            <h2>商品の情報</h2>

            <div class="category-row">
                <h3>カテゴリー</h3>
                <div  class="categories">
                    @foreach($item->categories as $category)
                        <span class="category-tag">{{ $category->name }}</span>
                    @endforeach
                </div>
            </div>

            <div class="condition-row">
                <h3>商品の状態</h3>
                <div  class="categories">
                    <span class="condition-text">{{ $item->condition}}</span>
                </div>
            </div>

            <div class="comment-section">
                <h2 class="comment-count">コメント(<span id="commentCount">{{$item->comments->count()}}</span>)</h2>

                <ul id="commentList">
                    @foreach($item->comments as $comment)
                        <li class="comment-item" data-comment-id="{{ $comment->id }}">
                            <div class="comment-header">
                                @php
                                    $profileImage = $comment->user->profile_image && file_exists(storage_path('app/public/' . $comment->user->profile_image))
                                        ? asset('storage/' . $comment->user->profile_image)
                                        : asset('images/profile_images/default-profile.png');
                                @endphp
                                <img src="{{ $profileImage }}" alt="{{ $comment->user->name }}" class="avatar">
                                <span class="comment-username">{{ $comment->user->name }}</span>
                            </div>

                            <div class="comment-body">
                                {{ $comment->comment }}
                            </div>
                        </li>
                    @endforeach
                </ul>


                {{-- コメント投稿フォーム --}}
                <div class="form-name">商品へのコメント</div>
                <div class="comment-form">
                    <form id="commentForm" action="{{ route('comments.store', $item->id) }}" method="POST">
                        @csrf
                        <textarea name="comment" rows="2"></textarea>
                            <div id="commentError" style="color:red;"></div>
                        <button type="submit" class="submit">コメントを送信する</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {

    document.querySelectorAll('.like-button').forEach(button => {
        button.addEventListener('click', async function() {
            const itemId = this.dataset.itemId;
            const icon = this.querySelector('i');
            const countEl = this.nextElementSibling;

            try {
                const response = await fetch(`/items/${itemId}/like`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                });

                if (response.status === 401 || response.status === 419) {
                    window.location.href = '/login';
                    return;
                }

                const data = await response.json();

                if (data.success) {
                    if (data.liked) {
                        icon.classList.remove('fa-regular');
                        icon.classList.add('fa-solid', 'liked');
                    } else {
                        icon.classList.remove('fa-solid', 'liked');
                        icon.classList.add('fa-regular');
                    }
                    countEl.textContent = data.count;
                } else {
                    console.error('いいね処理に失敗しました');
                }
            } catch (err) {
                console.error(err);
            }
        });
    });

    const commentForm = document.getElementById('commentForm');
    const errorDiv = document.getElementById('commentError');

    if(commentForm) {
        commentForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            errorDiv.innerHTML = '';

            const formData = new FormData(commentForm);

            try {
                const response = await fetch(commentForm.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                if (response.status === 401 || response.status === 419) {
                            window.location.href = '/login';
                            return;
                        }

                const data = await response.json();

                if (data.errors && data.errors.comment) {
                    data.errors.comment.forEach(msg => {
                        const p = document.createElement('p');
                        p.textContent = msg;
                        errorDiv.appendChild(p);
                    });
                    return;
                }

                if (data.status === 'success') {
                    const li = document.createElement('li');
                    li.classList.add('comment-item');

                    const header = document.createElement('div');
                    header.classList.add('comment-header');

                    const img = document.createElement('img');
                    img.classList.add('avatar');
                    img.src = data.comment.profile_image;
                    img.alt = data.comment.user_name;

                    const username = document.createElement('span');
                    username.classList.add('comment-username');
                    username.textContent = data.comment.user_name;

                    header.appendChild(img);
                    header.appendChild(username);

                    const body = document.createElement('div');
                    body.classList.add('comment-body');
                    body.textContent = data.comment.comment;

                    li.appendChild(header);
                    li.appendChild(body);

                    document.getElementById('commentList').prepend(li);

                    const iconCount = document.getElementById('commentIconCount');
                    if (iconCount) iconCount.textContent = data.comment_count;

                    const countEl = document.getElementById('commentCount');
                    if (countEl && data.comment_count !== undefined) {
                        countEl.textContent = data.comment_count;
                    }

                    commentForm.reset();
                }

                } catch (err) {
                    console.error(err);
                    errorDiv.textContent = "コメント送信中にエラーが発生しました。";
                }
        });
    }
});
</script>
@endsection



