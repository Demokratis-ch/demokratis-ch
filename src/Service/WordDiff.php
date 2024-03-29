<?php

namespace App\Service;

use DiffMatchPatch\DiffMatchPatch;
use DiffMatchPatch\DiffToolkit;
use DiffMatchPatch\Utils;

class WordDiff extends DiffToolkit
{
    public function diff(string $text1, string $text2): array
    {
        $a = $this->linesToWords($text1, $text2);

        $lineText1 = $a[0];
        $lineText2 = $a[1];
        $lineArray = $a[2];

        $dmp = new DiffMatchPatch();

        $diffs = $dmp->diff_main($lineText1, $lineText2, false);
        $this->charsToLines($diffs, $lineArray);

        return $diffs;
    }

    /**
     * See DiffMatchPatch\DiffToolkit for the original code.
     */
    public function linesToWords(string $text1, string $text2): array
    {
        // e.g. $lineArray[4] == "Hello\n"
        $lineArray = [];
        // e.g. $lineHash["Hello\n"] == 4
        $lineHash = [];

        // "\x00" is a valid character, but various debuggers don't like it.
        // So we'll insert a junk entry to avoid generating a null character.
        $lineArray[] = '';

        $chars1 = $this->linesToWordsMunge($text1, $lineArray, $lineHash);
        $chars2 = $this->linesToWordsMunge($text2, $lineArray, $lineHash);

        return [$chars1, $chars2, $lineArray];
    }

    /**
     * See DiffMatchPatch\DiffToolkit for the original code.
     *
     * The only difference is the delimiter: whitespace instead of newline to produce a word diff.
     *
     * See https://github.com/google/diff-match-patch/wiki/Line-or-Word-Diffs
     */
    protected function linesToWordsMunge(string $text, array &$lineArray, array &$lineHash): string
    {
        // different line break types mess up the diff
        $text = preg_replace('~\R~u', "\n", $text);

        // Simple string concat is even faster than implode() in PHP.
        $chars = '';

        // explode('\n', $text) would temporarily double our memory footprint,
        // but mb_strpos() and mb_substr() work too slow
        // preg_split may be even slower, but it rocks :)
        $lines = preg_split('/\b/', $text);

        foreach ($lines as $i => $line) {
            if (!isset($lineHash[$line])) {
                $lineArray[] = $line;
                $lineHash[$line] = count($lineArray) - 1;
            }
            $chars .= Utils::unicodeChr($lineHash[$line]);
        }

        return $chars;
    }
}
