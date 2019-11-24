<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Bluerhinos\phpMQTT;
use App\Temperature;

class GetTemperature implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $mqtt;

    public function __construct()
    {
        $client_id = uniqid();
        $this->mqtt = new phpMQTT(env('MQTT_HOST'), env('MQTT_PORT'), $client_id);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (!$this->mqtt->connect(true, NULL, env('MQTT_USERNAME'), env('MQTT_PASSWORD'))) {
            exit(1);
        }

        $topics["esp/test"] = [
            "qos" => 1,
            "function" => [$this, "procmsg"],
        ];

        $this->mqtt->subscribe($topics, 0);
        while ($this->mqtt->proc()) { }
        $this->mqtt->close();
    }

    function procmsg($topic, $msg)
    {
        if (!empty($msg)) {
            Temperature::create([
                'templrature' => $msg,
            ]);
        }
    }
}
