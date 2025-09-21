@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sell/create.css')}}">
@endsection

@section('search')
<form method="GET" action="{{ route('items.index') }}">
    <input type="text" name="keyword" class="header__search" value="{{ old('keyword', $keyword ?? '') }}"  placeholder="なにをお探しですか？">
    <input type="hidden" name="tab" value="{{ $tab ?? '' }}" >
</form>
@endsection

@section('link')
<div class="header__link">
    <form action="/logout" method="post" class="header__form">
        @csrf
        <input class="header__input" type="submit" value="ログアウト">
    </form>
    <div class="header__link--mypage">
        <a href="{{ route('profile.index')}}"" class="button-link" >マイページ</a>
    </div>
    <div class="header__link--sell button-wrapper">
        <a href="{{ route('sell.create')}}" class="button-link" >出品</a>
    </div>
</div>
@endsection

@section('content')
<div class="sell-form">
    <h1 class="sell-form__heading content__heading">商品の出品</h1>

    <div class="sell-form__inner">    
        <form class="sell-form__form" action="{{ route('sell.store') }}" method="post" enctype="multipart/form-data">
            @csrf
            <!-- 商品画像 -->
            <div class="sell-form-section">
                <div class="sell-form-section__title">商品画像</div>

                <!-- プレビュー領域（中央にボタン） -->
                <div class="image-drop" id="imageDrop">
                    <img id="preview" alt="プレビュー" />
                    <button type="button" class="choose-in-drop" id="overlayChoose">画像を選択する</button>
                </div>

                <!-- 実ファイル入力は隠す -->
                <input id="item_image" name="item_image" type="file" accept="image/*" hidden />
                @error('item_image')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            <!-- 商品の詳細 -->
            <div class="sell-form-section">
                <div class="sell-form-section__title">商品の詳細</div>
                
                <div class="sell-form__group">
                    <label class="sell-form__label">カテゴリー</label>
                    <div class="category-tags">
                        @foreach($categories as $category)
                        <label for="category-{{ $category->id }}" class="tag">{{ $category->name }}
                            <input type="checkbox"  id="category-{{ $category->id }}" name="categories[]" value="{{ $category->id }}">    
                        </label>
                        @endforeach
                    </div>
                        @error('categories')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror

                    <div class="sell-form__group">
                        <label class="sell-form__label" for="condition">商品の状態</label>
                        <div class="select-wrapper">
                            <select class="sell-form__select" class="condition" name="condition">
                                <option value="">選択してください</option>
                                <option value="1" {{
                old('condition')==1 ? 'selected' : '' }}>良好</option>
                                <option value="2" {{
                old('condition')==2 ? 'selected' : '' }}>目立った傷や汚れなし</option>
                                <option value="3" {{
                old('condition')==3 ? 'selected' : '' }}>やや傷や汚れあり</option>
                                <option value="4" {{
                old('condition')==4 ? 'selected' : '' }}>状態が悪い</option>
                            </select>
                        </div>
                        @error('condition')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                </div>
            </div>

            <!-- 商品名と説明 -->
            <div class="sell-form-section">
                <div class="sell-form-section__title">商品名と説明</div>
                
                <div class="sell-form__group">
                        <label class="sell-form__label" for="name">商品名</label>
                        <input class="sell-form__input" type="text" name="name" placeholder="商品名を入力してください" value="{{ old('name', $item->name ?? '') }}">
                        @error('name')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                </div>

                <div class="sell-form__group">
                    <label class="sell-form__label" for="brand">ブランド名</label>
                    <input class="sell-form__input" type="text" name="brand" placeholder="ブランド名を入力してください" value="{{ old('brand', $item->brand ?? '') }}" >
                        @error('brand')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                </div>

                <div class="sell-form__group">
                    <label class="sell-form__label" for="description">商品の説明</label>
                    <textarea class="sell-form__textarea" name="description" placeholder="商品の詳細や状態について説明してください">{{ old('description', $item->description ?? '') }}</textarea>
                        @error('description')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                </div>

                <div class="sell-form__group">
                    <label class="sell-form__label" for="price">販売価格</label>
                    <div class="price-input">
                        <input class="sell-form__input" type="text" name="price" placeholder="価格を入力してください" value="{{ old('price', $item->price ?? '') }}">
                    </div>
                        @error('price')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                </div>
            </div>
            <input type="hidden" name="seller_id" value="{{ Auth::id() }}" />
            <button type="submit" class="sell-form__btn btn">出品する</button>
        </form>
    </div>
</div>

<script>

  const input = document.getElementById('item_image');
  const drop  = document.getElementById('imageDrop');
  const preview = document.getElementById('preview');

  // 領域やボタン、画像をクリックしたら常にファイル選択を開く
  drop.addEventListener('click', () => input.click());

  let currentObjectUrl = null;

  // ファイル選択 → プレビュー表示
  input.addEventListener('change', () => {
    if (!input.files || input.files.length === 0) return;
    const file = input.files[0];
    if (!file.type.startsWith('image/')) {
      alert('画像ファイルを選択してください');
      input.value = '';
      return;
    }
    if (currentObjectUrl) URL.revokeObjectURL(currentObjectUrl);

    currentObjectUrl = URL.createObjectURL(file);
    preview.src = currentObjectUrl;

    // 見た目を「画像あり」状態へ
    drop.classList.add('has-image');
  });

/*
    function previewImage(obj)
    {
        var fileReader = new FileReader();
        fileReader.onload = (function() {
        document.getElementById('img').src = fileReader.result;
        });
        fileReader.readAsDataURL(obj.files[0]);
    }

    function toggleTag(tag) {
        const checkboxId = tag.getAttribute('data-checkbox');
        const checkbox = document.getElementById(checkboxId);
        
        // タグの見た目を切り替え
        tag.classList.toggle('selected');
        
        // 対応するチェックボックスの状態を切り替え
        checkbox.checked = tag.classList.contains('selected');
    }
*/
/*
    // 画像アップロードのハンドリング
    document.getElementById('imageInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const uploadDiv = document.querySelector('.image-upload');
            uploadDiv.innerHTML = `<p>画像が選択されました: ${file.name}</p><button type="button" class="upload-btn" onclick="document.getElementById('imageInput').click()">画像を変更する</button>`;
        }
    });
    */
/*
    // フォーム送信の処理
    document.querySelector('form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // 選択されたカテゴリーを取得
        const selectedCategories = [];
        const checkboxes = document.querySelectorAll('input[name="categories[]"]:checked');
        checkboxes.forEach(checkbox => {
            selectedCategories.push(checkbox.value);
        });
        
        // フォームデータの確認（実際の送信前にデータを確認）
        const formData = new FormData(this);
        console.log('送信データ:');
        console.log('カテゴリー配列:', selectedCategories);
        for (let [key, value] of formData.entries()) {
            console.log(key + ':', value);
        }
        
        alert('出品が完了しました！\n選択されたカテゴリー: ' + (selectedCategories.length > 0 ? selectedCategories.join(', ') : 'なし'));
    });
*/
/*
    // 価格入力のフォーマット
    document.getElementById('price').addEventListener('input', function(e) {
        let value = e.target.value.replace(/[^\d]/g, '');
        if (value) {
            value = parseInt(value).toLocaleString();
        }
        e.target.value = value;
    });
    */
</script>
@endsection
