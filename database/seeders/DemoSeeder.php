<?php

namespace Database\Seeders;

use App\Domain\Categorias\Models\Categoria;
use App\Domain\Comercios\Models\Comercio;
use App\Domain\Instituciones\Models\Institucion;
use App\Domain\Productores\Models\Productor;
use App\Domain\Servicios\Models\Servicio;
use App\Domain\Usuarios\Roles;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Datos de ejemplo para mostrar la plataforma con contenido (demo /
 * relevamiento de campo). Es idempotente: se puede correr varias veces.
 *
 * Usuario demo: vecino@elnaranjoconecta.local / demo1234
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $vecino = User::firstOrCreate(
            ['email' => 'vecino@elnaranjoconecta.local'],
            [
                'nombre' => 'Vecino Demo',
                'password' => 'demo1234',
                'rol' => Roles::VECINO,
                'whatsapp' => '3865456700',
                'zona' => 'Centro',
            ]
        );

        $admin = User::where('email', 'admin@elnaranjoconecta.local')->first();

        $this->comercios($vecino);
        $this->servicios($vecino);
        $this->productores($vecino);
        $this->empleos($vecino);
        $this->reclamos($vecino);
        $this->instituciones();
        $this->avisos($admin ?? $vecino);
        $this->resenas();
    }

    private function categoriaId(string $nombre, string $modulo): ?int
    {
        return Categoria::where('nombre', $nombre)->where('modulo', $modulo)->value('id');
    }

    private function comercios(User $user): void
    {
        $datos = [
            ['Almacén Don Ramón', 'Almacén', 'Ramos generales, bebidas y artículos de limpieza.', 'Ruta 301 s/n', 'Lun a Sáb 7 a 13 y 17 a 21', '3865456701', -26.4812, -64.7489],
            ['Kiosco La Esquina', 'Kiosco', 'Golosinas, cigarrillos, gaseosas y recargas.', 'Calle San Martín 120', 'Todos los días 8 a 23', '3865456702', -26.4835, -64.7512],
            ['Ferretería El Tornillo', 'Ferretería', 'Herramientas, pintura y materiales de construcción.', 'Belgrano 45', 'Lun a Vie 8 a 12 y 16 a 20', '3865456703', -26.4801, -64.7530],
            ['Carnicería Los Hermanos', 'Carnicería', 'Carne vacuna y de cerdo, cortes del día.', 'Sarmiento 78', 'Mar a Dom 7 a 13', '3865456704', -26.4849, -64.7461],
            ['Verdulería La Huerta', 'Verdulería', 'Frutas y verduras frescas de la zona.', 'Calle Rivadavia 12', 'Lun a Sáb 8 a 13 y 17 a 20', '3865456705', -26.4798, -64.7498],
            ['Farmacia Del Pueblo', 'Farmacia', 'Medicamentos, perfumería y atención al público.', 'San Martín 200', 'Lun a Sáb 8 a 13 y 17 a 22', '3865456706', -26.4821, -64.7525],
            ['Panadería Doña Rosa', 'Gastronomía', 'Pan casero, facturas y tortillas.', 'Mitre 33', 'Todos los días 7 a 14 y 17 a 21', '3865456707', -26.4856, -64.7472],
            ['Rotisería El Buen Sabor', 'Gastronomía', 'Comidas para llevar y empanadas por encargo.', 'Belgrano 210', 'Mar a Dom 11 a 15 y 19 a 23', '3865456708', -26.4810, -64.7540],
        ];

        foreach ($datos as [$nombre, $categoria, $descripcion, $direccion, $horarios, $whatsapp, $lat, $lng]) {
            $user->comercios()->firstOrCreate(
                ['nombre' => $nombre],
                [
                    'categoria_id' => $this->categoriaId($categoria, 'comercios'),
                    'descripcion' => $descripcion,
                    'direccion' => $direccion,
                    'horarios' => $horarios,
                    'whatsapp' => $whatsapp,
                    'zona' => 'Centro',
                    'lat' => $lat,
                    'lng' => $lng,
                ]
            );
        }
    }

    private function servicios(User $user): void
    {
        $datos = [
            ['Electricista matriculado', 'Electricidad', 'Instalaciones, tableros y reparaciones eléctricas.', '10 años', '3865456711', -26.4826, -64.7505],
            ['Plomería y gas', 'Plomería', 'Destapaciones, termotanques y conexiones de gas.', '8 años', '3865456712', -26.4805, -64.7479],
            ['Albañilería en general', 'Albañilería', 'Construcción, refacciones y revoques.', '15 años', '3865456713', -26.4851, -64.7533],
            ['Peluquería a domicilio', 'Peluquería', 'Cortes, color y peinados a domicilio.', '6 años', '3865456714', -26.4792, -64.7491],
            ['Mecánica ligera', 'Mecánica', 'Service, frenos y electricidad del automotor.', '12 años', '3865456715', -26.4844, -64.7455],
            ['Técnico en PC y celulares', 'Técnico informático', 'Reparación, instalación y cambio de pantallas.', '5 años', '3865456716', -26.4818, -64.7521],
            ['Fletes y mudanzas', 'Fletes', 'Viajes a Tucumán y alrededores. Camión con caja.', '7 años', '3865456717', -26.4830, -64.7482],
        ];

        foreach ($datos as [$titulo, $categoria, $descripcion, $experiencia, $whatsapp, $lat, $lng]) {
            $user->servicios()->firstOrCreate(
                ['titulo' => $titulo],
                [
                    'categoria_id' => $this->categoriaId($categoria, 'servicios'),
                    'descripcion' => $descripcion,
                    'experiencia' => $experiencia,
                    'whatsapp' => $whatsapp,
                    'zona' => 'El Naranjo',
                    'lat' => $lat,
                    'lng' => $lng,
                ]
            );
        }
    }

    private function productores(User $user): void
    {
        $datos = [
            ['Finca La Esperanza', 'Producción agrícola', 'Limones, naranjas y hortalizas de estación.', 'Venta por cajón o al por mayor.', 'Lun a Sáb 7 a 18', '3865456721', -26.4770, -64.7580],
            ['Granja El Sol', 'Producción ganadera', 'Huevos de campo, pollos y cerdos.', 'Venta directa en la granja.', 'Todos los días 8 a 18', '3865456722', -26.4905, -64.7420],
            ['Vivero Los Naranjos', 'Venta de insumos', 'Plantines, árboles frutales y sustratos.', 'Pedidos por WhatsApp.', 'Lun a Vie 8 a 17', '3865456723', -26.4755, -64.7450],
            ['Servicios Rurales El Monte', 'Servicios rurales', 'Arado, rastra y movimiento de suelo.', 'A convenir según el trabajo.', 'Lun a Sáb 7 a 19', '3865456724', -26.4880, -64.7600],
        ];

        foreach ($datos as [$nombre, $categoria, $queProduce, $queVende, $disponibilidad, $whatsapp, $lat, $lng]) {
            $user->productores()->firstOrCreate(
                ['nombre' => $nombre],
                [
                    'categoria_id' => $this->categoriaId($categoria, 'productores'),
                    'que_produce' => $queProduce,
                    'que_vende' => $queVende,
                    'disponibilidad' => $disponibilidad,
                    'zona' => 'Zona rural',
                    'whatsapp' => $whatsapp,
                    'lat' => $lat,
                    'lng' => $lng,
                ]
            );
        }
    }

    private function empleos(User $user): void
    {
        $datos = [
            ['busco_trabajo', 'Busco trabajo como albañil', 'Albañilería', 'Experiencia en construcción y refacciones. Disponibilidad full time.', '5 años', '3865456731', -26.4830, -64.7500],
            ['busco_trabajador', 'Se busca ayudante de cocina', 'Cocina', 'Rotisería del centro busca ayudante con experiencia básica.', null, '3865456732', -26.4820, -64.7510],
            ['busco_trabajo', 'Busco trabajo en comercio', 'Otros oficios', 'Atención al público, manejo de caja y reposición.', '3 años', '3865456733', -26.4840, -64.7490],
            ['busco_trabajador', 'Se busca peón rural', 'Trabajo rural', 'Trabajo en finca, con movilidad propia deseable.', null, '3865456734', -26.4860, -64.7530],
        ];

        foreach ($datos as [$tipo, $titulo, $categoria, $descripcion, $experiencia, $whatsapp, $lat, $lng]) {
            $user->empleos()->firstOrCreate(
                ['titulo' => $titulo],
                [
                    'tipo' => $tipo,
                    'categoria_id' => $this->categoriaId($categoria, 'servicios'),
                    'descripcion' => $descripcion,
                    'experiencia' => $experiencia,
                    'zona' => 'El Naranjo',
                    'whatsapp' => $whatsapp,
                    'lat' => $lat,
                    'lng' => $lng,
                ]
            );
        }
    }

    private function reclamos(User $user): void
    {
        $datos = [
            ['alumbrado', 'Lámparas quemadas en la plaza central.', 'Centro', 'pendiente'],
            ['calles', 'Calle con baches frente a la escuela.', 'Centro', 'en_revision'],
            ['agua', 'Falta de agua en el barrio Norte desde el lunes.', 'Barrio Norte', 'en_proceso'],
            ['basura', 'La recolección de basura pasa de forma irregular.', 'Centro', 'resuelto'],
        ];

        foreach ($datos as [$categoria, $descripcion, $zona, $estado]) {
            $user->reclamos()->firstOrCreate(
                ['descripcion' => $descripcion],
                [
                    'categoria' => $categoria,
                    'zona' => $zona,
                    'estado' => $estado,
                ]
            );
        }
    }

    private function instituciones(): void
    {
        $datos = [
            ['escuela', 'Escuela N° 123 El Naranjo', 'Educación primaria y secundaria.', 'Calle San Martín 500', '3865456741', 'Lun a Vie 7:30 a 18'],
            ['salud', 'Centro de Salud El Naranjo', 'Atención médica primaria y vacunación.', 'Belgrano 90', '3865456742', 'Lun a Vie 7 a 19'],
            ['comuna', 'Comuna de El Naranjo', 'Trámites, obras y servicios municipales.', 'Ruta 301 s/n', '3865456743', 'Lun a Vie 7 a 13'],
            ['club', 'Club Atlético El Naranjo', 'Fútbol, básquet y actividades para toda la familia.', 'Calle Rivadavia 300', '3865456744', 'Mar a Dom 16 a 22'],
            ['iglesia', 'Parroquia San José', 'Misas y actividades comunitarias.', 'San Martín 80', null, 'Misa Dom 19'],
        ];

        foreach ($datos as [$tipo, $nombre, $descripcion, $direccion, $telefono, $horarios]) {
            Institucion::firstOrCreate(
                ['nombre' => $nombre],
                [
                    'tipo' => $tipo,
                    'descripcion' => $descripcion,
                    'direccion' => $direccion,
                    'telefono' => $telefono,
                    'horarios' => $horarios,
                ]
            );
        }
    }

    private function avisos(User $admin): void
    {
        $datos = [
            ['reunion', 'Reunión vecinal en la comuna', 'Invitamos a todos los vecinos a la reunión del viernes a las 20 h en la comuna para tratar obras y seguridad.', 3],
            ['corte_servicio', 'Corte de agua programado', 'El jueves de 8 a 14 h habrá corte de agua por trabajos en la red. Se recomienda reservar.', 5],
            ['evento', 'Feria de productores el sábado', 'Este sábado desde las 9 h, feria de productores locales en la plaza central. Entrada libre.', 7],
            ['actividad', 'Campaña de vacunación', 'Campaña de vacunación en el Centro de Salud de lunes a viernes, de 8 a 16 h.', 2],
        ];

        foreach ($datos as [$tipo, $titulo, $cuerpo, $dias]) {
            $admin->avisos()->firstOrCreate(
                ['titulo' => $titulo],
                [
                    'tipo' => $tipo,
                    'cuerpo' => $cuerpo,
                    'fecha_evento' => now()->addDays($dias)->setTime(10, 0),
                ]
            );
        }
    }

    private function resenas(): void
    {
        $autores = collect([
            ['nombre' => 'María Gómez', 'email' => 'maria@elnaranjoconecta.local'],
            ['nombre' => 'Juan Pérez', 'email' => 'juan@elnaranjoconecta.local'],
            ['nombre' => 'Lucía Díaz', 'email' => 'lucia@elnaranjoconecta.local'],
        ])->map(fn ($u) => User::firstOrCreate(
            ['email' => $u['email']],
            ['nombre' => $u['nombre'], 'password' => 'demo1234', 'rol' => Roles::VECINO],
        ));

        $comentarios = [
            5 => 'Excelente atención, muy recomendable.',
            4 => 'Muy buena experiencia.',
            3 => 'Cumple, está bien.',
        ];

        $items = collect()
            ->merge(Comercio::all())
            ->merge(Servicio::all())
            ->merge(Productor::all());

        foreach ($items as $i => $item) {
            foreach ($autores as $j => $autor) {
                // No todos califican todo, para que los promedios varíen.
                if (($i + $j) % 3 === 0) {
                    continue;
                }

                $puntuacion = 3 + (($i + $j) % 3); // 3, 4 o 5
                $item->resenas()->firstOrCreate(
                    ['user_id' => $autor->id],
                    ['puntuacion' => $puntuacion, 'comentario' => $comentarios[$puntuacion]],
                );
            }
        }
    }
}
