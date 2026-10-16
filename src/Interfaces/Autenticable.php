<?php
/**
 * src/Interfaces/Autenticable.php
 * ---------------------------------------------------------------------
 * Contrato para todo aquello que puede iniciar sesion.
 *
 * PILAR POO - ABSTRACCION:
 *   Es una interface: define QUE se puede hacer (autenticar, rol, panel)
 *   sin decir COMO. Cualquier clase que la implemente se compromete a
 *   cumplir el contrato.
 * ---------------------------------------------------------------------
 */

declare(strict_types=1);

namespace App\Interfaces;

interface Autenticable
{
    /** Verifica la contrasena en texto plano contra el hash almacenado. */
    public function autenticar(string $password): bool;

    /** Devuelve el rol: administrador | profesor | estudiante. */
    public function getRol(): string;

    /** Ruta del panel principal segun el rol (POLIMORFISMO). */
    public function panelInicio(): string;

    /** Lista de permisos exclusivos del rol. */
    public function permisos(): array;
}
