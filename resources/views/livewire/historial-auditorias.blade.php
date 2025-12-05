<div>
    <!-- Modal Historial -->
    @if($mostrarModal)
        <div class="fixed inset-0 bg-black bg-opacity-60 z-50 flex items-center justify-center p-4 backdrop-blur-sm" x-data
            x-show="true" x-transition>
            <div
                class="bg-white rounded-3xl shadow-2xl max-w-7xl w-full max-h-[90vh] overflow-hidden transform transition-all">
                <!-- Header con gradiente mejorado -->
                <div
                    class="bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500 p-8 text-white relative overflow-hidden">
                    <div class="absolute inset-0 bg-black opacity-10"></div>
                    <div class="relative z-10 flex justify-between items-center">
                        <div>
                            <h2 class="text-3xl font-black mb-2 flex items-center gap-3">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                Historial de Auditorías
                            </h2>
                            <p class="text-indigo-100 text-sm">Revisa todas tus auditorías completadas</p>
                        </div>
                        <button wire:click="cerrarModal"
                            class="text-white hover:bg-white/20 p-3 rounded-xl transition transform hover:scale-110 hover:rotate-90">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-8 overflow-y-auto max-h-[calc(90vh-160px)] bg-gray-50">
                    @if($auditoriaSeleccionada)
                        <!-- Vista de Detalle -->
                        <button wire:click="$set('auditoriaSeleccionada', null)"
                            class="mb-6 text-indigo-600 hover:text-indigo-800 flex items-center gap-2 font-semibold text-lg transition transform hover:translate-x-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                                </path>
                            </svg>
                            Volver a la lista
                        </button>

                        <div class="space-y-4">
                            @foreach($detallesAuditoria as $paso => $preguntas)
                                <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-200">
                                    <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center gap-2">
                                        <span class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-md text-sm">
                                            {{ $paso }}
                                        </span>
                                    </h3>
                                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                        @foreach($preguntas as $item)
                                            <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                                                <p
                                                    class="text-[10px] font-bold text-gray-500 uppercase tracking-wide mb-1 leading-tight">
                                                    {{ $item->pregunta }}
                                                </p>
                                                <p class="font-bold text-gray-800 text-sm">{{ $item->respuesta }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <!-- Lista de Auditorías -->
                        @if(count($auditorias) > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                                @foreach($auditorias as $auditoria)
                                    <div wire:click="verDetalle({{ $auditoria['id'] }})"
                                        class="bg-white rounded-2xl shadow-md hover:shadow-2xl transition-all duration-300 cursor-pointer transform hover:-translate-y-2 border-2 border-transparent hover:border-indigo-500 overflow-hidden group">
                                        <div class="bg-gradient-to-br from-indigo-500 to-purple-600 p-6 text-white">
                                            <div class="flex justify-between items-start mb-4">
                                                <span class="text-xs font-bold px-3 py-1.5 rounded-full bg-white/20 backdrop-blur-sm">
                                                    {{ ucfirst($auditoria['estado']) }}
                                                </span>
                                                <svg class="w-6 h-6 opacity-50 group-hover:opacity-100 transition" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 5l7 7-7 7"></path>
                                                </svg>
                                            </div>
                                            <p class="text-xs opacity-80 mb-1">ID CIC</p>
                                            <p class="font-mono font-bold text-lg truncate">{{ $auditoria['id_cic'] }}</p>
                                        </div>
                                        <div class="p-6">
                                            <div class="flex items-center gap-2 text-gray-500 text-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                    </path>
                                                </svg>
                                                {{ $auditoria['fecha'] }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-20">
                                <div
                                    class="bg-gradient-to-br from-gray-100 to-gray-200 rounded-full p-8 w-32 h-32 mx-auto mb-6 flex items-center justify-center">
                                    <svg class="w-20 h-20 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                </div>
                                <h3 class="text-2xl font-bold text-gray-700 mb-2">No hay auditorías registradas</h3>
                                <p class="text-gray-500">Comienza creando tu primera auditoría</p>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>