<?php

namespace App\Console\Commands;

use App\Models\Release;
use App\Models\Tenant;
use Illuminate\Console\Command;

class ReleasePublish extends Command
{
    protected $signature = 'release:publish';

    protected $description = 'Publica un release en todos los tenants';

    public function handle()
    {
        $version = $this->ask('Versión');

        $title = $this->ask('Título');

        $description = $this->ask('Descripción');

        $this->newLine();

        $this->info('Características');
        $this->line('Escribe una característica por línea.');
        $this->line('Presiona Enter en una línea vacía para terminar.');
        $this->newLine();

        $features = [];

        while (true) {

            $feature = $this->ask('>');

            if ($feature === null || trim($feature) === '') {
                break;
            }

            $features[] = $feature;
        }

        $features = implode("\n", $features);

        $tenants = Tenant::all();

        if ($tenants->isEmpty()) {
            $this->warn('No existen tenants.');

            return self::SUCCESS;
        }

        $this->newLine();

        $this->table(
            ['Campo', 'Valor'],
            [
                ['Versión', $version],
                ['Título', $title],
                ['Descripción', $description],
                ['Características', $features],
                ['Tenants', $tenants->count()],
            ]
        );

        $this->newLine();

        if (!$this->confirm('¿Deseas publicar este release en todos los tenants?')) {
            $this->warn('Operación cancelada.');

            return self::SUCCESS;
        }

        $this->newLine();

        $this->info(
            "Publicando release {$version} en {$tenants->count()} tenants..."
        );

        foreach ($tenants as $tenant) {
            $this->line("→ Tenant: {$tenant->id}");

            try {
                $tenant->run(function () use (
                    $version,
                    $title,
                    $description,
                    $features
                ) {
                        Release::create([
                            'version' => $version,
                            'title' => $title,
                            'description' => $description,
                            'features' => $features,
                            'published_at' => now(),
                            'active' => true,
                        ]);
                    });

                $this->info('  ✓ Release creado');
            } catch (\Throwable $e) {
                $this->error(
                    "  ✗ Error: {$e->getMessage()}"
                );
            }
        }

        $this->newLine();

        $this->info('Proceso terminado.');

        return self::SUCCESS;
    }
}
