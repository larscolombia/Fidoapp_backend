<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\EBook;
use App\Models\EBookUser;
use App\Models\BookRating;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StoreEBookRequest;

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
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'number_of_pages' => 'nullable|integer|min:1',
            'language' => 'nullable|string|max:255',
            'price' => 'nullable|numeric',
        ]);

        // Manejar la carga de la imagen
        // Manejar la carga de la imagen
        if ($request->hasFile('cover_image')) {
            $image = $request->file('cover_image');
            $imageName = time() . '.avif';
            $pathRegister = 'images/ebooks/' . $imageName;
            $imagePath = public_path('images/ebooks/' . $imageName);

            // Convertir la imagen a .avif usando el helper
            $convertedPath = convertToWebP($image, $imagePath);

            if (!$convertedPath) {
                return redirect()->back()->withErrors(['cover_image' => 'Error al convertir la imagen'])->withInput();
            }
        } else {
            $imageName = '';
        }

        $ebook = EBook::create([
            'title' => $request->title,
            'description' => $request->description,
            'author' => $request->author,
            'url' => $request->url,
            'cover_image' => $pathRegister,
            'number_of_pages' => $request->number_of_pages,
            'language' => $request->language,
            'price' => $request->price,
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
        $ebook = EBook::findOrFail($e_book);
        return view('backend.ebooks.show', compact('ebook'));
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
            'number_of_pages' => 'nullable|integer|min:1',
            'language' => 'nullable|string|max:255',
            'price' => 'nullable|numeric',
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
            'number_of_pages' => $request->number_of_pages,
            'language' => $request->language,
            'price' => $request->price,
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

    public function get()
    {
        $ebooks = EBook::with('book_ratings')
            ->get()
            ->transform(function ($ebook) {
                $ebook->cover_image = asset($ebook->cover_image);
                return $ebook;
            });
        return response()->json([
            'success' => true,
            'message' => __('messages.ebooks_retrieved_successfully'),
            'data' => $ebooks
        ]);
    }

    public function getById($id)
    {
        $ebook = EBook::with('book_ratings')->find($id);

        if ($ebook) {
            $ebook->cover_image = asset($ebook->cover_image);
            return response()->json([
                'success' => true,
                'message' => __('messages.ebook_retrieved_successfully'),
                'data' => $ebook
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => __('messages.ebook_not_found')
            ], 404);
        }
    }

    public function bookRating(Request $request)
    {
        try {
            $data = $request->validate([
                'e_book_id' => 'required|exists:e_book,id',
                'user_id' => 'required|exists:users,id',
                'review_msg' => 'nullable|string',
                'rating' => 'nullable|numeric|min:1|max:5'
            ]);

            $bookRating = BookRating::create($data);
            return response()->json(['status' => true, 'data' => $bookRating], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getBookRatingByIdEbook(Request $request)
    {
        $data = $request->validate([
            'e_book_id' => 'required|exists:e_book,id',
        ]);

        $bookRating = BookRating::with('user')->where('e_book_id', $data['e_book_id'])->get();

        if ($bookRating->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No hay calificaciones disponibles para este libro.'
            ], 404);
        }

        $results = $bookRating->map(function ($rating) {
            return [
                'id' => $rating->id,
                'rating' => $rating->rating,
                'review_msg' => $rating->review_msg,
                'user_id' => $rating->user->id,
                'user_full_name' => $rating->user->full_name,
                'user_avatar' => asset($rating->user->avatar)
            ];
        });

        return response()->json([
            'status' => true,
            'data' =>  $results
        ], 200);
    }


    public function deleteBookRating($id)
    {
        try {
            $bookRating = BookRating::findOrFail($id);
            $bookRating->delete();
            return response()->json(['status' => true, 'message' => 'book rating deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function buyBook(Request $request)
    {
        try {
            $data = $request->validate([
                'e_book_id' => 'required|exists:e_book,id',
                'user_id' => 'required|exists:users,id',
            ]);

            $eBookUser = EBookUser::create($data);

            return response()->json([
                'status' => true,
                'data' =>  $eBookUser
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getBooksUser(Request $request)
    {
        try {
            $data = $request->validate([
                'user_id' => 'required|exists:users,id',
            ]);

            $eBooksUser = EBookUser::where('user_id', $data['user_id'])->orderByDesc('id')->get();
            $results = $eBooksUser->map(function ($eBookUser) {
                return [
                    'id' =>  $eBookUser->id,
                    'e_book_id' => $eBookUser->e_book->id,
                    'e_book_title' =>  $eBookUser->e_book->title,
                    'e_book_url' =>  $eBookUser->e_book->url,
                    'e_book_author' =>  $eBookUser->e_book->author,
                    'e_book_image' => !empty($eBookUser->e_book->cover_image) ?  $eBookUser->e_book->cover_image : null,
                    'e_book_description' => $eBookUser->e_book->description,
                    'e_book_language' => $eBookUser->e_book->language,
                    'user_id' => $eBookUser->user_id,
                    'user_full_name' => $eBookUser->user->full_name,
                    'user_avatar' =>   asset($eBookUser->user->avatar),
                    'created_at' => $eBookUser->created_at
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $results,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
