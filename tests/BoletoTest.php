<?php

namespace TrabajoTarjeta;

use PHPUnit\Framework\TestCase;

class BoletoTest extends TestCase {

  /**
   * Comprueba que el constructor y los metodos de obtener en Boleto funcionen correctamente
   */
  public function testObtenerDatos() {
    $tiempo = new TiempoFalso(0);
    $tarjeta = new Tarjeta(1, $tiempo);
    $colectivo = new Colectivo("102R", "Semtur", 120);

    $boleto = new Boleto($colectivo, $tarjeta);

    // Existe un conflicto entre la interpretación de fecha en Travis con PHP 7.1 y en PHPUnit con PHP 7.2.4
    $descripcion1 = "Linea: 102R\n";
    $fechaPHPUnit = "01/01/1970 01:00:00";
    $fechaTravis = "01/01/1970 00:00:00";
    $descripcion2 = "\nNormal \$0\nTotal abonado: \$0\nSaldo(S.E.U.O): \$0\nTarjeta: 1";

    $descripcionPHPUnit = "{$descripcion1}{$fechaPHPUnit}{$descripcion2}";
    $descripcionTravis = "{$descripcion1}{$fechaTravis}{$descripcion2}";

    $this->assertEquals($boleto->obtenerValor(), 0);
    $this->assertEquals($boleto->obtenerColectivo(), $colectivo);
    $this->assertEquals($boleto->obtenerTarjeta(), $tarjeta);
    $this->assertContains($boleto->obtenerFecha(), [$fechaPHPUnit, $fechaTravis]);
    $this->assertEquals($boleto->obtenerTarjetaTipo(), "Normal");
    $this->assertEquals($boleto->obtenerTarjetaID(), 1);
    $this->assertEquals($boleto->obtenerTarjetaSaldo(), 0);
    $this->assertEquals($boleto->obtenerAbonado(), 0);
    $this->assertEquals($boleto->obtenerTipo(), "Normal");
    $this->assertContains($boleto->obtenerDescripcion(), [$descripcionPHPUnit, $descripcionTravis]);
  }

  public function testTarjetaNormal() {
    $tiempo = new TiempoFalso(1433338200); // se inicializa en el 03/06/2015 a las 15:30 segun PHPUnit
    $colectivo = new Colectivo("133N", "Semtur", 120);
    $colectivo2 = new Colectivo("101", "Semtur", 89);
    $tarjeta = new Tarjeta(1, $tiempo);

    $tarjeta->recargar(20);

    $valor = $tarjeta->obtenerValorUltimoViaje($colectivo->linea());
    $tarjeta->pagar($colectivo->linea());
    $boleto = new Boleto($colectivo, $tarjeta);
    $descripcion1 = "Linea: 133N\n";
    $fechaPHPUnit = "03/06/2015 15:30:00";
    $fechaTravis = "03/06/2015 13:30:00";
    $descripcion2 = "\nNormal \$14.8\nTotal abonado: \$14.8\nSaldo(S.E.U.O): \$5.2\nTarjeta: 1";
    $descripcionPHPUnit = "{$descripcion1}{$fechaPHPUnit}{$descripcion2}";
    $descripcionTravis = "{$descripcion1}{$fechaTravis}{$descripcion2}";
    $this->assertContains($boleto->obtenerDescripcion(), [$descripcionPHPUnit, $descripcionTravis]);

    $valor = $tarjeta->obtenerValorUltimoViaje($colectivo->linea());
    $tarjeta->pagar($colectivo->linea());
    $boleto = new Boleto($colectivo, $tarjeta);
    $descripcion2 = "\nViaje Plus 1 \$0.00\nSaldo(S.E.U.O): \$5.2\nTarjeta: 1";
    $descripcionPHPUnit = "{$descripcion1}{$fechaPHPUnit}{$descripcion2}";
    $descripcionTravis = "{$descripcion1}{$fechaTravis}{$descripcion2}";
    $this->assertContains($boleto->obtenerDescripcion(), [$descripcionPHPUnit, $descripcionTravis]);

    $valor = $tarjeta->obtenerValorUltimoViaje($colectivo->linea());
    $tarjeta->pagar($colectivo->linea());
    $boleto = new Boleto($colectivo, $tarjeta);
    $descripcion2 = "\nViaje Plus 2 \$0.00\nSaldo(S.E.U.O): \$5.2\nTarjeta: 1";
    $descripcionPHPUnit = "{$descripcion1}{$fechaPHPUnit}{$descripcion2}";
    $descripcionTravis = "{$descripcion1}{$fechaTravis}{$descripcion2}";
    $this->assertContains($boleto->obtenerDescripcion(), [$descripcionPHPUnit, $descripcionTravis]);

    $tarjeta->recargar(100); // saldo: 105.2
    $valor = $tarjeta->obtenerValorUltimoViaje($colectivo2->linea());
    $tarjeta->pagar($colectivo2->linea());
    $boleto = new Boleto($colectivo2, $tarjeta);
    $descripcion1 = "Linea: 101\n";
    $descripcion2 = "\nAbona 2 Viajes Plus \$29.6 y\nNormal Transbordo \$4.88\nTotal abonado: \$34.48\nSaldo(S.E.U.O): \$70.72\nTarjeta: 1";
    $descripcionPHPUnit = "{$descripcion1}{$fechaPHPUnit}{$descripcion2}";
    $descripcionTravis = "{$descripcion1}{$fechaTravis}{$descripcion2}";
    $this->assertContains($boleto->obtenerDescripcion(), [$descripcionPHPUnit, $descripcionTravis]);
  }

