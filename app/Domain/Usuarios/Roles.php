<?php

namespace App\Domain\Usuarios;

/**
 * Roles definidos en el doc del proyecto (sección "Usuarios y roles").
 * Un usuario tiene un rol base; publicar un servicio/comercio/etc. no
 * cambia el rol, solo crea un registro asociado a su user_id.
 */
final class Roles
{
    public const VECINO = 'vecino';

    public const PRESTADOR = 'prestador';

    public const COMERCIANTE = 'comerciante';

    public const PRODUCTOR = 'productor';

    public const EMPLEADOR = 'empleador';

    public const ADMIN = 'admin';

    public static function todos(): array
    {
        return [
            self::VECINO,
            self::PRESTADOR,
            self::COMERCIANTE,
            self::PRODUCTOR,
            self::EMPLEADOR,
            self::ADMIN,
        ];
    }

    /**
     * Roles que un vecino puede elegir al registrarse. El rol admin
     * queda afuera a propósito: se asigna desde la base, no por la API.
     */
    public static function autoregistro(): array
    {
        return array_values(array_diff(self::todos(), [self::ADMIN]));
    }
}
