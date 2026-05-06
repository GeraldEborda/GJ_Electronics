<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>GJ Electronics - @yield('title', 'Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#2563eb',
                        ink: '#0f172a',
                        muted: '#64748b',
                        shell: '#f8fafc',
                    },
                    boxShadow: {
                        panel: '0 20px 45px rgba(15, 23, 42, 0.08)',
                    },
                },
            },
        };
    </script>
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --surface: rgba(255, 255, 255, 0.92);
            --surface-strong: #ffffff;
            --shell-top: #f7fbff;
            --shell-bottom: #eef4fb;
            --line: #dbe4ef;
            --line-soft: #e8eef5;
            --text-strong: #0f172a;
            --text: #334155;
            --muted: #64748b;
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background:
                radial-gradient(circle at top left, rgba(37, 99, 235, 0.12), transparent 28%),
                linear-gradient(180deg, var(--shell-top) 0%, var(--shell-bottom) 100%);
            color: var(--text-strong);
        }
        .card {
            background: var(--surface);
            border: 1px solid var(--line-soft);
            border-radius: 28px;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.06);
            backdrop-filter: blur(14px);
        }
        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            width: 100%;
            padding: 0.95rem 1rem;
            border-radius: 20px;
            color: var(--muted);
            font-size: 0.98rem;
            font-weight: 600;
            text-decoration: none;
            transition: background-color 180ms ease, color 180ms ease, transform 180ms ease;
        }
        .nav-link:hover {
            background: #f8fafc;
            color: var(--text-strong);
            transform: translateX(2px);
        }
        .nav-link.active {
            background: linear-gradient(180deg, #eff6ff 0%, #e8f0ff 100%);
            color: var(--primary);
            box-shadow: inset 0 0 0 1px rgba(37, 99, 235, 0.06);
        }
        .btn-primary,
        .btn-secondary,
        .btn-success {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.55rem;
            min-height: 48px;
            padding: 0.8rem 1.2rem;
            border-radius: 18px;
            border: 1px solid transparent;
            font-size: 0.92rem;
            font-weight: 700;
            text-decoration: none;
            transition: transform 180ms ease, box-shadow 180ms ease, background-color 180ms ease, border-color 180ms ease, color 180ms ease;
        }
        .btn-primary {
            background: linear-gradient(180deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: #fff;
            box-shadow: 0 14px 28px rgba(37, 99, 235, 0.22);
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 18px 34px rgba(37, 99, 235, 0.26);
        }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.95);
            border-color: var(--line);
            color: var(--text);
        }
        .btn-secondary:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
        }
        .btn-success {
            background: linear-gradient(180deg, #16a34a 0%, #15803d 100%);
            color: #fff;
            box-shadow: 0 14px 28px rgba(22, 163, 74, 0.18);
        }
        .form-label {
            display: block;
            margin-bottom: 0.65rem;
            color: var(--text);
            font-size: 0.92rem;
            font-weight: 700;
        }
        .form-input {
            width: 100%;
            min-height: 50px;
            border: 1px solid var(--line);
            border-radius: 18px;
            background: #f8fafc;
            padding: 0.85rem 1rem;
            color: var(--text);
            font-size: 0.95rem;
            outline: none;
            transition: border-color 180ms ease, box-shadow 180ms ease, background-color 180ms ease;
        }
        .form-input:focus {
            border-color: #93c5fd;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.12);
        }
        textarea.form-input {
            min-height: 120px;
            resize: vertical;
        }
        .badge-green,
        .badge-yellow,
        .badge-red,
        .badge-blue {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 0.42rem 0.8rem;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.01em;
        }
        .badge-green { background: #dcfce7; color: #15803d; }
        .badge-yellow { background: #fef3c7; color: #b45309; }
        .badge-red { background: #fee2e2; color: #b91c1c; }
        .badge-blue { background: #dbeafe; color: #1d4ed8; }
        .page-title {
            font-size: clamp(2.25rem, 4vw, 3.25rem);
            line-height: 1;
            font-weight: 900;
            letter-spacing: -0.03em;
            color: var(--text-strong);
        }
        .page-subtitle {
            margin-top: 0.55rem;
            font-size: 1.05rem;
            color: var(--muted);
        }
        .stat-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1.4rem 1.5rem;
        }
        .table-wrap {
            overflow-x: auto;
        }
        .data-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 0.92rem;
        }
        .data-table thead th {
            background: #f8fafc;
            color: var(--muted);
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }
        .data-table th,
        .data-table td {
            padding: 1rem 1.25rem;
            text-align: left;
            border-bottom: 1px solid #eef2f7;
            vertical-align: middle;
        }
        .data-table tbody tr:hover {
            background: rgba(248, 250, 252, 0.85);
        }
        .section-head {
            padding: 1.35rem 1.5rem;
            border-bottom: 1px solid #eef2f7;
        }
        .soft-panel {
            background: linear-gradient(180deg, rgba(248, 250, 252, 0.95), rgba(255, 255, 255, 0.98));
            border: 1px solid #e7edf5;
            border-radius: 24px;
        }
        @media (max-width: 1024px) {
            aside { width: 240px !important; }
            .ml-72 { margin-left: 240px !important; }
        }
    </style>
</head>
<body class="min-h-screen bg-shell text-ink">
    <div class="flex min-h-screen">
        <aside class="fixed inset-y-0 left-0 z-30 flex w-72 flex-col border-r border-slate-200 bg-white">
            <div class="border-b border-slate-200 px-6 py-5">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-primary text-white shadow-lg shadow-blue-200">
                        <i class="fa-solid fa-shield-halved text-lg"></i>
                    </div>
                    <div>
                        <div class="text-[15px] font-bold text-slate-900">GJ Electronics</div>
                        <div class="text-xs text-slate-500">Sales and Inventory Management System</div>
                    </div>
                </div>
            </div>

            <div class="border-b border-slate-200 px-4 py-5">
                <div class="flex items-center gap-4 rounded-3xl bg-slate-50 px-3 py-4">
                    <div class="flex h-11 w-11 items-center justify-center rounded-full bg-blue-100 text-lg font-bold text-primary">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="text-base font-semibold text-slate-800">{{ auth()->user()->name }}</div>
                        <div class="text-sm text-slate-500 capitalize">{{ auth()->user()->role }}</div>
                    </div>
                </div>
            </div>

            <nav class="flex-1 space-y-2 px-4 py-5">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-table-cells-large w-5 text-center"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('inventory.index') }}" class="nav-link {{ request()->routeIs('inventory.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-cubes w-5 text-center"></i>
                    <span>Inventory</span>
                </a>
                <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-box-open w-5 text-center"></i>
                    <span>Products</span>
                </a>
                <a href="{{ route('suppliers.index') }}" class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-truck-field w-5 text-center"></i>
                    <span>Suppliers</span>
                </a>
                <a href="{{ route('customers.index') }}" class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-user-group w-5 text-center"></i>
                    <span>Customers</span>
                </a>
                <a href="{{ route('stock-in.index') }}" class="nav-link {{ request()->routeIs('stock-in.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-arrow-trend-up w-5 text-center"></i>
                    <span>Stock In</span>
                </a>
                <a href="{{ route('sales.index') }}" class="nav-link {{ request()->routeIs('sales.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-cart-shopping w-5 text-center"></i>
                    <span>Sales</span>
                </a>
                <a href="{{ route('payments.index') }}" class="nav-link {{ request()->routeIs('payments.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-money-check-dollar w-5 text-center"></i>
                    <span>Payments</span>
                </a>
                <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-column w-5 text-center"></i>
                    <span>Reports</span>
                </a>
            </nav>

            <div class="border-t border-slate-200 px-4 py-5">
                <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="mb-3 flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
                        <div class="flex items-center gap-3 text-slate-600">
                            <i class="fa-regular fa-sun"></i>
                            <span class="text-sm font-semibold">Dark Mode</span>
                        </div>
                        <button type="button" class="h-6 w-11 rounded-full bg-slate-200 p-1">
                            <span class="block h-4 w-4 rounded-full bg-white shadow"></span>
                        </button>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="nav-link w-full text-red-500 hover:bg-red-50 hover:text-red-600">
                            <i class="fa-solid fa-right-from-bracket w-5 text-center"></i>
                            <span>Log out</span>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <div class="ml-72 flex min-h-screen flex-1 flex-col">
            <header class="sticky top-0 z-20 border-b border-slate-200 bg-white/85 backdrop-blur">
                <div class="flex items-center justify-end gap-3 px-8 py-4">
                    <span class="text-sm font-semibold text-slate-600">Welcome, {{ auth()->user()->name }}!</span>
                    <span class="text-slate-300">&bull;</span>
                    <span class="text-sm text-slate-500">{{ now()->format('l, F j, Y') }}</span>
                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-blue-100 text-sm font-bold text-primary">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                </div>
            </header>

            @if (session('success'))
                <div class="mx-8 mt-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                    <i class="fa-solid fa-circle-check mr-2"></i>{{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mx-8 mt-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                    <i class="fa-solid fa-circle-exclamation mr-2"></i>{{ session('error') }}
                </div>
            @endif

            <main class="flex-1 px-8 py-8">
                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        window.setupSearchCombobox = function ({ inputId, hiddenId, options, requiredMessage }) {
            const input = document.getElementById(inputId);
            const hidden = document.getElementById(hiddenId);

            if (!input || !hidden) {
                return;
            }

            const normalizedOptions = (options || []).map(option => ({
                id: String(option.id),
                label: String(option.label),
            }));

            const findMatch = (value) => {
                const normalizedValue = String(value || '').trim().toLowerCase();

                return normalizedOptions.find(option => option.label.toLowerCase() === normalizedValue);
            };

            const syncHiddenValue = () => {
                const match = findMatch(input.value);
                hidden.value = match ? match.id : '';
                input.setCustomValidity('');
            };

            input.addEventListener('input', syncHiddenValue);
            input.addEventListener('change', syncHiddenValue);

            input.form?.addEventListener('submit', (event) => {
                syncHiddenValue();

                if (!hidden.value) {
                    input.setCustomValidity(requiredMessage || 'Please select a valid option from the list.');
                    input.reportValidity();
                    event.preventDefault();
                    return;
                }

                input.setCustomValidity('');
            });
        };
    </script>
    @stack('scripts')
</body>
</html>