  public function testTarjetaFranquiciaMedia() {
    $tiempo = new TiempoFalso(1433338200); // se inicializa en el 03/06/2015 a las 15:30 segun PHPUnit
    $colectivo = new Colectivo("133N", "Semtur", 120);
    $colectivo2 = new Colectivo("101", "Semtur", 89);
    $tarjeta = new FranquiciaMedia(2, $tiempo);

    $tarjeta->recargar(10);

    // se usa un Medio
    $valor = $tarjeta->obtenerValorUltimoViaje($colectivo->linea());
    $tarjeta->pagar($colectivo->linea());
    $boleto = new Boleto($colectivo, $tarjeta);
    $descripcion1 = "Linea: 133N\n";
    $fechaPHPUnit = "03/06/2015 15:30:00";
    $fechaTravis = "03/06/2015 13:30:00";
    $descripcion2 = "\nMedio \$7.4\nTotal abonado: \$7.4\nSaldo(S.E.U.O): \$2.6\nTarjeta: 2";
    $descripcionPHPUnit = "{$descripcion1}{$fechaPHPUnit}{$descripcion2}";
    $descripcionTravis = "{$descripcion1}{$fechaTravis}{$descripcion2}";
    $this->assertContains($boleto->obtenerDescripcion(), [$descripcionPHPUnit, $descripcionTravis]);

    // se gasta 1 Plus
    $valor = $tarjeta->obtenerValorUltimoViaje($colectivo->linea());
    $tarjeta->pagar($colectivo->linea());
    $boleto = new Boleto($colectivo, $tarjeta);
    $descripcion2 = "\nViaje Plus 1 \$0.00\nSaldo(S.E.U.O): \$2.6\nTarjeta: 2";
    $descripcionPHPUnit = "{$descripcion1}{$fechaPHPUnit}{$descripcion2}";
    $descripcionTravis = "{$descripcion1}{$fechaTravis}{$descripcion2}";
    $this->assertContains($boleto->obtenerDescripcion(), [$descripcionPHPUnit, $descripcionTravis]);

    $tarjeta->recargar(100); // saldo: 102.6
    $tiempo->avanzar(360); // 6 minutos para Medio y Abonar el Plus
    $valor = $tarjeta->obtenerValorUltimoViaje($colectivo->linea());
    $tarjeta->pagar($colectivo->linea());
    $boleto = new Boleto($colectivo, $tarjeta);
    $fechaPHPUnit = "03/06/2015 15:36:00";
    $fechaTravis = "03/06/2015 13:36:00";
    $descripcion2 = "\nAbona 1 Viaje Plus \$14.8 y\nMedio \$7.4\nTotal abonado: \$22.2\nSaldo(S.E.U.O): \$80.4\nTarjeta: 2";
    $descripcionPHPUnit = "{$descripcion1}{$fechaPHPUnit}{$descripcion2}";
    $descripcionTravis = "{$descripcion1}{$fechaTravis}{$descripcion2}";
    $this->assertContains($boleto->obtenerDescripcion(), [$descripcionPHPUnit, $descripcionTravis]);

    $tiempo->avanzar(360); // 6 minutos, aunque ya se gastaron los medios del dia
    $valor = $tarjeta->obtenerValorUltimoViaje($colectivo->linea());
    $tarjeta->pagar($colectivo->linea());
    $boleto = new Boleto($colectivo, $tarjeta);
    $fechaPHPUnit = "03/06/2015 15:42:00";
    $fechaTravis = "03/06/2015 13:42:00";
    $descripcion2 = "\nNormal \$14.8\nTotal abonado: \$14.8\nSaldo(S.E.U.O): \$65.6\nTarjeta: 2";
    $descripcionPHPUnit = "{$descripcion1}{$fechaPHPUnit}{$descripcion2}";
    $descripcionTravis = "{$descripcion1}{$fechaTravis}{$descripcion2}";
    $this->assertContains($boleto->obtenerDescripcion(), [$descripcionPHPUnit, $descripcionTravis]);

    $tiempo->avanzar(60 * 30); // 30 minutos para Transbordo
    $valor = $tarjeta->obtenerValorUltimoViaje($colectivo2->linea());
    $tarjeta->pagar($colectivo2->linea());
    $boleto = new Boleto($colectivo2, $tarjeta);
    $descripcion1 = "Linea: 101\n";
    $fechaPHPUnit = "03/06/2015 16:12:00";
    $fechaTravis = "03/06/2015 14:12:00";
    $descripcion2 = "\nNormal Transbordo \$4.88\nTotal abonado: \$4.88\nSaldo(S.E.U.O): \$60.72\nTarjeta: 2";
    $descripcionPHPUnit = "{$descripcion1}{$fechaPHPUnit}{$descripcion2}";
    $descripcionTravis = "{$descripcion1}{$fechaTravis}{$descripcion2}";
    $this->assertContains($boleto->obtenerDescripcion(), [$descripcionPHPUnit, $descripcionTravis]);

    $tiempo->avanzar(86400); // 1 dia para poder usar Medio
    $valor = $tarjeta->obtenerValorUltimoViaje($colectivo->linea());
    $tarjeta->pagar($colectivo->linea());
    $boleto = new Boleto($colectivo, $tarjeta);
    $descripcion1 = "Linea: 133N\n";
    $fechaPHPUnit = "04/06/2015 16:12:00";
    $fechaTravis = "04/06/2015 14:12:00";
    $descripcion2 = "\nMedio \$7.4\nTotal abonado: \$7.4\nSaldo(S.E.U.O): \$53.32\nTarjeta: 2";
    $descripcionPHPUnit = "{$descripcion1}{$fechaPHPUnit}{$descripcion2}";
    $descripcionTravis = "{$descripcion1}{$fechaTravis}{$descripcion2}";
    $this->assertContains($boleto->obtenerDescripcion(), [$descripcionPHPUnit, $descripcionTravis]);

    $tiempo->avanzar(60 * 15); // 15 minutos para Medio Transbordo
    $valor = $tarjeta->obtenerValorUltimoViaje($colectivo2->linea());
    $tarjeta->pagar($colectivo2->linea());
    $boleto = new Boleto($colectivo2, $tarjeta);
    $descripcion1 = "Linea: 101\n";
    $fechaPHPUnit = "04/06/2015 16:27:00";
    $fechaTravis = "04/06/2015 14:27:00";
    $descripcion2 = "\nMedio Transbordo \$2.44\nTotal abonado: \$2.44\nSaldo(S.E.U.O): \$50.88\nTarjeta: 2";
    $descripcionPHPUnit = "{$descripcion1}{$fechaPHPUnit}{$descripcion2}";
    $descripcionTravis = "{$descripcion1}{$fechaTravis}{$descripcion2}";
    $this->assertContains($boleto->obtenerDescripcion(), [$descripcionPHPUnit, $descripcionTravis]);
  }

