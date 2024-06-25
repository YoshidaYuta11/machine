<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Company;
use Illuminate\Http\Request;

class ProductController extends Controller 
{
    
    public function index(Request $request)
{
    $query = Product::query();

    if ($search = $request->search) {
        $query->where('product_name', 'LIKE', "%{$search}%");
    }

    if ($min_price = $request->min_price) {
        $query->where('price', '>=', $min_price);
    }

    if ($max_price = $request->max_price) {
        $query->where('price', '<=', $max_price);
    }

    if ($min_stock = $request->min_stock) {
        $query->where('stock', '>=', $min_stock);
    }

    if ($max_stock = $request->max_stock) {
        $query->where('stock', '<=', $max_stock);
    }

    if ($company_id = $request->company_id) {
        $query->where('company_id', $company_id);
    }

    // ソート順
    $query->orderBy('id', 'desc');

    // 商品データを取得
    $products = $query->paginate(10);

    // メーカー一覧を取得
    $companies = Company::all();

    return view('products.index', [
        'products' => $products,
        'companies' => $companies
    ]);
}





    public function create()
    {
        
        $companies = Company::all();

       
        return view('products.create', compact('companies'));
    }

  
    public function store(Request $request)
{
    $request->validate([
        'product_name' => 'required',
        'company_id' => 'required',
        'price' => 'required',
        'stock' => 'required',
        'comment' => 'nullable',
        'img_path' => 'nullable|image|max:2048',
    ]);

    // デバッグメッセージ
    \Log::info('Validation passed');

    $product = new Product([
        'product_name' => $request->get('product_name'),
        'company_id' => $request->get('company_id'),
        'price' => $request->get('price'),
        'stock' => $request->get('stock'),
        'comment' => $request->get('comment'),
    ]);

    // デバッグメッセージ
    \Log::info('Product created: ', $product->toArray());

    if ($request->hasFile('img_path')) {
        $filename = $request->img_path->getClientOriginalName();
        $filePath = $request->img_path->storeAs('products', $filename, 'public');
        $product->img_path = '/storage/' . $filePath;
        // デバッグメッセージ
        \Log::info('Image uploaded: ', ['filePath' => $filePath]);
    } else {
        $product->img_path = 'https://picsum.photos/200/300';
        // デバッグメッセージ
        \Log::info('Using placeholder image');
    }

    $product->save();

    // デバッグメッセージ
    \Log::info('Product saved: ', $product->toArray());

    return redirect()->route('products.index')->with('success', 'Product created successfully');
}



    public function show(Product $product)
   
    {
        
        return view('products.show', ['product' => $product]);
  
    }

    public function edit(Product $product)
    {
       
        $companies = Company::all();

      
        return view('products.edit', compact('product', 'companies'));
    }

    public function update(Request $request, Product $product)
{
    $request->validate([
        'product_name' => 'required',
        'company_id' => 'required',  // メーカーIDのバリデーションを追加
        'price' => 'required',
        'stock' => 'required',
        'comment' => 'required',  // コメントのバリデーションを追加
    ]);

    $product->product_name = $request->product_name;
    $product->company_id = $request->company_id;  // メーカーIDを更新
    $product->price = $request->price;
    $product->stock = $request->stock;
    $product->comment = $request->comment;  // コメントを更新

    if ($request->hasFile('img_path')) {
        $filename = $request->img_path->getClientOriginalName();
        $filePath = $request->img_path->storeAs('products', $filename, 'public');
        $product->img_path = '/storage/' . $filePath;
    }

    $product->save();

    return redirect()->route('products.index')->with('success', 'Product updated successfully');
}




    public function destroy(Product $product)

    {
       
        $product->delete();

       
        return redirect('/products');
      
    }
}

