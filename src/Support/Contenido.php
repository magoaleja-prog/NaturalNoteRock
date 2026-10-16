<?php
/**
 * src/Support/Contenido.php
 * ---------------------------------------------------------------------
 * Catalogo de recursos de aprendizaje reales y banco de preguntas para
 * los juegos. Centraliza el contenido academico del proyecto.
 *
 * PILAR POO - ABSTRACCION: las vistas piden "recursos de Matemáticas"
 * y no se preocupan por donde viven los datos.
 * ---------------------------------------------------------------------
 */

declare(strict_types=1);

namespace App\Support;

final class Contenido
{
    /**
     * Lecturas y recursos por codigo de materia.
     * @return array<string, array<int, array{titulo:string,resumen:string,url:string,tipo:string}>>
     */
    public static function recursos(): array
    {
        return [
            'MATEM' => [
                ['titulo' => 'Khan Academy - Matemáticas', 'resumen' => 'Cursos completos de álgebra, cálculo y geometría con ejercicios.', 'url' => 'https://es.khanacademy.org/math', 'tipo' => 'Curso'],
                ['titulo' => 'Álgebra de Baldor (PDF)', 'resumen' => 'El clásico libro de álgebra elemental en PDF.', 'url' => 'https://www.uv.mx/personal/ermeneses/files/2018/08/Baldor.pdf', 'tipo' => 'Libro'],
            ],
            'NATUR' => [
                ['titulo' => 'Khan Academy - Biología', 'resumen' => 'Células, genética y ecosistemas explicados paso a paso.', 'url' => 'https://es.khanacademy.org/science/biology', 'tipo' => 'Curso'],
                ['titulo' => 'WWF - Planeta vivo', 'resumen' => 'Cómo proteger la biodiversidad y los ecosistemas.', 'url' => 'https://www.worldwildlife.org/', 'tipo' => 'Portal'],
            ],
            'QUIMI' => [
                ['titulo' => 'Tabla periódica interactiva', 'resumen' => 'Explora elementos, propiedades y reacciones.', 'url' => 'https://ptable.com/?lang=es', 'tipo' => 'Herramienta'],
                ['titulo' => 'Khan Academy - Química', 'resumen' => 'Átomos, enlaces y estequiometría.', 'url' => 'https://es.khanacademy.org/science/chemistry', 'tipo' => 'Curso'],
            ],
            'ESPAN' => [
                ['titulo' => 'Biblioteca Digital Mundial', 'resumen' => 'Textos y manuscritos de todas las culturas.', 'url' => 'https://www.wdl.org/es/', 'tipo' => 'Biblioteca'],
                ['titulo' => 'RAE - Diccionario de la lengua', 'resumen' => 'Consulta dudas de ortografía y vocabulario.', 'url' => 'https://www.rae.es/', 'tipo' => 'Diccionario'],
            ],
            'SOCIA' => [
                ['titulo' => 'Colombia Aprende - Ciencias Sociales', 'resumen' => 'Recursos del Ministerio de Educación de Colombia.', 'url' => 'https://www.colombiaaprende.edu.co/', 'tipo' => 'Portal'],
                ['titulo' => 'Historia Universal - Khan Academy', 'resumen' => 'De las primeras civilizaciones al mundo moderno.', 'url' => 'https://es.khanacademy.org/humanities/world-history', 'tipo' => 'Curso'],
            ],
            'FILOS' => [
                ['titulo' => 'Stanford Encyclopedia of Philosophy', 'resumen' => 'Enciclopedia de filosofía de referencia mundial.', 'url' => 'https://plato.stanford.edu/', 'tipo' => 'Enciclopedia'],
                ['titulo' => 'Filosofía en Khan Academy', 'resumen' => 'Introducción al pensamiento crítico y la ética.', 'url' => 'https://es.khanacademy.org/humanities', 'tipo' => 'Curso'],
            ],
            'TECNO' => [
                ['titulo' => 'freeCodeCamp', 'resumen' => 'Aprende a programar (HTML, CSS, JavaScript) gratis.', 'url' => 'https://www.freecodecamp.org/espanol/', 'tipo' => 'Curso'],
                ['titulo' => 'W3Schools', 'resumen' => 'Referencia práctica de tecnologías web.', 'url' => 'https://www.w3schools.com/', 'tipo' => 'Referencia'],
            ],
            'MEDIA' => [
                ['titulo' => 'SENA - Formación técnica', 'resumen' => 'Programas técnicos y tecnológicos gratuitos.', 'url' => 'https://www.sena.edu.co/es-co/formacion/Paginas/tecnologia.aspx', 'tipo' => 'Institucional'],
                ['titulo' => 'Aprendizaje basado en proyectos', 'resumen' => 'Guía para desarrollar proyectos productivos.', 'url' => 'https://www.edutopia.org/project-based-learning', 'tipo' => 'Guía'],
            ],
            'RELIG' => [
                ['titulo' => 'Valores para vivir en sociedad', 'resumen' => 'Recurso sobre ética, respeto y convivencia.', 'url' => 'https://www.colombiaaprende.edu.co/', 'tipo' => 'Lectura'],
                ['titulo' => 'Educación para la paz', 'resumen' => 'Materiales de cultura de paz y diálogo.', 'url' => 'https://www.un.org/es/peaceandsecurity/', 'tipo' => 'Lectura'],
            ],
            'POLIT' => [
                ['titulo' => 'Constitución Política de Colombia', 'resumen' => 'Texto completo de la Constitución de 1991.', 'url' => 'http://www.secretariasenado.gov.co/constitucion-politica', 'tipo' => 'Documento'],
                ['titulo' => 'Democracia y participación', 'resumen' => 'Cómo funcionan los mecanismos de participación.', 'url' => 'https://www.registraduria.gov.co/', 'tipo' => 'Portal'],
            ],
            'ETICA' => [
                ['titulo' => 'Proyecto de vida', 'resumen' => 'Herramientas para construir tu proyecto de vida.', 'url' => 'https://www.colombiaaprende.edu.co/', 'tipo' => 'Lectura'],
                ['titulo' => 'Dilemas éticos para jóvenes', 'resumen' => 'Casos para debatir decisiones y valores.', 'url' => 'https://www.edutopia.org/', 'tipo' => 'Actividad'],
            ],
            'ESTAD' => [
                ['titulo' => 'Estadística - Khan Academy', 'resumen' => 'Media, probabilidad y visualización de datos.', 'url' => 'https://es.khanacademy.org/math/statistics-probability', 'tipo' => 'Curso'],
                ['titulo' => 'Google Dataset Search', 'resumen' => 'Miles de conjuntos de datos reales para analizar.', 'url' => 'https://datasetsearch.research.google.com/', 'tipo' => 'Herramienta'],
            ],
        ];
    }

