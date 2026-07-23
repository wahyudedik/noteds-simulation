<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <svg class="inline w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Detail Payout #{{ $payout->id }}
            </h2>
            <a href="{{ route('admin.payouts.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Kembali</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            {{-- Success Message --}}
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                {{-- Payout Info --}}
                <div class="md:col-span-2 space-y-6">
                    <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Payout</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">ID Payout</p>
                                <p class="text-sm font-medium text-gray-900">#{{ $payout->id }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Status</p>
                                <span class="inline-flex px-2.5 py-0.5 text-xs font-medium rounded-full {{ $payout->status_badge_class }}">
                                    @switch($payout->status)
                                        @case('pending') Menunggu @break
                                        @case('processing') Diproses @break
                                        @case('approved') Disetujui @break
                                        @case('paid') Dibayar @break
                                        @case('rejected') Ditolak @break
                                        @default {{ ucfirst($payout->status) }}
                                    @endswitch
                                </span>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Jumlah</p>
                                <p class="text-xl font-bold text-gray-900">{{ $payout->formatted_amount }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Mata Uang</p>
                                <p class="text-sm font-medium text-gray-900">{{ $payout->currency }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Metode Pembayaran</p>
                                <p class="text-sm font-medium text-gray-900">
                                    @switch($payout->method)
                                        @case('bank_transfer') Bank Transfer @break
                                        @case('paypal') PayPal @break
                                        @case('midtrans') Midtrans @break
                                        @default {{ ucfirst($payout->method) }}
                                    @endswitch
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Tanggal Diajukan</p>
                                <p class="text-sm font-medium text-gray-900">{{ $payout->created_at->format('d M Y H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Payment Details --}}
                    <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Detail Pembayaran</h3>
                        @if($payout->method === 'bank_transfer')
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-500">Nama Bank</p>
                                    <p class="text-sm font-medium text-gray-900">{{ $payout->bank_name ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Nomor Rekening</p>
                                    <p class="text-sm font-medium text-gray-900 font-mono">{{ $payout->account_number ?? '-' }}</p>
                                </div>
                                <div class="col-span-2">
                                    <p class="text-sm text-gray-500">Atas Nama</p>
                                    <p class="text-sm font-medium text-gray-900">{{ $payout->account_holder ?? '-' }}</p>
                                </div>
                            </div>
                        @elseif($payout->method === 'paypal')
                            <div>
                                <p class="text-sm text-gray-500">PayPal Email</p>
                                <p class="text-sm font-medium text-gray-900">{{ $payout->paypal_email ?? '-' }}</p>
                            </div>
                        @else
                            <p class="text-sm text-gray-500">Metode: {{ ucfirst($payout->method) }}</p>
                        @endif
                    </div>

                    {{-- Review Info --}}
                    @if($payout->reviewed_by)
                        <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Info Review</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-500">Direview Oleh</p>
                                    <p class="text-sm font-medium text-gray-900">{{ $payout->reviewer->name ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Waktu Review</p>
                                    <p class="text-sm font-medium text-gray-900">{{ $payout->reviewed_at?->format('d M Y H:i') ?? '-' }}</p>
                                </div>
                                @if($payout->review_notes)
                                    <div class="col-span-2">
                                        <p class="text-sm text-gray-500">Catatan Review</p>
                                        <p class="text-sm text-gray-700 mt-1">{{ $payout->review_notes }}</p>
                                    </div>
                                @endif
                                @if($payout->paid_at)
                                    <div>
                                        <p class="text-sm text-gray-500">Waktu Dibayar</p>
                                        <p class="text-sm font-medium text-gray-900">{{ $payout->paid_at->format('d M Y H:i') }}</p>
                                    </div>
                                @endif
                                @if($payout->proof_path)
                                    <div class="col-span-2">
                                        <p class="text-sm text-gray-500">Bukti Pembayaran</p>
                                        <a href="{{ Storage::url($payout->proof_path) }}" target="_blank" class="text-blue-600 hover:underline text-sm">Lihat Bukti</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Sidebar: Creator Info & Actions --}}
                <div class="space-y-6">
                    {{-- Creator Info --}}
                    <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
                        <h3 class="text-sm font-semibold text-gray-900 mb-3">Creator</h3>
                        <div class="flex items-center gap-3 mb-3">
                            @if($payout->user->avatar)
                                <img src="{{ Storage::url($payout->user->avatar) }}" alt="{{ $payout->user->name }}" class="w-10 h-10 rounded-full object-cover">
                            @else
                                <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-sm font-semibold">{{ strtoupper(substr($payout->user->name, 0, 1)) }}</div>
                            @endif
                            <div>
                                <p class="font-medium text-gray-900 text-sm">{{ $payout->user->name }}</p>
                                <p class="text-xs text-gray-400">{{ $payout->user->email }}</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.users.show', $payout->user) }}" class="text-blue-600 hover:underline text-xs">Lihat Profil →</a>
                    </div>

                    {{-- Actions --}}
                    <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
                        <h3 class="text-sm font-semibold text-gray-900 mb-3">Aksi</h3>
                        <div class="space-y-3">
                            {{-- Approve --}}
                            @if($payout->canBeApproved())
                                <form method="POST" action="{{ route('admin.payouts.approve', $payout) }}">
                                    @csrf
                                    <div class="mb-2">
                                        <textarea name="review_notes" rows="2" placeholder="Catatan review (opsional)" class="w-full text-sm border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                                    </div>
                                    <button type="submit" class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition">
                                        ✓ Setujui
                                    </button>
                                </form>
                            @endif

                            {{-- Mark as Paid --}}
                            @if($payout->canBePaid())
                                <form method="POST" action="{{ route('admin.payouts.mark-paid', $payout) }}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-2">
                                        <textarea name="review_notes" rows="2" placeholder="Catatan pembayaran (opsional)" class="w-full text-sm border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                                    </div>
                                    <div class="mb-2">
                                        <label class="block text-xs text-gray-500 mb-1">Bukti Pembayaran</label>
                                        <input type="file" name="proof" accept=".jpg,.jpeg,.png,.pdf" class="w-full text-sm border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <button type="submit" class="w-full px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition">
                                        💰 Tandai Dibayar
                                    </button>
                                </form>
                            @endif

                            {{-- Reject --}}
                            @if(in_array($payout->status, ['pending', 'processing']))
                                <form method="POST" action="{{ route('admin.payouts.reject', $payout) }}">
                                    @csrf
                                    <div class="mb-2">
                                        <textarea name="review_notes" rows="2" placeholder="Alasan penolakan" class="w-full text-sm border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500"></textarea>
                                    </div>
                                    <button type="submit" class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition"
                                        onclick="return confirm('Yakin ingin menolak payout ini?')">
                                        ✗ Tolak
                                    </button>
                                </form>
                            @endif

                            @if(! $payout->canBeApproved() && ! $payout->canBePaid() && ! in_array($payout->status, ['pending', 'processing']))
                                <p class="text-sm text-gray-500 text-center">Tidak ada aksi tersedia untuk status ini.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
