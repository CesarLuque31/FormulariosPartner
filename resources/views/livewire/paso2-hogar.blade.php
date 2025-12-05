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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12">
                    </path>
                </svg>
                ATRÁS
            </button>

            <button wire:click="siguiente" wire:loading.attr="disabled"
                class="bg-gradient-to-r from-orange-600 to-orange-500 hover:from-orange-700 hover:to-orange-700 text-white px-8 py-3 rounded-lg font-semibold transition transform hover:scale-105 flex items-center gap-2">
                <span wire:loading.remove wire:target="siguiente">SIGUIENTE</span>
                <span wire:loading wire:target="siguiente">Cargando...</span>
                <svg wire:loading.remove wire:target="siguiente" class="w-5 h-5" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6">
                    </path>
                </svg>
            </button>
        </div>
    </div>
@endif