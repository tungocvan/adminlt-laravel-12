<?php

namespace Modules\User\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Helpers\TnvUserHelper;
use App\Services\UserService;
//use App\Services\UserMailService;
use App\Jobs\SendUserMailJob;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // 🔹 Lấy params từ query string
        $params = $request->only(['id', 'email', 'is_admin', 'referral_code', 'status', 'search', 'birthdate', 'birthdate_from', 'birthdate_to', 'created_at', 'created_at_from', 'created_at_to', 'updated_at', 'updated_at_from', 'updated_at_to', 'sort_by', 'sort_order', 'type', 'per_page']);

        // 🔹 Keyword fields cho search
        $params['keyword_fields'] = ['name', 'email'];

        // 🔹 Gọi service
        $users = UserService::getUsers($params);

        // 🔹 Chuẩn hóa response JSON
        return response()->json([
            'success' => true,
            'data' => $users,
            'meta' => method_exists($users, 'toArray') ? $users->toArray() : null,
        ]);
    }

    public function show(Request $request, $identifier)
    {
        $params = [];

        // Nếu là số => ID, ngược lại => email
        $params[is_numeric($identifier) ? 'id' : 'email'] = $identifier;

        $users = TnvUserHelper::getUsers($params);

        if ($users->isEmpty()) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'User not found',
                ],
                404,
            );
        }

        $user = $users->first();

        // ✅ Lấy toàn bộ options
        // Nếu model có hàm getAllOptions()
        if (method_exists($user, 'getAllOptions')) {
            $options = $user->getAllOptions();
        }
        // Nếu bạn lưu trong $user->options (json)
        else {
            $options = $user->options ?? [];
        }

        // Gắn options vào data trả ra API
        $data = $user->toArray();
        $data['options'] = $options;

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();
        $result = TnvUserHelper::updateUser((int) $id, $data);
        return response()->json($result);
    }
    public function updateApp(Request $request, $id)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:6',
            'profile' => 'nullable|array',
            'shipping_info' => 'nullable|array',
        ]);

        $user = User::find($id);
        if (!$user) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Người dùng không tồn tại.',
                ],
                404,
            );
        }

        // Cập nhật thông tin cơ bản
        if (!is_null($request->name)) {
            $user->name = $request->name;
        }

        if (!is_null($request->password)) {
            $user->password = Hash::make($request->password);
        }

        // Chỉ save nếu có thay đổi core fields
        if ($user->isDirty(['name', 'password'])) {
            $user->save();
        }

        // Cập nhật option
        if (!is_null($request->profile)) {
            $user->setOption('profile', $request->profile);
        }

        if (!is_null($request->shipping_info)) {
            $user->setOption('shipping_info', $request->shipping_info);
        }

        return [
            'status' => 'success',
            'data' => $user,
            'message' => 'Cập nhật người dùng thành công!',
        ];
    }

    public function destroy($id)
    {
        $result = TnvUserHelper::deleteUsers($id);
        return response()->json($result);
    }

    public function destroyMultiple(Request $request)
    {
        $ids = $request->input('ids', []); // ví dụ: [1, 2, 3]

        $result = TnvUserHelper::deleteUsers($ids);
        return response()->json($result);
    }

    public function showOption(Request $request, $id)
    {
        // Tìm người dùng theo ID
        $user = User::find($id);

        if (!$user) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Người dùng không tồn tại.',
                ],
                404,
            );
        }

        // Lấy danh sách options từ request
        $options = $request->input('options');

        $result = [];

        // Nếu không truyền options, lấy tất cả option từ getAllOptions
        if (empty($options)) {
            if (method_exists($user, 'getAllOptions')) {
                $allOptions = $user->getAllOptions();
                // Lọc bỏ giá trị null
                $result = array_filter($allOptions, fn($v) => !is_null($v));
            }
        } else {
            // Nếu truyền options, kiểm tra phải là mảng
            if (!is_array($options)) {
                return response()->json(
                    [
                        'status' => 'error',
                        'message' => 'Options phải là một mảng.',
                    ],
                    400,
                );
            }

            // Lọc và lấy option hợp lệ
            $result = collect($options)
                ->mapWithKeys(function ($optionKey) use ($user) {
                    if (method_exists($user, 'getOption') && !is_null($value = $user->getOption($optionKey))) {
                        return [$optionKey => $value];
                    }
                    return []; // Bỏ option không hợp lệ
                })
                ->all();
        }

        return response()->json([
            'status' => 'success',
            'data' => $result,
        ]);
    }

    public function send(Request $request)
    {
        $user = $request->user();

        // Validate request
        $data = $request->validate([
            'to' => 'required',
            'subject' => 'required|string|max:255',
            'body' => 'nullable|string',
            'html' => 'nullable|string',
            'cc' => 'nullable|array',
            'bcc' => 'nullable|array',
            'attachments' => 'nullable|array', // ['https://example.com/file.pdf', ...]
        ]);

        // Decode HTML nếu có
        if (isset($data['html'])) {
            $data['html'] = html_entity_decode($data['html'], ENT_QUOTES, 'UTF-8');
        }

        // Xử lý attachments URL → base64
        $attachments = $this->processAttachments($data['attachments'] ?? []);

        // Dispatch mail job
        SendUserMailJob::dispatch(
            $user,
            $data['to'],
            $data['subject'],
            $data['body'] ?? null,
            $data['html'] ?? null,
            $data['cc'] ?? [],
            $data['bcc'] ?? [],
            $attachments
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Mail đã được đưa vào queue, worker sẽ gửi.',
            'html' => $data['html'] ?? null,
            'attachments_count' => count($attachments),
        ]);
    }

    private function processAttachments(array $attachments): array
    {
        $result = [];

        foreach ($attachments as $url) {
            try {
                $response = Http::timeout(10)->get($url); // timeout 10s
                if ($response->ok()) {
                    $content = $response->body();

                    // Detect mime type từ extension
                    $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);
                    $mime = match(strtolower($extension)) {
                        'pdf' => 'application/pdf',
                        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'xls' => 'application/vnd.ms-excel',
                        'doc' => 'application/msword',
                        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'jpg', 'jpeg' => 'image/jpeg',
                        'png' => 'image/png',
                        default => 'application/octet-stream',
                    };

                    $result[] = [
                        'name' => basename(parse_url($url, PHP_URL_PATH)) ?: Str::random(8),
                        'content' => base64_encode($content),
                        'mime' => $mime,
                    ];
                }
            } catch (\Exception $e) {
                \Log::error("Failed to download attachment: $url. Error: ".$e->getMessage());
                // Optional: bạn có thể thêm record lỗi để trả về client
            }
        }

        return $result;
    }
}
