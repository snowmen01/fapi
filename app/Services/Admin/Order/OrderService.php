<?php

namespace App\Services\Admin\Order;

use App\Models\Order;
use Kjmtrue\VietnamZone\Models\District;
use Kjmtrue\VietnamZone\Models\Ward;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use LaravelDaily\Invoices\Invoice;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use LaravelDaily\Invoices\Classes\Party;
use App\Mail\Invoice as MailInvoice;
use App\Models\PropertyOption;
use App\Models\Sku;
use App\Services\Admin\Customer\CustomerService as CustomerCustomerService;
use App\Services\Admin\Product\ProductService;
use App\Services\Admin\Property\PropertyService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class OrderService
{
    protected $order;
    protected $customerService;
    protected $productService;
    protected $propertyService;
    protected $sku;

    public function __construct(
        Order $order,
        CustomerCustomerService $customerService,
        ProductService $productService,
        PropertyService $propertyService,
        Sku $sku,
    ) {
        $this->order            = $order;
        $this->customerService  = $customerService;
        $this->productService   = $productService;
        $this->sku              = $sku;
        $this->propertyService  = $propertyService;
    }

    public function index($params)
    {
        $orders = $this->order->with('childrenOrders', 'customer')->whereNull('order_id')->orderBy($params['sort_key'] ?? 'id', $params['order_by'] ?? 'DESC');

        if (isset($params['keywords'])) {
            $orders = $orders->leftJoin('customers', 'orders.customer_id', '=', 'customers.id')
                ->where('customers.phone', 'LIKE', '%' . $params['keywords'] . '%')
                ->orWhere('code', 'LIKE', '%' . $params['keywords'] . '%')
                ->orWhere('customers.name', 'LIKE', '%' . $params['keywords'] . '%')
                ->select('orders.*');
        }

        if (isset($params['status'])) {
            $orders = $orders->where('status', $params['status']);
        }

        if (isset($params['payment_type'])) {
            $orders = $orders->where('payment_type', $params['payment_type']);
        }

        if (isset($params['per_page'])) {
            $orders = $orders
                ->paginate(
                    $params['per_page'],
                    ['*'],
                    'page',
                    $params['page'] ?? 1
                );
        } else {
            $orders = $orders->get();
        }


        $orders->map(function ($order) {
            $date                     = date('d-m-Y H:i:s', strtotime($order->created_at));
            $order->createdAt         = $date;
            $order->filename          = $order->code . '_' . Str::slug($order->customer->name) . '.pdf';
            $order->class             = config("constant.className.$order->status");
            $order->class_status      = config("constant.classNameStatusPayment.$order->status_payment");
            $order->class_type        = config("constant.classNameTypePayment.$order->payment_type");
            $order->status_code       = $order->status;
            $order->status            = config("constant.status_order_common.$order->status");
            $order->status_payment    = config("constant.status_payment_common.$order->status_payment");
            $order->payment_type      = config("constant.payment_type_common.$order->payment_type");
        });

        return $orders;
    }

    public function show($id)
    {
        $order = $this->order->with('image')->find($id);

        return $order;
    }

    public function findOne($id)
    {
        $order = $this->order->find($id);

        return $order;
    }

    public function update($id, $data)
    {
        $order = $this->findOne($id);
        if ($data['status'] == 4) {
            $data['status_payment'] = 1;
        }
        $data['payment_at'] = null;
        if ($data['status_payment'] == 1) {
            $data['payment_at'] = Carbon::now()->toDateTimeString();
        }
        $order->update(['status' => $data['status'], 'status_payment' => $data['status_payment'], 'payment_at' => $data['payment_at']]);

        return $order;
    }

    public function getOrderById($id)
    {
        $order                          = $this->order->with('customer', 'childrenOrders.product', 'product.image', 'childrenOrders')->find($id);
        $order->createdAt               = date('d-m-Y H:i:s', strtotime($order->created_at));
        $order->paymentAt               = date('d-m-Y H:i:s', strtotime($order->payment_at));
        $order->filename                = $order->code . '_' . Str::slug($order->customer->name) . '.pdf';
        $order->status_code             = $order->status;
        $order->status                  = config("constant.status_order_common.$order->status");
        $order->status_payment_code     = $order->status_payment;
        $order->status_payment          = config("constant.status_payment_common.$order->status_payment");
        $order->payment_type            = config("constant.payment_type_common.$order->payment_type");

        return $order;
    }

    public function getOrderByCode($code)
    {
        $order = $this->order->where('code', $code)->first();

        return $order;
    }

    public function getOrderByEmail($email)
    {
        $order = $this->order->with('image')->where('email', $email)->first();

        return $order;
    }

    public function getOrders()
    {
        $order = $this->order->where('active', config('constant.active'))->with('image')->get();

        return $order;
    }

    public function exportInvoice($user, $orderParent)
    {
        $client = (new Party([
            'name'          => __('common.app_name'),
            'phone'         => '098 765 4321',
            'custom_fields' => [
                'note'        => __('common.app_name'),
                'business id' => __('common.app_name'),
            ],
        ]));

        $customer = (new Party([
            'name'          => $user->name,
            'address'       => $user->address,
            'custom_fields' => [
                'Mã hóa đơn' => $orderParent->code,
            ],
        ]));

        $items = [];
        foreach ($orderParent->childrenOrders as $child) {
            $product = $this->productService->getProductById($child->product_id);
            $title   = $product->name;
            $opt     = "";
            if (isset($child->sku_id)) {
                $sku     = $this->sku->with('propertyOptions')->find($child->sku_id);
                foreach ($sku->propertyOptions as $index => $option) {
                    if ($index == 0) {
                        $opt = $opt . $option->name;
                    } else {
                        $opt = $opt . " - " . $option->name;
                    }
                }
            }
            $items[] = (new InvoiceItem())->title("$title $opt")->pricePerUnit($child->price)->quantity($child->quantity);
        }

        $notes = $orderParent->description ?? 'Đây là hóa đơn điện tử tự động.';

        $invoice = Invoice::make(__('invoices::invoice.invoice'))
            ->seller($client)
            ->buyer($customer)
            ->date($orderParent->created_at)
            ->dateFormat('d/m/Y')
            ->currencyCode('VND')
            ->currencyFormat('{VALUE}đ')
            ->currencyThousandsSeparator('.')
            ->currencyDecimalPoint(',')
            ->filename($orderParent->code . '_' . Str::slug($user->name))
            ->addItems($items)
            ->notes($notes)
            ->logo(public_path('/images/favicon.png'))
            ->save('public');

        $link = $invoice->url();
        return $link;
    }

    public function vnpay($code, $total)
    {
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Returnurl = "http://localhost:3000/cart/checkout/payment-success";
        $vnp_TmnCode = "NLXP981Z";
        $vnp_HashSecret = "WJBSBDEBXMQXPSUQZWURUMLLALWCVDLI";

        $vnp_TxnRef = $code;
        $vnp_OrderInfo = "Mô tả";
        $vnp_OrderType = "Thanh toán đơn hàng";
        $vnp_Amount = $total * 100;
        $vnp_Locale = "vi-VN";
        $vnp_BankCode = "NCB";
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];
        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
        );

        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }
        if (isset($vnp_Bill_State) && $vnp_Bill_State != "") {
            $inputData['vnp_Bill_State'] = $vnp_Bill_State;
        }

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash =   hash_hmac('sha512', $hashdata, $vnp_HashSecret); //  
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }
        $returnData = array(
            'code' => '00', 'message' => 'success', 'data' => $vnp_Url
        );

        return $returnData;
    }

    public function store($data)
    {
        $parentOrder = [
            'customer_id'   => $data['customerId'],
            'address'       => $data['address'],
            'description'   => $data['description'],
            'code'          => 'FSTUDIOBYFPT' . random_int(1, 999999) . strtoupper(Str::random(2)),
            'payment_type'  => $data['typePayment'],
        ];
        $sku             = $this->sku;
        $productService  = $this->productService;
        $propertyService = $this->propertyService;
        $customerService = $this->customerService;
        $order           = $this->order->create($parentOrder);
        $total           = 0;
        $childOrder = [];
        if (isset($data['carts'])) {
            foreach ($data['carts'] as $cart) {
                $total += (float)($cart['quantity'] * $cart['price']);
                $childOrder[] = [
                    'order_id'      => $order->id,
                    'product_id'    => $cart['id'],
                    'sku_id'        => $cart['sku_id'] >= 0 ? $cart['sku_id'] : null,
                    'price'         => $cart['price'],
                    'quantity'      => $cart['quantity'],
                    'status'        => null,
                ];
            }
        }
        $order->childrenOrders()->createMany($childOrder);
        $order->update(['total' => $total]);
        $user = $this->customerService->getCustomerById($data['customerId']);
        $link = $this->exportInvoice($user, $order);
        Mail::to($user->email)->send(new MailInvoice($order, $link, $productService, $sku, $propertyService, $customerService));
        if ($order->payment_type == config('constant.payment_type.vnpay')) {
            $dataTranfer = $this->vnpay($order->code, $order->total);
            return [
                'statusCode'    => 200,
                'message'       => "Đặt hàng thành công",
                'url'           => $dataTranfer['data']
            ];
        }

        return [
            'statusCode'    => 200,
            'message'       => "Đặt hàng thành công",
        ];
    }

    public function payment($code, $data)
    {
        $order = $this->getOrderByCODE($code);
        $order->update(['status_payment' => $data['status_payment'], 'payment_at' => $data['payment_at']]);

        return $order;
    }

    public function delete($id)
    {
        $order = $this->getOrderById($id);
        $order->image()->delete();
        $order->delete();

        return $order;
    }
}
