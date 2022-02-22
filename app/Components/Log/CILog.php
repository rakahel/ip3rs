<?php

namespace App\Components\Log;

use App\Models\Log;
use Illuminate\Support\Facades\Session;

/**
 * Log Code
 * 00000    => Success
 * R0001    => Data not found
 * R0002    => Not Authenticated
 * R0003    => Other Errors
 *
 * F0001    => Failed to validating form
 * F0002    => Failed to save data
 * F0003    => Failed to update data
 * F0004    => Failed to delete data
 */

 /**
  * Log Type
  * Emergency
  * Alert
  * Critical
  * Error
  * Warning
  * Notice
  * Info
  * Debug
  */
class CILog extends Log
{
    protected $session_id;

    public function __construct()
    {
        if(!Session::has('cilog.session_id')) {
            throw new \Exception('cilog.session_id harus didefinisikan terlebih dahulu');
        }

        $session_id=trim(Session::get('cilog.session_id'));
        if(!is_string($session_id) || empty($session_id)) {
            throw new \Exception('cilog.session_id harus berupa string dan tidak boleh kosong');
        }

        $this->session_id = $session_id;
    }

    public function toDb($note, $type=null, $params=[])
    {
        try {

            if($type==null) {
                $type = Log::TYPE_LARAVEL_INFO;
            }

            $server = request()->server();
            $port = null;
            if(isset($server['SERVER_PORT'])) {
                $port = $server['SERVER_PORT'];
            }

            if(!array_key_exists('path', $params) || !file_exists(dirname($params['path']))) {
                $params['path'] = storage_path('logs/api/api_'.date('Ymd').'.log');
            }

            $model = new Log();
            $model->domain = request()->getHttpHost();
            // Server Ip = request()->ip();
            $model->ipv4 = \Request::ip();
            $model->port = $port;
            $model->url = request()->fullUrl();
            $model->http_method = request()->method();
            $model->header = json_encode(request()->header());
            $model->user_agent = request()->userAgent();
            $model->body = json_encode(array_merge(request()->input(),request()->post()));
            $model->type = $type;
            $model->note = json_encode([
                'uid' => $this->session_id,
                'path' => $params['path']
            ]);
            $user_ip = $this->getUserIp();
            $model->user_ip = empty($user_ip) ? request()->ip() : $user_ip;
            if(!$model->save()) {
                throw new \Exception(500,'Failed to save data into database');
            }

            $message = 'uid:'.$this->session_id.PHP_EOL;
            $message.= $note.PHP_EOL;

            if(!array_key_exists('toFile', $params) || $params['toFile']==true) {
                $this->toFile($message, $type, $params['path']);
            }

        } catch(\Exception $e) {
            throw $e;
        }
    }

    protected function getUserIp()
    {
        foreach(['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'] as $key) {
            if(array_key_exists($key, $_SERVER) === true) {
                foreach(explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip); // just to be safe
                    if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
    }

    public function toFile($message, $type=null, $path=null)
    {
        if($type==null) {
            $type = Log::TYPE_LARAVEL_INFO;
        }

        if($path==null) {
            $path = storage_path('logs/api/api_'.date('Ymd').'.log');
        }

        $log = \Log::build([
            'driver' => 'single',
            'path' => $path
        ]);

        $message = $message.PHP_EOL;
        switch ($type) {
            case Log::TYPE_LARAVEL_EMERGENCY    : $log->emergency($message); break;
            case Log::TYPE_LARAVEL_ALERT        : $log->alert($message); break;
            case Log::TYPE_LARAVEL_CRITICAL     : $log->critical($message); break;
            case Log::TYPE_LARAVEL_ERROR        : $log->error($message); break;
            case Log::TYPE_LARAVEL_WARNING      : $log->warning($message); break;
            case Log::TYPE_LARAVEL_NOTICE       : $log->notice($message); break;
            case Log::TYPE_LARAVEL_INFO         : $log->info($message); break;
            case Log::TYPE_LARAVEL_DEBUG        : $log->debug($message); break;
        }
    }

    public function exit($data)
    {
        print('<pre>'.print_r($data,true).'</pre>');exit;
    }
}
