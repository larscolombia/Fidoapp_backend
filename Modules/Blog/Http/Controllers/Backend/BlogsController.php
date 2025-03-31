<?php

namespace Modules\Blog\Http\Controllers\Backend;

use Carbon\Carbon;
use App\Authorizable;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Modules\Blog\Models\Blog;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Jobs\CalculateVideoBlogDuration;

class BlogsController extends Controller
{
    // use Authorizable;

    public function __construct()
    {
        // Page Title
        $this->module_title = 'blog.title';

        // module name
        $this->module_name = 'blogs';

        // directory path of the module
        $this->module_path = 'blog::backend';

        view()->share([
            'module_title' => $this->module_title,
            'module_icon' => 'fa-regular fa-sun',
            'module_name' => $this->module_name,
            'module_path' => $this->module_path,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $module_action = 'List';
        $create_title = "Blog";

        return view('blog::backend.blogs.index_datatable',  compact('module_action', 'create_title'));
    }
    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);

        $actionType = $request->action_type;

        $message = __('messages.bulk_update');

        switch ($actionType) {
            case 'change-status':
                $branches = Blog::whereIn('id', $ids)->update(['status' => $request->status]);
                $message = __('messages.bulk_blogs_update');
                break;

            case 'delete':
                Blog::whereIn('id', $ids)->delete();
                $message = __('messages.bulk_blogs_delete');
                break;

            default:
                return response()->json(['status' => false, 'message' => __('branch.invalid_action')]);
                break;
        }

        return response()->json(['status' => true, 'message' => __('messages.bulk_update')]);
    }

    /**
     * Select Options for Select 2 Request/ Response.
     *
     * @return Response
     */
    public function index_list(Request $request)
    {
        $term = trim($request->q);

        if (empty($term)) {
            return response()->json([]);
        }

        $query_data = Blog::where('name', 'LIKE', "%$term%")->orWhere('slug', 'LIKE', "%$term%")->limit(7)->get();

        $data = [];

        foreach ($query_data as $row) {
            $data[] = [
                'id' => $row->id,
                'text' => $row->name . ' (Slug: ' . $row->slug . ')',
            ];
        }
        return response()->json($data);
    }

