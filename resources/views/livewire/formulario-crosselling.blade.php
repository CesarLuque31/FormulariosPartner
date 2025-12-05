<div class="min-h-screen py-8">

    <!-- Barra de Progreso -->
    @if($paso > 0 && $paso < 99)
        <div class="max-w-7xl mx-auto mb-8 px-6 lg:px-8">
            <div class="flex justify-between text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wider">
                <span>Inicio</span>
                <span>Progreso: {{ $progress ?? 0 }}%</span>
                <span>Final</span>
            </div>
            <div class="h-3 bg-gray-200 rounded-full overflow-hidden shadow-inner">
                <div class="h-full bg-gradient-to-r from-green-500 to-teal-600 transition-all duration-500 ease-out shadow-lg"
                    style="width: {{ $progress ?? 0 }}%">
                </div>
            </div>
        </div>
    @endif

    <!-- Contenedor Principal -->
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white rounded-2xl shadow-xl p-8 mb-8 border border-gray-100">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-3xl font-black text-gray-900 mb-2">Formulario de Auditoría - Crosselling</h1>
                    <div class="flex items-center gap-3">
                        <span
                            class="bg-gradient-to-r from-green-500 to-teal-500 text-white px-4 py-1.5 rounded-full text-sm font-semibold">
                            @if($paso === 0)
                                Selección de Llamada
                            @elseif($paso === 99)
                                Auditoría Completada
                            @else
                                Paso {{ $paso }} - Datos de Crosselling
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mensajes Flash -->
        @if (session()->has('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded">
                {{ session('error') }}
            </div>
        @endif

        <!-- Paso 0: Selección Manual o Aleatorio -->
        @if($paso === 0)
            <div wire:key="paso-0" class="bg-white rounded-lg shadow-lg p-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6 text-center">Selecciona el método de carga
                    (Crosselling)</h2>

                <div class="max-w-md mx-auto mb-8">
                    {{-- Opción Manual - COMENTADO: Ahora se maneja desde FormularioAuditoria con redirección automática
                    --}}
                    {{--
                    <div class="border-2 border-gray-200 rounded-lg p-6 hover:border-green-500 transition">
                        <div class="text-center mb-4">
                            <svg class="w-16 h-16 mx-auto text-green-600 mb-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                </path>
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-800">Ingreso Manual</h3>
                            <p class="text-sm text-gray-600 mt-2">Ingresa un ID CIC específico</p>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                ID CIC
                            </label>
                            <input type="text" wire:model.live="idCicManual"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            @error('idCicManual')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <button wire:click="seleccionarManual"
                            class="w-full bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold transition">
                            Cargar ID Manual
                        </button>
                    </div>
                    --}}

                    <!-- Opción Aleatoria -->
                    <div class="border-2 border-gray-200 rounded-lg p-6 hover:border-teal-500 transition">
                        <div class="text-center mb-4">
                            <svg class="w-16 h-16 mx-auto text-teal-600 mb-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                </path>
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-800">Selección Aleatoria</h3>
                            <p class="text-sm text-gray-600 mt-2">Carga una llamada de Crosselling al azar</p>
                        </div>

                        <!-- Filtros Opcionales -->
                        <div class="mb-4 space-y-3">
                            <h4 class="text-sm font-semibold text-teal-700">Filtros Opcionales:</h4>

                            <!-- Filtro Campaña -->
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Campaña Crosselling</label>
                                <select wire:model.live="filtroCampana"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                                    <option value="">Todas las campañas de Crosselling</option>
                                    @foreach($campanasDisponibles as $campana)
                                        <option value="{{ $campana }}">{{ $campana }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Filtro DNI -->
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">DNI del Empleado</label>
                                <input type="text" wire:model.live="filtroDni"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                            </div>

                            <!-- Filtro Fecha -->
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Fecha Específica</label>
                                <input type="date" wire:model.live="filtroFecha" min="2025-10-01" max="{{ date('Y-m-d') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                                @error('filtroFecha')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Botón limpiar filtros -->
                            @if($filtroCampana || $filtroDni || $filtroFecha)
                                <button wire:click="limpiarFiltros" type="button"
                                    class="text-xs text-teal-600 hover:text-teal-800 underline">
                                    Limpiar filtros
                                </button>
                            @endif
                        </div>

                        <button wire:click="seleccionarAleatorio"
                            class="w-full bg-gradient-to-r from-teal-600 to-green-600 hover:from-teal-700 hover:to-green-700 text-white px-6 py-3 rounded-lg font-semibold transition transform hover:scale-105">
                            Cargar Aleatorio
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- Paso 1: Datos de la Llamada + Campos Crosselling -->
        @if($paso === 1)
            <div wire:key="paso-1" class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                <div class="flex justify-between items-center mb-8 pb-6 border-b border-gray-200">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Datos de la Llamada - Crosselling</h2>
                        <p class="text-gray-500 text-sm mt-1">Información de la llamada seleccionada</p>
                    </div>
                    @if($paso === 1 && !$guardado && !$bloquearRandomizar)
                        <button wire:click="randomizar"
                            class="bg-gradient-to-r from-green-500 to-teal-600 hover:from-green-600 hover:to-teal-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 hover:shadow-lg flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                </path>
                            </svg>
                            Randomizar
                        </button>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Datos básicos de la llamada -->
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

                <!-- Campos adicionales de Crosselling -->
                <div class="mt-8 pt-8 border-t border-gray-200">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Información Adicional - Crosselling</h3>

                    <!-- Tipo de Gestión -->
                    <div class="mb-6">
                        <label class="block text-lg font-medium text-gray-800 mb-4">
                            Tipo de Gestión <span class="text-red-500">*</span>
                        </label>
                        <div class="space-y-3">
                            <label
                                class="flex items-center p-4 bg-white border-2 rounded-xl cursor-pointer hover:bg-green-50 hover:border-green-300 transition-all
                                                                                                                {{ $tipoGestion === 'RENOVACIÓN' ? 'border-green-500 bg-green-50 ring-1 ring-green-500' : 'border-gray-200' }}">
                                <input type="radio" wire:model.live="tipoGestion" value="RENOVACIÓN"
                                    class="w-5 h-5 text-green-600 focus:ring-green-500 border-gray-300">
                                <span class="ml-3 text-base font-medium text-gray-900">RENOVACIÓN</span>
                            </label>

                            <label
                                class="flex items-center p-4 bg-white border-2 rounded-xl cursor-pointer hover:bg-green-50 hover:border-green-300 transition-all
                                                                                                                {{ $tipoGestion === 'PORTABILIDAD - LINEA NUEVA' ? 'border-green-500 bg-green-50 ring-1 ring-green-500' : 'border-gray-200' }}">
                                <input type="radio" wire:model.live="tipoGestion" value="PORTABILIDAD - LINEA NUEVA"
                                    class="w-5 h-5 text-green-600 focus:ring-green-500 border-gray-300">
                                <span class="ml-3 text-base font-medium text-gray-900">PORTABILIDAD - LINEA NUEVA</span>
                            </label>

                            <label
                                class="flex items-center p-4 bg-white border-2 rounded-xl cursor-pointer hover:bg-green-50 hover:border-green-300 transition-all
                                                                                                                {{ $tipoGestion === 'HOGAR' ? 'border-green-500 bg-green-50 ring-1 ring-green-500' : 'border-gray-200' }}">
                                <input type="radio" wire:model.live="tipoGestion" value="HOGAR"
                                    class="w-5 h-5 text-green-600 focus:ring-green-500 border-gray-300">
                                <span class="ml-3 text-base font-medium text-gray-900">HOGAR</span>
                            </label>

                            <label
                                class="flex items-center p-4 bg-white border-2 rounded-xl cursor-pointer hover:bg-green-50 hover:border-green-300 transition-all
                                                                                                                {{ $tipoGestion === 'MIGRACION' ? 'border-green-500 bg-green-50 ring-1 ring-green-500' : 'border-gray-200' }}">
                                <input type="radio" wire:model.live="tipoGestion" value="MIGRACION"
                                    class="w-5 h-5 text-green-600 focus:ring-green-500 border-gray-300">
                                <span class="ml-3 text-base font-medium text-gray-900">MIGRACION</span>
                            </label>
                        </div>
                        @error('tipoGestion')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tipo de Monitoreo -->
                    <div class="mb-6">
                        <label class="block text-lg font-medium text-gray-800 mb-4">
                            Tipo de Monitoreo <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <label
                                class="flex items-center p-4 bg-white border-2 rounded-xl cursor-pointer hover:bg-teal-50 hover:border-teal-300 transition-all
                                                                                                                {{ $tipoMonitoreo === 'Aleatorio' ? 'border-teal-500 bg-teal-50 ring-1 ring-teal-500' : 'border-gray-200' }}">
                                <input type="radio" wire:model.live="tipoMonitoreo" value="Aleatorio"
                                    class="w-5 h-5 text-teal-600 focus:ring-teal-500 border-gray-300">
                                <span class="ml-3 text-base font-medium text-gray-900">Aleatorio</span>
                            </label>

                            <label
                                class="flex items-center p-4 bg-white border-2 rounded-xl cursor-pointer hover:bg-teal-50 hover:border-teal-300 transition-all
                                                                                                                {{ $tipoMonitoreo === 'Auditoría' ? 'border-teal-500 bg-teal-50 ring-1 ring-teal-500' : 'border-gray-200' }}">
                                <input type="radio" wire:model.live="tipoMonitoreo" value="Auditoría"
                                    class="w-5 h-5 text-teal-600 focus:ring-teal-500 border-gray-300">
                                <span class="ml-3 text-base font-medium text-gray-900">Auditoría</span>
                            </label>
                        </div>
                        @error('tipoMonitoreo')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Botones / Mensaje de éxito -->
                @if($guardado)
                    <div
                        class="mt-8 bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-500 rounded-2xl p-8 text-center">
                        <div class="flex justify-center mb-4">
                            <div class="bg-green-500 rounded-full p-4">
                                <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-2xl font-bold text-green-800 mb-2">¡Auditoría de Crosselling Guardada!</h3>
                        <p class="text-green-700 mb-6">Los datos se guardaron correctamente en el sistema.</p>
                        <button wire:click="resetear" type="button"
                            class="bg-gradient-to-r from-green-600 to-teal-600 hover:from-green-700 hover:to-teal-700 text-white px-10 py-4 rounded-xl font-bold text-lg transition transform hover:scale-105 shadow-lg">
                            ← Volver a Selección de Llamadas
                        </button>
                    </div>
                @else
                    <div class="mt-8 flex justify-end gap-4">
                        <button wire:click="siguiente" wire:loading.attr="disabled"
                            class="bg-gradient-to-r from-green-600 to-teal-600 hover:from-green-700 hover:to-teal-700 text-white px-8 py-3 rounded-lg font-semibold transition transform hover:scale-105 flex items-center gap-2">
                            <span wire:loading.remove wire:target="siguiente">Continuar</span>
                            <span wire:loading wire:target="siguiente">Cargando...</span>
                            <svg wire:loading.remove wire:target="siguiente" class="w-5 h-5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </button>
                    </div>
                @endif
            </div>
        @endif

        <!-- Paso 2: Producto Ofertado -->
        @if($paso === 2)
            <div wire:key="paso-2" class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                <div class="mb-8 pb-6 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900">PRODUCTO OFERTADO</h2>
                    <p class="text-gray-500 text-sm mt-1">Ingresa el producto que se ofreció en la llamada</p>
                </div>

                <div class="mb-8">
                    <label class="block text-lg font-medium text-gray-800 mb-4">
                        Producto Ofertado <span class="text-red-500">*</span>
                    </label>
                    <input type="text" wire:model.live="productoOfertado"
                        class="w-full px-6 py-4 border-2 border-gray-300 rounded-xl text-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all">
                    @error('productoOfertado')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Botones de Navegación -->
                <div class="flex justify-between items-center gap-4">
                    <button wire:click="retroceder" type="button"
                        class="bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-lg font-semibold transition transform hover:scale-105 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                        </svg>
                        ATRÁS
                    </button>

                    <button wire:click="siguiente" wire:loading.attr="disabled"
                        class="bg-gradient-to-r from-green-600 to-teal-600 hover:from-green-700 hover:to-teal-700 text-white px-8 py-3 rounded-lg font-semibold transition transform hover:scale-105 flex items-center gap-2">
                        <span wire:loading.remove wire:target="siguiente">CONTINUAR</span>
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

        <!-- Paso 3: Evaluación (según plantilla) -->
        @if($paso === 3)
            <div wire:key="paso-3" class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                <div class="mb-8 pb-6 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900">Evaluación - Protocolos y Buenas Prácticas</h2>
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
                                                class="w-5 h-5 text-green-600 focus:ring-green-500 border-gray-300 cursor-pointer">
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
                        class="bg-gradient-to-r from-green-600 to-teal-600 hover:from-green-700 hover:to-teal-700 text-white px-8 py-3 rounded-lg font-semibold transition transform hover:scale-105 flex items-center gap-2">
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

        <!-- Paso 4: PEC-UF - Precisión Errores Críticos del Usuario Final -->
        @if($paso === 4)
            <div wire:key="paso-4" class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                <div class="mb-8 pb-6 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900">PEC-UF: PRECISIÓN ERRORES CRÍTICOS DEL USUARIO FINAL</h2>
                    <p class="text-gray-500 text-sm mt-1">Marca si cumple / no cumple / no aplica para cada ítem</p>
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
                                                class="w-5 h-5 text-green-600 focus:ring-green-500 border-gray-300 cursor-pointer">
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
                        class="bg-gradient-to-r from-green-600 to-teal-600 hover:from-green-700 hover:to-teal-700 text-white px-8 py-3 rounded-lg font-semibold transition transform hover:scale-105 flex items-center gap-2">
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

        <!-- Paso 5: PEC-NEG - Precisión Errores Críticos del Negocio -->
        @if($paso === 5)
            <div wire:key="paso-5" class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                <div class="mb-8 pb-6 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900">PEC-NEG: PRECISIÓN ERRORES CRÍTICOS DEL NEGOCIO</h2>
                    <p class="text-gray-500 text-sm mt-1">Marca si cumple / no cumple / no aplica para cada ítem</p>
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
                                                class="w-5 h-5 text-green-600 focus:ring-green-500 border-gray-300 cursor-pointer">
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
                        class="bg-gradient-to-r from-green-600 to-teal-600 hover:from-green-700 hover:to-teal-700 text-white px-8 py-3 rounded-lg font-semibold transition transform hover:scale-105 flex items-center gap-2">
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

        <!-- Paso 6: PEC CUM - Manejo de Información Confidencial -->
        @if($paso === 6)
            <div wire:key="paso-6" class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                <div class="mb-8 pb-6 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900">PEC CUM - MANEJO DE INFORMACIÓN CONFIDENCIAL</h2>
                    <p class="text-gray-500 text-sm mt-1">Marca si cumple / no cumple / no aplica para cada ítem</p>
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
                                                class="w-5 h-5 text-green-600 focus:ring-green-500 border-gray-300 cursor-pointer">
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
                        class="bg-gradient-to-r from-green-600 to-teal-600 hover:from-green-700 hover:to-teal-700 text-white px-8 py-3 rounded-lg font-semibold transition transform hover:scale-105 flex items-center gap-2">
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

        <!-- Paso 7: Novedades Críticas -->
        @if($paso === 7)
            <div wire:key="paso-7" class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                <div class="mb-8 pb-6 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900">DETALLAR LAS NOVEDADES CRÍTICAS PRESENTADAS EN LA LLAMADA
                    </h2>
                    <p class="text-gray-500 text-sm mt-1">Describe cualquier situación crítica o importante que haya
                        ocurrido durante la llamada</p>
                </div>

                <div class="mb-8">
                    <textarea wire:model.live="novedadesCriticas" rows="6"
                        placeholder="Describe las novedades críticas presentadas en la llamada..."
                        class="w-full px-6 py-4 border-2 border-gray-300 rounded-xl text-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all resize-none"></textarea>
                    @error('novedadesCriticas')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

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
                        class="bg-gradient-to-r from-green-600 to-teal-600 hover:from-green-700 hover:to-teal-700 text-white px-8 py-3 rounded-lg font-semibold transition transform hover:scale-105 flex items-center gap-2">
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

        <!-- Paso 8: Concretó la venta -->
        @if($paso === 8)
            <div wire:key="paso-8" class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                <div class="mb-8 pb-6 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900">Concretó la venta en la llamada</h2>
                    <p class="text-gray-500 text-sm mt-1">Selecciona si se concretó la venta durante la llamada</p>
                </div>

                <div class="mb-8 space-y-4">
                    <label
                        class="flex items-center p-4 border-2 border-gray-300 rounded-xl cursor-pointer hover:bg-gray-50 transition"
                        :class="{'border-green-500 bg-green-50': $wire.concretoVenta === 'SI'}">
                        <input type="radio" wire:model.live="concretoVenta" value="SI"
                            class="w-5 h-5 text-green-600 focus:ring-green-500 border-gray-300 cursor-pointer">
                        <span class="ml-3 text-lg font-medium text-gray-700">SI</span>
                    </label>

                    <label
                        class="flex items-center p-4 border-2 border-gray-300 rounded-xl cursor-pointer hover:bg-gray-50 transition"
                        :class="{'border-red-500 bg-red-50': $wire.concretoVenta === 'NO'}">
                        <input type="radio" wire:model.live="concretoVenta" value="NO"
                            class="w-5 h-5 text-red-600 focus:ring-red-500 border-gray-300 cursor-pointer">
                        <span class="ml-3 text-lg font-medium text-gray-700">NO</span>
                    </label>

                    <label
                        class="flex items-center p-4 border-2 border-gray-300 rounded-xl cursor-pointer hover:bg-gray-50 transition"
                        :class="{'border-gray-500 bg-gray-50': $wire.concretoVenta === 'NO APLICA'}">
                        <input type="radio" wire:model.live="concretoVenta" value="NO APLICA"
                            class="w-5 h-5 text-gray-600 focus:ring-gray-500 border-gray-300 cursor-pointer">
                        <span class="ml-3 text-lg font-medium text-gray-700">NO APLICA</span>
                    </label>

                    @error('concretoVenta')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

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
                        class="bg-gradient-to-r from-green-600 to-teal-600 hover:from-green-700 hover:to-teal-700 text-white px-8 py-3 rounded-lg font-semibold transition transform hover:scale-105 flex items-center gap-2">
                        <span wire:loading.remove wire:target="siguiente">SIGUIENTE</span>
                        <span wire:loading wire:target="siguiente">Procesando...</span>
                        <svg wire:loading.remove wire:target="siguiente" class="w-5 h-5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        <!-- Paso 9: Contenido según rama -->
        @if($paso === 9)
            @if($ramaFlujo === 'venta_si')
                <!-- Paso 9A: Instalación del Servicio (Rama SI) -->
                <div wire:key="paso-9" class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                    <div class="mb-8 pb-6 border-b border-gray-200">
                        <h2 class="text-2xl font-bold text-gray-900">¿SE INSTALÓ EL SERVICIO?</h2>
                        <p class="text-gray-500 text-sm mt-1">Información sobre la instalación y entrega del servicio</p>
                    </div>

                    <!-- Sección 1: Preguntas de Instalación -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Instalación del Servicio</h3>
                        <div
                            class="grid grid-cols-12 gap-4 mt-2 text-sm font-medium text-gray-500 uppercase tracking-wider text-center">
                            <div class="col-span-6 text-left">Ítem</div>
                            <div class="col-span-2">SI</div>
                            <div class="col-span-2">NO</div>
                            <div class="col-span-2">NO APLICA</div>
                        </div>

                        <div
                            class="grid grid-cols-12 gap-4 items-center py-3 border-b border-gray-100 hover:bg-gray-50 transition">
                            <div class="col-span-6 text-gray-700">¿SE INSTALÓ EL SERVICIO?</div>
                            <div class="col-span-2 flex justify-center">
                                <input type="radio" wire:model.live="seInstaloServicio" value="SI"
                                    class="w-5 h-5 text-green-600 focus:ring-green-500 border-gray-300 cursor-pointer">
                            </div>
                            <div class="col-span-2 flex justify-center">
                                <input type="radio" wire:model.live="seInstaloServicio" value="NO"
                                    class="w-5 h-5 text-red-600 focus:ring-red-500 border-gray-300 cursor-pointer">
                            </div>
                            <div class="col-span-2 flex justify-center">
                                <input type="radio" wire:model.live="seInstaloServicio" value="NO APLICA"
                                    class="w-5 h-5 text-gray-600 focus:ring-gray-500 border-gray-300 cursor-pointer">
                            </div>
                        </div>

                        <div
                            class="grid grid-cols-12 gap-4 items-center py-3 border-b border-gray-100 hover:bg-gray-50 transition">
                            <div class="col-span-6 text-gray-700">¿SE ENTREGÓ EQUIPO O CHIP?</div>
                            <div class="col-span-2 flex justify-center">
                                <input type="radio" wire:model.live="seEntregoEquipoChip" value="SI"
                                    class="w-5 h-5 text-green-600 focus:ring-green-500 border-gray-300 cursor-pointer">
                            </div>
                            <div class="col-span-2 flex justify-center">
                                <input type="radio" wire:model.live="seEntregoEquipoChip" value="NO"
                                    class="w-5 h-5 text-red-600 focus:ring-red-500 border-gray-300 cursor-pointer">
                            </div>
                            <div class="col-span-2 flex justify-center">
                                <input type="radio" wire:model.live="seEntregoEquipoChip" value="NO APLICA"
                                    class="w-5 h-5 text-gray-600 focus:ring-gray-500 border-gray-300 cursor-pointer">
                            </div>
                        </div>

                        <div
                            class="grid grid-cols-12 gap-4 items-center py-3 border-b border-gray-100 hover:bg-gray-50 transition">
                            <div class="col-span-6 text-gray-700">¿SE ACTIVÓ EL CHIP?</div>
                            <div class="col-span-2 flex justify-center">
                                <input type="radio" wire:model.live="seActivoChip" value="SI"
                                    class="w-5 h-5 text-green-600 focus:ring-green-500 border-gray-300 cursor-pointer">
                            </div>
                            <div class="col-span-2 flex justify-center">
                                <input type="radio" wire:model.live="seActivoChip" value="NO"
                                    class="w-5 h-5 text-red-600 focus:ring-red-500 border-gray-300 cursor-pointer">
                            </div>
                            <div class="col-span-2 flex justify-center">
                                <input type="radio" wire:model.live="seActivoChip" value="NO APLICA"
                                    class="w-5 h-5 text-gray-600 focus:ring-gray-500 border-gray-300 cursor-pointer">
                            </div>
                        </div>
                    </div>

                    <div class="mb-8">
                        <label class="block text-lg font-medium text-gray-800 mb-4">
                            Quien es el Responsable de Entrega
                        </label>
                        <input type="text" wire:model.live="responsableEntrega"
                            class="w-full px-6 py-4 border-2 border-gray-300 rounded-xl text-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all">
                    </div>

                    <div class="mb-8">
                        <label class="block text-lg font-medium text-gray-800 mb-4">
                            Porque no se realizó la entrega del chip, equipo, etc?
                        </label>
                        <textarea wire:model.live="razonNoEntrega" rows="3"
                            class="w-full px-6 py-4 border-2 border-gray-300 rounded-xl text-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all resize-none"></textarea>
                    </div>

                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Seguimiento</h3>
                        <div
                            class="grid grid-cols-12 gap-4 mt-2 text-sm font-medium text-gray-500 uppercase tracking-wider text-center">
                            <div class="col-span-6 text-left">Ítem</div>
                            <div class="col-span-2">SI</div>
                            <div class="col-span-2">NO</div>
                            <div class="col-span-2">NO APLICA</div>
                        </div>

                        <div
                            class="grid grid-cols-12 gap-4 items-center py-3 border-b border-gray-100 hover:bg-gray-50 transition">
                            <div class="col-span-6 text-gray-700">Asesor realizó Seguimiento?</div>
                            <div class="col-span-2 flex justify-center">
                                <input type="radio" wire:model.live="asesorRealizoSeguimiento" value="SI"
                                    class="w-5 h-5 text-green-600 focus:ring-green-500 border-gray-300 cursor-pointer">
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

                        <div
                            class="grid grid-cols-12 gap-4 items-center py-3 border-b border-gray-100 hover:bg-gray-50 transition">
                            <div class="col-span-6 text-gray-700">Venta fue recuperada?</div>
                            <div class="col-span-2 flex justify-center">
                                <input type="radio" wire:model.live="ventaRecuperada" value="SI"
                                    class="w-5 h-5 text-green-600 focus:ring-green-500 border-gray-300 cursor-pointer">
                            </div>
                            <div class="col-span-2 flex justify-center">
                                <input type="radio" wire:model.live="ventaRecuperada" value="NO"
                                    class="w-5 h-5 text-red-600 focus:ring-red-500 border-gray-300 cursor-pointer">
                            </div>
                            <div class="col-span-2 flex justify-center">
                                <input type="radio" wire:model.live="ventaRecuperada" value="NO APLICA"
                                    class="w-5 h-5 text-gray-600 focus:ring-gray-500 border-gray-300 cursor-pointer">
                            </div>
                        </div>

                        <div
                            class="grid grid-cols-12 gap-4 items-center py-3 border-b border-gray-100 hover:bg-gray-50 transition">
                            <div class="col-span-6 text-gray-700">Solicitó fue ingresada en meses anteriores</div>
                            <div class="col-span-2 flex justify-center">
                                <input type="radio" wire:model.live="solicitudMesesAnteriores" value="SI"
                                    class="w-5 h-5 text-green-600 focus:ring-green-500 border-gray-300 cursor-pointer">
                            </div>
                            <div class="col-span-2 flex justify-center">
                                <input type="radio" wire:model.live="solicitudMesesAnteriores" value="NO"
                                    class="w-5 h-5 text-red-600 focus:ring-red-500 border-gray-300 cursor-pointer">
                            </div>
                            <div class="col-span-2 flex justify-center">
                                <input type="radio" wire:model.live="solicitudMesesAnteriores" value="NO APLICA"
                                    class="w-5 h-5 text-gray-600 focus:ring-gray-500 border-gray-300 cursor-pointer">
                            </div>
                        </div>
                    </div>

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
                            class="bg-gradient-to-r from-green-600 to-teal-600 hover:from-green-700 hover:to-teal-700 text-white px-8 py-3 rounded-lg font-semibold transition transform hover:scale-105 flex items-center gap-2">
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
            @elseif($ramaFlujo === 'venta_no')
                <!-- Paso 9B: Causa Raíz Principal (Rama NO) -->
                <div wire:key="paso-9-no" class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                    <div class="mb-8 pb-6 border-b border-gray-200">
                        <h2 class="text-2xl font-bold text-gray-900">Causa Raíz Principal</h2>
                        <p class="text-gray-500 text-sm mt-1">Selecciona la causa principal por la que no se concretó la venta
                        </p>
                    </div>

                    <div class="mb-8 space-y-4">
                        <label
                            class="flex items-center p-4 border-2 border-gray-300 rounded-xl cursor-pointer hover:bg-gray-50 transition"
                            :class="{'border-blue-500 bg-blue-50': $wire.causaRaizPrincipal === 'Proceso'}">
                            <input type="radio" wire:model.live="causaRaizPrincipal" value="Proceso"
                                class="w-5 h-5 text-blue-600 focus:ring-blue-500 border-gray-300 cursor-pointer">
                            <span class="ml-3 text-lg font-medium text-gray-700">Proceso</span>
                        </label>

                        <label
                            class="flex items-center p-4 border-2 border-gray-300 rounded-xl cursor-pointer hover:bg-gray-50 transition"
                            :class="{'border-orange-500 bg-orange-50': $wire.causaRaizPrincipal === 'Agente'}">
                            <input type="radio" wire:model.live="causaRaizPrincipal" value="Agente"
                                class="w-5 h-5 text-orange-600 focus:ring-orange-500 border-gray-300 cursor-pointer">
                            <span class="ml-3 text-lg font-medium text-gray-700">Agente</span>
                        </label>

                        <label
                            class="flex items-center p-4 border-2 border-gray-300 rounded-xl cursor-pointer hover:bg-gray-50 transition"
                            :class="{'border-purple-500 bg-purple-50': $wire.causaRaizPrincipal === 'Cliente'}">
                            <input type="radio" wire:model.live="causaRaizPrincipal" value="Cliente"
                                class="w-5 h-5 text-purple-600 focus:ring-purple-500 border-gray-300 cursor-pointer">
                            <span class="ml-3 text-lg font-medium text-gray-700">Cliente</span>
                        </label>

                        @error('causaRaizPrincipal')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

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
                            class="bg-gradient-to-r from-green-600 to-teal-600 hover:from-green-700 hover:to-teal-700 text-white px-8 py-3 rounded-lg font-semibold transition transform hover:scale-105 flex items-center gap-2">
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
        @endif

        <!-- Paso 10: Contenido según rama -->
        @if($paso === 10)
            @if($ramaFlujo === 'venta_si')
                <!-- Paso 10A: Observaciones PostVenta (Rama SI) -->
                <div wire:key="paso-10-si" class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                    <div class="mb-8 pb-6 border-b border-gray-200">
                        <h2 class="text-2xl font-bold text-gray-900">Detalla las observaciones en la PostVenta</h2>
                        <p class="text-gray-500 text-sm mt-1">Describe cualquier observación relevante sobre el proceso de
                            postventa</p>
                    </div>

                    <div class="mb-8">
                        <textarea wire:model.live="observacionesPostVenta" rows="6"
                            placeholder="Escribe las observaciones sobre la postventa..."
                            class="w-full px-6 py-4 border-2 border-gray-300 rounded-xl text-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all resize-none"></textarea>
                    </div>

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
                            class="bg-gradient-to-r from-green-600 to-teal-600 hover:from-green-700 hover:to-teal-700 text-white px-8 py-3 rounded-lg font-semibold transition transform hover:scale-105 flex items-center gap-2">
                            <span wire:loading.remove wire:target="guardarDatos">ENVIAR</span>
                            <span wire:loading wire:target="guardarDatos">Guardando...</span>
                            <svg wire:loading.remove wire:target="guardarDatos" class="w-5 h-5" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            @elseif($ramaFlujo === 'venta_no')
                <!-- Paso 10B: Detalles según Causa Raíz (Rama NO) -->
                <div wire:key="paso-10-no" class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                    <div class="mb-8 pb-6 border-b border-gray-200">
                        <h2 class="text-2xl font-bold text-gray-900">{{ strtoupper($causaRaizPrincipal) }}</h2>
                        <p class="text-gray-500 text-sm mt-1">Detalla la información sobre {{ $causaRaizPrincipal }}</p>
                    </div>

                    <!-- Campo de texto dinámico según causa raíz -->
                    <div class="mb-8">
                        <label class="block text-lg font-medium text-gray-800 mb-4">
                            Detalles de {{ $causaRaizPrincipal }}
                        </label>
                        <textarea wire:model.live="detallesCausaRaiz" rows="4"
                            placeholder="Describe los detalles de {{ $causaRaizPrincipal }}..."
                            class="w-full px-6 py-4 border-2 border-gray-300 rounded-xl text-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all resize-none"></textarea>
                    </div>

                    <!-- Sección: ¿SE INSTALÓ EL SERVICIO? -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">¿SE INSTALÓ EL SERVICIO?</h3>
                        <div
                            class="grid grid-cols-12 gap-4 mt-2 text-sm font-medium text-gray-500 uppercase tracking-wider text-center">
                            <div class="col-span-6 text-left">Ítem</div>
                            <div class="col-span-2">SI</div>
                            <div class="col-span-2">NO</div>
                            <div class="col-span-2">NO APLICA</div>
                        </div>

                        <div
                            class="grid grid-cols-12 gap-4 items-center py-3 border-b border-gray-100 hover:bg-gray-50 transition">
                            <div class="col-span-6 text-gray-700">¿SE INSTALÓ EL SERVICIO?</div>
                            <div class="col-span-2 flex justify-center">
                                <input type="radio" wire:model.live="p10_seInstaloServicio" value="SI"
                                    class="w-5 h-5 text-green-600 focus:ring-green-500 border-gray-300 cursor-pointer">
                            </div>
                            <div class="col-span-2 flex justify-center">
                                <input type="radio" wire:model.live="p10_seInstaloServicio" value="NO"
                                    class="w-5 h-5 text-red-600 focus:ring-red-500 border-gray-300 cursor-pointer">
                            </div>
                            <div class="col-span-2 flex justify-center">
                                <input type="radio" wire:model.live="p10_seInstaloServicio" value="NO APLICA"
                                    class="w-5 h-5 text-gray-600 focus:ring-gray-500 border-gray-300 cursor-pointer">
                            </div>
                        </div>

                        <div
                            class="grid grid-cols-12 gap-4 items-center py-3 border-b border-gray-100 hover:bg-gray-50 transition">
                            <div class="col-span-6 text-gray-700">¿SE ENTREGÓ EQUIPO O CHIP?</div>
                            <div class="col-span-2 flex justify-center">
                                <input type="radio" wire:model.live="p10_seEntregoEquipoChip" value="SI"
                                    class="w-5 h-5 text-green-600 focus:ring-green-500 border-gray-300 cursor-pointer">
                            </div>
                            <div class="col-span-2 flex justify-center">
                                <input type="radio" wire:model.live="p10_seEntregoEquipoChip" value="NO"
                                    class="w-5 h-5 text-red-600 focus:ring-red-500 border-gray-300 cursor-pointer">
                            </div>
                            <div class="col-span-2 flex justify-center">
                                <input type="radio" wire:model.live="p10_seEntregoEquipoChip" value="NO APLICA"
                                    class="w-5 h-5 text-gray-600 focus:ring-gray-500 border-gray-300 cursor-pointer">
                            </div>
                        </div>

                        <div
                            class="grid grid-cols-12 gap-4 items-center py-3 border-b border-gray-100 hover:bg-gray-50 transition">
                            <div class="col-span-6 text-gray-700">¿SE ACTIVÓ EL CHIP?</div>
                            <div class="col-span-2 flex justify-center">
                                <input type="radio" wire:model.live="p10_seActivoChip" value="SI"
                                    class="w-5 h-5 text-green-600 focus:ring-green-500 border-gray-300 cursor-pointer">
                            </div>
                            <div class="col-span-2 flex justify-center">
                                <input type="radio" wire:model.live="p10_seActivoChip" value="NO"
                                    class="w-5 h-5 text-red-600 focus:ring-red-500 border-gray-300 cursor-pointer">
                            </div>
                            <div class="col-span-2 flex justify-center">
                                <input type="radio" wire:model.live="p10_seActivoChip" value="NO APLICA"
                                    class="w-5 h-5 text-gray-600 focus:ring-gray-500 border-gray-300 cursor-pointer">
                            </div>
                        </div>
                    </div>

                    <!-- Campos de texto -->
                    <div class="mb-8">
                        <label class="block text-lg font-medium text-gray-800 mb-4">
                            Quien es el Responsable de Entrega
                        </label>
                        <input type="text" wire:model.live="p10_responsableEntrega"
                            class="w-full px-6 py-4 border-2 border-gray-300 rounded-xl text-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all">
                    </div>

                    <div class="mb-8">
                        <label class="block text-lg font-medium text-gray-800 mb-4">
                            Porque no se realizó la entrega del chip, equipo, etc?
                        </label>
                        <textarea wire:model.live="p10_razonNoEntrega" rows="3"
                            class="w-full px-6 py-4 border-2 border-gray-300 rounded-xl text-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all resize-none"></textarea>
                    </div>

                    <!-- Sección: Seguimiento -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Seguimiento</h3>
                        <div
                            class="grid grid-cols-12 gap-4 mt-2 text-sm font-medium text-gray-500 uppercase tracking-wider text-center">
                            <div class="col-span-6 text-left">Ítem</div>
                            <div class="col-span-2">SI</div>
                            <div class="col-span-2">NO</div>
                            <div class="col-span-2">NO APLICA</div>
                        </div>

                        <div
                            class="grid grid-cols-12 gap-4 items-center py-3 border-b border-gray-100 hover:bg-gray-50 transition">
                            <div class="col-span-6 text-gray-700">Asesor realizó Seguimiento?</div>
                            <div class="col-span-2 flex justify-center">
                                <input type="radio" wire:model.live="p10_asesorRealizoSeguimiento" value="SI"
                                    class="w-5 h-5 text-green-600 focus:ring-green-500 border-gray-300 cursor-pointer">
                            </div>
                            <div class="col-span-2 flex justify-center">
                                <input type="radio" wire:model.live="p10_asesorRealizoSeguimiento" value="NO"
                                    class="w-5 h-5 text-red-600 focus:ring-red-500 border-gray-300 cursor-pointer">
                            </div>
                            <div class="col-span-2 flex justify-center">
                                <input type="radio" wire:model.live="p10_asesorRealizoSeguimiento" value="NO APLICA"
                                    class="w-5 h-5 text-gray-600 focus:ring-gray-500 border-gray-300 cursor-pointer">
                            </div>
                        </div>

                        <div
                            class="grid grid-cols-12 gap-4 items-center py-3 border-b border-gray-100 hover:bg-gray-50 transition">
                            <div class="col-span-6 text-gray-700">Venta fue recuperada?</div>
                            <div class="col-span-2 flex justify-center">
                                <input type="radio" wire:model.live="p10_ventaRecuperada" value="SI"
                                    class="w-5 h-5 text-green-600 focus:ring-green-500 border-gray-300 cursor-pointer">
                            </div>
                            <div class="col-span-2 flex justify-center">
                                <input type="radio" wire:model.live="p10_ventaRecuperada" value="NO"
                                    class="w-5 h-5 text-red-600 focus:ring-red-500 border-gray-300 cursor-pointer">
                            </div>
                            <div class="col-span-2 flex justify-center">
                                <input type="radio" wire:model.live="p10_ventaRecuperada" value="NO APLICA"
                                    class="w-5 h-5 text-gray-600 focus:ring-gray-500 border-gray-300 cursor-pointer">
                            </div>
                        </div>

                        <div
                            class="grid grid-cols-12 gap-4 items-center py-3 border-b border-gray-100 hover:bg-gray-50 transition">
                            <div class="col-span-6 text-gray-700">Solicitó fue ingresada en meses anteriores</div>
                            <div class="col-span-2 flex justify-center">
                                <input type="radio" wire:model.live="p10_solicitudMesesAnteriores" value="SI"
                                    class="w-5 h-5 text-green-600 focus:ring-green-500 border-gray-300 cursor-pointer">
                            </div>
                            <div class="col-span-2 flex justify-center">
                                <input type="radio" wire:model.live="p10_solicitudMesesAnteriores" value="NO"
                                    class="w-5 h-5 text-red-600 focus:ring-red-500 border-gray-300 cursor-pointer">
                            </div>
                            <div class="col-span-2 flex justify-center">
                                <input type="radio" wire:model.live="p10_solicitudMesesAnteriores" value="NO APLICA"
                                    class="w-5 h-5 text-gray-600 focus:ring-gray-500 border-gray-300 cursor-pointer">
                            </div>
                        </div>
                    </div>

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
                            class="bg-gradient-to-r from-green-600 to-teal-600 hover:from-green-700 hover:to-teal-700 text-white px-8 py-3 rounded-lg font-semibold transition transform hover:scale-105 flex items-center gap-2">
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
        @endif

        <!-- Paso 11: Observaciones PostVenta Final (rama NO) -->
        @if($paso === 11 && $ramaFlujo === 'venta_no')
            <div wire:key="paso-11" class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                <div class="mb-8 pb-6 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900">Detalla las observaciones en la PostVenta</h2>
                    <p class="text-gray-500 text-sm mt-1">Observaciones finales sobre el proceso</p>
                </div>

                <div class="mb-8">
                    <textarea wire:model.live="observacionesPostVentaFinal" rows="6"
                        placeholder="Escribe las observaciones finales..."
                        class="w-full px-6 py-4 border-2 border-gray-300 rounded-xl text-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all resize-none"></textarea>
                </div>

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
                        class="bg-gradient-to-r from-green-600 to-teal-600 hover:from-green-700 hover:to-teal-700 text-white px-8 py-3 rounded-lg font-semibold transition transform hover:scale-105 flex items-center gap-2">
                        <span wire:loading.remove wire:target="guardarDatos">ENVIAR</span>
                        <span wire:loading wire:target="guardarDatos">Guardando...</span>
                        <svg wire:loading.remove wire:target="guardarDatos" class="w-5 h-5" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        <!-- Paso 99: Pantalla de Éxito -->
        @if($paso === 99)
            <div wire:key="paso-99" class="bg-white rounded-2xl shadow-xl p-12 border border-gray-100 text-center">
                <div class="mb-8">
                    <div class="mx-auto w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">¡Auditoría Registrada Correctamente!</h2>
                    <p class="text-gray-600 text-lg mb-8">
                        La auditoría ha sido guardada exitosamente en el sistema.
                    </p>
                </div>

                <div class="flex justify-center gap-4">
                    <a href="/auditoria/nueva"
                        class="bg-gradient-to-r from-green-600 to-teal-600 hover:from-green-700 hover:to-teal-700 text-white px-10 py-4 rounded-lg font-semibold text-lg transition transform hover:scale-105 flex items-center gap-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Registrar Nueva Auditoría
                    </a>
                </div>
            </div>
        @endif
    </div>

    {{-- Componente de Historial --}}
    @livewire('historial-auditorias')
</div>