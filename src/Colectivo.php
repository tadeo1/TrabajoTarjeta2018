<?php

namespace TrabajoTarjeta;

class Colectivo implements ColectivoInterface {

  protected $linea; // string
  protected $empresa; // string
  protected $numero; // int

  public function __construct($linea, $empresa, $numero) {
    $this->linea = $linea;
    $this->empresa = $empresa;
    $this->numero = $numero;
  }

  public function linea() : string {
    return $this->linea;
  }

  public function empresa() : string {
    return $this->empresa;
  }

  public function numero() : int {
    return $this->numero;
  }

  public function pagarCon(TarjetaInterface $tarjeta) {
    if ($tarjeta->pagar($this->linea)) {
      return (new Boleto($this, $tarjeta));
    }
    return false;
  }

}
