<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Subscription Status Widget -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Subscription Status</h3>
                    @if(auth()->user()->hasActiveSubscription())
                        @php $subscription = auth()->user()->activeSubscription(); @endphp
                        <div class="bg-green-50 dark:bg-green-900 p-4 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="font-medium text-green-800 dark:text-green-200">
                                        {{ $subscription->plan->name }}
                                    </h4>
                                    <p class="text-sm text-green-600 dark:text-green-300">
                                        Expires: {{ $subscription->end_date->format('M d, Y') }}
                                    </p>
                                    @if(auth()->user()->isInstructor())
                                        <p class="text-sm text-green-600 dark:text-green-300">
                                            Courses: {{ auth()->user()->courses()->count() }} / {{ $subscription->plan->max_courses == -1 ? 'Unlimited' : $subscription->plan->max_courses }}
                                        </p>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Active
                                    </span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="bg-yellow-50 dark:bg-yellow-900 p-4 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="font-medium text-yellow-800 dark:text-yellow-200">
                                        No Active Subscription
                                    </h4>
                                    <p class="text-sm text-yellow-600 dark:text-yellow-300">
                                        Choose a plan to unlock premium features
                                    </p>
                                </div>
                                <div>
                                    <a href="{{ route('pricing') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                        View Plans
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
