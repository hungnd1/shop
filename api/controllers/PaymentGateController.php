<?php
/**
 * Created by PhpStorm.
 * User: bibon
 * Date: 4/6/2016
 * Time: 5:11 PM
 */

namespace api\controllers;


use common\helpers\MyCurl;
use common\models\Subscriber;
use common\models\SubscriberTransaction;
use Yii;
use yii\web\Controller;
use yii\web\Response;

class PaymentGateController extends Controller
{
    const PAYMENT_GATE_URL = 'https://pay.smartgate.vn/Checkout';
//    const RETURN_URL = 'http://localhost:8080/api/web/payment-gate/receive-result';
//    const CANCEL_URL = 'http://localhost:8080/api/web/payment-gate/cancel-transaction';

    const RETURN_URL = 'http://103.31.126.223/api/web/index.php/payment-gate/receive-result';
    const CANCEL_URL = 'http://103.31.126.223/api/web/index.php/payment-gate/cancel-transaction';

    const MERCHANT_ID = 'tvod';
    const SECRET_KEY = 'uk3h3f';
    const COMMAND = 'PAY';

    const ORDER_TYPE_DIGITAL = 2;

    public function actionChargeCoin($username, $amount = 0, $currency_code = 'VND', $language = 'vn', $channel_type)
    {

        if (empty($amount) || $amount <= 0) return $this->responseError('Amount Empty or not positive');
        if (empty($currency_code)) return $this->responseError('Currency Empty');
        if (empty($channel_type)) return $this->responseError('Channel Type Empty');

        $subscriber = Subscriber::findOne(['username' => $username, 'status' => Subscriber::STATUS_ACTIVE]);
        if (!$subscriber) {
            return "Thuê bao không tồn tại hoặc chưa được kích hoạt";
        }

        $site = $subscriber->site;

        $description = 'Nạp coin';
        $transaction = $subscriber->newTransaction(SubscriberTransaction::TYPE_CHARGE_COIN, $channel_type, $description, null, null, SubscriberTransaction::STATUS_PENDING, $amount, $currency_code, 0, $site);

        $token = md5(self::MERCHANT_ID . $transaction->id . self::COMMAND . $amount . self::RETURN_URL . self::SECRET_KEY);

        $paymentUrl = self::PAYMENT_GATE_URL . '?' . http_build_query(['merchant_id' => self::MERCHANT_ID,
                'command' => self::COMMAND,
                'order_id' => $transaction->id,
                "amount" => $amount,
                "shipping_fee" => 0,
                "tax_fee" => 0,
                "currency_code" => $currency_code,
                "return_url" => self::RETURN_URL,
                "cancel_url" => self::CANCEL_URL,
                "language" => $language,
                "order_info" => "Nap coin",
                "order_type" => self::ORDER_TYPE_DIGITAL,
                "checksum" => $token,
            ]);

        return $this->redirect($paymentUrl);

    }

    public function actionReceiveResult($merchant_id, $order_id, $created_on, $transaction_id = '',
                                        $result_code, $result_description, $command, $amount, $netAmount = 0, $feeAmount = 0,
                                        $currency_code, $payment_method, $transaction_type, $transaction_status,
                                        $description, $checksum)
    {
        $token = md5(self::MERCHANT_ID . $order_id . self::COMMAND . $created_on .
            $result_code . $amount . $payment_method . $transaction_type .
            $transaction_status . self::SECRET_KEY);

        if ($token != $checksum) {
            return 'Lỗi hệ thống: Invalid Checksum';
        }

        $transaction = SubscriberTransaction::findOne(['id' => $order_id, 'status' => SubscriberTransaction::STATUS_PENDING]);

        if (!$transaction) {
            return "Không tìm thấy giao dịch hoặc giao dịch đã được xử lý. Vui lòng kiểm tra lại tài khoản hoặc liên hệ <hotline>. Mã giao dịch: $order_id.";
        }

        $result = $this->getResultByCode($result_code);

        $transaction->status = $result['success'] ? SubscriberTransaction::STATUS_SUCCESS : SubscriberTransaction::STATUS_FAIL;
        $transaction->cost = $amount;
        $transaction->balance = $amount;
        $transaction->updated_at = time();
        if (!$result['success']) {
            $transaction->error_code = $result_code;
        }

        if ($transaction->save()) {
            $subscriber = $transaction->subscriber;
            if ($result['success']) {
                $subscriber->balance = $subscriber->balance + $amount;
                if (!$subscriber->update(false)) {
                    Yii::error($subscriber->errors);
                }
            }
        } else {
            Yii::error($transaction->errors);
        }

        if ($result['success']) {
            return 'Quý khách đã nạp tiền thành công vào ví điện tử với mệnh giá ' . $amount . ', số dư sau khi nạp là: ' . $subscriber->balance . ' coin. Cảm ơn quý khách đã sử dụng dịch vụ TVOD!';
        } else {
            return $result['message'];
        }
    }

    public function actionCancelTransaction($merchant_id, $order_id, $created_on, $transaction_id = '',
                                            $result_code, $command, $amount, $netAmount = 0, $feeAmount = 0,
                                            $currency_code, $payment_method, $transaction_type, $transaction_status, $checksum)
    {
        $token = md5(self::MERCHANT_ID . $order_id . self::COMMAND . $created_on .
            $result_code . $amount . $payment_method . $transaction_type .
            $transaction_status . self::SECRET_KEY);

        if ($token != $checksum) {
            return 'Lỗi hệ thống: Invalid Checksum';
        }

        $transaction = SubscriberTransaction::findOne(['id' => $order_id, 'status' => SubscriberTransaction::STATUS_PENDING]);

        if (!$transaction) {
            return "Không tìm thấy giao dịch hoặc giao dịch đã được xử lý. Vui lòng kiểm tra lại tài khoản hoặc liên hệ <hotline>. Mã giao dịch: $order_id.";
        }

        $transaction->status = SubscriberTransaction::STATUS_FAIL;
        $transaction->updated_at = time();
        $transaction->error_code = $result_code;

        if ($transaction->save()) {
            $subscriber = $transaction->subscriber;
            $subscriber->balance = $subscriber->balance + $amount;
            if (!$subscriber->update()) {
                Yii::error($subscriber->errors);
            }
        } else {
            Yii::error($transaction->errors);
        }

        return 'Giao dich bi huy';
    }

    private function getResultByCode($result_code)
    {
        switch ($result_code) {
            case 0:
                return ['success' => true, 'message' => 'Thanh toán thành công'];
            case 1:
                return ['success' => false, 'message' => 'Tham số không hợp lệ'];
            case 2:
                return ['success' => false, 'message' => 'Chữ ký sai'];
            case 3:
                return ['success' => false, 'message' => 'Merchant không đúng'];
            case 4:
                return ['success' => false, 'message' => 'Từ chối thanh toán'];
        }
        return ['success' => false, 'message' => 'Lỗi không xác định'];
    }

    private function responseError($string)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'error' => true,
            'message' => $string,
            'code' => 400
        ];
    }
}