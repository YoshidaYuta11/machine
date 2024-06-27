<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Company;
use App\Http\Requests\ProductRequest;
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

    public function store(ProductRequest $request)
    {
        try {
            $product = new Product($request->validated());

            if ($request->hasFile('img_path')) {
                $filename = $request->img_path->getClientOriginalName();
                $filePath = $request->img_path->storeAs('products', $filename, 'public');
                $product->img_path = '/storage/' . $filePath;
            } else {
                $product->img_path = 'https://picsum.photos/200/300';
            }

            $product->save();

            return redirect()->route('products.index')->with('success', 'Product created successfully');
        } catch (\Exception $e) {
            \Log::error('Failed to create product: ', ['error' => $e->getMessage()]);
            return redirect()->route('products.index')->with('error', 'Failed to create product: ' . $e->getMessage());
        }
    }

    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $companies = Company::all();
        return view('products.edit', compact('product', 'companies'));
    }

    public function update(ProductRequest $request, Product $product)
    {
        try {
            $product->fill($request->validated());

            if ($request->hasFile('img_path')) {
                $filename = $request->img_path->getClientOriginalName();
                $filePath = $request->img_path->storeAs('products', $filename, 'public');
                $product->img_path = '/storage/' . $filePath;
            }

            $product->save();

            return redirect()->route('products.index')->with('success', 'Product updated successfully');
        } catch (\Exception $e) {
            \Log::error('Failed to update product: ', ['error' => $e->getMessage()]);
            return redirect()->route('products.index')->with('error', 'Failed to update product: ' . $e->getMessage());
        }
    }

    public function destroy(Product $product)
    {
        try {
            $product->delete();

            return redirect()->route('products.index')->with('success', 'Product deleted successfully');
        } catch (\Exception $e) {
            \Log::error('Failed to delete product: ', ['error' => $e->getMessage()]);
            return redirect()->route('products.index')->with('error', 'Failed to delete product: ' . $e->getMessage());
        }
    }
}
