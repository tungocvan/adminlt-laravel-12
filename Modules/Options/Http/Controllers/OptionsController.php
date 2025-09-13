<?php

namespace Modules\Options\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Option;
class OptionsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
         $this->middleware('permission:options-list|options-create|options-edit|options-delete', ['only' => ['index','show']]);
         $this->middleware('permission:options-create', ['only' => ['create','store']]);
         $this->middleware('permission:options-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:options-delete', ['only' => ['destroy']]);
    }
    public function index(Request $request)
    {
        $query = Option::query();

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('option_name', 'like', '%' . $request->search . '%')
                ->orWhere('option_value', 'like', '%' . $request->search . '%');
            });
        }

        $sort = $request->get('sort', 'asc');
        $query->orderBy('option_id', $sort);

        $options = $query->paginate(20)->appends($request->all());

        return view('Options::index', compact('options', 'sort'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('Options::create');
    }

    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'option_name'  => 'required|string|max:191|unique:wp_options,option_name',
            'option_value' => 'required',
            'autoload'     => 'in:yes,no',
        ]);

        Option::set_option(
            $request->option_name,
            $request->option_value,
            $request->autoload ?? 'yes'
        );

        return redirect()->route('options.index')->with('success', 'Option created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Option $option)
    {
        return view('Options::edit', compact('option'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Option $option)
    {
        $request->validate([
            'option_name'  => 'required|string|max:191|unique:wp_options,option_name,' . $option->option_id . ',option_id',
            'option_value' => 'required',
            'autoload'     => 'in:yes,no',
        ]);

        Option::update_option(
            $request->option_name,
            $request->option_value,
            $request->autoload
        );

        return redirect()->route('options.index')->with('success', 'Option updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Option $option)
    {
        Option::delete_option($option->option_name);
        return redirect()->route('options.index')->with('success', 'Option deleted successfully.');
    }
    public function bulkAction(Request $request)
    {

        $ids = $request->input('ids', []);
        $action = $request->input('action');
        
        if (empty($ids)) {
            return redirect()->back()->with('error', 'Bạn chưa chọn bản ghi nào.');
        }

        switch ($action) {
            case 'delete':
                Option::whereIn('option_id', $ids)->delete();
                return redirect()->back()->with('success', 'Đã xóa thành công.');

            case 'autoload_yes':
                Option::whereIn('option_id', $ids)->update(['autoload' => 'yes']);
                return redirect()->back()->with('success', 'Đã cập nhật autoload = yes.');

            case 'autoload_no':
                Option::whereIn('option_id', $ids)->update(['autoload' => 'no']);
                return redirect()->back()->with('success', 'Đã cập nhật autoload = no.');
        }

        return redirect()->back()->with('error', 'Hành động không hợp lệ.');
    }
}
