<?php

namespace Tests\Unit;

use App\Models\Inicio;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * El número de WhatsApp se puede escribir como sea; el enlace siempre sale con el código de Perú.
 */
class WhatsappTest extends TestCase
{
    public static function numeros(): array
    {
        return [
            'con espacios'          => ['957 924 586', '51957924586'],
            'con +51 y guiones'     => ['+51 957-924-586', '51957924586'],
            'ya con código de país' => ['51957924586', '51957924586'],
        ];
    }

    #[DataProvider('numeros')]
    public function test_normaliza_el_numero(string $escrito, string $esperado): void
    {
        $this->assertSame($esperado, Inicio::normalizarWhatsapp($escrito));
    }

    public function test_sin_numero_no_hay_enlace(): void
    {
        $this->assertNull(Inicio::normalizarWhatsapp(''));
        $this->assertNull(Inicio::enlaceWhatsapp(null));
    }

    public function test_el_enlace_incluye_el_mensaje_codificado(): void
    {
        $this->assertSame('https://wa.me/51957924586?text=Hola%20Concretera', Inicio::enlaceWhatsapp('957 924 586', 'Hola Concretera'));
    }
}
