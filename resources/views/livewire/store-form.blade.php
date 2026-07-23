<div>

    <form wire:submit="save">
        <div class="modal-body">
            <div class="row mb-3">
                <div class="col">
                    <label for="">Nombre</label>
                    <input type="text" class="form-control" placeholder="Ingrese Nombre" wire:model="name">
                    @error('name')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col">
                    <label for="">Celular</label>
                    <input type="text" class="form-control" placeholder="Ingrese Celular" wire:model="phone">
                    @error('phone')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col">
                    <label for="">Correo Electronico</label>
                    <input type="email" class="form-control" placeholder="Ingrese Correo Electronico" wire:model="email">
                    @error('email')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="row mb-3">
                <div class="col">
                    <label for="">Direccion</label>
                    <input type="text" class="form-control" placeholder="Ingrese Direccion" wire:model="address">
                    @error('address')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col">
                    <label for="">Tipo</label>
                    <select class="form-select" wire:model="type">
                        <option value="">Seleccione un Tipo</option>
                        <option value="{{ \App\Enums\Type::STORE }}">{{ __('messages.'.\App\Enums\Type::STORE->name)  }}</option>
                        <option value="{{ \App\Enums\Type::WAREHOUSE }}">{{ __('messages.'.\App\Enums\Type::WAREHOUSE->name) }}</option>
                    </select>
                    @error('type')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col">
                    <label for="">Estado</label>
                    <select class="form-select" wire:model="status">
                        <option value="">Seleccione un Estado</option>
                        @foreach (App\Enums\Status::cases() as $item)
                            <option value="{{ $item->value }}">{{ __('messages.'.$item->name) }}</option>
                        @endforeach
                    </select>
                    @error('status')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="row mb-3">
                <div class="col">
                    <div id="map" style="height: 400px;width: 100%;"></div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col">
                    <label for="">Latitud</label>
                    <input type="text" class="form-control" wire:model="lat" readonly>
                </div>
                <div class="col">
                    <label for="">Longitud</label>
                    <input type="text" class="form-control" wire:model="long" readonly>
                </div>
            </div>


            @if($edit)
                <div class="row">
                    <div class="col">
                        <h5>En Inventario</h5>
                        <livewire:table :heads="$heads" :searchable="false">
                            @foreach($stock ?? [] as $item)
                                <tr>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->pivot->quantity }}</td>
                                    <td>{{ Number::format($item->price,2) }}</td>
                                    <td>
                                        <a href="{{ route('admin.product.id', $item->id) }}" class="btn btn-primary"><i class="fa fa-eye"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                        </livewire:table>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <h5>Vendidos</h5>
                        <livewire:table :heads="$heads1" :searchable="false">
                            @foreach($sales ?? [] as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->product->name }}</td>
                                    <td>{{ $item->referenceable?->customer?->name ?? '---'}}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ Number::format($item->price,2) }}</td>
                                    <td>{{ $item->user->name }}</td>
                                </tr>
                            @endforeach
                        </livewire:table>
                    </div>
                </div>
            @endif
        </div>
        @if($edit)
            <hr>
            <div class="d-flex justify-content-end mb-3">
                <button type="submit" class="btn btn-primary me-1">Guardar</button>
                <a href="{{ route('admin.stores') }}" class="btn btn-secondary">Cerrar</a>
            </div>
            @else
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Guardar</button>
                <button data-bs-dismiss="modal" type="reset" class="btn btn-secondary">Cancelar</button>
            </div>
        @endif
    </form>
</div>

@script
    <script>

        const modalElement = document.getElementById('modal-store');
        let map;


        map = L.map('map').setView([-17.9833, -67.15], 13);

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);



        const drawnItems = new L.FeatureGroup();
        map.addLayer(drawnItems);

        const drawControl = new L.Control.Draw({
            draw: {
                marker: false,
                polyline: false,
                rectangle: false,
                circlemarker: false,

                circle: true,
                polygon: false
            },
            edit: {
                featureGroup: drawnItems
            }
        });

        map.addControl(drawControl);

        let geofence = null;

        if($wire.lat != null){
            geofence = {
                "lat": $wire.lat,
                "lng": $wire.long,
                "radius": $wire.radius
            }
        }

        if (geofence) {

            circle = L.circle(
                [geofence.lat, geofence.lng],
                {
                    radius: geofence.radius,
                    color: '#0d6efd',
                    fillColor: '#0d6efd',
                    fillOpacity: 0.25
                }
            ).addTo(map);

            map.fitBounds(circle.getBounds());
        }

        map.on(L.Draw.Event.CREATED, function (e) {

            // Eliminar el círculo anterior
            if (geofence) {
                drawnItems.removeLayer(geofence);
            }

            geofence = e.layer;

            drawnItems.addLayer(geofence);

            console.log(geofence.getLatLng().lng);
            console.log(geofence.getRadius());

            $wire.lat = geofence.getLatLng().lat;
            $wire.long = geofence.getLatLng().lng;
            $wire.radius = geofence.getRadius();

        });

        if(modalElement){
            modalElement.addEventListener('shown.bs.modal', function () {
                map.invalidateSize();
            });
        }

    </script>
@endscript
