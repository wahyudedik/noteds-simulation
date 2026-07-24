<x-studio-layout title="Pengaturan Pembayaran">
    <div class="max-w-2xl mx-auto">

        <div class="bg-white border border-gray-100 rounded-xl shadow-sm">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Pengaturan Pembayaran</h3>
                <p class="text-sm text-gray-500 mb-6">Atur metode pembayaran yang ingin digunakan untuk menerima payout dari revenue iklan.</p>

                <form method="POST" action="{{ route('studio.payment-settings.update') }}">
                    @csrf
                    @method('PUT')

                    {{-- Preferred Method --}}
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Metode Pembayaran</label>
                        <div class="grid grid-cols-3 gap-3">
                            <label class="relative cursor-pointer">
                                <input type="radio" name="preferred_method" value="bank_transfer" {{ $settings->preferred_method === 'bank_transfer' ? 'checked' : '' }} class="peer sr-only">
                                <div class="border-2 border-gray-200 rounded-lg p-4 text-center peer-checked:border-blue-600 peer-checked:bg-blue-50 transition">
                                    <svg class="w-6 h-6 mx-auto mb-1 text-gray-400 peer-checked:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                    <p class="text-sm font-medium text-gray-700">Bank Transfer</p>
                                </div>
                            </label>
                            <label class="relative cursor-pointer">
                                <input type="radio" name="preferred_method" value="paypal" {{ $settings->preferred_method === 'paypal' ? 'checked' : '' }} class="peer sr-only">
                                <div class="border-2 border-gray-200 rounded-lg p-4 text-center peer-checked:border-blue-600 peer-checked:bg-blue-50 transition">
                                    <svg class="w-6 h-6 mx-auto mb-1 text-gray-400 peer-checked:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <p class="text-sm font-medium text-gray-700">PayPal</p>
                                </div>
                            </label>
                            <label class="relative cursor-pointer">
                                <input type="radio" name="preferred_method" value="midtrans" {{ $settings->preferred_method === 'midtrans' ? 'checked' : '' }} class="peer sr-only">
                                <div class="border-2 border-gray-200 rounded-lg p-4 text-center peer-checked:border-blue-600 peer-checked:bg-blue-50 transition">
                                    <svg class="w-6 h-6 mx-auto mb-1 text-gray-400 peer-checked:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                    <p class="text-sm font-medium text-gray-700">Midtrans</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Bank Transfer Fields --}}
                    <div id="bank-fields" class="space-y-4 mb-6 {{ $settings->preferred_method !== 'bank_transfer' ? 'hidden' : '' }}">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Bank</label>
                            <input type="text" name="bank_name" value="{{ $settings->bank_name }}" placeholder="contoh: BCA, Mandiri, BRI"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Rekening</label>
                            <input type="text" name="account_number" value="{{ $settings->account_number }}" placeholder="contoh: 1234567890"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-mono">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Atas Nama Rekening</label>
                            <input type="text" name="account_holder" value="{{ $settings->account_holder }}" placeholder="contoh: John Doe"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                        </div>
                    </div>

                    {{-- PayPal Fields --}}
                    <div id="paypal-fields" class="space-y-4 mb-6 {{ $settings->preferred_method !== 'paypal' ? 'hidden' : '' }}">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">PayPal Email</label>
                            <input type="email" name="paypal_email" value="{{ $settings->paypal_email }}" placeholder="contoh: email@example.com"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                        </div>
                    </div>

                    {{-- Midtrans Info --}}
                    <div id="midtrans-fields" class="mb-6 {{ $settings->preferred_method !== 'midtrans' ? 'hidden' : '' }}">
                        <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg text-sm text-blue-700">
                            Pembayaran Midtrans akan diproses otomatis ke rekening bank yang terdaftar di akun Midtrans Anda.
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                            Simpan Pengaturan
                        </button>
                        <a href="{{ route('studio.payouts') }}" class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">
                            Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const radios = document.querySelectorAll('input[name="preferred_method"]');
            const bankFields = document.getElementById('bank-fields');
            const paypalFields = document.getElementById('paypal-fields');
            const midtransFields = document.getElementById('midtrans-fields');

            radios.forEach(function(radio) {
                radio.addEventListener('change', function() {
                    bankFields.classList.add('hidden');
                    paypalFields.classList.add('hidden');
                    midtransFields.classList.add('hidden');

                    if (this.value === 'bank_transfer') {
                        bankFields.classList.remove('hidden');
                    } else if (this.value === 'paypal') {
                        paypalFields.classList.remove('hidden');
                    } else if (this.value === 'midtrans') {
                        midtransFields.classList.remove('hidden');
                    }
                });
            });
        });
    </script>
    @endpush
</x-studio-layout>
