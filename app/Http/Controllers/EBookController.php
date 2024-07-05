<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEBookRequest;
use App\Models\EBook;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class EBookController extends Controller
{
    public $module_title;
    public $module_name;
    public $module_icon;

    public function __construct()
    {
        // Page Title
        $this->module_title = 'EBooks.title';
        // module name
        $this->module_name = 'EBooks.title';

        // module icon
        $this->module_icon = 'fa-solid fa-clipboard-list';

        view()->share([
            'module_title' => $this->module_title,
            'module_icon' => $this->module_icon,
            'module_name' => $this->module_name,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $export_import = false;

        return view('backend.ebooks.index', compact('export_import'));
    }

    public function index_data(DataTables $datatable, Request $request)
    {
        $ebooks = EBook::query();

        $filter = $request->filter;

        $posOrder = [];

        if (isset($filter)) {
        }

        return $datatable->eloquent($ebooks)
            ->addColumn('action', function ($data) {
                return view('backend.ebooks.action_column', compact('data'));
            })
            ->editColumn('url', function ($data) {
                return $data->url;
            })
            ->editColumn('description', function ($data) {
                return $data->description ?? 'N/A';
            })
          

            ->editColumn('title', function ($data) {
                return $data->title;
            })  

            // ->orderColumn('total_amount', function ($query, $order) {
            //     $query->select('orders.*')
            //         ->leftJoin('order_groups', 'order_groups.id', '=', 'orders.id')
            //         ->orderBy('order_groups.grand_total_amount', $order);
            // }, 1)
            //   ->editColumn('payment', function ($data) {
            //       return view('product::backend.order.columns.payment_column', compact('data'));
            //   })
            //   ->editColumn('status', function ($data) {
            //       return view('product::backend.order.columns.status_column', compact('data'));
            //   })
            //   ->editColumn('location', function ($data) {
            //       return $data->location ? $data->location->name : 'N/A';
            //   })
            //   ->filterColumn('customer_name', function ($query, $keyword) {
            //       if (! empty($keyword)) {
            //           $query->whereHas('user', function ($q) use ($keyword) {
            //               $q->where('first_name', 'like', '%'.$keyword.'%');
            //               $q->orWhere('last_name', 'like', '%'.$keyword.'%');
            //           });
            //       }
            //   })
            ->editColumn('updated_at', function ($data) {
                if ($data->updated_at === null) {
                    return 'N/A'; // O cualquier otro valor predeterminado adecuado
                }
            
                $diff = Carbon::now()->diffInHours($data->updated_at);
                if ($diff < 25) {
                    return $data->updated_at->diffForHumans();
                } else {
                    return $data->updated_at->isoFormat('llll');
                }
            })
              ->orderColumns(['id'], '-:column $1')
              ->rawColumns(['action', 'check'])
              ->toJson();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.ebooks.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validar los datos del formulario
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'author' => 'nullable|string|max:255',
            'url' => 'required|url',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Manejar la carga de la imagen
        if ($request->hasFile('cover_image')) {
            $image = $request->file('cover_image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/ebooks'), $imageName);
        } else {
            $imageName = '';
        }

        $ebook = EBook::create([
            'title' => $request->title,
            'description' => $request->description,
            'author' => $request->author,
            'url' => $request->url,
            'cover_image' => $imageName,
        ]);

        return redirect()->route('backend.e-books.index')->with('success', __('EBooks.EBook has been created successfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($e_book)
    {
        return "aa";
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $ebook = EBook::find($id);
        return view('backend.ebooks.edit', compact('ebook'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'author' => 'nullable|string|max:255',
            'url' => 'required|url',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $ebook = EBook::find($id);

        // Manejar la carga de la imagen
        if ($request->hasFile('cover_image')) {
            $image = $request->file('cover_image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/ebooks'), $imageName);
        } else {
            $imageName = '';
        }

        $ebook->update([
            'title' => $request->title,
            'description' => $request->description,
            'author' => $request->author,
            'url' => $request->url,
            'cover_image' => $imageName,
        ]);

        return redirect()->route('backend.e-books.index')->with('success', __('EBooks.EBook has been updateds successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $ebook = EBook::find($id);
        $ebook->delete();
        return redirect()->route('backend.e-books.index')->with('success', __('EBooks.EBook has been deleted successfully'));

    }
}
