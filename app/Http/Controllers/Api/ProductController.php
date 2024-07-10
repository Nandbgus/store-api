<?php

namespace App\Http\Controllers\Api;

//import model Post
use App\Models\Post;

use Illuminate\Http\Request;

//import resource PostResource
use App\Http\Controllers\Controller;

//import Http request
use App\Http\Resources\PostResource;

//import facade Validator
use App\Http\Resources\ProductResource;
use App\Models\Product;
//import facade Storage
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        //get all posts
        $products = Product::latest()->paginate(5);

        //return collection of posts as a resource
        return new ProductResource(true, 'List Data Product', $products);
    }

    /**
     * store
     *
     * @param  mixed $request
     * @return void
     */
    public function store(Request $request)
    {
        //define validation rules
        $validator = Validator::make($request->all(), [
            'nama_produk' => 'required',
            'harga'   => 'required',
            'description' => 'required',
            'image'     => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //upload image
        $image = $request->file('image');
        $image->storeAs('public/products', $image->hashName());

        //create post
        $product = Product::create([
            'nama_produk'     => $request->nama_produk,
            'harga'   => $request->harga,
            'description'   => $request->description,
            'image'     => $image->hashName(),
        ]);

        //return response
        return new ProductResource(true, 'Data Product Berhasil Ditambahkan!', $product);
    }

    /**
     * show
     *
     * @param  mixed $id
     * @return void
     */
    public function show($id)
    {
        //find post by ID
        $product = Product::find($id);

        //return single Product as a resource
        return new ProductResource(true, 'Detail Data Product!', $product);
    }

    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return void
     */
    public function update(Request $request, $id)
    {
        //define validation rules
        $validator = Validator::make($request->all(), [
            'nama_produk' => 'required',
            'harga'   => 'required',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //find post by ID
        $product = Product::find($id);

        //check if image is not empty
        if ($request->hasFile('image')) {

            //upload image
            $image = $request->file('image');
            $image->storeAs('public/products', $image->hashName());

            //delete old image
            Storage::delete('public/products/' . basename($product->image));

            //update Product with new image
            $product->update([
                'nama_produk'  => $request->nama_produk,
                'harga'   => $request->harga,
                'description'   => $request->description,
                'image'     => $image->hashName(),
            ]);
        } else {

            //update Product without image
            $product->update([
                'nama_produk'  => $request->nama_produk,
                'harga'   => $request->harga,
                'description'   => $request->description,
            ]);
        }

        //return response
        return new ProductResource(true, 'Data Product Berhasil Diubah!', $product);
    }
}
