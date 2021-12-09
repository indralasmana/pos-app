<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use JWTAuth;
use DB;

use App\Models\Merchant;
use App\Models\Outlet;
use App\Models\Transaction;
use App\Models\Date;


class TransactionController extends Controller
{
    /*
    CREATE TABLE `Dates` (
       `id` bigint(20) NOT NULL AUTO_INCREMENT,
       `date` DATE DEFAULT NULL,
       PRIMARY KEY (`id`)
    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

    INSERT INTO Dates(date) VALUES 
    ('2021-11-01'), ('2021-11-02'), ('2021-11-03'), ('2021-11-04'), ('2021-11-05'), 
    ('2021-11-06'), ('2021-11-07'), ('2021-11-08'), ('2021-11-09'), ('2021-11-10'), 
    ('2021-11-11'), ('2021-11-12'), ('2021-11-13'), ('2021-11-14'), ('2021-11-15'), 
    ('2021-11-16'), ('2021-11-17'), ('2021-11-18'), ('2021-11-19'), ('2021-11-20'), 
    ('2021-11-21'), ('2021-11-22'), ('2021-11-23'), ('2021-11-24'), ('2021-11-25'), 
    ('2021-11-26'), ('2021-11-27'), ('2021-11-28'), ('2021-11-29'), ('2021-11-30');
    */
    
    public function merchant(){
        
        $user = JWTAuth::user();

        /*
        $merchants = Transaction::selectRaw('Merchants.merchant_name as nama_merchant, DATE(Transactions.created_at) as tanggal, SUM(Transactions.bill_total) as omzet')
                        ->join('Merchants', 'Transactions.merchant_id', '=', 'Merchants.id')
                        ->whereDate('Transactions.created_at', '>=', '2021-11-01')
                        ->whereDate('Transactions.created_at', '<=', '2021-11-30')
                        ->where('Merchants.user_id', $user->id)
                        ->groupBy('Transactions.merchant_id')
                        ->groupBy('tanggal')
                        ->orderBy('tanggal', 'asc')
                        ->paginate(10);
        */

        $merchants = Date::selectRaw('Merchants.merchant_name as nama_merchant, Dates.date as tanggal, (SELECT IFNULL(SUM(ts.bill_total),0) FROM Transactions ts WHERE DATE(ts.created_at) = Dates.date AND ts.merchant_id = Merchants.id ) as omzet')
                        ->crossJoin('Merchants')
                        ->where('Merchants.user_id', $user->id)
                        ->whereDate('Dates.date', '>=', '2021-11-01')
                        ->whereDate('Dates.date', '<=', '2021-11-30')
                        ->orderBy('Merchants.id', 'asc')
                        ->orderBy('Dates.date', 'asc')
                        ->paginate(10);
        
        return response()->json(
            [
                'success' => true,
                'data' => $merchants,
            ], 
            Response::HTTP_OK
        );
    }

    public function outlet(){
        
        $user = JWTAuth::user();
        
        /*
        $outlets = Transaction::selectRaw('Merchants.merchant_name as nama_merchant, Outlets.outlet_name as nama_outlet, DATE(Transactions.created_at) as tanggal, SUM(Transactions.bill_total) as omzet')
                        ->join('Merchants', 'Merchants.id', '=', 'Transactions.merchant_id')
                        ->join('Outlets', 'Outlets.id', '=', 'Transactions.outlet_id')
                        ->whereDate('Transactions.created_at', '>=', '2021-11-01')
                        ->whereDate('Transactions.created_at', '<=', '2021-11-30')
                        ->where('Merchants.user_id', $user->id)
                        ->groupBy('Transactions.merchant_id')
                        ->groupBy('Transactions.outlet_id')
                        ->groupBy('tanggal')
                        ->orderBy('tanggal', 'asc')
                        ->orderBy('Transactions.merchant_id', 'asc')
                        ->orderBy('Transactions.outlet_id', 'asc')
                        ->paginate(10);
        */

        $outlets = Date::selectRaw('Merchants.merchant_name as nama_merchant, Outlets.outlet_name as nama_outlet, Dates.date as tanggal, (SELECT IFNULL(SUM(ts.bill_total),0) FROM Transactions ts WHERE DATE(ts.created_at) = Dates.date AND ts.merchant_id = Merchants.id AND ts.outlet_id = Outlets.id) as omzet')
                        ->crossJoin('Outlets')
                        ->join('Merchants','Merchants.id','=','Outlets.merchant_id')
                        ->where('Merchants.user_id', $user->id) 
                        ->whereDate('Dates.date', '>=', '2021-11-01')
                        ->whereDate('Dates.date', '<=', '2021-11-30')
                        ->orderBy('Merchants.id', 'asc')
                        ->orderBy('Outlets.id', 'asc')
                        ->orderBy('Dates.date', 'asc')
                        ->paginate(10);

        return response()->json(
            [
                'success' => true,
                'data' => $outlets,
            ], 
            Response::HTTP_OK
        );

        return response()->json(
            [
                'success' => true,
                'token' => $outlets,
            ], 
            Response::HTTP_OK
        );
    }
}