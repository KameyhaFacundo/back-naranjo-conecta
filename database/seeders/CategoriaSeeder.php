<?php

namespace Database\Seeders;

use App\Domain\Categorias\Models\Categoria;
use Illuminate\Database\Seeder;

class CategoriaSeeder extends Seeder
{
    /**
     * Categorías iniciales tomadas del doc del proyecto (sección Empleo y
     * Servicios / Comercios locales). Ajustar según lo que surja del
     * relevamiento de campo.
     */
    public function run(): void
    {
        $servicios = [
            'Albañilería', 'Electricidad', 'Plomería', 'Pintura', 'Mecánica',
            'Reparación de motos', 'Técnico informático', 'Peluquería', 'Barbería',
            'Cocina', 'Repostería', 'Limpieza', 'Cuidado de personas', 'Fletes',
            'Remises', 'Trabajo rural', 'Operador de maquinaria', 'Otros oficios',
        ];

        $comercios = [
            'Almacén', 'Kiosco', 'Ferretería', 'Ropa', 'Gastronomía',
            'Carnicería', 'Verdulería', 'Farmacia', 'Servicio técnico', 'Otros',
        ];

        $productores = [
            'Producción agrícola', 'Producción ganadera', 'Venta de insumos',
            'Maquinaria y herramientas', 'Servicios rurales',
        ];

        foreach ($servicios as $nombre) {
            Categoria::firstOrCreate(['nombre' => $nombre, 'modulo' => 'servicios']);
        }

        foreach ($comercios as $nombre) {
            Categoria::firstOrCreate(['nombre' => $nombre, 'modulo' => 'comercios']);
        }

        foreach ($productores as $nombre) {
            Categoria::firstOrCreate(['nombre' => $nombre, 'modulo' => 'productores']);
        }
    }
}