    public function index_data()
    {
        $query = Blog::query();

        return Datatables::of($query)
            ->addColumn('check', function ($row) {
                return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-' . $row->id . '"  name="datatable_ids[]" value="' . $row->id . '" onclick="dataTableRowCheck(' . $row->id . ')">';
            })
            ->addColumn('action', function ($data) {
                return view('blog::backend.blogs.action_column', compact('data'));
            })
            ->editColumn('image', function ($data) {
                return "<img src='" . $data->blog_image . "'class='avatar avatar-40 img-fluid rounded-pill'>";
            })
            ->editColumn('date', function ($data) {
                // Asegúrate de que $data->created_at sea un objeto Carbon
                $date = Carbon::parse($data->created_at);
                $diff = Carbon::now()->diffInHours($date);

                if ($diff < 25) {
                    return $date->diffForHumans();
                } else {
                    return $date->isoFormat('llll');
                }
            })
            ->orderColumn('date', function ($query, $order) {
                $query->orderBy('created_at', $order);
            }, 1)
            ->filterColumn('date', function ($query, $keyword) {
                if (! empty($keyword)) {
                    $query->where('created_at', 'like', '%' . $keyword . '%');
                }
            })
            ->editColumn('status', function ($row) {
                $checked = '';
                if ($row->status) {
                    $checked = 'checked="checked"';
                }

                return '
                            <div class="form-check form-switch ">
                                <input type="checkbox" data-url="' . route('backend.blogs.update_status', $row->id) . '" data-token="' . csrf_token() . '" class="switch-status-change form-check-input"  id="datatable-row-' . $row->id . '"  name="status" value="' . $row->id . '" ' . $checked . '>
                            </div>
                           ';
            })
            // ->editColumn('updated_at', function ($data) {
            //     $module_name = $this->module_name;

            //     $diff = Carbon::now()->diffInHours($data->updated_at);

            //     if ($diff < 25) {
            //         return $data->updated_at->diffForHumans();
            //     } else {
            //         return $data->updated_at->isoFormat('llll');
            //     }
            // })
            ->rawColumns(['action', 'status', 'check', 'image'])
            ->orderColumns(['id'], '-:column $1')
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $module_action = 'Create';
        return view('blog::backend.blogs.create', compact('module_action'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $data = $request->except('blog_image');
        if ($request->hasFile('video')) {
            if (!file_exists(public_path('videos/blog'))) {
                mkdir(public_path('videos/blog'), 0755, true);
            }
            $video = $request->file('video');
            $videoName = time() . '_' . uniqid() . '.' . $video->getClientOriginalExtension();
            // Mover el video a la carpeta correspondiente
            $video->move(public_path('videos/blog'), $videoName);

            // Generar la URL del video
            $videoUrl = url('videos/blog/' . $videoName);
            //agregar a data
            $data['url'] = $videoUrl;
            $data['video'] = $videoName;
        }
        $query = Blog::create($data);
        //llamada al job para generar la duracion automatica
        dispatch(new CalculateVideoBlogDuration($query));
        storeMediaFile($query, $request->file('blog_image'), 'blog_image');
        $message = __('messages.create_form', ['form' => __($this->module_title)]);

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $module_action = 'Show';

        $data = Blog::findOrFail($id);

        return view('blog::backend.blogs.show', compact('module_action', "$data"));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $data = Blog::findOrFail($id);

        $data['blog_image'] = $data->blog_image;
        $data['tags'] = explode(',', $data->tags);
        return response()->json(['data' => $data, 'status' => true]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $query = Blog::findOrFail($id);

        $data = $request->except('event_image');
        if ($request->hasFile('video')) {
            if (!file_exists(public_path('videos/blog'))) {
                mkdir(public_path('videos/blog'), 0755, true);
            }
            // Verificar si ya existe un video asociado
            if ($query->video) {
                // Eliminar el video anterior
                $oldVideoPath = public_path('videos/blog/' . $query->video);
                if (file_exists($oldVideoPath)) {
                    unlink($oldVideoPath);
                }
            }
            $video = $request->file('video');
            $videoName = time() . '_' . uniqid() . '.' . $video->getClientOriginalExtension();
            // Mover el video a la carpeta correspondiente
            $video->move(public_path('videos/blog'), $videoName);

            // Generar la URL del video
            $videoUrl = url('videos/blog/' . $videoName);
            //agregar a data
            $data['url'] = $videoUrl;
            $data['video'] = $videoName;
        }
        $query->update($data);

        storeMediaFile($query, $request->file('event_image'), 'event_image');
        $message = __('messages.update_form', ['form' => __($this->module_title)]);
        //llamada al job para generar la duracion automatica
        dispatch(new CalculateVideoBlogDuration($query));
        return response()->json(['message' => $message, 'status' => true], 200);
    }
    public function destroy($id)
    {
        $data = Blog::findOrFail($id);

        $data->delete();

        $message = __('messages.delete_form', ['form' => __($this->module_title)]);

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    /**
     * List of trashed ertries
     * works if the softdelete is enabled.
     *
     * @return Response
     */
    public function trashed()
    {
        $module_name_singular = Str::singular($this->module_name);

        $module_action = 'Trash List';

        $data = Blog::with('user')->onlyTrashed()->orderBy('deleted_at', 'desc')->paginate();

        return view('pet::backend.blogs.trash', compact('data', 'module_name_singular', 'module_action'));
    }
    public function restore($id)
    {
        $data = Blog::withTrashed()->find($id);
        $data->restore();

        $message = Str::singular($this->module_title) . ' Data Restoreded Successfully';

        return redirect('app/blogs');
    }
    public function update_status(Request $request, Blog $id)
    {
        $id->update(['status' => $request->status]);

        return response()->json(['status' => true, 'message' => __('branch.status_update')]);
    }
}
