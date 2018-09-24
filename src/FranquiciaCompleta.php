<?php

namespace TrabajoTarjeta;

class FranquiciaCompleta extends Tarjeta implements TarjetaInterface {

    public function obtenerTipo() {
        return "Franquicia Completa";
    }

    public function recargar($monto) {
        return false;
    }

    public function obtenerValorViaje() {
        return 0;
    }

}
