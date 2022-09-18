<?php

namespace Amot\Conversate\Console\Commands;

use Amot\Conversate\SocketService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;

class StartServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'conversate:start
                                            {--P|port=6001 : The port to serve on}
                                            {--S|secure : Whether to serve using ssl or not}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Starts Conversate API Server';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $port = $this->option("port");
        $secure = $this->option("secure");
        require_once base_path("routes/actions.php");
        if($secure){
            $app = new \Ratchet\Http\HttpServer(
                new \Ratchet\WebSocket\WsServer(
                    new SocketService()
                )
            );
            $loop = \React\EventLoop\Loop::get();

            $wss = new \React\Socket\SocketServer("0.0.0.0:$port");

            $wss = new \React\Socket\SecureServer($wss, $loop, [
                'local_cert' => config('conversate.ssl_cert'),
                'local_pk' => config('conversate.ssl_key'),
                'verify_peer' => false
            ]);

            $server = new \Ratchet\Server\IoServer($app, $wss, $loop);
            echo "Secure Mode...\n";
            echo "Waiting for connections on port $port...\n";
            $server->run();
        }else{
            $server = IoServer::factory(
                new HttpServer(
                    new WsServer(
                        new SocketService()
                    )
                ),
                $port
            );
            echo "Non Secure Mode...\n";
            echo "Waiting for connections on port $port...\n";
            $server->run();
        }
      return 0;
    }
}
