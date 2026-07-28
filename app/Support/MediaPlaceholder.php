<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Placeholder imagery for seeded demo content.
 *
 * Every URL here is deterministic — the same coach always gets the same face —
 * so the demo looks stable across reseeds rather than reshuffling on refresh.
 *
 * Only the seeders call this. Once the board uploads real photography through
 * the admin panel, the columns hold relative disk paths instead and
 * ResolvesMedia serves those; nothing else needs to change.
 *
 * Note on sources: images.unsplash.com is not reliably reachable from Iran, so
 * these use services that are.
 */
final class MediaPlaceholder
{
    /**
     * Wide scene — hero slides, news covers, event posters.
     *
     * The seed is hashed rather than URL-encoded: percent-encoding a Persian
     * slug expands every character to six bytes and blows past the 255-char
     * column. Hashing keeps it short and still deterministic.
     */
    public static function scene(string $seed, int $width = 1600, int $height = 900): string
    {
        return sprintf('https://picsum.photos/seed/%s/%d/%d', self::token($seed), $width, $height);
    }

    private static function token(string $seed): string
    {
        return substr(md5($seed), 0, 12);
    }

    /** Portrait photo for a person. pravatar offers 70 distinct faces. */
    public static function portrait(string $seed, int $size = 600): string
    {
        $index = (crc32($seed) % 70) + 1;

        return sprintf('https://i.pravatar.cc/%d?img=%d', $size, $index);
    }

    /** Lettermark standing in for a sponsor logo. */
    public static function logo(string $name): string
    {
        return sprintf(
            'https://ui-avatars.com/api/?name=%s&size=256&background=141F4A&color=D97706&bold=true&format=png',
            urlencode(mb_substr($name, 0, 2)),
        );
    }
}
