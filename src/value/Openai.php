<?php

namespace panlatent\craft\element\generator\value;

use craft\helpers\App;
use OpenAI\Client;
use yii\base\Component;

/**
 * @property-read Client $client
 */
class Openai extends Component
{
    public string $baseUrl = 'https://api.openai.com/v1/';

    public string $apiKey = '';

    public string $model = 'gpt-3.5-turbo-instruct';

    private ?Client $_client = null;

    public function getApiKey(): string
    {
        return App::parseEnv($this->apiKey);
    }

    public function getBaseUrl(): string
    {
        return App::parseEnv($this->baseUrl);
    }

    public function getModel(): string
    {
        return App::parseEnv($this->model);
    }

    public function getClient(): Client
    {
        if ($this->_client === null) {
            $this->_client = \OpenAI::factory()
                ->withApiKey($this->getApiKey())
                ->withBaseUri($this->getBaseUrl())
                ->make();
        }
        return $this->_client;
    }

    public function generate($input, array $options = []): string
    {
        $options = array_merge([
            "frequency_penalty" => 0,
            "presence_penalty" => 0,
            "temperature" => 0.7,
            "top_k" => 0,
            "top_p" => 0.95,
            "length_penalty" => 1,
            "max_tokens"=> 512,
            "max_new_tokens"=> 512,
            "stop_sequences"=> "<|end_of_text|>,<|eot_id|>",
            "prompt_template"=> "<|begin_of_text|><|start_header_id|>system<|end_header_id|>\\n\\n{system_prompt}<|eot_id|><|start_header_id|>user<|end_header_id|>\\n\\n{prompt}<|eot_id|><|start_header_id|>assistant<|end_header_id|>\\n\\n",
        ], $options);

        $response = $this->getClient()->chat()->create(array_merge([
            'model' => $this->getModel(),
            'messages' => [
                ['role' => 'system', 'content' => 'You are a helpful assistant.'],
                ['role' => 'user', 'content' => $input],
            ],
        ], $options));

        $content = '';
        foreach ($response->choices as $result) {
            $content .= $result->message->content;
        }

        return $content;
    }


}