<div class="w-full">
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Mis Hábitos</h2>
        
        @if($habits->isEmpty())
            <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-8 text-center">
                <p class="text-gray-500 dark:text-gray-400">No tienes hábitos creados. ¡Crea tu primer hábito para comenzar!</p>
            </div>
        @else
            <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                Racha
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                Hábito
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                Estado
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                Completar
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                        @foreach($habits as $habit)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                {{-- Círculo de racha con tooltip --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div 
                                        class="relative inline-block"
                                        x-data="{ showTooltip: false }"
                                        @mouseenter="showTooltip = true"
                                        @mouseleave="showTooltip = false"
                                    >
                                        <div 
                                            class="w-10 h-10 rounded-full border-2 border-violet-500"
                                            style="background-color: rgba(139, 92, 246, {{ $habit->getStreakOpacity() }})"
                                        ></div>
                                        
                                        {{-- Tooltip --}}
                                        <div 
                                            x-show="showTooltip"
                                            x-transition:enter="transition ease-out duration-200"
                                            x-transition:enter-start="opacity-0 transform scale-95"
                                            x-transition:enter-end="opacity-100 transform scale-100"
                                            x-transition:leave="transition ease-in duration-150"
                                            x-transition:leave-start="opacity-100 transform scale-100"
                                            x-transition:leave-end="opacity-0 transform scale-95"
                                            class="absolute z-10 bottom-full left-1/2 -translate-x-1/2 mb-2 px-3 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-lg whitespace-nowrap"
                                            style="display: none;"
                                        >
                                            Racha: {{ $habit->getCurrentStreak() }} día{{ $habit->getCurrentStreak() !== 1 ? 's' : '' }}
                                            <div class="absolute top-full left-1/2 -translate-x-1/2 -mt-px">
                                                <div class="border-4 border-transparent border-t-gray-900"></div>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Nombre del hábito --}}
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $habit->name }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        {{ $habit->frequency->getLabel() }}
                                        @if($habit->total_completions > 0)
                                            • {{ $habit->total_completions }} completado{{ $habit->total_completions !== 1 ? 's' : '' }}
                                        @endif
                                    </div>
                                </td>

                                {{-- Estado --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($habit->isCompletedToday())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                            Completado
                                        </span>
                                    @elseif($habit->isDueToday())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                            </svg>
                                            Por completar
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                            No programado
                                        </span>
                                    @endif
                                </td>

                                {{-- Checkbox para completar --}}
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($habit->isDueToday() && !$habit->isCompletedToday())
                                        <button
                                            wire:click="completeHabit({{ $habit->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="completeHabit({{ $habit->id }})"
                                            class="inline-flex items-center justify-center w-6 h-6 rounded border-2 border-gray-300 dark:border-gray-600 hover:border-green-500 dark:hover:border-green-500 hover:bg-green-50 dark:hover:bg-green-900/20 transition-colors cursor-pointer"
                                            title="Marcar como completado"
                                        >
                                            <svg 
                                                class="w-4 h-4 text-transparent hover:text-green-600 dark:hover:text-green-400 transition-colors"
                                                wire:loading.class="animate-pulse"
                                                wire:target="completeHabit({{ $habit->id }})"
                                                fill="currentColor" 
                                                viewBox="0 0 20 20"
                                            >
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                        </button>
                                    @elseif($habit->isCompletedToday())
                                        <div class="inline-flex items-center justify-center w-6 h-6 rounded bg-green-500 dark:bg-green-600">
                                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    @else
                                        <div class="inline-flex items-center justify-center w-6 h-6 rounded border-2 border-gray-200 dark:border-gray-700 opacity-50">
                                            <span class="text-xs text-gray-400">–</span>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
