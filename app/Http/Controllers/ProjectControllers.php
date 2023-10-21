<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;

class ProjectControllers extends Controller
{
    public function create()
    {
        return view('projects.create');
    }

    public function index()
    {
        $userID = Auth::user()->userID;
        $projects =
            Project::where('projectCreator', $userID)->get();

        return view('project', ['projects' => $projects]);
    }

    public function show($id)
    {
        // Lấy dự án dựa trên ID
        $project = Project::find($id);

        // Kiểm tra xem dự án có tồn tại hay không
        if (!$project) {
            // Xử lý khi không tìm thấy dự án
            return redirect()->route('projects.index')->with('error', 'Dự án không tồn tại');
        }

        return view('project-detail', ['project' => $project]);
    }

    public function destroy($id)
    {
        // Find the project by ID
        $project = Project::find($id);

        if (!$project) {
            return redirect()->route('projects.index')->with('error', 'Project not found');
        }

        // Kiểm tra xem người dùng đã đăng nhập có quyền xóa dự án (bạn có thể thực hiện logic kiểm tra quyền của riêng bạn ở đây)

        // Delete the project
        $project->delete();

        return redirect()->route('projects.index')->with('success', 'Project has been deleted');
    }

    public function store(Request $request)
    {
        // Xác thực và xử lý dữ liệu từ form
        $request->validate([
            'project_name' => 'required|string',
            'description' => 'required|string',
        ]);

        // Lưu dữ liệu vào cơ sở dữ liệu
        $user = Auth::user();
        $project = new Project;
        $project->projectName = $request->input('project_name');
        $project->description = $request->input('description');
        $project->projectCreator = $user->userID; // Bạn cũng có thể lấy user_id từ session hoặc Auth::user()
        $project->isOpen = true;

        $project->save();

        return redirect()->route('projects.index')->with('success', 'Dự án đã được tạo thành công');
    }
}
