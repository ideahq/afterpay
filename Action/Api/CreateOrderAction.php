<?php
namespace IdeaHq\Afterpay\Action\Api;

use IdeaHq\Afterpay\Request\Api\CreateOrder;
use Payum\Core\ApiAwareTrait;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareTrait;

class CreateOrderAction extends BaseApiAwareAction
{
    use ApiAwareTrait;
    use GatewayAwareTrait;

    /**
     * {@inheritDoc}
     */
    public function execute($request)
    {
        /** @var $request CreateOrder */
        RequestNotSupportedException::assertSupports($this, $request);
        $model = ArrayObject::ensureArrayObject($request->getModel());

        try {
            $token = $this->api->createOrder([
                'totalAmount' => ['amount' => $model['amount']/100, 'currency' => $model['currency']],
                'consumer' => $model['consumer'],
                'merchant' => [
                    'redirectConfirmUrl' => $model['targetUrl'],
                    'redirectCancelUrl' => $model['targetUrl']
                ],
                'merchantReference' => $model['number']
            ]);

            $model->replace(['token' => $token]);
        } catch (\Exception $e) {
            $model->replace(['error' => $e->getMessage()]);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return $request instanceof CreateOrder &&
            $request->getModel() instanceof \ArrayAccess;
    }
}
