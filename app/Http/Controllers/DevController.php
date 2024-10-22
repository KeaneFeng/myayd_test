<?php

namespace App\Http\Controllers;

use App\Models\SqlLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SqlExport;
use Illuminate\Support\Facades\Auth;


class DevController extends Controller
{
    public function index()
    {
        // 获取当前登录用户
        $user = Auth::user();

        // 渲染 dev 页面并传递用户信息
        return view('dev.index', compact('user'));
    }


// 执行 SQL 并返回结果
    public function executeSQL(Request $request)
    {
        try {
            // 验证 SQL 语句并定义自定义错误消息
            $validated = $this->validateSQL($request);

            $sql = $validated['sql'];
            // 对 SQL 语句进行转义
            $escapedSql = $this->escapeString($sql);
            $results = DB::select($sql);
            $filteredResults = $this->filterSensitiveFields($results);

            // 记录日志（成功情况）
            $this->logSql($escapedSql);

            // 分页处理
            $perPage = 5;
            $page = $request->input('page', 1);
            $pagedResults = array_slice($filteredResults, ($page - 1) * $perPage, $perPage);

            return response()->json([
                'data' => $pagedResults,
                'total' => count($filteredResults),
                'current_page' => $page,
            ]);
        } catch (\Exception $e) {
            // 记录日志（错误情况）
            !empty($request->sql)??$this->logSql($this->escapeString($request->sql), $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    private function validateSQL(Request $request): array
    {
        return $request->validate([
            'sql' => ['required', 'string', 'regex:/^select/i'],
        ], [
            'sql.required' => 'SQL is empty.',
            'sql.string' => 'SQL must be of string.。',
            'sql.regex' => 'Only SELECT is allowed',
        ]);
    }

    // 转义 SQL 字符串
    private function escapeString(string $sql): string
    {
        return addslashes($sql);
    }

    // 过滤敏感字段
    private function filterSensitiveFields(array $results): array
    {
        $sensitiveFields = ['password', 'email_verified_at', 'remember_token'];

        return array_map(function ($row) use ($sensitiveFields) {
            return collect($row)->except($sensitiveFields)->toArray();
        }, $results);
    }

    // 日志记录
    private function logSql(string $sql, string $error = null): void
    {
        SqlLog::create([
            'user_id' => Auth::id(),
            'nickname' => $this->escapeString(Auth::user()->nickname),
            'sql' => $sql,
            'error' => $error,
        ]);
    }

    public function export($format, Request $request)
    {
        try {
            // 验证 SQL 语句并定义自定义错误消息
            $validated = $this->validateSQL($request);

            $sql = $validated['sql'];

            // 执行 SQL 语句
            $results = DB::select($sql);
            $filteredResults = $this->filterSensitiveFields($results);


            if ($format === 'excel') {
                $data = $filteredResults;//导出数据
                $fileName = 'sql_results_' . date('Y-m-d_H-i-s') . '.xls';
                $head = array_keys($data[0]);//第一行的列标题
                return Excel::download(new SqlExport($data, $head , 'Sheet1'), $fileName);
            } elseif ($format === 'json') {
                $fileName = 'sql_results_' . date('Y-m-d_H-i-s') . '.json';
                // 将结果编码为 JSON 并下载
                $json = json_encode($filteredResults, JSON_PRETTY_PRINT);
                return response($json, 200)
                    ->header('Content-Type', 'application/json')
                    ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
            }

            return response()->json(['error' => 'Export format not supported.'], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