    /** Recursos de una materia; si no existe, devuelve un recurso generico. */
    public static function recursosDe(string $codigo): array
    {
        return self::recursos()[$codigo] ?? [
            ['titulo' => 'Colombia Aprende', 'resumen' => 'Recursos educativos abiertos para todas las áreas.', 'url' => 'https://www.colombiaaprende.edu.co/', 'tipo' => 'Portal'],
        ];
    }

    /**
     * Banco de preguntas para el juego "Trivia Rockera".
     * @return array<int, array{p:string, opciones:array<int,string>, correcta:int, materia:string}>
     */
    public static function trivia(): array
    {
        return [
            ['p' => '¿Cuál es el resultado de 7 × 8?', 'opciones' => ['54', '56', '64', '48'], 'correcta' => 1, 'materia' => 'Matemáticas'],
            ['p' => '¿Cuál es el proceso por el que las plantas producen oxígeno?', 'opciones' => ['Respiración', 'Fotosíntesis', 'Fermentación', 'Digestión'], 'correcta' => 1, 'materia' => 'Naturales'],
            ['p' => '¿Qué gas respiramos principalmente para vivir?', 'opciones' => ['Dióxido de carbono', 'Hidrógeno', 'Oxígeno', 'Nitrógeno'], 'correcta' => 2, 'materia' => 'Química'],
            ['p' => '¿Quién escribió "Cien años de soledad"?', 'opciones' => ['García Márquez', 'Borges', 'Cortázar', 'Neruda'], 'correcta' => 0, 'materia' => 'Español'],
            ['p' => '¿En qué año se proclamó la Constitución Política de Colombia?', 'opciones' => ['1886', '1991', '1975', '2001'], 'correcta' => 1, 'materia' => 'Política'],
            ['p' => '¿Cuál es el símbolo químico del oro?', 'opciones' => ['Ag', 'Au', 'Fe', 'Or'], 'correcta' => 1, 'materia' => 'Química'],
            ['p' => 'La media de 4, 8 y 12 es:', 'opciones' => ['6', '8', '9', '24'], 'correcta' => 1, 'materia' => 'Estadística'],
            ['p' => '¿Cuál filósofo dijo "Solo sé que no sé nada"?', 'opciones' => ['Platón', 'Sócrates', 'Aristóteles', 'Descartes'], 'correcta' => 1, 'materia' => 'Filosofía'],
            ['p' => '¿Qué lenguaje se usa para dar estilo a una página web?', 'opciones' => ['HTML', 'CSS', 'SQL', 'PHP'], 'correcta' => 1, 'materia' => 'Tecnología'],
            ['p' => 'La capital de Colombia es:', 'opciones' => ['Medellín', 'Cali', 'Bogotá', 'Bucaramanga'], 'correcta' => 2, 'materia' => 'Sociales'],
        ];
    }
}
