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

    
    if($search = $request->search){
        $query->where('product_name', 'LIKE', "%{$search}%");
    }

    
    if($min_price = $request->min_price){
        $query->where('price', '>=', $min_price);
    }

    
    if($max_price = $request->max_price){
        $query->where('price', '<=', $max_price);
    }

    
    if($min_stock = $request->min_stock){
        $query->where('stock', '>=', $min_stock);
    }

   
    if($max_stock = $request->max_stock){
        $query->where('stock', '<=', $max_stock);
    }

   
    $query->orderBy('id', 'desc');

    
    $products = $query->paginate(10);

    
    return view('products.index', ['products' => $products]);
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
        'price' => 'required',
        'stock' => 'required',
    ]);

    $product->product_name = $request->product_name;
    $product->price = $request->price;
    $product->stock = $request->stock;

    if ($request->hasFile('img_path')) {
        $filename = $request->img_path->getClientOriginalName();
        $filePath = $request->img_path->storeAs('products', $filename, 'public');
        $product->img_path = '/storage/' . $filePath;
    } else {
        // 既存の商品の画像パスを更新
        $product->img_path = '/storage/your_actual_image.jpg';
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

