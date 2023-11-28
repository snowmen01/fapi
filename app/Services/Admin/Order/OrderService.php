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
        $orders = $this->order->with('childrenOrders')->whereNull('order_id')->orderBy($params['sort_key'] ?? 'id', $params['order_by'] ?? 'DESC');

        if (isset($params['keywords'])) {
            $orders = $orders->leftJoin('customers', 'orders.customer_id', '=', 'customers.id')
                ->where('customers.phone', 'LIKE', '%' . $params['keywords'] . '%')
                ->orWhere('code', 'LIKE', '%' . $params['keywords'] . '%')
                ->orWhere('customers.name', 'LIKE', '%' . $params['keywords'] . '%')
                ->select('orders.*');
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

        if (isset($params['status'])) {
            $orders = $orders->where('status', $params['status']);
        }

        $orders->map(function ($order) {
            $order->customer_id = $order->customer->name;
            $time   = date('h:i:s', strtotime($order->created_at));
            $ampm   = date('A', strtotime($order->created_at));
            $period = $ampm == 'AM' ? 'Sáng' : 'Chiều';
            $date   = $period . ', Ngày ' . date('d-m-Y', strtotime($order->created_at));
            $order->createdAt = $time . ' ' . $date;
            $order->filename  = $order->code . '_' . Str::slug($order->customer_id) . '.pdf';
            $order->status    = config("constant.status_order_common.$order->status");
        });

        return $orders;
    }

    public function show($id)
    {
        $order = $this->order->with('image')->find($id);

        return $order;
    }

    public function getOrderById($id)
    {
        $order = $this->order->with('image')->find($id);

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
            $items[] = (new InvoiceItem())->title($this->productService->getProductById($child->product_id)->name)->pricePerUnit($child->price)->quantity($child->quantity);
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

    public function store($data)
    {
        $parentOrder = [
            'customer_id' => $data['customerId'],
            'address'     => $data['address'],
            'description' => $data['description'],
            'code'          => 'FSTUDIOBYFPT' . random_int(1, 999999) . strtoupper(Str::random(2)),
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

        return $order->code;
    }

    // public function status($id, $data)
    // {
    //     $order = $this->getCustomerById($id);
    //     $order->update(['active' => $data['active']]);

    //     return $order;
    // }

    public function delete($id)
    {
        $order = $this->getOrderById($id);
        $order->image()->delete();
        $order->delete();

        return $order;
    }
}
