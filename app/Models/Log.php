<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/* ======================================
Attributes :
id              Int         (PK)
domain          String
ipv4            String
port            String
url             String
http_method     Type
header          String
user_agent      String
body            String
created_at      Timestamp
type            Type
note            String
user_ip         String
======================================= */

class Log extends Model
{
    // use HasFactory;
    const HTTP_METHOD_GET      = 'GET';
    const HTTP_METHOD_POST     = 'POST';
    const HTTP_METHOD_PUT      = 'PUT';
    const HTTP_METHOD_PATCH    = 'PATCH';
    const HTTP_METHOD_DELETE   = 'DELETE';
    const HTTP_METHOD_HEAD     = 'HEAD';
    const HTTP_METHOD_CONNECT  = 'CONNECT';
    const HTTP_METHOD_OPTIONS  = 'OPTIONS';
    const HTTP_METHOD_TRACE    = 'TRACE';

    const TYPE_REQUEST_ACCEPTED     = 'Request_Accepted';
    const TYPE_SET_RESPONSE         = 'Set_Response';
    const TYPE_EXEC_SP              = 'Exec_Sp';
    const TYPE_SP_RESPONSE          = 'Sp_Response';
    const TYPE_RUN_QUERY            = 'Run_Query';
    const TYPE_QUERY_RESULT         = 'Query_Result';
    const TYPE_SET_REQUEST          = 'Set_Request';
    const TYPE_RESPONSE_ACCEPTED    = 'Response_Accepted';

    const TYPE_LARAVEL_ALERT        = 'Alert';
    const TYPE_LARAVEL_CRITICAL     = 'Critical';
    const TYPE_LARAVEL_ERROR        = 'Error';
    const TYPE_LARAVEL_WARNING      = 'Warning';
    const TYPE_LARAVEL_NOTICE       = 'Notice';
    const TYPE_LARAVEL_INFO         = 'Info';
    const TYPE_LARAVEL_DEBUG        = 'Debug';
    const TYPE_LARAVEL_EMERGENCY    = 'Emergency';

    protected $guarded = ['id', 'created_at'];

    protected $connection = 'pgsql';

    protected $table = 'public.log';

    protected $primaryKey = 'id';

    protected $keyType = 'int';

    public $incrementing = true;

    public $timestamps = true;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;
}
