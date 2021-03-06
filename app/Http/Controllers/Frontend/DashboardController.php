<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Model\Product;
use App\Model\Tproduct;
use Illuminate\Http\Request;
use Auth;
use App\Model\Stproduct;
use App\Model\Supplier;
use App\Model\Unit;
use App\Model\Category;
use App\User;
class DashboardController extends Controller
{

  public function view(Request $request){
      $id=Auth::user()->id;
//      $data['allData']=User::find($id);
//      $data['allData']=Stproduct::find($id);
//      $data['tproducts']=Tproduct::all();
//      $data['suppliers']=Supplier::all();
//      $data['units']=Unit::all();
//      $data['categories']=Category::all();
      $data['stproducts']=Stproduct::select(
          "stproducts.id as stp_id",
          'tproducts.name as product_name',
          'stproducts.brand',
          'stproducts.orgin',
          'stproducts.unit',
          'stproducts.pack_size',
          'tproducts.total_qty as total_qty',
          'stproducts.total_price',
          'users.name as user_name',
          'users.organization'
      )
          ->where('stproducts.created_by', Auth::user()->id)
          ->join('tproducts', ['stproducts.tproduct_id' => 'tproducts.id'])
          ->join('users', ['users.id' => 'stproducts.created_by'])
          ->get();


      return view('frontend.stproduct.view-stproduct', $data);
  }


    public function dashboard(){
/*dd('ok');*/
        $data['tproducts']=Tproduct::all();
        $data['suppliers']=Supplier::all();
        $data['units']=Unit::all();
        $data['categories']=Category::all();

        return view('frontend.product',$data);

}


    public function store(Request $request){
        $countClass=count($request->tproduct_id);

        if($countClass != Null) {
            for ($i = 0; $i < $countClass; $i++) {

                $specificationOfProducts = Stproduct::where('created_by', Auth::user()->id)
                    ->where('tproduct_id', $request->tproduct_id[$i])
                    ->first();
                if (!$specificationOfProducts) {
                    $product = new Stproduct();
                    $product->tproduct_id = $request->tproduct_id[$i];
                    $product->brand = $request->brand[$i];
                    $product->orgin = $request->orgin[$i];
                    $product->unit = $request->unit[$i];
                    $product->unit_price = $request->unit_price[$i];
                    $product->pack_size = $request->pack_size[$i];
                    $product->total_qty = $request->total_qty[$i];
                    $product->total_price = $request->total_price[$i];
                    $product->created_by = Auth::user()->id;
                    $product->save();


                }
            }
        }


        return redirect()->route('dashboard')->with('success','Product  installed');
    }

    public function edit(Request $request) {
        $data['tproducts'] = Tproduct::all();
        $data['stproduct'] = Stproduct::find($request->id);

        return view('frontend.stproduct.edit-stproduct', $data);
    }

    public function updateProductInDatabase($product, $request) {
        $product->tproduct_id = $request->tproduct_id;
        $product->brand = $request->brand;
        $product->orgin = $request->orgin;
     /*   $product->unit = $request->unit;*/
        $product->pack_size = $request->pack_size;
        /*$product->total_qty = $request->total_qty;*/
        $product->unit_price = $request->unit_price;
       $product->total_price = $request->total_price;
        $product->created_by = Auth::user()->id;
        $product->update();
    }

    public function update(Request $request) {
        $product = Stproduct::find($request->id);
        if ($request->tproduct_id == $product->tproduct_id) {
            $this->updateProductInDatabase($product, $request);
        } else {
            $specificationOfProductsCount = Stproduct::where('created_by', Auth::user()->id)
                ->where('tproduct_id', $request->tproduct_id)
                ->count();

            if ($specificationOfProductsCount > 0) {
                return redirect()->back()->with('error', 'Product attribute already exists!');
            } else {
                $this->updateProductInDatabase($product, $request);
            }
        }

        return redirect()->route('dashboard.view')->with('success','Product saved');
    }



}
