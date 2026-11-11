    public function test_service_rejects_invalid_json_format(): void
    {
        Http::fake(['*' => Http::response(['choices' => [['message' => ['content' => 'bukan json']]]])]);

        $this->expectExceptionMessage('Format respons AI tidak valid.');
        app(OpenAiPostGeneratorService::class)->generate('x', 1);
    }

    public function test_service_rejects_when_no_valid_articles(): void
    {
        $this->fakeOpenAi([['title' => '', 'content' => '']]);

        $this->expectExceptionMessage('AI tidak menghasilkan artikel yang valid.');
        app(OpenAiPostGeneratorService::class)->generate('x', 1);
    }