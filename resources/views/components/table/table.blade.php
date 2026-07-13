
<div>
    <div class="d-flex justify-content-between mb-3">
        @if($list['pages'] ?? null != null)
        <div class="input-group w-25">
            <h5 class="input-group-text">Pagina</h5>
            <input type="number" class="form-control" wire:model.live="pages">
            <div class="input-group-text">/{{ $pages_max }}</div>
        </div>
        @else
            <h5></h5>
        @endif
        <input type="text" class="form-control w-25" placeholder="Buscar...." wire:model.live="search">
    </div>
    <table class="table table-striped">
        <thead>
            @foreach ($heads as $item => $value)
                <th style="cursor:pointer;" class="user-select-none"
                    @if ($value != null) style="cursor: pointer;" wire:click="sortBy('{{ $value }}')" @endif>
                    <div class="d-flex justify-content-between align-items-center">
                        {{ $item }} @if ($value != null)
                            <i @class([

                                            'fas',
                                            'fa-sort',
                                            'fa-sort' => $sort_field == $item && $sort_direction == 'desc',
                                            'text-secondary' => $sort_field != $value,
                                            'text-dark' => $sort_field == $value,
                                        ])></i>
                        @endif
                    </div>

                </th>
            @endforeach
        </thead>
        <tbody>
            {{ $slot }}
        </tbody>
        <tfoot>
            {{ $slot['footer'] }}
        </tfoot>
    </table>
</div>
