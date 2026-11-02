<?php

namespace Tests\Feature;

use App\Models\Contacto;
use App\Models\Inicio;
use App\Models\LibroReclamacion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Web pública, formularios y vista previa por ngrok (solo la web, nunca el panel).
 */
class WebPublicaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        Inicio::create(['is_active' => true, 'logo_text' => 'Concretera Mantaro', 'whatsapp_number' => '51957924586']);
    }

    public function test_todas_las_paginas_publicas_cargan(): void
    {
        foreach (['/', '/nosotros', '/productos', '/sostenibilidad', '/experiencia', '/concretips', '/contacto', '/libro-reclamaciones'] as $ruta) {
            $this->get($ruta)->assertOk()->assertSee('cm-header', false);
        }
    }

    public function test_el_boton_de_whatsapp_lleva_al_numero_con_codigo_de_pais(): void
    {
        $this->get('/')->assertSee('https://wa.me/51957924586', false);
    }

    public function test_cotiza_tu_obra_guarda_la_solicitud(): void
    {
        $this->post('/cotizar', ['nombre' => 'Juan', 'telefono' => '957111222', 'lugar' => 'El Tambo', 'volumen' => 12, 'estructura' => 'LOSA ALIGERADA'])
            ->assertRedirect();

        $contacto = Contacto::sole();
        $this->assertSame('Solicitud de cotización (web)', $contacto->subject);
        $this->assertStringContainsString('12 m³', $contacto->message);
    }

    public function test_cotiza_tu_obra_valida_los_datos_y_descarta_bots(): void
    {
        $this->post('/cotizar', ['nombre' => '', 'telefono' => 'abc', 'lugar' => ''])
            ->assertSessionHasErrors(['nombre', 'telefono', 'lugar']);

        $this->post('/cotizar', ['nombre' => 'Bot', 'telefono' => '999999999', 'lugar' => 'x', 'sitio_web' => 'http://spam']);

        $this->assertSame(0, Contacto::count());
    }

    public function test_el_libro_de_reclamaciones_registra_la_hoja_con_codigo(): void
    {
        $this->post('/libro-reclamaciones', [
            'claimant_name' => 'Pedro Ruiz', 'document_type' => 'DNI', 'document_number' => '12345678',
            'email' => 'pedro@correo.com', 'phone' => '957000222', 'address' => 'Huancayo', 'menor_edad' => '0',
            'bien_tipo' => 'producto', 'bien_descripcion' => '10 m³ de concreto', 'tipo' => 'reclamo',
            'claim_description' => 'Llegó tarde', 'petition' => 'Descuento', 'acepta' => '1',
        ])->assertRedirect()->assertSessionHas('reclamo_codigo');

        $this->assertSame(1, LibroReclamacion::count());
    }

    public function test_por_ngrok_se_ve_la_web_con_https_y_el_panel_no_existe(): void
    {
        // ngrok entrega la visita desde esta PC (127.0.0.1) avisando que llegó por https
        $this->withServerVariables(['REMOTE_ADDR' => '127.0.0.1'])->withHeader('X-Forwarded-Proto', 'https');

        $this->get('http://ab12.ngrok-free.app/')->assertOk()
            ->assertSee('https://ab12.ngrok-free.app/nosotros', false)
            ->assertDontSee('cm-header__login', false);

        foreach (['/admin', '/admin/login', '/operador', '/operador/login'] as $ruta) {
            $this->get('http://ab12.ngrok-free.app' . $ruta)->assertNotFound();
        }
    }

    public function test_desde_la_pc_el_panel_sigue_disponible(): void
    {
        $this->get('/admin/login')->assertOk();
    }

    public function test_alguien_de_afuera_no_puede_hacerse_pasar_por_https(): void
    {
        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.9'])->withHeader('X-Forwarded-Proto', 'https');

        $this->get('http://otro-sitio.test/')
            ->assertSee('http://otro-sitio.test/nosotros', false)
            ->assertDontSee('https://otro-sitio.test/nosotros', false);
    }

    public function test_los_formularios_publicos_funcionan_aunque_falten_roles(): void
    {
        // Instalación nueva sin roles creados: avisar a los administradores no debe tumbar el formulario
        $this->post('/contacto', ['name' => 'Ana', 'email' => 'ana@correo.com', 'phone' => '957000111', 'subject' => 'Consulta', 'message' => 'Hola'])
            ->assertRedirect()
            ->assertSessionHas('success');
    }
}
