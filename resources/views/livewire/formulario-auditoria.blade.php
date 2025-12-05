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
                <div class="h-full bg-gradient-to-r from-blue-500 to-indigo-600 transition-all duration-500 ease-out shadow-lg"
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
                    <h1 class="text-3xl font-black text-gray-900 mb-2">Formulario de Auditoría</h1>
                    <div class="flex items-center gap-3">
                        <span
                            class="bg-gradient-to-r from-blue-500 to-purple-500 text-white px-4 py-1.5 rounded-full text-sm font-semibold">
                            @if($paso === 0)
                                Selección de Llamada
                            @elseif($paso === 99)
                                Auditoría Completada
                            @else
                                Paso {{ $paso }} de 12
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
                <h2 class="text-2xl font-semibold text-gray-800 mb-6 text-center">Selecciona el método de carga</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <!-- Opción Manual -->
                    <div class="border-2 border-gray-200 rounded-lg p-6 hover:border-blue-500 transition">
                        <div class="text-center mb-4">
                            <svg class="w-16 h-16 mx-auto text-blue-600 mb-3" fill="none" stroke="currentColor"
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
                            <input type="text" wire:model.live="idCicManual" placeholder="Ej: 123456789"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('idCicManual')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <button wire:click="seleccionarManual"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition">
                            Cargar ID Manual
                        </button>
                    </div>

                    <!-- Opción Aleatoria -->
                    <div class="border-2 border-gray-200 rounded-lg p-6 hover:border-purple-500 transition">
                        <div class="text-center mb-4">
                            <svg class="w-16 h-16 mx-auto text-purple-600 mb-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                </path>
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-800">Selección Aleatoria</h3>
                            <p class="text-sm text-gray-600 mt-2">Carga una llamada al azar</p>
                        </div>

                        <!-- Filtros Opcionales -->
                        <div class="mb-4 space-y-3">
                            <h4 class="text-sm font-semibold text-purple-700">Filtros Opcionales:</h4>

                            <!-- Filtro Campaña -->
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Campaña</label>
                                <select wire:model.live="filtroCampana"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                    <option value="">Todas las campañas</option>
                                    @foreach($campanasDisponibles as $campana)
                                        <option value="{{ $campana }}">{{ $campana }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Filtro DNI -->
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">DNI del Empleado</label>
                                <input type="text" wire:model.live="filtroDni" placeholder="Ej: 12345678"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            </div>

                            <!-- Filtro Fecha -->
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Fecha Específica</label>
                                <input type="date" wire:model.live="filtroFecha" min="2025-10-01" max="{{ date('Y-m-d') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                @error('filtroFecha')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Botón limpiar filtros -->
                            @if($filtroCampana || $filtroDni || $filtroFecha)
                                <button wire:click="limpiarFiltros" type="button"
                                    class="text-xs text-purple-600 hover:text-purple-800 underline">
                                    Limpiar filtros
                                </button>
                            @endif
                        </div>

                        <button wire:click="seleccionarAleatorio"
                            class="w-full bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition transform hover:scale-105">
                            Cargar Aleatorio
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- Paso 1: Datos de la Llamada -->
        @if($paso === 1)
            <div wire:key="paso-1" class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                <div class="flex justify-between items-center mb-8 pb-6 border-b border-gray-200">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Datos de la Llamada</h2>
                        <p class="text-gray-500 text-sm mt-1">Información de la llamada seleccionada</p>
                    </div>
                    @if($paso === 1 && !$guardado && !$bloquearRandomizar)
                        <button wire:click="randomizar"
                            class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 hover:shadow-lg flex items-center gap-2">
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
                    @foreach($preguntasPaso1 as $pregunta)
                        @php
                            // Mapear pregunta a propiedad del componente
                            $valor = match ($pregunta->texto) {
                                'Nombre del Analista' => $nombreAnalista,
                                'ID de Interacción (CIC)' => $idCic,
                                'Teléfono' => $telefono,
                                'Fecha de Llamada' => $fechaLlamada,
                                'Fecha de Monitoreo' => $fechaMonitoreo,
                                'Duración de Llamada' => $duracionFormato,
                                'Nombre del Asesor' => $nombreAsesor,
                                'Usuario del Asesor' => $usuarioAsesor,
                                'Campaña' => $campana,
                                default => ''
                            };

                            // Determinar si el campo debe ocupar 2 columnas
                            $colSpan = in_array($pregunta->texto, ['Campaña']) ? 'md:col-span-2' : '';
                        @endphp

                        <div class="{{ $colSpan }}">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ $pregunta->texto }}
                                @if($pregunta->requerido)
                                    <span class="text-red-500">*</span>
                                @endif
                            </label>

                            @if($pregunta->tipo_campo === 'textarea')
                                <textarea readonly
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 resize-none"
                                    rows="3">{{ $valor }}</textarea>
                            @else
                                <input type="{{ $pregunta->tipo_campo }}" value="{{ $valor }}" readonly
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 
                                                                               {{ in_array($pregunta->texto, ['ID de Interacción (CIC)', 'Duración de Llamada']) ? 'font-mono' : '' }}
                                                                               {{ $pregunta->texto === 'Fecha de Monitoreo' ? 'bg-gray-100 font-semibold' : '' }}">
                            @endif
                        </div>
                    @endforeach
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
                                <h3 class="text-2xl font-bold text-green-800 mb-2">¡Auditoría Guardada Exitosamente!</h3>
                                <p class="text-green-700 mb-6">Los datos de la llamada se guardaron correctamente en el sistema.</p>
                                <button wire:click="resetear" type="button"
                                    class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-10 py-4 rounded-xl font-bold text-lg transition transform hover:scale-105 shadow-lg">
                                    ← Volver a Selección de Llamadas
                                </button>
                            </div>
                @else
                        <div class="mt-8 flex justify-end gap-4">
                            <button wire:click="siguiente" wire:loading.attr="disabled"
                                class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-8 py-3 rounded-lg font-semibold transition transform hover:scale-105 flex items-center gap-2">
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

        <!-- Paso 2: Preguntas Dinámicas -->
        @if($paso === 2)
            <div wire:key="paso-2" class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                <div class="mb-8 pb-6 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900">Evaluación de Calidad</h2>
                    <p class="text-gray-500 text-sm mt-1">Completa las siguientes preguntas</p>
                </div>

                <div class="space-y-6">
                    @php $seccionActual = null; @endphp
                    @foreach($preguntasPaso2 as $pregunta)
                        @if($seccionActual !== $pregunta->seccion)
                            @if($seccionActual !== null) </div></div> @endif
                            @php $seccionActual = $pregunta->seccion; @endphp
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">{{ $pregunta->seccion }}</h3>
                                <div class="space-y-4">
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ $pregunta->texto }}
                                @if($pregunta->requerido) <span class="text-red-500">*</span> @endif
                            </label>

                            @if($pregunta->tipo_campo === 'textarea')
                                <textarea wire:model.live="respuestasPaso2.{{ $pregunta->id }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    rows="3" {{ $pregunta->requerido ? 'required' : '' }}></textarea>
                            @elseif($pregunta->tipo_campo === 'radio')
                                @php
                                    $opciones = json_decode($pregunta->opciones, true) ?? [];
                                    $esFormatoNuevo = !empty($opciones) && is_array($opciones[0] ?? null);
                                @endphp
                                @if($esFormatoNuevo)
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        @foreach($opciones as $opcion)
                                                                <label class="relative flex items-center p-4 border-2 rounded-xl cursor-pointer transition-all
                                                                    {{ isset($respuestasPaso2[$pregunta->id]) && $respuestasPaso2[$pregunta->id] == $opcion['value']
                                            ? 'border-blue-500 bg-blue-50'
                                            : 'border-gray-300 hover:border-blue-300 hover:bg-gray-50' }}">
                                                                    <input type="radio" name="pregunta_{{ $pregunta->id }}"
                                                                           wire:model.live="respuestasPaso2.{{ $pregunta->id }}" 
                                                                           value="{{ $opcion['value'] }}"
                                                                           class="w-5 h-5 text-blue-600 focus:ring-blue-500">
                                                                    <span class="ml-3 text-base font-medium text-gray-900">{{ $opcion['label'] }}</span>
                                                                </label>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="space-y-2">
                                        @foreach($opciones as $opcion)
                                            <label class="flex items-center">
                                                <input type="radio" wire:model.live="respuestasPaso2.{{ $pregunta->id }}" value="{{ $opcion }}"
                                                    class="w-4 h-4 text-blue-600 focus:ring-blue-500">
                                                <span class="ml-2 text-gray-700">{{ $opcion }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                @endif
                            @else
                                <input type="{{ $pregunta->tipo_campo }}" wire:model.live="respuestasPaso2.{{ $pregunta->id }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    {{ $pregunta->requerido ? 'required' : '' }}>
                            @endif
                        </div>
                    @endforeach
                    @if($seccionActual !== null) </div></div> @endif

                    <div class="mt-8 flex justify-between gap-4">
                        <button wire:click="retroceder"
                            class="bg-gray-500 hover:bg-gray-600 text-white px-8 py-3 rounded-lg font-semibold transition flex items-center gap-2">
                            Retroceder
                        </button>
                        <button wire:click="siguiente" wire:loading.attr="disabled"
                            class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-8 py-3 rounded-lg font-semibold transition transform hover:scale-105 flex items-center gap-2">
                            <span wire:loading.remove wire:target="siguiente">Continuar</span>
                            <span wire:loading wire:target="siguiente">Cargando...</span>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- Paso 3: Cierre (Producto Ofertado) -->
        @if($paso === 3)
            <div wire:key="paso-3" class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                <div class="mb-8 pb-6 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900">Cierre de Auditoría</h2>
                    <p class="text-gray-500 text-sm mt-1">Información final</p>
                </div>
                <div class="space-y-6">
                    @foreach($preguntasPaso3 as $pregunta)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ $pregunta->texto }}
                                @if($pregunta->requerido) <span class="text-red-500">*</span> @endif
                            </label>
                            @if($pregunta->tipo_campo === 'textarea')
                                <textarea wire:model.live="respuestasPaso3.{{ $pregunta->id }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    rows="3" {{ $pregunta->requerido ? 'required' : '' }}></textarea>
                            @else
                                <input type="{{ $pregunta->tipo_campo }}" wire:model.live="respuestasPaso3.{{ $pregunta->id }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    {{ $pregunta->requerido ? 'required' : '' }}>
                            @endif
                        </div>
                    @endforeach
                </div>
                <div class="mt-8 flex justify-between gap-4">
                    <button wire:click="retroceder" class="bg-gray-500 hover:bg-gray-600 text-white px-8 py-3 rounded-lg font-semibold transition">Retroceder</button>
                    <button wire:click="siguiente" class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-8 py-3 rounded-lg font-semibold transition">Continuar</button>
                </div>
            </div>
        @endif

        <!-- Paso 4: Protocolos y Buenas Prácticas -->
        @if($paso === 4)
            <div wire:key="paso-4" class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                <div class="mb-8 pb-6 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900">PENC - ASESOR:Protocolos y Buenas Prácticas</h2>
                </div>
                <div class="space-y-8">
                    @php $currentSection = ''; @endphp
                    @foreach($preguntasPaso4 as $pregunta)
                        @if($currentSection !== $pregunta->seccion)
                            @php 
                                                    $currentSection = $pregunta->seccion;
                                $opcionesHeader = json_decode($pregunta->opciones, true) ?? [];
                            @endphp
                            <div class="mt-8 mb-4">
                                <h3 class="text-xl font-semibold text-gray-800 border-b pb-2">{{ $currentSection }}</h3>
                                <div class="grid grid-cols-12 gap-4 mt-4 text-sm font-medium text-gray-500 uppercase tracking-wider text-center">
                                    <div class="col-span-6 text-left">Pregunta</div>
                                    @foreach($opcionesHeader as $opcion)
                                        <div class="col-span-2">{{ $opcion['label'] }}</div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        <div class="grid grid-cols-12 gap-4 items-center py-3 border-b border-gray-100 hover:bg-gray-50 transition">
                            <div class="col-span-6 text-gray-700 font-medium">
                                {{ $pregunta->texto }}
                                @if($pregunta->requerido) <span class="text-red-500">*</span> @endif
                            </div>
                            @php $opciones = json_decode($pregunta->opciones, true) ?? []; @endphp
                            @foreach($opciones as $opcion)
                                <div class="col-span-2 flex justify-center">
                                    <label class="cursor-pointer">
                                        <input type="radio" name="pregunta_{{ $pregunta->id }}"
                                            wire:model.live="respuestasPaso4.{{ $pregunta->id }}" 
                                            value="{{ $opcion['value'] }}"
                                            class="w-6 h-6 text-blue-600 focus:ring-blue-500 border-gray-300">
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
                <div class="mt-8 flex justify-between gap-4">
                    <button wire:click="retroceder" class="bg-gray-500 hover:bg-gray-600 text-white px-8 py-3 rounded-lg font-semibold transition">Retroceder</button>
                    <button wire:click="siguiente" class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-8 py-3 rounded-lg font-semibold transition">Continuar</button>
                </div>
            </div>
        @endif

        <!-- Paso 5: Precisión y Errores Críticos -->
        @if($paso === 5)
            <div wire:key="paso-5" class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                <div class="mb-8 pb-6 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900">PEC-UF: Precisión y Errores Críticos del usuario final</h2>
                </div>
                <div class="space-y-8">
                    @php $currentSection = ''; @endphp
                    @foreach($preguntasPaso5 as $pregunta)
                        @if($currentSection !== $pregunta->seccion)
                            @php 
                                                    $currentSection = $pregunta->seccion;
                                $opcionesHeader = json_decode($pregunta->opciones, true) ?? [];
                            @endphp
                            <div class="mt-8 mb-4">
                                <h3 class="text-xl font-semibold text-gray-800 border-b pb-2">{{ $currentSection }}</h3>
                                <div class="grid grid-cols-12 gap-4 mt-4 text-sm font-medium text-gray-500 uppercase tracking-wider text-center">
                                    <div class="col-span-6 text-left">Pregunta</div>
                                    @foreach($opcionesHeader as $opcion)
                                        <div class="col-span-2">{{ $opcion['label'] }}</div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        <div class="grid grid-cols-12 gap-4 items-center py-3 border-b border-gray-100 hover:bg-gray-50 transition">
                            <div class="col-span-6 text-gray-700 font-medium">
                                {{ $pregunta->texto }}
                                @if($pregunta->requerido) <span class="text-red-500">*</span> @endif
                            </div>
                            @php $opciones = json_decode($pregunta->opciones, true) ?? []; @endphp
                            @foreach($opciones as $opcion)
                                <div class="col-span-2 flex justify-center">
                                    <label class="cursor-pointer">
                                        <input type="radio" name="pregunta_{{ $pregunta->id }}"
                                            wire:model.live="respuestasPaso5.{{ $pregunta->id }}" 
                                            value="{{ $opcion['value'] }}"
                                            class="w-6 h-6 text-blue-600 focus:ring-blue-500 border-gray-300">
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
                <div class="mt-8 flex justify-between gap-4">
                    <button wire:click="retroceder" class="bg-gray-500 hover:bg-gray-600 text-white px-8 py-3 rounded-lg font-semibold transition">Retroceder</button>
                    <button wire:click="siguiente" class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-8 py-3 rounded-lg font-semibold transition">Continuar</button>
                </div>
            </div>
        @endif

        <!-- Paso 6: Errores Críticos del Negocio -->
        @if($paso === 6)
            <div wire:key="paso-6" class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                <div class="mb-8 pb-6 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900">PEC-NEG:Precisión errores Críticos del Negocio</h2>
                </div>
                <div class="space-y-8">
                    @php $currentSection = ''; @endphp
                    @foreach($preguntasPaso6 as $pregunta)
                        @if($currentSection !== $pregunta->seccion)
                            @php 
                                                    $currentSection = $pregunta->seccion;
                                $opcionesHeader = json_decode($pregunta->opciones, true) ?? [];
                            @endphp
                            <div class="mt-8 mb-4">
                                <h3 class="text-xl font-semibold text-gray-800 border-b pb-2">{{ $currentSection }}</h3>
                                <div class="grid grid-cols-12 gap-4 mt-4 text-sm font-medium text-gray-500 uppercase tracking-wider text-center">
                                    <div class="col-span-6 text-left">Pregunta</div>
                                    @foreach($opcionesHeader as $opcion)
                                        <div class="col-span-2">{{ $opcion['label'] }}</div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        <div class="grid grid-cols-12 gap-4 items-center py-3 border-b border-gray-100 hover:bg-gray-50 transition">
                            <div class="col-span-6 text-gray-700 font-medium">
                                {{ $pregunta->texto }}
                                @if($pregunta->requerido) <span class="text-red-500">*</span> @endif
                            </div>
                            @php $opciones = json_decode($pregunta->opciones, true) ?? []; @endphp
                            @foreach($opciones as $opcion)
                                <div class="col-span-2 flex justify-center">
                                    <label class="cursor-pointer">
                                        <input type="radio" name="pregunta_{{ $pregunta->id }}"
                                            wire:model.live="respuestasPaso6.{{ $pregunta->id }}" 
                                            value="{{ $opcion['value'] }}"
                                            class="w-6 h-6 text-blue-600 focus:ring-blue-500 border-gray-300">
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
                <div class="mt-8 flex justify-between gap-4">
                    <button wire:click="retroceder" class="bg-gray-500 hover:bg-gray-600 text-white px-8 py-3 rounded-lg font-semibold transition">Retroceder</button>
                    <button wire:click="siguiente" class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-8 py-3 rounded-lg font-semibold transition">Continuar</button>
                </div>
            </div>
        @endif

        <!-- Paso 7: Manejo de información confidencial -->
        @if($paso === 7)
            <div wire:key="paso-7" class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                <div class="mb-8 pb-6 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900">PEC CUM - Manejo de información confidencial</h2>
                </div>
                <div class="space-y-8">
                    @php $currentSection = ''; @endphp
                    @foreach($preguntasPaso7 as $pregunta)
                        @if($currentSection !== $pregunta->seccion)
                            @php 
                                                    $currentSection = $pregunta->seccion;
                                $opcionesHeader = json_decode($pregunta->opciones, true) ?? [];
                            @endphp
                            <div class="mt-8 mb-4">
                                <h3 class="text-xl font-semibold text-gray-800 border-b pb-2">{{ $currentSection }}</h3>
                                <div class="grid grid-cols-12 gap-4 mt-4 text-sm font-medium text-gray-500 uppercase tracking-wider text-center">
                                    <div class="col-span-6 text-left">Pregunta</div>
                                    @foreach($opcionesHeader as $opcion)
                                        <div class="col-span-2">{{ $opcion['label'] }}</div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        <div class="grid grid-cols-12 gap-4 items-center py-3 border-b border-gray-100 hover:bg-gray-50 transition">
                            <div class="col-span-6 text-gray-700 font-medium">
                                {{ $pregunta->texto }}
                                @if($pregunta->requerido) <span class="text-red-500">*</span> @endif
                            </div>
                            @php $opciones = json_decode($pregunta->opciones, true) ?? []; @endphp
                            @foreach($opciones as $opcion)
                                <div class="col-span-2 flex justify-center">
                                    <label class="cursor-pointer">
                                        <input type="radio" name="pregunta_{{ $pregunta->id }}"
                                            wire:model.live="respuestasPaso7.{{ $pregunta->id }}" 
                                            value="{{ $opcion['value'] }}"
                                            class="w-6 h-6 text-blue-600 focus:ring-blue-500 border-gray-300">
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
                <div class="mt-8 flex justify-between gap-4">
                    <button wire:click="retroceder" class="bg-gray-500 hover:bg-gray-600 text-white px-8 py-3 rounded-lg font-semibold transition">Retroceder</button>
                    <button wire:click="siguiente" class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-8 py-3 rounded-lg font-semibold transition">Continuar</button>
                </div>
            </div>
        @endif

        <!-- Paso 8: Novedades Críticas -->
        @if($paso === 8)
            <div wire:key="paso-8" class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                <div class="mb-8 pb-6 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900">Novedades Críticas</h2>
                </div>
                <div class="space-y-8">
                    @foreach($preguntasPaso8 as $pregunta)
                        <div class="bg-gray-50 p-6 rounded-xl border border-gray-200">
                            <label class="block text-lg font-medium text-gray-800 mb-4">
                                {{ $pregunta->texto }}
                                @if($pregunta->requerido) <span class="text-red-500">*</span> @endif
                            </label>
                            <textarea wire:model.live="respuestasPaso8.{{ $pregunta->id }}"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition"
                                rows="6" placeholder="Detalla las novedades críticas aquí..."></textarea>
                        </div>
                    @endforeach
                </div>
                <div class="mt-8 flex justify-between gap-4">
                    <button wire:click="retroceder" class="bg-gray-500 hover:bg-gray-600 text-white px-8 py-3 rounded-lg font-semibold transition">Retroceder</button>
                    <button wire:click="siguiente" class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-8 py-3 rounded-lg font-semibold transition">Continuar</button>
                </div>
            </div>
        @endif

        <!-- Paso 9: Concretó la venta en la llamada -->
        @if($paso === 9)
            <div wire:key="paso-9" class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                <div class="mb-8 pb-6 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900">Concretó la venta en la llamada</h2>
                </div>
                <div class="space-y-8">
                    @foreach($preguntasPaso9 as $pregunta)
                        <div class="bg-gray-50 p-6 rounded-xl border border-gray-200">
                            <label class="block text-lg font-medium text-gray-800 mb-4">
                                {{ $pregunta->texto }}
                                @if($pregunta->requerido) <span class="text-red-500">*</span> @endif
                            </label>
                            @php $opciones = json_decode($pregunta->opciones, true) ?? []; @endphp
                            <div class="space-y-3">
                                @foreach($opciones as $opcion)
                                    <label class="flex items-center p-4 bg-white border border-gray-200 rounded-lg cursor-pointer hover:bg-blue-50 hover:border-blue-300 transition-all
                                        {{ isset($respuestasPaso9[$pregunta->id]) && $respuestasPaso9[$pregunta->id] == $opcion['value'] ? 'bg-blue-50 border-blue-500 ring-1 ring-blue-500' : '' }}">
                                        <input type="radio" name="pregunta_{{ $pregunta->id }}"
                                            wire:model.live="respuestasPaso9.{{ $pregunta->id }}" 
                                            value="{{ $opcion['value'] }}"
                                            class="w-5 h-5 text-blue-600 focus:ring-blue-500 border-gray-300">
                                        <span class="ml-3 text-gray-700 font-medium">{{ $opcion['label'] }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-8 flex justify-between gap-4">
                    <button wire:click="retroceder" class="bg-gray-500 hover:bg-gray-600 text-white px-8 py-3 rounded-lg font-semibold transition">Retroceder</button>
                    <button wire:click="siguiente" class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-8 py-3 rounded-lg font-semibold transition">Continuar</button>
                </div>
            </div>
        @endif

        <!-- Paso 10: Validación Final Dinámica -->
        @if($paso === 10)
            <div wire:key="paso-10" class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                <div class="mb-8 pb-6 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900">Validación Final</h2>
                    <p class="text-gray-500 text-sm mt-1">
                        @if(isset($respuestasPaso9) && reset($respuestasPaso9) == 'SI') Validación de Venta Exitosa
                        @elseif(isset($respuestasPaso9) && reset($respuestasPaso9) == 'NO') Análisis de No Venta
                        @else Validación No Aplica @endif
                    </p>
                </div>
                <div class="space-y-8">
                    @php $currentSection = ''; @endphp
                    @foreach($this->preguntasPaso10 as $pregunta)
                        @if($currentSection !== $pregunta->seccion)
                            @php 
                                                    $currentSection = $pregunta->seccion;
                                $opcionesHeader = json_decode($pregunta->opciones, true) ?? [];
                                if ($pregunta->tipo_campo === 'textarea') {
                                    $opcionesHeader = [];
                                }
                            @endphp
                            @if(!empty($opcionesHeader))
                                <div class="mt-8 mb-4">
                                    <h3 class="text-xl font-semibold text-gray-800 border-b pb-2">{{ $currentSection }}</h3>
                                    <div class="grid grid-cols-12 gap-4 mt-4 text-sm font-medium text-gray-500 uppercase tracking-wider text-center">
                                        <div class="col-span-6 text-left">Pregunta</div>
                                        @foreach($opcionesHeader as $opcion)
                                            <div class="col-span-2">{{ $opcion['label'] }}</div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div class="mt-8 mb-4"><h3 class="text-xl font-semibold text-gray-800 border-b pb-2">{{ $currentSection }}</h3></div>
                            @endif
                        @endif

                        @if($pregunta->tipo_campo === 'radio')
                            <div class="grid grid-cols-12 gap-4 items-center py-3 border-b border-gray-100 hover:bg-gray-50 transition">
                                <div class="col-span-6 text-gray-700 font-medium">
                                    {{ $pregunta->texto }}
                                    @if($pregunta->requerido) <span class="text-red-500">*</span> @endif
                                </div>
                                @php $opciones = json_decode($pregunta->opciones, true) ?? []; @endphp
                                @foreach($opciones as $opcion)
                                    <div class="col-span-2 flex justify-center">
                                        <label class="cursor-pointer">
                                            <input type="radio" name="pregunta_{{ $pregunta->id }}"
                                                wire:model.live="respuestasPaso10.{{ $pregunta->id }}" 
                                                value="{{ $opcion['value'] }}"
                                                class="w-6 h-6 text-blue-600 focus:ring-blue-500 border-gray-300">
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        @elseif($pregunta->tipo_campo === 'textarea')
                            <div class="py-4">
                                <label class="block text-lg font-medium text-gray-800 mb-2">
                                    {{ $pregunta->texto }}
                                    @if($pregunta->requerido) <span class="text-red-500">*</span> @endif
                                </label>
                                <textarea wire:model.live="respuestasPaso10.{{ $pregunta->id }}"
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition"
                                    rows="4" placeholder="Escribe tu respuesta aquí..."></textarea>
                            </div>
                        @endif
                    @endforeach
                </div>
                <div class="mt-8 flex justify-between gap-4">
                    <button wire:click="retroceder" class="bg-gray-500 hover:bg-gray-600 text-white px-8 py-3 rounded-lg font-semibold transition">Retroceder</button>
                    <button wire:click="siguiente" class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-8 py-3 rounded-lg font-semibold transition">Continuar</button>
                </div>
            </div>
        @endif

        <!-- Paso 11: Seguimiento Final Dinámico -->
        @if($paso === 11)
            <div wire:key="paso-11" class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                <div class="mb-8 pb-6 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900">Seguimiento Final</h2>
                </div>
                <div class="space-y-8">
                    @php $currentSection = '';
                    $headersPrinted = false; @endphp
                    @foreach($this->preguntasPaso11 as $pregunta)
                        @if($currentSection !== $pregunta->seccion)
                            @php $currentSection = $pregunta->seccion;
                            $headersPrinted = false; @endphp
                            <div class="mt-8 mb-4"><h3 class="text-xl font-semibold text-gray-800 border-b pb-2">{{ $currentSection }}</h3></div>
                        @endif
                        @php $opciones = json_decode($pregunta->opciones, true) ?? [];
                        $esRadio = $pregunta->tipo_campo === 'radio'; @endphp
                        @if($esRadio && !$headersPrinted && !empty($opciones))
                            @php $headersPrinted = true;
                            $opcionesHeader = $opciones; @endphp
                            <div class="grid grid-cols-12 gap-4 mt-4 mb-2 text-sm font-medium text-gray-500 uppercase tracking-wider text-center">
                                <div class="col-span-6 text-left">Pregunta</div>
                                @foreach($opciones as $opcion) <div class="col-span-2">{{ $opcion['label'] }}</div> @endforeach
                            </div>
                        @endif

                        @if($pregunta->tipo_campo === 'radio')
                            <div class="grid grid-cols-12 gap-4 items-center py-3 border-b border-gray-100 hover:bg-gray-50 transition">
                                <div class="col-span-6 text-gray-700 font-medium">
                                    {{ $pregunta->texto }}
                                    @if($pregunta->requerido) <span class="text-red-500">*</span> @endif
                                </div>
                                @php $opciones = json_decode($pregunta->opciones, true) ?? [];
                                $opcionesEnHeader = !empty($opcionesHeader); @endphp
                                @if($opcionesEnHeader)
                                    @foreach($opciones as $opcion)
                                        <div class="col-span-2 flex justify-center">
                                            <label class="cursor-pointer">
                                                <input type="radio" name="pregunta_{{ $pregunta->id }}"
                                                    wire:model.live="respuestasPaso11.{{ $pregunta->id }}" 
                                                    value="{{ $opcion['value'] }}"
                                                    class="w-6 h-6 text-blue-600 focus:ring-blue-500 border-gray-300">
                                            </label>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="col-span-6 flex gap-6 justify-end">
                                        @foreach($opciones as $opcion)
                                            <label class="flex items-center cursor-pointer gap-2">
                                                <input type="radio" name="pregunta_{{ $pregunta->id }}"
                                                    wire:model.live="respuestasPaso11.{{ $pregunta->id }}" 
                                                    value="{{ $opcion['value'] }}"
                                                    class="w-5 h-5 text-blue-600 focus:ring-blue-500 border-gray-300">
                                                <span class="text-gray-700">{{ $opcion['label'] }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @elseif($pregunta->tipo_campo === 'textarea')
                            <div class="py-4">
                                <label class="block text-lg font-medium text-gray-800 mb-2">
                                    {{ $pregunta->texto }}
                                    @if($pregunta->requerido) <span class="text-red-500">*</span> @endif
                                </label>
                                <textarea wire:model.live="respuestasPaso11.{{ $pregunta->id }}"
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition"
                                    rows="4" placeholder="Escribe tu respuesta aquí..."></textarea>
                            </div>
                        @endif
                    @endforeach
                </div>
                <div class="mt-8 flex justify-between gap-4">
                    <button wire:click="retroceder" class="bg-gray-500 hover:bg-gray-600 text-white px-8 py-3 rounded-lg font-semibold transition">Retroceder</button>
                    @php $respuestaPaso9 = isset($respuestasPaso9) ? reset($respuestasPaso9) : null;
                    $esFlujoCierre = ($respuestaPaso9 === 'SI' || $respuestaPaso9 === 'NO APLICA'); @endphp
                    @if($esFlujoCierre)
                        <button wire:click="guardarDatos" class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-lg font-semibold transition">Guardar Auditoría</button>
                    @else
                        <button wire:click="siguiente" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-semibold transition">Continuar</button>
                    @endif
                </div>
            </div>
        @endif

        <!-- Paso 12: Seguimiento Final (Solo para flujo No Venta) -->
        @if($paso === 12)
            <div wire:key="paso-12" class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                <div class="mb-8 pb-6 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900">Seguimiento Final</h2>
                    <p class="text-gray-500 text-sm mt-1">Correos y Retroalimentación</p>
                </div>
                <div class="space-y-8">
                    @foreach($this->preguntasPaso12 as $pregunta)
                        <div class="bg-gray-50 p-6 rounded-xl border border-gray-200">
                            <label class="block text-lg font-medium text-gray-800 mb-4">
                                {{ $pregunta->texto }}
                                @if($pregunta->requerido) <span class="text-red-500">*</span> @endif
                            </label>

                            @if($pregunta->tipo_campo === 'radio')
                                @php $opciones = json_decode($pregunta->opciones, true) ?? []; @endphp
                                <div class="space-y-3">
                                    @foreach($opciones as $opcion)
                                        <label class="flex items-center p-4 bg-white border border-gray-200 rounded-lg cursor-pointer hover:bg-blue-50 hover:border-blue-300 transition-all
                                            {{ isset($respuestasPaso12[$pregunta->id]) && $respuestasPaso12[$pregunta->id] == $opcion['value'] ? 'bg-blue-50 border-blue-500 ring-1 ring-blue-500' : '' }}">
                                            <input type="radio" name="pregunta_{{ $pregunta->id }}"
                                                wire:model.live="respuestasPaso12.{{ $pregunta->id }}" 
                                                value="{{ $opcion['value'] }}"
                                                class="w-5 h-5 text-blue-600 focus:ring-blue-500 border-gray-300">
                                            <span class="ml-3 text-gray-700 font-medium">{{ $opcion['label'] }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            @else
                                <textarea wire:model.live="respuestasPaso12.{{ $pregunta->id }}"
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition"
                                    rows="6" placeholder="Escribe tu respuesta aquí..."></textarea>
                            @endif
                        </div>
                    @endforeach
                </div>
                <div class="mt-8 flex justify-between gap-4">
                    <button wire:click="retroceder" class="bg-gray-500 hover:bg-gray-600 text-white px-8 py-3 rounded-lg font-semibold transition">Retroceder</button>
                    <button wire:click="guardarDatos" class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-lg font-semibold transition">Guardar Auditoría</button>
                </div>
            </div>
        @endif

    </div>

    <!-- Historial Component -->
    @livewire('historial-auditorias')

    <!-- Script para auto-avance -->
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('showAlert', (data) => {
                Swal.fire({
                    icon: data[0].type,
                    title: data[0].title,
                    text: data[0].text,
                    confirmButtonColor: '#3085d6',
                });
            });
        });
    </script>
    <!-- Paso 99: Pantalla de Éxito Final -->
    <!-- Paso 99: Pantalla de Éxito Final -->
    @if($paso === 99)
        <div class="bg-white rounded-2xl shadow-xl p-12 border border-gray-100 flex flex-col items-center justify-center min-h-[500px] text-center animate-fade-in-up">
            
            <!-- Icono de Éxito Animado -->
            <div class="mb-8">
                <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-green-100 mb-6">
                    <svg class="h-12 w-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>

            <h2 class="text-4xl font-extrabold text-gray-900 mb-4 tracking-tight">
                ¡Auditoría Registrada!
            </h2>
            
            <p class="text-xl text-gray-500 mb-12 max-w-lg mx-auto">
                La evaluación ha sido guardada exitosamente en el sistema.
            </p>

            <div class="mt-8">
                <button onclick="location.reload()" 
                    class="inline-flex items-center justify-center px-8 py-4 border border-transparent text-lg font-medium rounded-xl text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 transform hover:scale-105 transition-all duration-200 shadow-lg hover:shadow-xl">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Realizar Nueva Auditoría
                </button>
            </div>
        </div>
    @endif
</div>
