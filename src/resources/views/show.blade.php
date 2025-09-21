@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/show.css')}}">
@endsection

@section('search')
<form method="GET" action="{{ route('items.index') }}">
    <input type="text" name="keyword" class="header__search" value="{{ old('keyword', $keyword ?? '') }}"  placeholder="なにをお探しですか？">
    <input type="hidden" name="tab" value="{{ $tab ?? '' }}" >
</form>
@endsection

@section('link')
<div class="header__link">
    @auth
        <form action="/logout" method="post"  class="header__form">
            @csrf
            <input class="header__input" type="submit" value="ログアウト">
        </form>
    @else
        <div class="header__link--logout">
            <a href="/login" class="button-link" >ログイン</a>
        </div>
    @endauth
    <div class="header__link--mypage">
        <a href="{{ route('profile.index')}}"" class="button-link" >マイページ</a>
    </div>
    <div class="header__link--sell button-wrapper">
        <a href="{{ route('sell.create')}}" class="button-link" >出品</a>
    </div>
</div>
@endsection

@section('content')
<div class="container">
    <div class="item-detail">
        {{-- 左：商品画像 --}}
        <div class="item-image">
            @if(!empty($item->item_image))
                <img src="{{ asset('storage/' . $item->item_image) }}"  
                alt="{{ $item->name }}">
            @else
                <div class="placeholder" aria-hidden="true">商品画像</div>
            @endif
        </div>

        {{-- 右：情報 --}}
        <div class="item-info">
            <div class="section">
                <h1 class="item-title">{{ $item->name }}</h1>
                <div class="info-row">
                    <div class="info-label">ブランド名</div>
                    <div class="info-value">
                        {{ $item->brand }}
                    </div>
                </div>
                <div class="info-row">
                    <div class="item-price">{{ $item->formatted_price }} <small>(税込)</small></div>
                </div>
                
                <div class="action-buttons">
                    <button class="favorite-btn" 
                            data-item-id="{{ $item->id }}"
                            data-is-favorited="{{ $item->isFavoritedBy(auth()->user()) ? 'true' : 'false' }}">
                        <span class="star-icon {{ $item->isFavoritedBy(auth()->user()) ? 'active' : '' }}">
                            {{ $item->isFavoritedBy(auth()->user()) ? '★' : '☆' }}
                        </span>
                        <span class="favorite-count">{{ $item->favoritesCount() }}</span>
                    </button>
                    <button class="comment-icon">
                        <span class="comment-count">{{ $item->commentsCount() }}</span>
                    </button>
                </div>
                
                @if(!$item->is_sold)
                    <div class="purchase-btn btn">
                        <a href="/purchase/{{$item->id}}" class="item-name button-link">購入手続きへ</a>
                    </div>
                @else
                    <button class="purchase-btn" disabled>売り切れ</button>
                @endif
            </div>

            {{-- 商品説明 --}}
            <div class="section">
                <h2 class="section-title">商品説明</h2>
                <div class="description-text">{{($item->description)}}</div>
            </div>

            {{-- 商品の情報 --}}
            <div class="section">
                <h2 class="section-title">商品の情報</h2>
                <div class="info-table">
                    <div class="info-row">
                        <div class="info-label">カテゴリー</div>
                        <div class="info-value">
                            <div class="category-tags">
                                @foreach($item->categories as $category)
                                    <span class="category-tag">{{ $category->name }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">商品の状態</div>
                        <div class="info-value">
                            @if($item['condition'] == 1)
                                良好
                            @elseif($item['condition'] == 2)
                                目立った傷や汚れなし
                            @elseif($item['condition'] == 3)
                                やや傷や汚れあり
                            @else
                                状態が悪い
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- コメント --}}
            <div class="comment-section">
                <div class="comment-header">
                    <h2 class="comment-title">コメント</h2>
                    <span class="comment-count-text">({{ $item->commentsCount() }})</span>
                </div>
                    <div class="comments-container" id="comments-container">
                        @foreach($item->comments as $comment)
                            <div class="comment-item">
                                <div class="avatar">
                                    @if(!empty($comment->user->profile->profile_image))
                                        <img class="avatar-img-top" id=avatar src="{{ asset('storage/' .$comment->user->profile->profile_image) }}">
                                    @else
                                        <div class="avatar-img-top">
                                        </div>
                                    @endif
                                </div>
                                <div class="comment-content">
                                    <div class="comment-author">{{ $comment->user->name }}</div>
                                    <div class="comment-text">{{ $comment->comment }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @auth
                    <div class="comment-form">
                        <h3 class="comment-form-title">商品へのコメント</h3>
                        <form id="comment-form">
                            @csrf
                            <textarea class="comment-textarea" 
                                    name="comment" 
                                    placeholder="コメントを入力してください"></textarea>
                                    @error('comment')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                            
                            <button type="submit" class="comment-submit">コメントを送信する</button>
                        </form>
                    </div>
                @endauth
            </div>

        </div>

    </div>

</div>
@endsection

@section('scripts')
<script>
// お気に入り機能
document.querySelector('.favorite-btn').addEventListener('click', function() {
    const itemId = this.dataset.itemId;
    
    fetch(`/item/${itemId}/favorite`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const star = this.querySelector('.star-icon');
            if (data.is_favorited) {
                star.classList.add('active');
                star.textContent = '★';
            } else {
                star.classList.remove('active');
                star.textContent = '☆';
            }
            // お気に入り数を更新
            document.querySelector('.favorite-count').textContent = data.favorites_count;
        }
    });
});