  public function testTarjetaFranquiciaCompleta() {
    $tiempo = new TiempoFalso(1433338200); // se inicializa en el 03/06/2015 a las 15:30 segun PHPUnit
    $colectivo = new Colectivo("133N", "Semtur", 120);
    $tarjeta = new FranquiciaCompleta(3, $tiempo);

    $tarjeta->recargar(10); // no recarga por ser franquicia completa

    $valor = $tarjeta->obtenerValorUltimoViaje($colectivo->linea());
    $tarjeta->pagar($colectivo->linea());
    $boleto = new Boleto($colectivo, $tarjeta);
    $descripcion1 = "Linea: 133N\n";
    $fechaPHPUnit = "03/06/2015 15:30:00";
    $fechaTravis = "03/06/2015 13:30:00";
    $descripcion2 = "\nFranquicia Completa \$0\nTotal abonado: \$0\nSaldo(S.E.U.O): \$0\nTarjeta: 3";
    $descripcionPHPUnit = "{$descripcion1}{$fechaPHPUnit}{$descripcion2}";
    $descripcionTravis = "{$descripcion1}{$fechaTravis}{$descripcion2}";
    $this->assertContains($boleto->obtenerDescripcion(), [$descripcionPHPUnit, $descripcionTravis]);

    $valor = $tarjeta->obtenerValorUltimoViaje($colectivo->linea());
    $tarjeta->pagar($colectivo->linea());
    $boleto = new Boleto($colectivo, $tarjeta);
    $this->assertContains($boleto->obtenerDescripcion(), [$descripcionPHPUnit, $descripcionTravis]);

    $tarjeta->recargar(100); // saldo: 0
    $tiempo->avanzar(360); // 6 minutos
    $valor = $tarjeta->obtenerValorUltimoViaje($colectivo->linea());
    $tarjeta->pagar($colectivo->linea());
    $boleto = new Boleto($colectivo, $tarjeta);
    $fechaPHPUnit = "03/06/2015 15:36:00";
    $fechaTravis = "03/06/2015 13:36:00";
    $descripcionPHPUnit = "{$descripcion1}{$fechaPHPUnit}{$descripcion2}";
    $descripcionTravis = "{$descripcion1}{$fechaTravis}{$descripcion2}";
    $this->assertContains($boleto->obtenerDescripcion(), [$descripcionPHPUnit, $descripcionTravis]);

    $tiempo->avanzar(360); // 6 minutos
    $valor = $tarjeta->obtenerValorUltimoViaje($colectivo->linea());
    $tarjeta->pagar($colectivo->linea());
    $boleto = new Boleto($colectivo, $tarjeta);
    $fechaPHPUnit = "03/06/2015 15:42:00";
    $fechaTravis = "03/06/2015 13:42:00";
    $descripcionPHPUnit = "{$descripcion1}{$fechaPHPUnit}{$descripcion2}";
    $descripcionTravis = "{$descripcion1}{$fechaTravis}{$descripcion2}";
    $this->assertContains($boleto->obtenerDescripcion(), [$descripcionPHPUnit, $descripcionTravis]);
  }

}
