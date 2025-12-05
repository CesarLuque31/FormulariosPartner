<div class="min-h-screen py-8">

    {{-- Barra de Progreso --}}
    @if($paso > 0 && $paso < 99)
        <div class="max-w-7xl mx-auto mb-8 px-6 lg:px-8">
            <div class="flex justify-between text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wider">
                <span>Inicio</span>
                <span>Progreso: {{ $progress ?? 0 }}%</span>
                <span>Final</span>
            </div>
            <div class="h-3 bg-gray-200 rounded-full overflow-hidden shadow-inner">
                <div class="h-full bg-gradient-to-r from-orange-500 to-orange-600 transition-all duration-500 ease-out shadow-lg"
                    style="width: {{ $progress ?? 0 }}%">
                </div>
            </div>
        </div>
    @endif

    {{-- Contenedor Principal --}}
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        {{-- Header --}}
        <div class="bg-white rounded-2xl shadow-xl p-8 mb-8 border border-gray-100">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-3xl font-black text-gray-900 mb-2">Formulario de Auditoría - Hogar</h1>
                    <div class="flex items-center gap-3">
                        <span
                            class="bg-gradient-to-r from-orange-500 to-orange-600 text-white px-4 py-1.5 rounded-full text-sm font-semibold">
                            @if($paso === 0)
                                Selección de Llamada
                            @elseif($paso === 99)
                                Auditoría Completada
                            @else
                                Paso {{ $paso }} - Datos de Hogar
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Mensajes Flash --}}
        @if (session()->has('success'))
            <div class="bg-green-100 border-l-4 border-orange-500 text-orange-700 p-4 mb-4 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded">
                {{ session('error') }}
            </div>
        @endif

        {{-- Paso 0: Selección Manual o Aleatorio --}}
        @if($paso === 0)
            <div wire:key="paso-0" class="bg-white rounded-lg shadow-lg p-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6 text-center">Selecciona el método de carga
                    (Hogar)</h2>

                <div class="max-w-md mx-auto mb-8">
                    {{-- Opción Aleatoria --}}
                    <div class="border-2 border-gray-200 rounded-lg p-6 hover:border-orange-400 transition">
                        <div class="text-center mb-4">
                            <svg class="w-16 h-16 mx-auto text-orange-500 mb-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                </path>
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-800">Selección Aleatoria</h3>
                            <p class="text-sm text-gray-600 mt-2">Carga una llamada de Hogar al azar</p>
                        </div>

                        {{-- Filtros Opcionales --}}
                        <div class="mb-4 space-y-3">
                            <h4 class="text-sm font-semibold text-orange-700">Filtros Opcionales:</h4>

                            {{-- Filtro Campaña --}}
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Campaña Hogar</label>
                                <select wire:model.live="filtroCampana"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-orange-400 focus:border-transparent">
                                    <option value="">Todas las campañas de Hogar</option>
                                    @foreach($campanasDisponibles as $campana)
                                        <option value="{{ $campana }}">{{ $campana }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Filtro DNI --}}
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">DNI del Empleado</label>
                                <input type="text" wire:model.live="filtroDni"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-orange-400 focus:border-transparent">
                            </div>

                            {{-- Filtro Fecha --}}
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Fecha Específica</label>
                                <input type="date" wire:model.live="filtroFecha" min="2025-10-01" max="{{ date('Y-m-d') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-orange-400 focus:border-transparent">
                                @error('filtroFecha')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Botón limpiar filtros --}}
                            @if($filtroCampana || $filtroDni || $filtroFecha)
                                <button wire:click="limpiarFiltros" type="button"
                                    class="text-xs text-orange-500 hover:text-orange-800 underline">
                                    Limpiar filtros
                                </button>
                            @endif
                        </div>

                        <button wire:click="seleccionarAleatorio"
                            class="w-full bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-700 hover:to-orange-700 text-white px-6 py-3 rounded-lg font-semibold transition transform hover:scale-105">
                            Cargar Aleatorio
                        </button>
                    </div>
                </div>
            </div>
        @endif

        {{-- Paso 1: Tipo de Monitoreo --}}
        @if($paso === 1)
            <div wire:key="paso-1" class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                <div class="mb-8 pb-6 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900">Tipo de Monitoreo</h2>
                    <p class="text-gray-500 text-sm mt-1">Selecciona el tipo de monitoreo realizado</p>
                </div>

                <div class="mb-8 space-y-4">
                    <label
                        class="flex items-center p-4 border-2 border-gray-300 rounded-xl cursor-pointer hover:bg-gray-50 transition"
                        :class="{'border-orange-500 bg-orange-50': $wire.tipoMonitoreo === 'MONITOREO ALEATORIO'}">
                        <input type="radio" wire:model.live="tipoMonitoreo" value="MONITOREO ALEATORIO"
                            class="w-5 h-5 text-orange-600 focus:ring-orange-500 border-gray-300 cursor-pointer">
                        <span class="ml-3 text-lg font-medium text-gray-700">MONITOREO ALEATORIO</span>
                    </label>

                    <label
                        class="flex items-center p-4 border-2 border-gray-300 rounded-xl cursor-pointer hover:bg-gray-50 transition"
                        :class="{'border-orange-500 bg-orange-50': $wire.tipoMonitoreo === 'AUDITORÍA'}">
                        <input type="radio" wire:model.live="tipoMonitoreo" value="AUDITORÍA"
                            class="w-5 h-5 text-orange-600 focus:ring-orange-500 border-gray-300 cursor-pointer">
                        <span class="ml-3 text-lg font-medium text-gray-700">AUDITORÍA</span>
                    </label>

                    @error('tipoMonitoreo')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Botones de Navegación --}}
                <div class="flex justify-end items-center gap-4">
                    <button wire:click="siguiente" wire:loading.attr="disabled"
                        class="bg-gradient-to-r from-orange-600 to-orange-500 hover:from-orange-700 hover:to-orange-700 text-white px-8 py-3 rounded-lg font-semibold transition transform hover:scale-105 flex items-center gap-2">
                        <span wire:loading.remove wire:target="siguiente">SIGUIENTE</span>
                        <span wire:loading wire:target="siguiente">Cargando...</span>
                        <svg wire:loading.remove wire:target="siguiente" class="w-5 h-5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        {{-- Paso 2: Datos de la Llamada + Campos de Hogar --}}
        @if($paso === 2)
            <div wire:key="paso-2" class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                <div class="mb-8 pb-6 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900">Datos de la Llamada - Hogar</h2>
                    <p class="text-gray-500 text-sm mt-1">Información de la llamada seleccionada</p>
                </div>

                {{-- Datos básicos de la llamada (readonly) --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nombre del Analista</label>
                        <input type="text" value="{{ $nombreAnalista }}" readonly
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">ID de Interacción (CIC)</label>
                        <input type="text" value="{{ $idCic }}" readonly
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 font-mono">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Teléfono</label>
                        <input type="text" value="{{ $telefono }}" readonly
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Llamada</label>
                        <input type="text" value="{{ $fechaLlamada }}" readonly
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Monitoreo</label>
                        <input type="text" value="{{ $fechaMonitoreo }}" readonly
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-700 font-semibold">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Duración de Llamada</label>
                        <input type="text" value="{{ $duracionFormato }}" readonly
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 font-mono">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nombre del Asesor</label>
                        <input type="text" value="{{ $nombreAsesor }}" readonly
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Usuario del Asesor</label>
                        <input type="text" value="{{ $usuarioAsesor }}" readonly
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Campaña</label>
                        <input type="text" value="{{ $campana }}" readonly
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700">
                    </div>
                </div>

                {{-- Campos adicionales de Hogar --}}
                <div class="mt-8 pt-8 border-t border-gray-200">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Información Adicional - Hogar</h3>

                    {{-- Seleccione Tipo de Gestión --}}
                    <div class="mb-6">
                        <label class="block text-lg font-medium text-gray-800 mb-4">
                            Seleccione Tipo de Gestión <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <label
                                class="flex items-center p-4 bg-white border-2 rounded-xl cursor-pointer hover:bg-orange-50 hover:border-orange-300 transition-all
                                                                                                            {{ $tipoGestion === 'INBOUND' ? 'border-orange-500 bg-orange-50 ring-1 ring-orange-500' : 'border-gray-200' }}">
                                <input type="radio" wire:model.live="tipoGestion" value="INBOUND"
                                    class="w-5 h-5 text-orange-600 focus:ring-orange-500 border-gray-300">
                                <span class="ml-3 text-base font-medium text-gray-900">INBOUND</span>
                            </label>

                            <label
                                class="flex items-center p-4 bg-white border-2 rounded-xl cursor-pointer hover:bg-orange-50 hover:border-orange-300 transition-all
                                                                                                            {{ $tipoGestion === 'OUTBOUND' ? 'border-orange-500 bg-orange-50 ring-1 ring-orange-500' : 'border-gray-200' }}">
                                <input type="radio" wire:model.live="tipoGestion" value="OUTBOUND"
                                    class="w-5 h-5 text-orange-600 focus:ring-orange-500 border-gray-300">
                                <span class="ml-3 text-base font-medium text-gray-900">OUTBOUND</span>
                            </label>
                        </div>
                        @error('tipoGestion')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- ORIGEN (SOLO APLICA PARA VENTAS DE DIGITAL) ES OPCIONAL --}}
                    <div class="mb-6">
                        <label class="block text-lg font-medium text-gray-800 mb-4">
                            ORIGEN (SOLO APLICA PARA VENTAS DE DIGITAL) ES OPCIONAL
                        </label>
                        <div class="space-y-3">
                            <label
                                class="flex items-center p-4 bg-white border-2 rounded-xl cursor-pointer hover:bg-orange-50 hover:border-orange-300 transition-all
                                                                                                            {{ $origen === 'TIENDA' ? 'border-orange-500 bg-orange-50 ring-1 ring-orange-500' : 'border-gray-200' }}">
                                <input type="radio" wire:model.live="origen" value="TIENDA"
                                    class="w-5 h-5 text-orange-600 focus:ring-orange-500 border-gray-300">
                                <span class="ml-3 text-base font-medium text-gray-900">TIENDA</span>
                            </label>

                            <label
                                class="flex items-center p-4 bg-white border-2 rounded-xl cursor-pointer hover:bg-orange-50 hover:border-orange-300 transition-all
                                                                                                            {{ $origen === 'WHATSAPP' ? 'border-orange-500 bg-orange-50 ring-1 ring-orange-500' : 'border-gray-200' }}">
                                <input type="radio" wire:model.live="origen" value="WHATSAPP"
                                    class="w-5 h-5 text-orange-600 focus:ring-orange-500 border-gray-300">
                                <span class="ml-3 text-base font-medium text-gray-900">WHATSAPP</span>
                            </label>

                            <label
                                class="flex items-center p-4 bg-white border-2 rounded-xl cursor-pointer hover:bg-orange-50 hover:border-orange-300 transition-all
                                                                                                            {{ $origen === 'DIGITAL' ? 'border-orange-500 bg-orange-50 ring-1 ring-orange-500' : 'border-gray-200' }}">
                                <input type="radio" wire:model.live="origen" value="DIGITAL"
                                    class="w-5 h-5 text-orange-600 focus:ring-orange-500 border-gray-300">
                                <span class="ml-3 text-base font-medium text-gray-900">DIGITAL</span>
                            </label>
                        </div>
                    </div>

                    {{-- Seleccione Tipo de Gestión 2 --}}
                    <div class="mb-6">
                        <label class="block text-lg font-medium text-gray-800 mb-4">
                            Seleccione Tipo de Gestión <span class="text-red-500">*</span>
                        </label>
                        <div class="space-y-3">
                            <label
                                class="flex items-center p-4 bg-white border-2 rounded-xl cursor-pointer hover:bg-orange-50 hover:border-orange-300 transition-all
                                                                                                            {{ $tipoGestion2 === 'Crosseling' ? 'border-orange-500 bg-orange-50 ring-1 ring-orange-500' : 'border-gray-200' }}">
                                <input type="radio" wire:model.live="tipoGestion2" value="Crosseling"
                                    class="w-5 h-5 text-orange-600 focus:ring-orange-500 border-gray-300">
                                <span class="ml-3 text-base font-medium text-gray-900">Crosseling</span>
                            </label>

                            <label
                                class="flex items-center p-4 bg-white border-2 rounded-xl cursor-pointer hover:bg-orange-50 hover:border-orange-300 transition-all
                                                                                                            {{ $tipoGestion2 === 'Venta Nueva' ? 'border-orange-500 bg-orange-50 ring-1 ring-orange-500' : 'border-gray-200' }}">
                                <input type="radio" wire:model.live="tipoGestion2" value="Venta Nueva"
                                    class="w-5 h-5 text-orange-600 focus:ring-orange-500 border-gray-300">
                                <span class="ml-3 text-base font-medium text-gray-900">Venta Nueva</span>
                            </label>

                            <label
                                class="flex items-center p-4 bg-white border-2 rounded-xl cursor-pointer hover:bg-orange-50 hover:border-orange-300 transition-all
                                                                                                            {{ $tipoGestion2 === 'Up selling' ? 'border-orange-500 bg-orange-50 ring-1 ring-orange-500' : 'border-gray-200' }}">
                                <input type="radio" wire:model.live="tipoGestion2" value="Up selling"
                                    class="w-5 h-5 text-orange-600 focus:ring-orange-500 border-gray-300">
                                <span class="ml-3 text-base font-medium text-gray-900">Up selling</span>
                            </label>

                            <label
                                class="flex items-center p-4 bg-white border-2 rounded-xl cursor-pointer hover:bg-orange-50 hover:border-orange-300 transition-all
                                                                                                            {{ $tipoGestion2 === 'Sin Cobertura' ? 'border-orange-500 bg-orange-50 ring-1 ring-orange-500' : 'border-gray-200' }}">
                                <input type="radio" wire:model.live="tipoGestion2" value="Sin Cobertura"
                                    class="w-5 h-5 text-orange-600 focus:ring-orange-500 border-gray-300">
                                <span class="ml-3 text-base font-medium text-gray-900">Sin Cobertura</span>
                            </label>
                        </div>
                        @error('tipoGestion2')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Producto Ofertado --}}
                    <div class="mb-6">
                        <label class="block text-lg font-medium text-gray-800 mb-4">
                            Producto Ofertado <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <label
                                class="flex items-center p-4 bg-white border-2 rounded-xl cursor-pointer hover:bg-orange-50 hover:border-orange-300 transition-all
                                                                                                            {{ $productoOfertado === 'HFC' ? 'border-orange-500 bg-orange-50 ring-1 ring-orange-500' : 'border-gray-200' }}">
                                <input type="radio" wire:model.live="productoOfertado" value="HFC"
                                    class="w-5 h-5 text-orange-600 focus:ring-orange-500 border-gray-300">
                                <span class="ml-3 text-base font-medium text-gray-900">HFC</span>
                            </label>

                            <label
                                class="flex items-center p-4 bg-white border-2 rounded-xl cursor-pointer hover:bg-orange-50 hover:border-orange-300 transition-all
                                                                                                            {{ $productoOfertado === 'GPON' ? 'border-orange-500 bg-orange-50 ring-1 ring-orange-500' : 'border-gray-200' }}">
                                <input type="radio" wire:model.live="productoOfertado" value="GPON"
                                    class="w-5 h-5 text-orange-600 focus:ring-orange-500 border-gray-300">
                                <span class="ml-3 text-base font-medium text-gray-900">GPON</span>
                            </label>

                            <label
                                class="flex items-center p-4 bg-white border-2 rounded-xl cursor-pointer hover:bg-orange-50 hover:border-orange-300 transition-all
                                                                                                            {{ $productoOfertado === 'DTH' ? 'border-orange-500 bg-orange-50 ring-1 ring-orange-500' : 'border-gray-200' }}">
                                <input type="radio" wire:model.live="productoOfertado" value="DTH"
                                    class="w-5 h-5 text-orange-600 focus:ring-orange-500 border-gray-300">
                                <span class="ml-3 text-base font-medium text-gray-900">DTH</span>
                            </label>

                            <label
                                class="flex items-center p-4 bg-white border-2 rounded-xl cursor-pointer hover:bg-orange-50 hover:border-orange-300 transition-all
                                                                                                            {{ $productoOfertado === 'WTTX' ? 'border-orange-500 bg-orange-50 ring-1 ring-orange-500' : 'border-gray-200' }}">
                                <input type="radio" wire:model.live="productoOfertado" value="WTTX"
                                    class="w-5 h-5 text-orange-600 focus:ring-orange-500 border-gray-300">
                                <span class="ml-3 text-base font-medium text-gray-900">WTTX</span>
                            </label>
                        </div>
                        @error('productoOfertado')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- PRODUCTO OFRECIDO (DETALLAR QUE PRODUCTO OFRECIO EL ASESOR) --}}
                    <div class="mb-6">
                        <label class="block text-lg font-medium text-gray-800 mb-4">
                            PRODUCTO OFRECIDO (DETALLAR QUE PRODUCTO OFRECIO EL ASESOR) <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model.live="productoOfrecidoDetalle"
                            placeholder="Escribe los detalles del producto ofrecido..."
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl text-base focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all">
                        @error('productoOfrecidoDetalle')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Botones de Navegación --}}
                <div class="mt-8 flex justify-between items-center gap-4">
                    <button wire:click="retroceder" type="button"
                        class="bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-lg font-semibold transition transform hover:scale-105 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                        </svg>
                        ATRÁS
                    </button>

                    <button wire:click="siguiente" wire:loading.attr="disabled"
                        class="bg-gradient-to-r from-orange-600 to-orange-500 hover:from-orange-700 hover:to-orange-700 text-white px-8 py-3 rounded-lg font-semibold transition transform hover:scale-105 flex items-center gap-2">
                        <span wire:loading.remove wire:target="siguiente">SIGUIENTE</span>
                        <span wire:loading wire:target="siguiente">Cargando...</span>
                        <svg wire:loading.remove wire:target="siguiente" class="w-5 h-5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        {{-- Paso 3: PENC - ASESOR: PROTOCOLOS // BUENAS PRACTICAS --}}
        @if($paso === 3)
            <div wire:key="paso-3" class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                <div class="mb-8 pb-6 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900">PENC - ASESOR: PROTOCOLOS // BUENAS PRACTICAS</h2>
                    <p class="text-gray-500 text-sm mt-1">Marca si cumple / no cumple / no aplica para cada ítem</p>
                </div>

                <div class="space-y-8">
                    @foreach($preguntasPaso3 as $sectionIndex => $section)
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ $section['seccion'] }}</h3>
                            <div
                                class="grid grid-cols-12 gap-4 mt-2 text-sm font-medium text-gray-500 uppercase tracking-wider text-center">
                                <div class="col-span-6 text-left">Ítem</div>
                                <div class="col-span-2">SI CUMPLE</div>
                                <div class="col-span-2">NO CUMPLE</div>
                                <div class="col-span-2">NO APLICA</div>
                            </div>

                            @foreach($section['preguntas'] as $preguntaIndex => $pregunta)
                                @php
                                    $fieldName = "respuestasPaso3.{$pregunta}";
                                @endphp
                                <div
                                    class="grid grid-cols-12 gap-4 items-center py-3 border-b border-gray-100 hover:bg-gray-50 transition">
                                    <div class="col-span-6 text-gray-700">{{ $pregunta }}</div>
                                    <div class="col-span-2 flex justify-center">
                                        <label class="flex items-center justify-center cursor-pointer">
                                            <input type="radio" wire:model.live="{{ $fieldName }}" value="SI"
                                                class="w-5 h-5 text-orange-600 focus:ring-orange-500 border-gray-300 cursor-pointer">
                                        </label>
                                    </div>
                                    <div class="col-span-2 flex justify-center">
                                        <label class="flex items-center justify-center cursor-pointer">
                                            <input type="radio" wire:model.live="{{ $fieldName }}" value="NO"
                                                class="w-5 h-5 text-red-600 focus:ring-red-500 border-gray-300 cursor-pointer">
                                        </label>
                                    </div>
                                    <div class="col-span-2 flex justify-center">
                                        <label class="flex items-center justify-center cursor-pointer">
                                            <input type="radio" wire:model.live="{{ $fieldName }}" value="NO APLICA"
                                                class="w-5 h-5 text-gray-600 focus:ring-gray-500 border-gray-300 cursor-pointer">
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>

                {{-- Botones de Navegación --}}
                <div class="mt-8 flex justify-between items-center gap-4">
                    <button wire:click="retroceder" type="button"
                        class="bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-lg font-semibold transition transform hover:scale-105 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                        </svg>
                        ATRÁS
                    </button>

                    <button wire:click="siguiente" wire:loading.attr="disabled"
                        class="bg-gradient-to-r from-orange-600 to-orange-500 hover:from-orange-700 hover:to-orange-700 text-white px-8 py-3 rounded-lg font-semibold transition transform hover:scale-105 flex items-center gap-2">
                        <span wire:loading.remove wire:target="siguiente">SIGUIENTE</span>
                        <span wire:loading wire:target="siguiente">Cargando...</span>
                        <svg wire:loading.remove wire:target="siguiente" class="w-5 h-5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        {{-- Paso 4: Evaluación adicional --}}
        @if($paso === 4)
            <div wire:key="paso-4" class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                <div class="mb-8 pb-6 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900">Evaluación de Calidad del Servicio</h2>
                    <p class="text-gray-500 text-sm mt-1">Evalúa cada aspecto del servicio prestado</p>
                </div>

                <div class="space-y-8">
                    @foreach($preguntasPaso4 as $sectionIndex => $section)
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ $section['seccion'] }}</h3>
                            <div
                                class="grid grid-cols-12 gap-4 mt-2 text-sm font-medium text-gray-500 uppercase tracking-wider text-center">
                                <div class="col-span-6 text-left">Ítem</div>
                                <div class="col-span-2">SI CUMPLE</div>
                                <div class="col-span-2">NO CUMPLE</div>
                                <div class="col-span-2">NO APLICA</div>
                            </div>

                            @foreach($section['preguntas'] as $preguntaIndex => $pregunta)
                                @php
                                    $fieldName = "respuestasPaso4.{$pregunta}";
                                @endphp
                                <div
                                    class="grid grid-cols-12 gap-4 items-center py-3 border-b border-gray-100 hover:bg-gray-50 transition">
                                    <div class="col-span-6 text-gray-700">{{ $pregunta }}</div>
                                    <div class="col-span-2 flex justify-center">
                                        <label class="flex items-center justify-center cursor-pointer">
                                            <input type="radio" wire:model.live="{{ $fieldName }}" value="SI"
                                                class="w-5 h-5 text-orange-600 focus:ring-orange-500 border-gray-300 cursor-pointer">
                                        </label>
                                    </div>
                                    <div class="col-span-2 flex justify-center">
                                        <label class="flex items-center justify-center cursor-pointer">
                                            <input type="radio" wire:model.live="{{ $fieldName }}" value="NO"
                                                class="w-5 h-5 text-red-600 focus:ring-red-500 border-gray-300 cursor-pointer">
                                        </label>
                                    </div>
                                    <div class="col-span-2 flex justify-center">
                                        <label class="flex items-center justify-center cursor-pointer">
                                            <input type="radio" wire:model.live="{{ $fieldName }}" value="NO APLICA"
                                                class="w-5 h-5 text-gray-600 focus:ring-gray-500 border-gray-300 cursor-pointer">
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>

                {{-- Botones de Navegación --}}
                <div class="mt-8 flex justify-between items-center gap-4">
                    <button wire:click="retroceder" type="button"
                        class="bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-lg font-semibold transition transform hover:scale-105 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                        </svg>
                        ATRÁS
                    </button>

                    <button wire:click="siguiente" wire:loading.attr="disabled"
                        class="bg-gradient-to-r from-orange-600 to-orange-500 hover:from-orange-700 hover:to-orange-700 text-white px-8 py-3 rounded-lg font-semibold transition transform hover:scale-105 flex items-center gap-2">
                        <span wire:loading.remove wire:target="siguiente">SIGUIENTE</span>
                        <span wire:loading wire:target="siguiente">Cargando...</span>
                        <svg wire:loading.remove wire:target="siguiente" class="w-5 h-5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        {{-- Paso 5: Gestión comercial --}}
        @if($paso === 5)
            <div wire:key="paso-5" class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                <div class="mb-8 pb-6 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900">Gestión comercial</h2>
                    <p class="text-gray-500 text-sm mt-1">Evalúa la gestión comercial realizada</p>
                </div>

                <div class="space-y-8">
                    @foreach($preguntasPaso5 as $sectionIndex => $section)
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ $section['seccion'] }}</h3>
                            <div
                                class="grid grid-cols-12 gap-4 mt-2 text-sm font-medium text-gray-500 uppercase tracking-wider text-center">
                                <div class="col-span-6 text-left">Ítem</div>
                                <div class="col-span-2">SI CUMPLE</div>
                                <div class="col-span-2">NO CUMPLE</div>
                                <div class="col-span-2">NO APLICA</div>
                            </div>

                            @foreach($section['preguntas'] as $preguntaIndex => $pregunta)
                                @php
                                    $fieldName = "respuestasPaso5.{$pregunta}";
                                @endphp
                                <div
                                    class="grid grid-cols-12 gap-4 items-center py-3 border-b border-gray-100 hover:bg-gray-50 transition">
                                    <div class="col-span-6 text-gray-700">{{ $pregunta }}</div>
                                    <div class="col-span-2 flex justify-center">
                                        <label class="flex items-center justify-center cursor-pointer">
                                            <input type="radio" wire:model.live="{{ $fieldName }}" value="SI"
                                                class="w-5 h-5 text-orange-600 focus:ring-orange-500 border-gray-300 cursor-pointer">
                                        </label>
                                    </div>
                                    <div class="col-span-2 flex justify-center">
                                        <label class="flex items-center justify-center cursor-pointer">
                                            <input type="radio" wire:model.live="{{ $fieldName }}" value="NO"
                                                class="w-5 h-5 text-red-600 focus:ring-red-500 border-gray-300 cursor-pointer">
                                        </label>
                                    </div>
                                    <div class="col-span-2 flex justify-center">
                                        <label class="flex items-center justify-center cursor-pointer">
                                            <input type="radio" wire:model.live="{{ $fieldName }}" value="NO APLICA"
                                                class="w-5 h-5 text-gray-600 focus:ring-gray-500 border-gray-300 cursor-pointer">
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>

                {{-- Botones de Navegación --}}
                <div class="mt-8 flex justify-between items-center gap-4">
                    <button wire:click="retroceder" type="button"
                        class="bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-lg font-semibold transition transform hover:scale-105 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                        </svg>
                        ATRÁS
                    </button>

                    <button wire:click="siguiente" wire:loading.attr="disabled"
                        class="bg-gradient-to-r from-orange-600 to-orange-500 hover:from-orange-700 hover:to-orange-700 text-white px-8 py-3 rounded-lg font-semibold transition transform hover:scale-105 flex items-center gap-2">
                        <span wire:loading.remove wire:target="siguiente">SIGUIENTE</span>
                        <span wire:loading wire:target="siguiente">Cargando...</span>
                        <svg wire:loading.remove wire:target="siguiente" class="w-5 h-5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        {{-- Paso 6: PEC CUMPLIMIENTO --}}
        @if($paso === 6)
            <div wire:key="paso-6" class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                <div class="mb-8 pb-6 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900">PEC CUMPLIMIENTO</h2>
                    <p class="text-gray-500 text-sm mt-1">Evalúa el cumplimiento del protocolo de entrega de información</p>
                </div>

                <div class="space-y-8">
                    @foreach($preguntasPaso6 as $sectionIndex => $section)
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ $section['seccion'] }}</h3>
                            <div
                                class="grid grid-cols-12 gap-4 mt-2 text-sm font-medium text-gray-500 uppercase tracking-wider text-center">
                                <div class="col-span-6 text-left">Ítem</div>
                                <div class="col-span-2">SI CUMPLE</div>
                                <div class="col-span-2">NO CUMPLE</div>
                                <div class="col-span-2">NO APLICA</div>
                            </div>

                            @foreach($section['preguntas'] as $preguntaIndex => $pregunta)
                                @php
                                    $fieldName = "respuestasPaso6.{$pregunta}";
                                @endphp
                                <div
                                    class="grid grid-cols-12 gap-4 items-center py-3 border-b border-gray-100 hover:bg-gray-50 transition">
                                    <div class="col-span-6 text-gray-700">{{ $pregunta }}</div>
                                    <div class="col-span-2 flex justify-center">
                                        <label class="flex items-center justify-center cursor-pointer">
                                            <input type="radio" wire:model.live="{{ $fieldName }}" value="SI"
                                                class="w-5 h-5 text-orange-600 focus:ring-orange-500 border-gray-300 cursor-pointer">
                                        </label>
                                    </div>
                                    <div class="col-span-2 flex justify-center">
                                        <label class="flex items-center justify-center cursor-pointer">
                                            <input type="radio" wire:model.live="{{ $fieldName }}" value="NO"
                                                class="w-5 h-5 text-red-600 focus:ring-red-500 border-gray-300 cursor-pointer">
                                        </label>
                                    </div>
                                    <div class="col-span-2 flex justify-center">
                                        <label class="flex items-center justify-center cursor-pointer">
                                            <input type="radio" wire:model.live="{{ $fieldName }}" value="NO APLICA"
                                                class="w-5 h-5 text-gray-600 focus:ring-gray-500 border-gray-300 cursor-pointer">
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>

                {{-- Botones de Navegación --}}
                <div class="mt-8 flex justify-between items-center gap-4">
                    <button wire:click="retroceder" type="button"
                        class="bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-lg font-semibold transition transform hover:scale-105 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                        </svg>
                        ATRÁS
                    </button>

                    <button wire:click="siguiente" wire:loading.attr="disabled"
                        class="bg-gradient-to-r from-orange-600 to-orange-500 hover:from-orange-700 hover:to-orange-700 text-white px-8 py-3 rounded-lg font-semibold transition transform hover:scale-105 flex items-center gap-2">
                        <span wire:loading.remove wire:target="siguiente">SIGUIENTE</span>
                        <span wire:loading wire:target="siguiente">Cargando...</span>
                        <svg wire:loading.remove wire:target="siguiente" class="w-5 h-5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        {{-- Paso 7: Novedades Críticas --}}
        @if($paso === 7)
            <div wire:key="paso-7" class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                <div class="mb-8 pb-6 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900 uppercase">Detallar las novedades críticas presentadas en la
                        llamada</h2>
                    <p class="text-gray-500 text-sm mt-1">Describe cualquier situación crítica o relevante identificada
                        durante la auditoría</p>
                </div>

                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Novedades Críticas
                        </label>
                        <textarea wire:model="novedadesCriticas" rows="6"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition"
                            placeholder="Escribe aquí las novedades críticas o deja en blanco si no hay..."></textarea>
                        @error('novedadesCriticas')
                            <span class="text-red-600 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Botones de Navegación --}}
                <div class="mt-8 flex justify-between items-center gap-4">
                    <button wire:click="retroceder" type="button"
                        class="bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-lg font-semibold transition transform hover:scale-105 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                        </svg>
                        ATRÁS
                    </button>

                    <button wire:click="siguiente" wire:loading.attr="disabled"
                        class="bg-gradient-to-r from-orange-600 to-orange-500 hover:from-orange-700 hover:to-orange-700 text-white px-8 py-3 rounded-lg font-semibold transition transform hover:scale-105 flex items-center gap-2">
                        <span wire:loading.remove wire:target="siguiente">SIGUIENTE</span>
                        <span wire:loading wire:target="siguiente">Cargando...</span>
                        <svg wire:loading.remove wire:target="siguiente" class="w-5 h-5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        {{-- Paso 8: Concretó la venta --}}
        @if($paso === 8)
            <div wire:key="paso-8" class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                <div class="mb-8 pb-6 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900">Concretó la venta en la llamada</h2>
                    <p class="text-gray-500 text-sm mt-1">Indica si la venta fue concretada durante la gestión</p>
                </div>

                <div class="space-y-4">
                    <div class="flex flex-col gap-3">
                        <label
                            class="flex items-center p-4 border-2 rounded-lg cursor-pointer transition hover:bg-gray-50 {{ $concretoVenta === 'SI' ? 'border-orange-500 bg-orange-50' : 'border-gray-200' }}">
                            <input type="radio" wire:model.live="concretoVenta" value="SI"
                                class="w-5 h-5 text-orange-600 focus:ring-orange-500 border-gray-300 cursor-pointer">
                            <span class="ml-3 text-lg font-medium text-gray-900">SI</span>
                        </label>

                        <label
                            class="flex items-center p-4 border-2 rounded-lg cursor-pointer transition hover:bg-gray-50 {{ $concretoVenta === 'NO' ? 'border-orange-500 bg-orange-50' : 'border-gray-200' }}">
                            <input type="radio" wire:model.live="concretoVenta" value="NO"
                                class="w-5 h-5 text-orange-600 focus:ring-orange-500 border-gray-300 cursor-pointer">
                            <span class="ml-3 text-lg font-medium text-gray-900">NO</span>
                        </label>

                        <label
                            class="flex items-center p-4 border-2 rounded-lg cursor-pointer transition hover:bg-gray-50 {{ $concretoVenta === 'NO APLICA' ? 'border-orange-500 bg-orange-50' : 'border-gray-200' }}">
                            <input type="radio" wire:model.live="concretoVenta" value="NO APLICA"
                                class="w-5 h-5 text-orange-600 focus:ring-orange-500 border-gray-300 cursor-pointer">
                            <span class="ml-3 text-lg font-medium text-gray-900">NO APLICA</span>
                        </label>
                    </div>

                    @error('concretoVenta')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Botones de Navegación --}}
                <div class="mt-8 flex justify-between items-center gap-4">
                    <button wire:click="retroceder" type="button"
                        class="bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-lg font-semibold transition transform hover:scale-105 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                        </svg>
                        ATRÁS
                    </button>

                    <button wire:click="siguiente" wire:loading.attr="disabled"
                        class="bg-gradient-to-r from-orange-600 to-orange-500 hover:from-orange-700 hover:to-orange-700 text-white px-8 py-3 rounded-lg font-semibold transition transform hover:scale-105 flex items-center gap-2">
                        <span wire:loading.remove wire:target="siguiente">SIGUIENTE</span>
                        <span wire:loading wire:target="siguiente">Cargando...</span>
                        <svg wire:loading.remove wire:target="siguiente" class="w-5 h-5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        {{-- Paso 9: Causa Raíz Principal (rama NO) --}}
        @if($paso === 9 && $ramaFlujo === 'venta_no')
            <div wire:key="paso-9-venta-no" class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                <div class="mb-8 pb-6 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900">Causa Raíz Principal</h2>
                    <p class="text-gray-500 text-sm mt-1">Selecciona la causa principal de no venta</p>
                </div>

                <div class="space-y-4">
                    <div class="flex flex-col gap-3">
                        <label
                            class="flex items-center p-4 border-2 rounded-lg cursor-pointer transition hover:bg-gray-50 {{ $causaRaizPrincipal === 'Proceso' ? 'border-orange-500 bg-orange-50' : 'border-gray-200' }}">
                            <input type="radio" wire:model.live="causaRaizPrincipal" value="Proceso"
                                class="w-5 h-5 text-orange-600 focus:ring-orange-500 border-gray-300 cursor-pointer">
                            <span class="ml-3 text-lg font-medium text-gray-900">Proceso</span>
                        </label>

                        <label
                            class="flex items-center p-4 border-2 rounded-lg cursor-pointer transition hover:bg-gray-50 {{ $causaRaizPrincipal === 'Agente' ? 'border-orange-500 bg-orange-50' : 'border-gray-200' }}">
                            <input type="radio" wire:model.live="causaRaizPrincipal" value="Agente"
                                class="w-5 h-5 text-orange-600 focus:ring-orange-500 border-gray-300 cursor-pointer">
                            <span class="ml-3 text-lg font-medium text-gray-900">Agente</span>
                        </label>

                        <label
                            class="flex items-center p-4 border-2 rounded-lg cursor-pointer transition hover:bg-gray-50 {{ $causaRaizPrincipal === 'Cliente' ? 'border-orange-500 bg-orange-50' : 'border-gray-200' }}">
                            <input type="radio" wire:model.live="causaRaizPrincipal" value="Cliente"
                                class="w-5 h-5 text-orange-600 focus:ring-orange-500 border-gray-300 cursor-pointer">
                            <span class="ml-3 text-lg font-medium text-gray-900">Cliente</span>
                        </label>
                    </div>

                    @error('causaRaizPrincipal')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Botones de Navegación --}}
                <div class="mt-8 flex justify-between items-center gap-4">
                    <button wire:click="retroceder" type="button"
                        class="bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-lg font-semibold transition transform hover:scale-105 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                        </svg>
                        ATRÁS
                    </button>

                    <button wire:click="siguiente" wire:loading.attr="disabled"
                        class="bg-gradient-to-r from-orange-600 to-orange-500 hover:from-orange-700 hover:to-orange-700 text-white px-8 py-3 rounded-lg font-semibold transition transform hover:scale-105 flex items-center gap-2">
                        <span wire:loading.remove wire:target="siguiente">SIGUIENTE</span>
                        <span wire:loading wire:target="siguiente">Cargando...</span>
                        <svg wire:loading.remove wire:target="siguiente" class="w-5 h-5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        {{-- Paso 10: Detalle Causa Raíz + Instalación (rama NO) --}}
        @if($paso === 10 && $ramaFlujo === 'venta_no')
            <div wire:key="paso-10-venta-no" class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                {{-- Campo condicional según causa raíz seleccionada --}}
                @if($causaRaizPrincipal === 'Proceso')
                    <div class="mb-6">
                        <label class="block text-xl font-bold text-gray-900 mb-2">PROCESO</label>
                        <textarea wire:model="causaRaizDetalle" rows="2"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition resize-none"
                            placeholder="Describe el proceso..."></textarea>
                    </div>
                @elseif($causaRaizPrincipal === 'Agente')
                    <div class="mb-6">
                        <label class="block text-xl font-bold text-gray-900 mb-2">AGENTE</label>
                        <textarea wire:model="causaRaizDetalle" rows="2"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition resize-none"
                            placeholder="Describe el problema del agente..."></textarea>
                    </div>
                @elseif($causaRaizPrincipal === 'Cliente')
                    <div class="mb-6">
                        <label class="block text-xl font-bold text-gray-900 mb-2">CLIENTE</label>
                        <textarea wire:model="causaRaizDetalle" rows="2"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition resize-none"
                            placeholder="Describe la situación del cliente..."></textarea>
                    </div>
                @endif

                <div class="mb-8 pb-6 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900">¿SE INSTALÓ EL SERVICIO?</h2>
                    <p class="text-gray-500 text-sm mt-1">Indica si el servicio fue instalado</p>
                </div>

                <div class="space-y-4">
                    <div class="flex flex-col gap-3">
                        <label
                            class="flex items-center p-4 border-2 rounded-lg cursor-pointer transition hover:bg-gray-50 {{ $seInstaloServicio === 'SI' ? 'border-orange-500 bg-orange-50' : 'border-gray-200' }}">
                            <input type="radio" wire:model.live="seInstaloServicio" value="SI"
                                class="w-5 h-5 text-orange-600 focus:ring-orange-500 border-gray-300 cursor-pointer">
                            <span class="ml-3 text-lg font-medium text-gray-900">SI</span>
                        </label>

                        <label
                            class="flex items-center p-4 border-2 rounded-lg cursor-pointer transition hover:bg-gray-50 {{ $seInstaloServicio === 'NO' ? 'border-orange-500 bg-orange-50' : 'border-gray-200' }}">
                            <input type="radio" wire:model.live="seInstaloServicio" value="NO"
                                class="w-5 h-5 text-orange-600 focus:ring-orange-500 border-gray-300 cursor-pointer">
                            <span class="ml-3 text-lg font-medium text-gray-900">NO</span>
                        </label>

                        <label
                            class="flex items-center p-4 border-2 rounded-lg cursor-pointer transition hover:bg-gray-50 {{ $seInstaloServicio === 'NO APLICA' ? 'border-orange-500 bg-orange-50' : 'border-gray-200' }}">
                            <input type="radio" wire:model.live="seInstaloServicio" value="NO APLICA"
                                class="w-5 h-5 text-orange-600 focus:ring-orange-500 border-gray-300 cursor-pointer">
                            <span class="ml-3 text-lg font-medium text-gray-900">NO APLICA</span>
                        </label>
                    </div>

                    @error('seInstaloServicio')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Botones de Navegación --}}
                <div class="mt-8 flex justify-between items-center gap-4">
                    <button wire:click="retroceder" type="button"
                        class="bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-lg font-semibold transition transform hover:scale-105 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                        </svg>
                        ATRÁS
                    </button>

                    <button wire:click="siguiente" wire:loading.attr="disabled"
                        class="bg-gradient-to-r from-orange-600 to-orange-500 hover:from-orange-700 hover:to-orange-700 text-white px-8 py-3 rounded-lg font-semibold transition transform hover:scale-105 flex items-center gap-2">
                        <span wire:loading.remove wire:target="siguiente">SIGUIENTE</span>
                        <span wire:loading wire:target="siguiente">Cargando...</span>
                        <svg wire:loading.remove wire:target="siguiente" class="w-5 h-5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        {{-- Paso 9: ¿Se instaló el servicio? (rama SI/NO APLICA) --}}
        @if($paso === 9 && $ramaFlujo === 'venta_si_no_aplica')
            <div wire:key="paso-9-venta-si" class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                <div class="mb-8 pb-6 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900">¿SE INSTALÓ EL SERVICIO?</h2>
                    <p class="text-gray-500 text-sm mt-1">Indica si el servicio fue instalado</p>
                </div>

                <div class="space-y-6">
                    {{-- Pregunta principal --}}
                    <div class="flex flex-col gap-3">
                        <label
                            class="flex items-center p-4 border-2 rounded-lg cursor-pointer transition hover:bg-gray-50 {{ $seInstaloServicio === 'SI' ? 'border-orange-500 bg-orange-50' : 'border-gray-200' }}">
                            <input type="radio" wire:model.live="seInstaloServicio" value="SI"
                                class="w-5 h-5 text-orange-600 focus:ring-orange-500 border-gray-300 cursor-pointer">
                            <span class="ml-3 text-lg font-medium text-gray-900">SI</span>
                        </label>

                        <label
                            class="flex items-center p-4 border-2 rounded-lg cursor-pointer transition hover:bg-gray-50 {{ $seInstaloServicio === 'NO' ? 'border-orange-500 bg-orange-50' : 'border-gray-200' }}">
                            <input type="radio" wire:model.live="seInstaloServicio" value="NO"
                                class="w-5 h-5 text-orange-600 focus:ring-orange-500 border-gray-300 cursor-pointer">
                            <span class="ml-3 text-lg font-medium text-gray-900">NO</span>
                        </label>

                        <label
                            class="flex items-center p-4 border-2 rounded-lg cursor-pointer transition hover:bg-gray-50 {{ $seInstaloServicio === 'NO APLICA' ? 'border-orange-500 bg-orange-50' : 'border-gray-200' }}">
                            <input type="radio" wire:model.live="seInstaloServicio" value="NO APLICA"
                                class="w-5 h-5 text-orange-600 focus:ring-orange-500 border-gray-300 cursor-pointer">
                            <span class="ml-3 text-lg font-medium text-gray-900">NO APLICA</span>
                        </label>
                    </div>

                    @error('seInstaloServicio')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Botones de Navegación --}}
                <div class="mt-8 flex justify-between items-center gap-4">
                    <button wire:click="retroceder" type="button"
                        class="bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-lg font-semibold transition transform hover:scale-105 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                        </svg>
                        ATRÁS
                    </button>

                    <button wire:click="siguiente" wire:loading.attr="disabled"
                        class="bg-gradient-to-r from-orange-600 to-orange-500 hover:from-orange-700 hover:to-orange-700 text-white px-8 py-3 rounded-lg font-semibold transition transform hover:scale-105 flex items-center gap-2">
                        <span wire:loading.remove wire:target="siguiente">SIGUIENTE</span>
                        <span wire:loading wire:target="siguiente">Cargando...</span>
                        <svg wire:loading.remove wire:target="siguiente" class="w-5 h-5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        {{-- Paso 10: Seguimiento (rama SI/NO APLICA) --}}
        @if($paso === 10 && $ramaFlujo === 'venta_si_no_aplica')
            <div wire:key="paso-10-seguimiento" class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                <div class="mb-8 pb-6 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900">Seguimiento</h2>
                    <p class="text-gray-500 text-sm mt-1">Evalúa el seguimiento realizado</p>
                </div>

                <div class="space-y-6">
                    {{-- Campo condicional: solo si seleccionó NO en el paso anterior --}}
                    @if($seInstaloServicio === 'NO')
                        <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                ¿Por qué no instaló el servicio?
                            </label>
                            <textarea wire:model="porqueNoInstalo" rows="3"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition"
                                placeholder="Describe la razón..."></textarea>
                        </div>
                    @endif

                    {{-- Tabla de preguntas de Seguimiento --}}
                    <div>
                        <div
                            class="grid grid-cols-12 gap-4 mt-2 text-sm font-medium text-gray-500 uppercase tracking-wider text-center mb-4">
                            <div class="col-span-6 text-left"></div>
                            <div class="col-span-2">SI</div>
                            <div class="col-span-2">NO</div>
                            <div class="col-span-2">NO APLICA</div>
                        </div>

                        {{-- Pregunta 1: Asesor realizó Seguimiento? --}}
                        <div
                            class="grid grid-cols-12 gap-4 items-center py-3 border-b border-gray-100 hover:bg-gray-50 transition">
                            <div class="col-span-6 text-gray-700">Asesor realizó Seguimiento?</div>
                            <div class="col-span-2 flex justify-center">
                                <label class="flex items-center justify-center cursor-pointer">
                                    <input type="radio" wire:model.live="asesorRealizoSeguimiento" value="SI"
                                        class="w-5 h-5 text-orange-600 focus:ring-orange-500 border-gray-300 cursor-pointer">
                                </label>
                            </div>
                            <div class="col-span-2 flex justify-center">
                                <label class="flex items-center justify-center cursor-pointer">
                                    <input type="radio" wire:model.live="asesorRealizoSeguimiento" value="NO"
                                        class="w-5 h-5 text-red-600 focus:ring-red-500 border-gray-300 cursor-pointer">
                                </label>
                            </div>
                            <div class="col-span-2 flex justify-center">
                                <label class="flex items-center justify-center cursor-pointer">
                                    <input type="radio" wire:model.live="asesorRealizoSeguimiento" value="NO APLICA"
                                        class="w-5 h-5 text-gray-600 focus:ring-gray-500 border-gray-300 cursor-pointer">
                                </label>
                            </div>
                        </div>

                        {{-- Pregunta 2: Venta fue recuperada? --}}
                        <div
                            class="grid grid-cols-12 gap-4 items-center py-3 border-b border-gray-100 hover:bg-gray-50 transition">
                            <div class="col-span-6 text-gray-700">Venta fue recuperada?</div>
                            <div class="col-span-2 flex justify-center">
                                <label class="flex items-center justify-center cursor-pointer">
                                    <input type="radio" wire:model.live="ventaFueRecuperada" value="SI"
                                        class="w-5 h-5 text-orange-600 focus:ring-orange-500 border-gray-300 cursor-pointer">
                                </label>
                            </div>
                            <div class="col-span-2 flex justify-center">
                                <label class="flex items-center justify-center cursor-pointer">
                                    <input type="radio" wire:model.live="ventaFueRecuperada" value="NO"
                                        class="w-5 h-5 text-red-600 focus:ring-red-500 border-gray-300 cursor-pointer">
                                </label>
                            </div>
                            <div class="col-span-2 flex justify-center">
                                <label class="flex items-center justify-center cursor-pointer">
                                    <input type="radio" wire:model.live="ventaFueRecuperada" value="NO APLICA"
                                        class="w-5 h-5 text-gray-600 focus:ring-gray-500 border-gray-300 cursor-pointer">
                                </label>
                            </div>
                        </div>

                        {{-- Pregunta 3: Solicitud fue ingresada en meses anteriores --}}
                        <div
                            class="grid grid-cols-12 gap-4 items-center py-3 border-b border-gray-100 hover:bg-gray-50 transition">
                            <div class="col-span-6 text-gray-700">Solicitud fue ingresada en meses anteriores</div>
                            <div class="col-span-2 flex justify-center">
                                <label class="flex items-center justify-center cursor-pointer">
                                    <input type="radio" wire:model.live="solicitudFueIngresadaMesesAnteriores" value="SI"
                                        class="w-5 h-5 text-orange-600 focus:ring-orange-500 border-gray-300 cursor-pointer">
                                </label>
                            </div>
                            <div class="col-span-2 flex justify-center">
                                <label class="flex items-center justify-center cursor-pointer">
                                    <input type="radio" wire:model.live="solicitudFueIngresadaMesesAnteriores" value="NO"
                                        class="w-5 h-5 text-red-600 focus:ring-red-500 border-gray-300 cursor-pointer">
                                </label>
                            </div>
                            <div class="col-span-2 flex justify-center">
                                <label class="flex items-center justify-center cursor-pointer">
                                    <input type="radio" wire:model.live="solicitudFueIngresadaMesesAnteriores"
                                        value="NO APLICA"
                                        class="w-5 h-5 text-gray-600 focus:ring-gray-500 border-gray-300 cursor-pointer">
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Botones de Navegación --}}
                <div class="mt-8 flex justify-between items-center gap-4">
                    <button wire:click="retroceder" type="button"
                        class="bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-lg font-semibold transition transform hover:scale-105 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                        </svg>
                        ATRÁS
                    </button>

                    <button wire:click="siguiente" wire:loading.attr="disabled"
                        class="bg-gradient-to-r from-orange-600 to-orange-500 hover:from-orange-700 hover:to-orange-700 text-white px-8 py-3 rounded-lg font-semibold transition transform hover:scale-105 flex items-center gap-2">
                        <span wire:loading.remove wire:target="siguiente">SIGUIENTE</span>
                        <span wire:loading wire:target="siguiente">Cargando...</span>
                        <svg wire:loading.remove wire:target="siguiente" class="w-5 h-5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        {{-- Paso 11: Observaciones PostVenta (rama SI/NO APLICA) --}}
        @if($paso === 11 && $ramaFlujo === 'venta_si_no_aplica')
            <div wire:key="paso-11-postventa" class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                <div class="mb-8 pb-6 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900">Detalla las observaciones en la PostVenta</h2>
                    <p class="text-gray-500 text-sm mt-1">Campo opcional para observaciones adicionales</p>
                </div>

                <div class="space-y-4">
                    <textarea wire:model="observacionesPostVenta" rows="6"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition resize-none"
                        placeholder="Escribe tus observaciones aquí..."></textarea>
                </div>

                {{-- Botones de Navegación --}}
                <div class="mt-8 flex justify-between items-center gap-4">
                    <button wire:click="retroceder" type="button"
                        class="bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-lg font-semibold transition transform hover:scale-105 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                        </svg>
                        ATRÁS
                    </button>

                    <button wire:click="guardarDatos" wire:loading.attr="disabled"
                        class="bg-gradient-to-r from-green-600 to-green-500 hover:from-green-700 hover:to-green-700 text-white px-8 py-3 rounded-lg font-semibold transition transform hover:scale-105 flex items-center gap-2">
                        <span wire:loading.remove wire:target="guardarDatos">ENVIAR</span>
                        <span wire:loading wire:target="guardarDatos">Enviando...</span>
                        <svg wire:loading.remove wire:target="guardarDatos" class="w-5 h-5" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        {{-- Paso 11: Seguimiento (rama NO) --}}
        @if($paso === 11 && $ramaFlujo === 'venta_no')
            <div wire:key="paso-11-seguimiento-no" class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                <div class="mb-8 pb-6 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900">Seguimiento</h2>
                    <p class="text-gray-500 text-sm mt-1">Evalúa el seguimiento realizado</p>
                </div>

                <div class="space-y-6">
                    {{-- Campo condicional: solo si seleccionó NO en el paso anterior --}}
                    @if($seInstaloServicio === 'NO')
                        <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                ¿Por qué no instaló el servicio?
                            </label>
                            <textarea wire:model="porqueNoInstalo" rows="3"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition"
                                placeholder="Describe la razón..."></textarea>
                        </div>
                    @endif

                    {{-- Tabla de preguntas de Seguimiento --}}
                    <div>
                        <div
                            class="grid grid-cols-12 gap-4 mt-2 text-sm font-medium text-gray-500 uppercase tracking-wider text-center mb-4">
                            <div class="col-span-6 text-left"></div>
                            <div class="col-span-2">SI</div>
                            <div class="col-span-2">NO</div>
                            <div class="col-span-2">NO APLICA</div>
                        </div>

                        {{-- Pregunta 1 --}}
                        <div
                            class="grid grid-cols-12 gap-4 items-center py-3 border-b border-gray-100 hover:bg-gray-50 transition">
                            <div class="col-span-6 text-gray-700">Asesor realizó Seguimiento?</div>
                            <div class="col-span-2 flex justify-center">
                                <input type="radio" wire:model.live="asesorRealizoSeguimiento" value="SI"
                                    class="w-5 h-5 text-orange-600 focus:ring-orange-500 border-gray-300 cursor-pointer">
                            </div>
                            <div class="col-span-2 flex justify-center">
                                <input type="radio" wire:model.live="asesorRealizoSeguimiento" value="NO"
                                    class="w-5 h-5 text-red-600 focus:ring-red-500 border-gray-300 cursor-pointer">
                            </div>
                            <div class="col-span-2 flex justify-center">
                                <input type="radio" wire:model.live="asesorRealizoSeguimiento" value="NO APLICA"
                                    class="w-5 h-5 text-gray-600 focus:ring-gray-500 border-gray-300 cursor-pointer">
                            </div>
                        </div>

                        {{-- Pregunta 2 --}}
                        <div
                            class="grid grid-cols-12 gap-4 items-center py-3 border-b border-gray-100 hover:bg-gray-50 transition">
                            <div class="col-span-6 text-gray-700">Venta fue recuperada?</div>
                            <div class="col-span-2 flex justify-center">
                                <input type="radio" wire:model.live="ventaFueRecuperada" value="SI"
                                    class="w-5 h-5 text-orange-600 focus:ring-orange-500 border-gray-300 cursor-pointer">
                            </div>
                            <div class="col-span-2 flex justify-center">
                                <input type="radio" wire:model.live="ventaFueRecuperada" value="NO"
                                    class="w-5 h-5 text-red-600 focus:ring-red-500 border-gray-300 cursor-pointer">
                            </div>
                            <div class="col-span-2 flex justify-center">
                                <input type="radio" wire:model.live="ventaFueRecuperada" value="NO APLICA"
                                    class="w-5 h-5 text-gray-600 focus:ring-gray-500 border-gray-300 cursor-pointer">
                            </div>
                        </div>

                        {{-- Pregunta 3 --}}
                        <div
                            class="grid grid-cols-12 gap-4 items-center py-3 border-b border-gray-100 hover:bg-gray-50 transition">
                            <div class="col-span-6 text-gray-700">Solicitud fue ingresada en meses anteriores</div>
                            <div class="col-span-2 flex justify-center">
                                <input type="radio" wire:model.live="solicitudFueIngresadaMesesAnteriores" value="SI"
                                    class="w-5 h-5 text-orange-600 focus:ring-orange-500 border-gray-300 cursor-pointer">
                            </div>
                            <div class="col-span-2 flex justify-center">
                                <input type="radio" wire:model.live="solicitudFueIngresadaMesesAnteriores" value="NO"
                                    class="w-5 h-5 text-red-600 focus:ring-red-500 border-gray-300 cursor-pointer">
                            </div>
                            <div class="col-span-2 flex justify-center">
                                <input type="radio" wire:model.live="solicitudFueIngresadaMesesAnteriores" value="NO APLICA"
                                    class="w-5 h-5 text-gray-600 focus:ring-gray-500 border-gray-300 cursor-pointer">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Botones --}}
                <div class="mt-8 flex justify-between items-center gap-4">
                    <button wire:click="retroceder" type="button"
                        class="bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-lg font-semibold transition transform hover:scale-105 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                        </svg>
                        ATRÁS
                    </button>

                    <button wire:click="siguiente" wire:loading.attr="disabled"
                        class="bg-gradient-to-r from-orange-600 to-orange-500 hover:from-orange-700 hover:to-orange-700 text-white px-8 py-3 rounded-lg font-semibold transition transform hover:scale-105 flex items-center gap-2">
                        <span wire:loading.remove wire:target="siguiente">SIGUIENTE</span>
                        <span wire:loading wire:target="siguiente">Cargando...</span>
                        <svg wire:loading.remove wire:target="siguiente" class="w-5 h-5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        {{-- Paso 12: Observaciones PostVenta (rama NO) --}}
        @if($paso === 12 && $ramaFlujo === 'venta_no')
            <div wire:key="paso-12-postventa-no" class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                <div class="mb-8 pb-6 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900">Detalla las observaciones en la PostVenta</h2>
                    <p class="text-gray-500 text-sm mt-1">Campo opcional para observaciones adicionales</p>
                </div>

                <div class="space-y-4">
                    <textarea wire:model="observacionesPostVenta" rows="6"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition resize-none"
                        placeholder="Escribe tus observaciones aquí..."></textarea>
                </div>

                {{-- Botones --}}
                <div class="mt-8 flex justify-between items-center gap-4">
                    <button wire:click="retroceder" type="button"
                        class="bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-lg font-semibold transition transform hover:scale-105 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                        </svg>
                        ATRÁS
                    </button>

                    <button wire:click="guardarDatos" wire:loading.attr="disabled"
                        class="bg-gradient-to-r from-green-600 to-green-500 hover:from-green-700 hover:to-green-700 text-white px-8 py-3 rounded-lg font-semibold transition transform hover:scale-105 flex items-center gap-2">
                        <span wire:loading.remove wire:target="guardarDatos">ENVIAR</span>
                        <span wire:loading wire:target="guardarDatos">Enviando...</span>
                        <svg wire:loading.remove wire:target="guardarDatos" class="w-5 h-5" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        {{-- Paso 99: Auditoría Completada --}}
        @if($paso === 99 && $guardado)
            <div wire:key="paso-99" class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                <div class="text-center">
                    <div class="flex justify-center mb-4">
                        <div class="bg-orange-500 rounded-full p-4">
                            <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-green-800 mb-2">¡Auditoría de Hogar Guardada!</h3>
                    <p class="text-orange-700 mb-6">Los datos se guardaron correctamente en el sistema.</p>
                    <button wire:click="resetear" type="button"
                        class="bg-gradient-to-r from-orange-600 to-orange-500 hover:from-orange-700 hover:to-orange-700 text-white px-10 py-4 rounded-xl font-bold text-lg transition transform hover:scale-105 shadow-lg">
                        ← Volver a Selección de Llamadas
                    </button>
                </div>
            </div>
        @endif

    </div>

    {{-- Componente de Historial --}}
    @livewire('historial-auditorias')
</div>