<?php

namespace App\Http\Controllers;

use App\Http\Controllers;
use Illuminate\Http\Request;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;
use App\Transformers\SupplierTransformer;
use App\Supplier;

class SupplierController extends Controller
{
    protected $fractal;

    private $supplierTransformer;

    public function __construct(Manager $fractal, SupplierTransformer $supplierTransformer)
    {
      $this->fractal = $fractal;
      $this->supplierTransformer = $supplierTransformer;
    }

    public function index()
    {   
  	  $suppliers = Supplier::paginate(10);

      return $this->respondWithCollection($suppliers, $this->supplierTransformer);
    }

    public function show($id)
    {   
      $suppliers = Supplier::find($id);
      
      if (!$suppliers) {
         return $this->sendError('Could not find Supplier');
      }

      return $this->respondWithItem($suppliers, $this->supplierTransformer);
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $this->validate($request, [
            'name' => 'required|unique:suppliers',
            'address' => 'required',
            'phone_number' => 'required|max:13',
            'city_id'=> 'required|exists:cities,id'
        ]);
        
        $supplier = Supplier::create($input);
        if($supplier){
            return $this->sendData($supplier->toArray(), 'The resource is created successfully');
          }else{
          return $this->sendCustomResponse(500, 'Internal Error');
        }
    }

    public function update(Request $request, $id)
    {
      $input = $request->all();
      $supplier = Supplier::find($id);

      if (is_null($supplier)) {
        return $this->sendError('Supplier not found');
      }

      $supplier->name = $input['name'];
      $supplier->address = $input['address'];
      $supplier->phone_number = $input['phone_number'];
      $supplier->city_id = $input['city_id'];

      if ($supplier->save()) {
        return $this->sendResponse($supplier->toArray(), 'Customer updated successfully.');
      }
    }

    public function destroy(Request $request, $id)
    {
      $supplier = Supplier::find($id);

      if (!$supplier) {
        return $this->sendError('Supplier not found');
      }
      if ($supplier->delete()) {
        return $this->sendCustomResponse(200, 'Supplier: ' . $supplier->name . ' Telah Dihapus');
      }
    }
}
