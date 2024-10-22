<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>User login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-screen bg-gray-50 flex items-center justify-center">

<div class="w-full max-w-md bg-white rounded-lg shadow-lg p-8">
    <h2 class="text-2xl font-bold text-center text-gray-700">User login</h2>
    <p class="mt-2 text-sm text-center text-gray-600">Please enter your account and password.</p>

    <form id="login-form" class="mt-6">
        @csrf
        <div class="mb-4">
            <label for="account" class="block text-sm font-medium text-gray-700">account</label>
            <input
                type="text"
                id="account"
                name="account"
                class="mt-1 block w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                required
                @keyup.enter="submitForm"
            />
        </div>

        <div class="mb-4">
            <label for="password" class="block text-sm font-medium text-gray-700">password</label>
            <input
                type="password"
                id="password"
                name="password"
                class="mt-1 block w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                required
                @keyup.enter="submitForm"
            />
        </div>

        <div class="flex items-center justify-between mb-4">
            <label class="flex items-center">
                <input type="checkbox" name="remember" class="h-4 w-4 text-indigo-600 border-gray-300 rounded" />
                <span class="ml-2 text-sm text-gray-600">Remember me</span>
            </label>
            <a href="#" class="text-sm text-indigo-600 hover:text-indigo-500">Forgot Password？</a>
        </div>

        <button
            type="submit"
            class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-300"
        >
            Sign in
        </button>
    </form>

{{--    <p class="mt-4 text-sm text-center text-gray-600">--}}
{{--        还没有账号？<a href="#" class="text-indigo-600 hover:text-indigo-500">注册</a>--}}
{{--    </p>--}}
</div>

<!-- Toast 弹窗 -->
<div id="toast" class="fixed bottom-4 right-4 hidden">
    <div class="bg-red-500 text-white px-4 py-2 rounded-lg shadow-lg flex items-center space-x-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-12.728 12.728M12 12l-5.364 5.364M18.364 18.364L5.636 5.636"></path>
        </svg>
        <span id="toast-message"></span>
    </div>
</div>

<script>
    document.getElementById('login-form').addEventListener('submit', handleFormSubmit);

    function submitForm() {
        document.getElementById('login-form').dispatchEvent(new Event('submit', { cancelable: true }));
    }

    async function handleFormSubmit(e) {
        e.preventDefault();

        const form = e.target;
        const account = form.account.value;
        const password = form.password.value;
        const remember = form.remember.checked;

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({ account, password, remember }),
            });

            const data = await response.json();

            if (response.ok) {
                window.location.href = '/dev';
            } else {
                showToast(data.error || 'Login failed, please try again.');
            }
        } catch (error) {
            showToast('Network error, please try again later...');
        }
    }

    function showToast(message) {
        const toast = document.getElementById('toast');
        const toastMessage = document.getElementById('toast-message');
        toastMessage.textContent = message;
        toast.classList.remove('hidden');

        setTimeout(() => {
            toast.classList.add('hidden');
        }, 3000);  // 3秒后自动隐藏
    }
</script>

</body>
</html>
