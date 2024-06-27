@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">商品情報一覧</h1>

    <a href="{{ route('products.create') }}" class="btn btn-primary mb-3">商品新規登録</a>

    <form method="GET" action="{{ route('products.index') }}">
        <div class="mb-3">
            <label for="search" class="form-label">商品名で検索:</label>
            <input type="text" id="search" name="search" class="form-control" value="{{ request('search') }}">
        </div>

        <div class="mb-3">
            <label for="company_id" class="form-label">メーカーで検索:</label>
            <select class="form-select" id="company_id" name="company_id">
                <option value="">全てのメーカー</option>
                @foreach($companies as $company)
                    <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>{{ $company->company_name }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">検索</button>
    </form>

    <div class="products mt-5">
        <h2>商品情報</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>商品名</th>
                    <th>メーカー</th>
                    <th>価格</th>
                    <th>在庫数</th>
                    <th>コメント</th>
                    <th>商品画像</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($products as $product)
                <tr>
                    <td>{{ $product->id }}</td>
                    <td>{{ $product->product_name }}</td>
                    <td>{{ $product->company->company_name }}</td>
                    <td>{{ $product->price }}</td>
                    <td>{{ $product->stock }}</td>
                    <td>{{ $product->comment }}</td>
                    <td>
                        @if (filter_var($product->img_path, FILTER_VALIDATE_URL))
                            <img src="{{ $product->img_path }}" alt="商品画像" width="100">
                        @else
                            <img src="{{ asset($product->img_path) }}" alt="商品画像" width="100">
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('products.show', $product) }}" class="btn btn-info btn-sm mx-1">詳細表示</a>
                        <form method="POST" action="{{ route('products.destroy', $product) }}" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm mx-1">削除</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    
    {{ $products->links() }}
</div>
@endsection
