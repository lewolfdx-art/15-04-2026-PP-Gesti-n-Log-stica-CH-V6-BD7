<?php

namespace Database\Seeders;

use App\Models\GastoCategoria;
use Illuminate\Database\Seeder;

class GastoCategoriaSeeder extends Seeder
{
    public function run()
    {
        GastoCategoria::truncate();

        $categorias = [
            // MATERIA PRIMA
            ['grupo' => 'MATERIA PRIMA', 'nombre' => 'CEMENTO GRANEL', 'orden' => 1],
            ['grupo' => 'MATERIA PRIMA', 'nombre' => 'PIEDRA 3/4', 'orden' => 2],
            ['grupo' => 'MATERIA PRIMA', 'nombre' => 'PIEDRA 1/2', 'orden' => 3],
            ['grupo' => 'MATERIA PRIMA', 'nombre' => 'POLVILLO', 'orden' => 4],
            ['grupo' => 'MATERIA PRIMA', 'nombre' => 'ARENA DE RIO', 'orden' => 5],
            ['grupo' => 'MATERIA PRIMA', 'nombre' => 'ARENA DE CERRO', 'orden' => 6],
            ['grupo' => 'MATERIA PRIMA', 'nombre' => 'AGUA', 'orden' => 7],
            ['grupo' => 'MATERIA PRIMA', 'nombre' => 'ADITIVOS', 'orden' => 8],
            ['grupo' => 'MATERIA PRIMA', 'nombre' => 'M-OTROS', 'orden' => 9],

            // COMBUSTIBLE
            ['grupo' => 'COMBUSTIBLE', 'nombre' => 'PRODUCCION', 'orden' => 1],
            ['grupo' => 'COMBUSTIBLE', 'nombre' => 'VENDEDORES', 'orden' => 2],
            ['grupo' => 'COMBUSTIBLE', 'nombre' => 'COMB-OTROS', 'orden' => 3],

            // GASTOS FIJOS
            ['grupo' => 'GASTOS FIJOS', 'nombre' => 'ALQUILER DE LOCAL DE PLANTA', 'orden' => 1],
            ['grupo' => 'GASTOS FIJOS', 'nombre' => 'CONT. EXTERNO', 'orden' => 2],
            ['grupo' => 'GASTOS FIJOS', 'nombre' => 'PUBLICIDAD', 'orden' => 3],
            ['grupo' => 'GASTOS FIJOS', 'nombre' => 'GF-OTROS', 'orden' => 4],

            // GASTOS PERSONAL
            ['grupo' => 'GASTOS PERSONAL', 'nombre' => 'PLANILLA', 'orden' => 1],
            ['grupo' => 'GASTOS PERSONAL', 'nombre' => 'EPPS', 'orden' => 2],
            ['grupo' => 'GASTOS PERSONAL', 'nombre' => 'ALIMENTACION', 'orden' => 3],
            ['grupo' => 'GASTOS PERSONAL', 'nombre' => 'PASAJES', 'orden' => 4],
            ['grupo' => 'GASTOS PERSONAL', 'nombre' => 'GP-OTROS', 'orden' => 5],
            ['grupo' => 'GASTOS PERSONAL', 'nombre' => 'GRATIFICACION', 'orden' => 6],
            ['grupo' => 'GASTOS PERSONAL', 'nombre' => 'LIQUIDACIONES', 'orden' => 7],

            // EQUIPOS ALQUILER
            ['grupo' => 'EQUIPOS ALQUILER', 'nombre' => 'MX 01-CBZ-733-TONY', 'orden' => 1],
            ['grupo' => 'EQUIPOS ALQUILER', 'nombre' => 'MX 02-CEI-706', 'orden' => 2],
            ['grupo' => 'EQUIPOS ALQUILER', 'nombre' => 'MX 03-CEI-707', 'orden' => 3],
            ['grupo' => 'EQUIPOS ALQUILER', 'nombre' => 'PLANTA', 'orden' => 4],
            ['grupo' => 'EQUIPOS ALQUILER', 'nombre' => 'BOMBA HYO', 'orden' => 5],
            ['grupo' => 'EQUIPOS ALQUILER', 'nombre' => 'RETROESCAVADORA', 'orden' => 6],
            ['grupo' => 'EQUIPOS ALQUILER', 'nombre' => 'GRUPO GENERADOR', 'orden' => 7],
            ['grupo' => 'EQUIPOS ALQUILER', 'nombre' => 'EQUIPOS DE LABORATORIO', 'orden' => 8],
            ['grupo' => 'EQUIPOS ALQUILER', 'nombre' => 'AUXILIO MECANICO', 'orden' => 9],
            ['grupo' => 'EQUIPOS ALQUILER', 'nombre' => 'ACCESORIOS', 'orden' => 10],
            ['grupo' => 'EQUIPOS ALQUILER', 'nombre' => 'EQUIPOS-OTROS', 'orden' => 11],
            ['grupo' => 'EQUIPOS ALQUILER', 'nombre' => 'MIXER TERCEROS', 'orden' => 12],

            // FLETES
            ['grupo' => 'FLETES', 'nombre' => 'VOLQUETE 01', 'orden' => 1],
            ['grupo' => 'FLETES', 'nombre' => 'VOLQUETE 02', 'orden' => 2],
            ['grupo' => 'FLETES', 'nombre' => 'CEMENTO 01', 'orden' => 3],
            ['grupo' => 'FLETES', 'nombre' => 'CEMENTO 02', 'orden' => 4],
            ['grupo' => 'FLETES', 'nombre' => 'CEMENTO 03', 'orden' => 5],
            ['grupo' => 'FLETES', 'nombre' => 'FLETES-OTROS', 'orden' => 6],

            // PRESTAMOS
            ['grupo' => 'PRESTAMOS', 'nombre' => 'BBVA', 'orden' => 1],
            ['grupo' => 'PRESTAMOS', 'nombre' => 'BCP', 'orden' => 2],
            ['grupo' => 'PRESTAMOS', 'nombre' => 'CAJA HUANCAYO', 'orden' => 3],
            ['grupo' => 'PRESTAMOS', 'nombre' => 'PRESTAMOS-OTROS', 'orden' => 4],

            // SEGUROS IMPUESTOS
            ['grupo' => 'SEGUROS IMPUESTOS', 'nombre' => 'SALUD Y AFP', 'orden' => 1],
            ['grupo' => 'SEGUROS IMPUESTOS', 'nombre' => 'FRACCIONAMIENTO', 'orden' => 2],
            ['grupo' => 'SEGUROS IMPUESTOS', 'nombre' => 'IGV', 'orden' => 3],
            ['grupo' => 'SEGUROS IMPUESTOS', 'nombre' => 'RENTA', 'orden' => 4],
            ['grupo' => 'SEGUROS IMPUESTOS', 'nombre' => 'SI-OTROS', 'orden' => 5],
            ['grupo' => 'SEGUROS IMPUESTOS', 'nombre' => 'SCTR PENSION', 'orden' => 6],
            ['grupo' => 'SEGUROS IMPUESTOS', 'nombre' => 'SCTR SALUD', 'orden' => 7],
            ['grupo' => 'SEGUROS IMPUESTOS', 'nombre' => 'VIDA LEY', 'orden' => 8],
            ['grupo' => 'SEGUROS IMPUESTOS', 'nombre' => 'FRACCIONAMIENTO DE ITAN', 'orden' => 9],

            // COMISIONES VENTAS
            ['grupo' => 'COMISIONES VENTAS', 'nombre' => 'COMISIONES', 'orden' => 1],
            ['grupo' => 'COMISIONES VENTAS', 'nombre' => 'COM-OTROS', 'orden' => 2],
        ];

        foreach ($categorias as $cat) {
            GastoCategoria::create($cat);
        }
    }
}