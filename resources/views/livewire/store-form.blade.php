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
                            @if($item != \App\Enums\Status::SALE)
                                <option value="{{ $item->value }}">{{ __('messages.'.$item->name) }}</option>
                            @endif
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

            <div class="row mb-3">
                <div class="col">
                    <label>Imagen de Producto</label>
                    @if($photo)
                        {{-- Vista previa de la nueva imagen --}}
                        <div class="d-flex justify-content-center" style="height: 300px;">
                            <img src="{{ $photo->temporaryUrl() }}" class="img-thumbnail">
                        </div>
                    @elseif($photo_url)
                        {{-- Imagen guardada --}}
                        <div class="d-flex justify-content-center" style="height: 300px;">
                            <img src="{{ $photo_url }}" class="img-thumbnail">
                        </div>
                    @else
                        {{-- Sin imagen --}}
                        <div class="border rounded p-5 text-center">
                            Sin imagen
                        </div>
                    @endif
                    <input type="file" class="form-control" wire:model="photo" id="photo">
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
                                    <td>{{ $item->pivot->quantity ?? 0 }}</td>
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

        const input = document.querySelector('#photo');

        input.addEventListener('change', (evento) => {
            const archivo = evento.target.files[0];
            if (!archivo) return;

            const lector = new FileReader();
            lector.readAsDataURL(archivo);

            lector.onload = (e) => {
                const imagen = new Image();
                imagen.src = e.target.result;

                imagen.onload = () => {
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');

                    // Ajusta el ancho y alto máximo deseado
                    const anchoMaximo = 800;
                    const escala = anchoMaximo / imagen.width;
                    canvas.width = anchoMaximo;
                    canvas.height = imagen.height * escala;

                    // Dibuja la imagen redimensionada en el canvas
                    ctx.drawImage(imagen, 0, 0, canvas.width, canvas.height);

                    // Convierte el canvas a un Blob comprimido (calidad 0.7 = 70%)
                    canvas.toBlob((blob) => {
                        const imagenOptimizado = new File([blob], archivo.name, {
                            type: 'image/jpeg',
                            lastModified: Date.now(),
                        });
                        console.log('Imagen lista:', imagenOptimizado);
                        @this.upload('photo', imagenOptimizado);
                    }, 'image/jpeg', 0.7);
                };
            };
        });




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
