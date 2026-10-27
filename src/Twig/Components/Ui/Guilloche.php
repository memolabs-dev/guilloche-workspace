<?php

namespace App\Twig\Components\Ui;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

/**
 * Guilloché tracé côté serveur : une rosette (épitrochoïde) par graine,
 * ou une bande de lignes ondulées entrelacées. Le SVG reste lisible sans JavaScript ;
 * le CSS ne fait qu'animer le tracé jusqu'à l'avancement.
 */
#[AsTwigComponent]
final class Guilloche
{
    private const LOOPS = [14, 18, 22, 16, 26, 20, 24];

    private static int $instances = 0;

    public string $variant = 'rosette';
    public int $seed = 1;
    public ?int $progress = null;
    public int $size = 40;
    public ?string $label = null;
    /** Anime l'impression du trait : réservé au moment où la valeur apparaît. */
    public bool $animated = false;

    /**
     * Identifiant propre à l'instance, pour que les <use> visent la contre-courbe de leur rosette.
     * Un compteur par processus suffit : chaque requête rend sa page, une même page ne se répète pas.
     */
    public readonly string $uid;

    public function __construct()
    {
        $this->uid = 'guilloche-'.++self::$instances;
    }

    /**
     * Angles des copies de la contre-courbe : leur décalage tisse la trame.
     *
     * @return list<float>
     */
    public function weave(): array
    {
        $loops = self::LOOPS[$this->index($this->seed + 2)];

        return array_map(static fn (int $i): float => round($i * 360 / $loops / 4, 2), [1, 2, 3]);
    }

    /** Courbe principale, celle qui porte l'avancement. */
    public function trace(): string
    {
        $loops = self::LOOPS[$this->index($this->seed - 1)];
        $depth = 0.34 + 0.05 * (($this->seed % 4 + 4) % 4);

        return $this->epitrochoid($loops, $depth, 460, 0.0);
    }

    /** Contre-courbe intérieure, décalée d'un demi-lobe. */
    public function counter(): string
    {
        $loops = self::LOOPS[$this->index($this->seed + 2)];

        return $this->epitrochoid($loops, 0.22, 300, M_PI / $loops);
    }

    /** @return list<string> Lignes de la bande, une par phase. */
    public function bandLines(): array
    {
        $lines = [];
        for ($i = 0; $i < 9; ++$i) {
            $phase = $i * 0.7 + $this->seed;
            $points = [];
            for ($x = 0; $x <= 1200; $x += 12) {
                $t = $x / 1200 * 2 * M_PI;
                $y = 60 + 22 * sin(3 * $t + $phase) + 9 * sin(11 * $t - $phase * 1.6) + ($i - 4) * 2.2;
                $points[] = [$x, $y];
            }
            $lines[] = $this->path($points);
        }

        return $lines;
    }

    /** Index toujours valide, y compris pour une graine nulle ou négative. */
    private function index(int $n): int
    {
        $count = count(self::LOOPS);

        return (($n % $count) + $count) % $count;
    }

    public function isComplete(): bool
    {
        return null !== $this->progress && $this->progress >= 100;
    }

    /**
     * Épitrochoïde normalisée dans un carré de 1000 unités centré en 500,500.
     * $loops boucles, $depth proportion de la boucle par rapport au rayon.
     */
    private function epitrochoid(int $loops, float $depth, float $radius, float $rotation): string
    {
        $r = 1.0;
        $big = $loops * $r;
        $d = $r * (1 + $depth * 6);
        $extent = $big + $r + $d;
        $steps = $loops * 22;
        $points = [];
        for ($i = 0; $i <= $steps; ++$i) {
            $t = $i / $steps * 2 * M_PI;
            $x = ($big + $r) * cos($t) - $d * cos(($big + $r) / $r * $t);
            $y = ($big + $r) * sin($t) - $d * sin(($big + $r) / $r * $t);
            $c = cos($rotation - M_PI / 2);
            $s = sin($rotation - M_PI / 2);
            $points[] = [500 + ($x * $c - $y * $s) / $extent * $radius, 500 + ($x * $s + $y * $c) / $extent * $radius];
        }

        return $this->path($points);
    }

    /**
     * Chemin relatif en entiers : compact et suffisamment précis à l'échelle d'affichage.
     *
     * @param list<array{float|int, float|int}> $points
     */
    private function path(array $points): string
    {
        if ([] === $points) {
            return '';
        }
        [$px, $py] = [(int) round($points[0][0]), (int) round($points[0][1])];
        $out = 'M'.$px.' '.$py;
        foreach (array_slice($points, 1) as [$x, $y]) {
            [$nx, $ny] = [(int) round($x), (int) round($y)];
            $out .= 'l'.($nx - $px).' '.($ny - $py);
            [$px, $py] = [$nx, $ny];
        }

        return $out;
    }
}
