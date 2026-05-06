<div class="mx-auto max-w-4xl">
    <div class="mb-8">
        <h1 class="page-title">{{ $title }}</h1>
        <p class="page-subtitle">{{ $subtitle }}</p>
    </div>

    @if($errors->any())
        <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card p-8">
        <form method="POST" action="{{ $action }}" class="space-y-6">
            @csrf
            @if($method !== 'POST')
                @method($method)
            @endif
            <div>
                <label class="form-label">Payment Method Name</label>
                <input type="text" name="payment_method_name" value="{{ old('payment_method_name', $paymentMethod?->payment_method_name) }}" class="form-input" required>
            </div>
            <div class="flex justify-end gap-3 pt-4">
                <a href="{{ route('payment-methods.index') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary"><i class="fa-solid fa-floppy-disk"></i><span>{{ $method === 'POST' ? 'Add Method' : 'Save Changes' }}</span></button>
            </div>
        </form>
    </div>
</div>
