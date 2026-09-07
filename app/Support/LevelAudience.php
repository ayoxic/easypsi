<?php

namespace App\Support;

class LevelAudience
{
    public static function matches(string $audience, string $selection): bool
    {
        [$level, $tracks] = array_pad(explode('::', $audience, 2), 2, '');
        [$selectedLevel, $selectedTracks] = array_pad(explode('::', $selection, 2), 2, '');

        return $level === $selectedLevel && ($tracks === '' || $selectedTracks === ''
            || count(array_intersect(explode('|', $tracks), explode('|', $selectedTracks))) > 0);
    }

    public static function choices(string $key, string $label): array
    {
        [$level, $tracks] = array_pad(explode('::', $key, 2), 2, '');
        [$levelLabel, $trackLabels] = array_pad(explode(' / ', $label, 2), 2, '');
        if ($tracks === '') {
            return [$key => $label];
        }
        $labels = explode(' + ', $trackLabels);
        $choices = [];
        foreach (explode('|', $tracks) as $index => $track) {
            $choices[$level.'::'.$track] = $levelLabel.' / '.($labels[$index] ?? $track);
        }

        return $choices;
    }
}
