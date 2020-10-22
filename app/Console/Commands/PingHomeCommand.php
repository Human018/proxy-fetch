<?php
namespace App\Console\Commands;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Console\Command;


class PingHomeCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = "ping:home";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Let home know we're ready for instructions";


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $home = new Client();
        $response = $home->request('GET',
            env('PING_TARGET'),
            [
                'query' => [
                    'id' => config('app.name')
                ]
            ]
        );
        $body = $response->getBody();
        $commands = json_decode($body);

        if($commands->search) {
            $this->info('Engine: ' . $commands->search->engine);

            $client = new Client();
            $response = $client->request(
                'GET',
                $commands->search->base_uri,
                [
                    'query' => json_decode(json_encode($commands->search->params), true),
                    'headers' => json_decode(json_encode($commands->search->headers), true)
                ]
            );
            $body = $response->getBody();
            $content = $body->getContents();
            dump($content);
        } else {
        }
    }
}
