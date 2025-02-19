<?php

namespace App\Helpers;

use Exception;
use Illuminate\Support\Facades\DB;

class Mongo
{
    private static $client = null;
    private static $session = null;

    private static function checkTransactionStatus()
    {
        if (self::$client == null || self::$session == null) {
            throw new Exception('Transaction has not begun');
        }
    }

    public static function beginTransaction()
    {
        try {
            self::$client = DB::connection(env('DB_CONNECTION'))->getMongoClient();
            self::$session = self::$client->startSession();
            self::$session->startTransaction();
        } catch (Exception $e) {
            throw $e;
        }
    }

    private static function end()
    {
        try {
            self::$session ? self::$session->endSession() : true;
            self::$session = null;
            self::$client = null;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public static function commit()
    {
        try {
            self::checkTransactionStatus();
            self::$session->commitTransaction();
            self::end();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public static function rollback()
    {
        try {
            self::$session ? self::$session->abortTransaction() : true;
            self::end();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public static function session()
    {
        try {
            self::checkTransactionStatus();
            return self::$session;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public static function collection($collectionName)
    {
        try {
            return DB::connection(env('DB_CONNECTION'))->getMongoDB()->selectCollection($collectionName);
        } catch (Exception $e) {
            throw $e;
        }
    }
}
