<div>

    @if ($show && $release)

        <div wire:ignore.self class="modal fade" id="releaseNotificationModal" tabindex="-1" role="dialog"
            aria-labelledby="releaseNotificationModalLabel" aria-hidden="true">

            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">

                <div class="modal-content">

                    {{-- Header --}}
                    <div class="modal-header">

                        <div>

                            <div class="d-flex align-items-center mb-1">

                                <span class="badge badge-primary mr-2">
                                    Versión {{ $release->version }}
                                </span>

                                <small class="text-muted">
                                    Novedades
                                </small>

                            </div>

                            <h5 class="modal-title" id="releaseNotificationModalLabel">
                                <i class="fas fa-rocket text-primary mr-2"></i>
                                Novedades de Vendex
                            </h5>

                        </div>


                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">
                                &times;
                            </span>
                        </button>

                    </div>


                    {{-- Body --}}
                    <div class="modal-body">

                        {{-- Release --}}
                        <div class="d-flex align-items-start">

                            <div class="mr-3">

                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 56px; height: 56px;">
                                    <i class="fas fa-gift fa-lg"></i>
                                </div>

                            </div>


                            <div class="flex-grow-1">

                                <h4 class="mb-2">
                                    {{ $release->title }}
                                </h4>

                                @if ($release->description)
                                    <div class="text-muted">

                                        {!! nl2br(e($release->description)) !!}

                                    </div>
                                @endif

                            </div>

                        </div>


                        <hr>


                        {{-- Información --}}
                        <div class="card card-outline card-primary mb-0">

                            <div class="card-header">

                                <h6 class="card-title mb-0">

                                    <i class="fas fa-sparkles mr-1"></i>

                                    ¿Qué hay de nuevo?

                                </h6>

                            </div>


                            <div class="card-body">

                                <div class="mb-0 text-muted">

                                    {!! nl2br(e($release->features)) !!}

                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- Footer --}}
                    <div class="modal-footer">

                        <button type="button" class="btn btn-primary" wire:click="markAsSeen"
                            wire:loading.attr="disabled" wire:target="markAsSeen">

                            <span wire:loading.remove wire:target="markAsSeen">
                                <i class="fas fa-check mr-1"></i>
                                Entendido
                            </span>


                            <span wire:loading wire:target="markAsSeen">
                                <span class="spinner-border spinner-border-sm mr-2"></span>
                                Guardando...
                            </span>

                        </button>

                    </div>

                </div>

            </div>

        </div>


        <script>
            document.addEventListener('livewire:init', () => {

                setTimeout(() => {

                    $('#releaseNotificationModal').modal('show');

                }, 100);

            });
        </script>

    @endif

</div>
