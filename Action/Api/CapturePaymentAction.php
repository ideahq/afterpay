<?php
namespace IdeaHq\Afterpay\Action\Api;

use IdeaHq\Afterpay\Request\Api\CapturePayment;
use Payum\Core\ApiAwareTrait;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareTrait;

class CapturePaymentAction extends BaseApiAwareAction
{
    use ApiAwareTrait;
    use GatewayAwareTrait;

    /**
     * {@inheritDoc}
     */
    public function execute($request)
    {
        /** @var $request CapturePayment */
        RequestNotSupportedException::assertSupports($this, $request);
        $model = ArrayObject::ensureArrayObject($request->getModel());

        try {
            $payment = $this->api->capturePayment($model['token']['token'], $model['number']);

            $model->replace(['payment' => $payment]);
        } catch (\Exception $e) {
            $model->replace(['error' => $e->getMessage()]);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return $request instanceof CapturePayment &&
            $request->getModel() instanceof \ArrayAccess;
    }
}
