<?php
namespace App\Http\Controllers;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\File;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $products=Product::when(!$request->user()->hasPermission('shop.manage'),fn($q)=>$q->where('active',true)->where('stock','>',0))->orderBy('name')->get();
        $orders=$request->user()->hasPermission('shop.manage')?Order::with(['member','items'])->latest('ordered_at')->limit(20)->get():Order::with('items')->where('member_id',$request->user()->id)->latest('ordered_at')->get();
        return view('shop.index',compact('products','orders'));
    }
    public function storeProduct(Request $request)
    {
        $data=$this->productData($request); $path=$request->file('image')?->store('products','public'); Product::create([...$data,'image_path'=>$path]);
        return back()->with('success','Produto cadastrado.');
    }
    public function updateProduct(Request $request, Product $product)
    {
        $data=$this->productData($request); if($request->file('image')){ if($product->image_path)Storage::disk('public')->delete($product->image_path); $data['image_path']=$request->file('image')->store('products','public'); } $product->update($data);
        return back()->with('success','Produto atualizado.');
    }
    public function order(Request $request, Product $product)
    {
        abort_unless($request->user()->role==='member',403); $data=$request->validate(['quantity'=>['required','integer','min:1','max:20']]);
        DB::transaction(function()use($request,$product,$data){$product=Product::lockForUpdate()->findOrFail($product->id); abort_unless($product->active&&$product->stock >= $data['quantity'],422,'Estoque insuficiente.'); $subtotal=(float)$product->price*$data['quantity']; $order=Order::create(['member_id'=>$request->user()->id,'status'=>'pending','total'=>$subtotal,'ordered_at'=>now()]); $order->items()->create(['product_id'=>$product->id,'product_name'=>$product->name,'unit_price'=>$product->price,'quantity'=>$data['quantity'],'subtotal'=>$subtotal]); $product->decrement('stock',$data['quantity']);});
        return back()->with('success','Pedido realizado. A academia fará a confirmação.');
    }
    private function productData(Request $request): array
    {
        return $request->validate(
            [
                'name'=>['required','string','max:120'],
                'description'=>['nullable','string','max:1000'],
                'price'=>['required','numeric','min:0.01'],
                'stock'=>['required','integer','min:0'],
                'active'=>['required','boolean'],
                'image'=>['nullable',File::image()->max(3*1024)],
            ],
            [
                'image.uploaded'=>'Não foi possível enviar a imagem. Use um arquivo de até 3 MB.',
                'image.image'=>'A imagem deve ser um arquivo JPG, PNG, GIF, BMP ou WebP.',
                'image.max'=>'A imagem deve ter no máximo 3 MB.',
            ],
            ['image'=>'imagem'],
        );
    }
}