// コメント投稿
document.getElementById('comment-form').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const itemId = {{ $item->id }};
    const submitButton = this.querySelector('.comment-submit');
    const textarea = this.querySelector('.comment-textarea');

    // ローディング状態
    submitButton.disabled = true;
    submitButton.textContent = '送信中...';

    // エラー表示をクリア
    clearErrors();
    
    fetch(`/item/${itemId}/comments`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => {

        if (response.ok) {
            return response.json();
        } else if (response.status === 422) {
            // バリデーションエラー
            return response.json().then(errorData => {
                throw new Error(JSON.stringify(errorData));
            });
        } else {
            throw new Error('サーバーエラーが発生しました');
        }
    })
    .then(data => {
        if (data.success) {

            // 新しいコメントを追加
            addCommentToList(data.comment);
            updateCommentCount(data.comments_count);
            
            // フォームリセット
            this.reset();

            
        }
    })
    .catch(error => {
        console.error('Error:', error);

        // バリデーションエラー表示
        const errorData = JSON.parse(error.message);
        showValidationErrors(errorData.errors);
    })
    .finally(() => {
        // ローディング状態解除
        submitButton.disabled = false;
        submitButton.textContent = 'コメントを送信する';
    });    
});

// コメントをリストに追加
function addCommentToList(comment) {
    const commentHtml = `
        <div class="comment-item">
            <div class="avatar">
                @if(!empty($comment->user->profile->profile_image))
                                <img id=avatar src="http://localhost/storage/${comment.profile_image}">

                @endif
            </div>
            <div class="comment-content">
                <div class="comment-author">${comment.user_name}</div>
                <div class="comment-text">${comment.comment}</div>
                
            </div>
        </div>
    `;
    
    const commentsContainer = document.getElementById('comments-container');
    commentsContainer.insertAdjacentHTML('beforeend', commentHtml);
    
    // 最新コメントにスクロール
    commentsContainer.scrollTop = commentsContainer.scrollHeight;
}

// コメント数更新
function updateCommentCount(count) {
    document.querySelector('.comment-count').textContent = count;
    document.querySelector('.comment-count-text').textContent = `(${count})`;
}

// バリデーションエラー表示
function showValidationErrors(errors) {
    for (const field in errors) {
        const messages = errors[field];
        messages.forEach(message => {
            showFieldError(field, message);
        });
    }
}

// フィールド別エラー表示
function showFieldError(field, message) {
    const fieldElement = document.querySelector(`[name="${field}"]`);
    if (fieldElement) {
        // 既存のエラーメッセージを削除
        const existingError = fieldElement.parentNode.querySelector('.error-message');
        if (existingError) {
            existingError.remove();
        }
        
        // エラーメッセージ要素を作成
        const errorElement = document.createElement('div');
        errorElement.className = 'alert alert-danger';
        errorElement.textContent = message;
        
        // フィールドの後にエラーメッセージを挿入
        fieldElement.parentNode.insertBefore(errorElement, fieldElement.nextSibling);
        
    }
}

// エラー表示をクリア
function clearErrors() {
    // エラーメッセージを削除
    document.querySelectorAll('.alert').forEach(element => {
        element.remove();
    });
}


</script>
@endsection