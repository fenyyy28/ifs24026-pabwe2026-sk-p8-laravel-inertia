<?php

namespace App\Features\Ai\Contracts;

use App\Features\Ai\DTO\GeneratedPostItem;

interface AiClientInterface
{
    /**
     * @return list<GeneratedPostItem>
     *
     * @throws \RuntimeException jika layanan AI gagal atau respons tidak valid
     */
    public function generate(string $topic, int $count): array;
}
