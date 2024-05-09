<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {

        return view('setting.product.index', [
            'title' => 'Products',
            'products' => Product::all(),
        ]);
    }

    public function edit($id)
    {
        $product = Product::find($id);

        return view('setting.product/edit', [
            'title' => 'Edit Product',
            'product' => $product
        ]);
    }

    public function show()
    {
        return view('setting.product/show', [
            'title' => 'Show Product',
        ]);
    }

    public function store(Request $request)
    {
        $validate = $request->validate([
            'name' => 'required',
            'cost' => 'required|numeric',
        ]);

        Product::create($validate);

        return redirect('/setting/product')->with('success', 'Product Added');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'cost' => 'required|numeric',
        ]);

        $product = Product::find($id);
        $product->name = $request->name;
        $product->cost = $request->cost;
        $product->save();

        return redirect('/setting/product')->with('success', 'Product Updated');
    }

    public function destroy($id)
    {
        Product::destroy($id);

        return redirect('/setting/product')->with('success', 'Product Deleted');
    }
}
