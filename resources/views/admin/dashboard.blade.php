<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>
<!-- KPIs Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-4 rounded shadow">
        <p class="text-sm text-gray-500">Total Users</p>
        <p class="text-2xl font-bold text-gray-800">{{ $kpis['total_users'] }}</p>
    </div>
    <div class="bg-white p-4 rounded shadow">
        <p class="text-sm text-gray-500">Buyers</p>
        <p class="text-xl font-bold text-blue-700">{{ $kpis['buyers'] }}</p>
    </div>
    <div class="bg-white p-4 rounded shadow">
        <p class="text-sm text-gray-500">Sellers</p>
        <p class="text-xl font-bold text-green-700">{{ $kpis['sellers'] }}</p>
    </div>
    <div class="bg-white p-4 rounded shadow">
        <p class="text-sm text-gray-500">Both (Buyer+Seller)</p>
        <p class="text-xl font-bold text-purple-700">{{ $kpis['both'] }}</p>
    </div>
    <div class="bg-white p-4 rounded shadow">
        <p class="text-sm text-gray-500">Devices (New)</p>
        <p class="text-xl font-bold text-green-700">{{ $kpis['new_devices'] }}</p>
    </div>
    <div class="bg-white p-4 rounded shadow">
        <p class="text-sm text-gray-500">Devices (Used)</p>
        <p class="text-xl font-bold text-yellow-700">{{ $kpis['used_devices'] }}</p>
    </div>
    <div class="bg-white p-4 rounded shadow">
        <p class="text-sm text-gray-500">Devices (Refurbished)</p>
        <p class="text-xl font-bold text-indigo-700">{{ $kpis['refurb_devices'] }}</p>
    </div>
    <div class="bg-white p-4 rounded shadow">
        <p class="text-sm text-gray-500">Total Contact Requests</p>
        <p class="text-xl font-bold text-red-700">{{ $kpis['total_inquiries'] }}</p>
    </div>
    <div class="bg-white p-4 rounded shadow">
        <p class="text-sm text-gray-500">Total Value of Listings</p>
        <p class="text-xl font-bold text-gray-800">${{ number_format($kpis['total_value'], 2) }}</p>
    </div>
</div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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
        </div>
    </div>
</x-app-layout>
