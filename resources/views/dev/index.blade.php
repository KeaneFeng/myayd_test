<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SQL 执行页面</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-100 min-h-screen">

<!-- 顶部导航栏 -->
<nav class="bg-white shadow-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <h1 class="text-xl font-semibold text-indigo-600">SQL 执行工具</h1>
            <div class="flex items-center space-x-6">
                <p class="text-gray-700">
                    用户：<strong>{{ $user->nickname }}</strong> |
                    权限组：<strong>{{ $user->role }}</strong>
                </p>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-red-500">退出</button>
                </form>
            </div>
        </div>
    </div>
</nav>

<!-- SQL 表单与结果 -->
<div class="w-full max-w-4xl bg-white rounded-lg shadow-md p-8 mt-8 mx-auto">
    <form id="sql-form" class="mb-6">
        @csrf
        <textarea
            id="sql"
            name="sql"
            rows="5"
            class="w-full border rounded-lg p-4 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            placeholder="输入 SELECT 语句 例：SELECT * FROM `myayd`.`sql_logs` LIMIT 0,1000;"></textarea>

        <div class="mt-4 flex justify-between">
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg">执行</button>
            <div>
                <a href="#" id="export-excel" class="text-indigo-500 mr-4">导出 Excel</a>
                <a href="#" id="export-json" class="text-indigo-500">导出 JSON</a>
            </div>
        </div>
    </form>

    <div id="error" class="text-red-500 mt-2"></div>
    <div id="results" class="mt-6 overflow-x-auto"></div>
    <div id="pagination" class="mt-4 flex justify-center"></div>
</div>

<script>
    const resultsDiv = document.getElementById('results');
    const paginationDiv = document.getElementById('pagination');
    const errorDiv = document.getElementById('error');
    let currentPage = 1;

    async function fetchResults(page = 1) {
        const sql = document.getElementById('sql').value;
        const response = await fetch(`{{ route('dev.execute') }}?page=${page}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: JSON.stringify({ sql }),
        });

        const result = await response.json();
        if (response.ok) {
            errorDiv.textContent = ''; // 清除错误信息
            renderResults(result.data);
            renderPagination(result.total, result.current_page);
        } else {
            errorDiv.textContent = result.error; // 显示错误信息
        }
    }

    function renderResults(data) {
        resultsDiv.innerHTML = `
            <table class="w-full border-collapse border border-gray-300">
                <thead>
                    <tr>
                        ${Object.keys(data[0] || {}).map(
            key => `<th class="border px-4 py-2 bg-gray-100">${key}</th>`
        ).join('')}
                    </tr>
                </thead>
                <tbody>
                    ${data.map(row => `
                        <tr>
                            ${Object.values(row).map(val => `
                                <td class="border px-4 py-2 max-w-[45px] truncate">
                                    <span title="${val}">${val}</span>
                                </td>
                            `).join('')}
                        </tr>
                    `).join('')}
                </tbody>
            </table>`;
    }

    function renderPagination(total, currentPage) {
        const totalPages = Math.ceil(total / 5);
        paginationDiv.innerHTML = '';

        for (let i = 1; i <= totalPages; i++) {
            const button = document.createElement('button');
            button.textContent = i;
            button.className = `mx-1 px-3 py-1 ${i === currentPage ? 'bg-indigo-600 text-white' : 'bg-gray-200'}`;
            button.onclick = () => fetchResults(i);
            paginationDiv.appendChild(button);
        }
    }

    document.getElementById('sql-form').addEventListener('submit', function (e) {
        e.preventDefault();
        fetchResults();
    });

    document.getElementById('export-excel').addEventListener('click', function () {
        window.location.href = '{{ route('dev.export', 'excel') }}?sql=' + encodeURIComponent(document.getElementById('sql').value);
    });

    document.getElementById('export-json').addEventListener('click', function () {
        window.location.href = '{{ route('dev.export', 'json') }}?sql=' + encodeURIComponent(document.getElementById('sql').value);
    });
</script>

</body>
</html>
