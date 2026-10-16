<?php
/**
 * src/Support/Jardin.php
 * ---------------------------------------------------------------------
 * Traduce una nota (1.0 - 5.0) en el estado del JARDIN VIVO.
 *
 * PILAR POO - ABSTRACCION + ENCAPSULAMIENTO:
 *   El resto del sistema solo llama a Jardin::paraNota($valor) y recibe
 *   un arreglo listo. Las reglas pedagogicas (umbrales, musica, plagas)
 *   quedan encerradas aqui y se cambian en un solo lugar.
 * ---------------------------------------------------------------------
 */

declare(strict_types=1);

namespace App\Support;

final class Jardin
{
    /** Umbrales de rendimiento. */
    public const ALTO  = 4.0;
    public const MEDIO = 3.0;

    /**
     * Devuelve la descripcion completa del jardin para una nota dada.
     *
     * @return array{
     *   estado:string, etiqueta:string, emoji:string, musica:string,
     *   descripcion:string, color:string, factor:float,
     *   flores:int, frutos:int, aves:int, insectos:int, plagas:int
     * }
     */
    public static function paraNota(?float $nota): array
    {
        // Sin nota: jardin en barbecho (semillas esperando).
        if ($nota === null) {
            return [
                'estado'      => 'sin_nota',
                'etiqueta'    => 'Sin notas aun',
                'emoji'       => '🌱',
                'musica'      => 'calm',
                'descripcion' => 'Tu jardin espera su primera evaluacion. ¡Animo!',
                'color'       => '#8d6e63',
                'factor'      => 0.15,
                'flores'      => 2, 'frutos' => 0, 'aves' => 0,
                'insectos'    => 1, 'plagas' => 0,
            ];
        }

        if ($nota >= self::ALTO) {
            return [
                'estado'      => 'alto',
                'etiqueta'    => 'Jardin florecido',
                'emoji'       => '🌸',
                'musica'      => 'rock_alegre',
                'descripcion' => '¡Espectacular! Flores, frutos, aves y polinizadores llenan tu jardin.',
                'color'       => '#43a047',
                'factor'      => min(1.0, ($nota - self::ALTO) / (5.0 - self::ALTO) * 0.5 + 0.5),
                'flores'      => 12, 'frutos' => 6, 'aves' => 4,
                'insectos'    => 8, 'plagas' => 0,
            ];
        }

        if ($nota >= self::MEDIO) {
            return [
                'estado'      => 'medio',
                'etiqueta'    => 'Crecimiento moderado',
                'emoji'       => '🌿',
                'musica'      => 'rock_medio',
                'descripcion' => 'Tu jardin crece. Un poco mas de riego (estudio) y florecera.',
                'color'       => '#9e9d24',
                'factor'      => 0.4 + ($nota - self::MEDIO) / (self::ALTO - self::MEDIO) * 0.25,
                'flores'      => 5, 'frutos' => 1, 'aves' => 1,
                'insectos'    => 3, 'plagas' => 1,
            ];
        }

        return [
            'estado'      => 'bajo',
            'etiqueta'    => 'Plantas marchitas y plagas',
            'emoji'       => '🥀',
            'musica'      => 'rock_melancolico',
            'descripcion' => 'Tu jardin necesita cuidados urgentes: hay plantas marchitas y plagas. ¡Vamos a recuperarlo!',
            'color'       => '#8d6e63',
            'factor'      => max(0.1, $nota / self::MEDIO * 0.35),
            'flores'      => 1, 'frutos' => 0, 'aves' => 0,
            'insectos'    => 1, 'plagas' => 6,
        ];
    }

    /** Etiqueta corta segun la nota, util en tablas. */
    public static function etiquetaRapida(?float $nota): string
    {
        return self::paraNota($nota)['etiqueta'];
    }
}
