<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GJ Electronics - Sign In</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-[radial-gradient(circle_at_top,_#f8fbff,_#eef4ff_45%,_#f8fafc_100%)]">
    <div class="mx-auto grid min-h-screen max-w-7xl items-center gap-16 px-6 py-10 lg:grid-cols-2">
        <section class="space-y-8">
            <div class="flex items-center gap-4">
                <div class="flex h-16 w-16 items-center justify-center rounded-3xl bg-blue-600 text-white shadow-2xl shadow-blue-200">
                    <i class="fa-solid fa-shield-halved text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-5xl font-black tracking-tight text-slate-900">GJ Electronics</h1>
                    <p class="text-2xl text-slate-500">Sales & Inventory Management System</p>
                </div>
            </div>

            <p class="max-w-2xl text-2xl leading-relaxed text-slate-600">
                Comprehensive sales and inventory management for Fire Safety Equipment, CCTV Systems, and IT Solutions.
            </p>

            <div class="overflow-hidden rounded-[2rem] border border-blue-100 bg-white shadow-[0_30px_80px_rgba(37,99,235,0.18)]">
                <div class="relative h-[430px] bg-[linear-gradient(180deg,rgba(15,23,42,0.08),rgba(37,99,235,0.45)),url('https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=1200&q=80')] bg-cover bg-center">
                    <div class="absolute inset-0 bg-gradient-to-t from-blue-950/70 via-transparent to-transparent"></div>
                    <div class="absolute bottom-8 left-8 right-8 text-white">
                        <p class="text-3xl font-semibold">Manage Sales and Inventory Easily</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="mx-auto w-full max-w-xl rounded-[2rem] border border-slate-200 bg-white px-10 py-12 shadow-[0_35px_85px_rgba(15,23,42,0.14)]">
            <h2 class="text-5xl font-black text-slate-900">Welcome Back</h2>
            <p class="mt-3 text-2xl text-slate-500">Please sign in to your account</p>

            @if($errors->any())
                <div class="mt-8 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                    <i class="fa-solid fa-circle-exclamation mr-2"></i>{{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="mt-14 space-y-7">
                @csrf

                <div>
                    <label class="mb-3 block text-lg font-semibold text-slate-800">Username</label>
                    <div class="relative">
                        <i class="fa-regular fa-user absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input
                            type="text"
                            name="username"
                            value="{{ old('username') }}"
                            placeholder="Enter your username"
                            class="w-full rounded-2xl border border-slate-200 bg-slate-50 py-4 pl-12 pr-4 text-lg text-slate-700 outline-none transition focus:border-blue-400 focus:bg-white focus:ring-4 focus:ring-blue-100"
                            required
                            autofocus
                        >
                    </div>
                </div>

                <div>
                    <label class="mb-3 block text-lg font-semibold text-slate-800">Password</label>
                    <div class="relative">
                        <i class="fa-regular fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input
                            type="password"
                            name="password"
                            placeholder="Enter your password"
                            class="w-full rounded-2xl border border-slate-200 bg-slate-50 py-4 pl-12 pr-4 text-lg text-slate-700 outline-none transition focus:border-blue-400 focus:bg-white focus:ring-4 focus:ring-blue-100"
                            required
                        >
                    </div>
                </div>

                <button type="submit" class="w-full rounded-2xl bg-blue-600 py-4 text-2xl font-semibold text-white shadow-xl shadow-blue-200 transition hover:bg-blue-700">
                    Sign In
                </button>
            </form>

            <div class="mt-14 border-t border-slate-200 pt-8 text-center">
                <a href="#" class="text-xl text-slate-500 underline underline-offset-4 transition hover:text-blue-600">Forgot Password?</a>
            </div>
        </section>
    </div>
</body>
</html>
