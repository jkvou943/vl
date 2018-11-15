<?php
/**
 * Created by PhpStorm.
 * Date: 18.10.30
 * Time: 10:05
 */

namespace App\Http\Controllers\Frank;


use App\Accounts;
use App\Classes\SapRfcRequest;
use App\Exceptions\DataInputException;
use App\Models\Ctg;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CtgController extends Controller {

    use \App\Traits\Mysqli;
    use \App\Traits\DataTables;

    // 不需要登录验证
    protected static $authExcept = ['import'];

    /**
     * @throws \App\Traits\MysqliException
     * @throws \App\Traits\DataTablesException
     */
    public function list(Request $req) {

        if ($req->isMethod('GET')) {

            $rows = DB::table('users')->select('id', 'name')->get();

            foreach ($rows as $row) {
                $users[$row->id] = $row->name;
            }

            return view('frank.ctgList', compact('users'));
        }


        // query data list

        $where = $this->dtWhere($req, ['processor' => 't2.name', 'phone' => 't1.phone'], ['phone' => 't1.phone'], ['rating' => 't1.rating', 'processor' => 't1.processor', 'status' => 't1.status']);
        $orderby = $this->dtOrderBy($req);
        $limit = $this->dtLimit($req);

        $sql = "
        SELECT SQL_CALC_FOUND_ROWS
            DATE(t1.created_at) AS created_at,
            t1.name,
            t1.email,
            phone,
            rating,
            commented,
            steps,
            status,
            t2.name AS processor,
            order_id
        FROM ctg t1
        LEFT JOIN users t2
          ON t2.id = t1.processor
        WHERE $where
        ORDER BY $orderby
        LIMIT $limit
        ";

        $data = $this->queryRows($sql);

        $recordsTotal = $recordsFiltered = $this->queryOne('SELECT FOUND_ROWS()');

        return compact('data', 'recordsTotal', 'recordsFiltered');

    }

    public function batchAssignTask(Request $req) {

        $processor = (int)$req->input('processor');

        $user = User::findOrFail($processor);

        Ctg::whereIn('order_id', $req->input('order_ids'))->update(compact('processor'));

        // foreach ($req->input('order_ids') as $order_id) {
        //     // todo 干掉 id 字段，使用表分区
        //     // 要求 select 中包含主键，否则无法保存
        //     $row = Ctg::select('id')->where('order_id', $order_id)->first();
        //     $row->processor = $processor;
        //     $row->save();
        // }

        return [true, $user->name];
    }

    /**
     * @throws DataInputException
     */
    public function process(Request $req) {

        $ctgRow = Ctg::selectRaw('*')->where('order_id', $req->input('order_id'))->first();

        if (empty($ctgRow)) throw new DataInputException('ctg not found');

        if ($req->isMethod('GET')) {

            $sap = new SapRfcRequest();

            $order = SapRfcRequest::sapOrderDataTranslate($sap->getOrder(['orderId' => $req->input('order_id')]));

            $order['SellerName'] = Accounts::where('account_sellerid', $order['SellerId'])->first()->account_name;


            $emails = DB::table('sendbox')->where('to_address', $order['BuyerEmail'])->orderBy('date', 'desc')->get();
            $emails = json_decode(json_encode($emails), true); // todo


            $userRows = DB::table('users')->select('id', 'name')->get();

            foreach ($userRows as $row) {
                $users[$row->id] = $row->name;
            }


            return view('frank.ctgProcess', compact('ctgRow', 'users', 'order', 'emails'));

        }


        // Update

        if ($req->has('processor')) {
            $ctgRow->processor = (int)$req->input('processor');
        }

        if ($req->has('steps')) {
            $ctgRow->status = $req->input('status');
            $ctgRow->commented = $req->input('commented');
            $ctgRow->steps = json_encode($req->input('steps'));
        }

        $ctgRow->save();

        return [true];
    }

    /**
     * 提交 CTG 数据
     * 由 claimthegift.com 调用
     * 加密方式及密码都是写好的
     *
     * @throws \App\Exceptions\HypocriteException
     */
    public function import(Request $req) {

        $binStr = $req->getContent();

        $json = openssl_decrypt($binStr, 'AES-256-CFB', 'frank-is-ok', OPENSSL_RAW_DATA, 'mnoefpaghijbcdkl');

        Ctg::add(json_decode($json, true));

        return [true];
    }

}
