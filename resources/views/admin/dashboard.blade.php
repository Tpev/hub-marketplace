<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- KPI Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
            @php
                $kpiCards = [
                    ['label' => 'Total Users', 'value' => $kpis['total_users'], 'color' => 'text-gray-800', 'icon' => '👥'],
                    ['label' => 'Buyers', 'value' => $kpis['buyers'], 'color' => 'text-blue-600', 'icon' => '🛒'],
                    ['label' => 'Sellers', 'value' => $kpis['sellers'], 'color' => 'text-green-600', 'icon' => '💼'],
                    ['label' => 'Both (Buyer + Seller)', 'value' => $kpis['both'], 'color' => 'text-purple-600', 'icon' => '🔁'],
					['label' => 'Total Devices Listed', 'value' => $kpis['total_devices'], 'color' => 'text-sky-700', 'icon' => '📦'],
                    ['label' => 'Devices (New)', 'value' => $kpis['new_devices'], 'color' => 'text-green-600', 'icon' => '✨'],
                    ['label' => 'Devices (Used)', 'value' => $kpis['used_devices'], 'color' => 'text-yellow-600', 'icon' => '🔧'],
                    ['label' => 'Devices (Refurbished)', 'value' => $kpis['refurb_devices'], 'color' => 'text-indigo-600', 'icon' => '♻️'],
                    ['label' => 'Contact Requests', 'value' => $kpis['total_inquiries'], 'color' => 'text-red-600', 'icon' => '📨'],
                    ['label' => 'Total Listing Value', 'value' => '$' . number_format($kpis['total_value'], 2), 'color' => 'text-gray-900', 'icon' => '💰'],
                    ['label' => 'Average Price', 'value' => '$' . number_format($kpis['average_price'], 2), 'color' => 'text-blue-800', 'icon' => '📊'],
                ];
            @endphp

            @foreach($kpiCards as $card)
                <div class="bg-white p-6 rounded-xl shadow flex items-center justify-between hover:shadow-md transition">
                    <div>
                        <p class="text-sm text-gray-500">{{ $card['label'] }}</p>
                        <p class="text-2xl font-bold {{ $card['color'] }}">{{ $card['value'] }}</p>
                    </div>
                    <div class="text-3xl opacity-30">
                        {{ $card['icon'] }}
                    </div>
                </div>
            @endforeach
        </div>

        <!-- User Table (Already exists below) -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-2xl font-bold mb-6">All Users</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Intent</th>
								<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Professional?</th>
								<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Business Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subscribed</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">License Tier</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items Listed</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact Requests</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registered</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($users as $user)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $user->id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $user->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $user->email }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $user->intent }}</td>
									<td class="px-6 py-4 whitespace-nowrap">
										@if($user->is_professional)
											<span class="text-green-600 font-semibold">Yes</span>
										@else
											<span class="text-gray-500">No</span>
										@endif
									</td>
									<td class="px-6 py-4 whitespace-nowrap">
										{{ $user->business_type ?? '—' }}
									</td>

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($user->is_subscribed)
                                            <span class="text-green-600 font-semibold">Yes</span>
                                        @else
                                            <span class="text-red-600 font-semibold">No</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($user->license_tier)
                                            <span class="capitalize">{{ $user->license_tier }}</span>
                                        @else
                                            <span class="text-gray-400 italic">—</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $user->medical_devices_count }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $user->contact_requests_count }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $user->created_at->format('Y-m-d') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
		
		
		<div class="mt-12">
    <h3 class="text-xl font-bold mb-4">Buyer Inquiries</h3>
    @forelse($inquiries as $inquiry)
        <div class="border p-4 mb-4 rounded bg-white">
            <p class="text-sm text-gray-500">{{ $inquiry->created_at->format('Y-m-d H:i') }}</p>
            <p class="font-semibold">{{ $inquiry->name ?? 'Guest' }} - {{ $inquiry->email ?? 'No email' }}</p>
            <p class="mt-2 text-gray-700">{{ $inquiry->message }}</p>
        </div>
    @empty
        <p class="text-gray-500">No inquiries yet.</p>
    @endforelse
</div>

    </div>
</x-app-layout>
