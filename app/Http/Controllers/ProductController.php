<?php
namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return Product::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'originalPrice' => 'required|numeric',
            'discountedPrice' => 'required|numeric',
            'image' => 'required|url',
            'category' => 'required|string|max:255',
            'isFlashSale' => 'required|boolean',
            'isDiscount' => 'required|boolean',
            'inputDate' => 'required|date',
            'transactionCount' => 'required|integer',
            'description' => 'required|string',
        ]);

        $product = Product::create($request->all());
        return response()->json($product, 201);
    }

    public function show($id)
    {
        return Product::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'string|max:255',
            'originalPrice' => 'numeric',
            'discountedPrice' => 'numeric',
            'image' => 'url',
            'category' => 'string|max:255',
            'isFlashSale' => 'boolean',
            'isDiscount' => 'boolean',
            'inputDate' => 'date',
            'transactionCount' => 'integer',
            'description' => 'string',
        ]);

        $product = Product::findOrFail($id);
        $product->update($request->all());
        return response()->json($product, 200);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return response()->json(null, 204);
    }
}
