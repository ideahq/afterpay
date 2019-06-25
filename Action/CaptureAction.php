<?php
namespace IdeaHq\Afterpay\Action;

use IdeaHq\Afterpay\Request\Api\CapturePayment;
use IdeaHq\Afterpay\Request\Api\CreateOrder;
use IdeaHq\Afterpay\Request\Api\GetConfig;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Reply\HttpRedirect;
use Payum\Core\Request\Capture;
use Payum\Core\Exception\RequestNotSupportedException;

class CaptureAction implements ActionInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;

    /**
     * {@inheritDoc}
     *
     * @param Capture $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);
        $model = ArrayObject::ensureArrayObject($request->getModel());

        if ($model['token']['token']) {
            // We have an Afterpay token, check to see if we can capture payment with it
            $this->gateway->execute(new CapturePayment($model));
            return;
        }

        $this->gateway->execute(new GetConfig($model));

        if ($model['error']) {
            return;
        }
        $this->gateway->execute(new CreateOrder($model));

        if ($model['error']) {
            return;
        }

        if (!$model['token']) {
            $model['error'] = 'No token created';
            return;
        }

        throw new HttpRedirect(sprintf('https://portal.afterpay.com/nz/checkout/?token=%s'
            , $model['token']['token']));
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return $request instanceof Capture &&
            $request->getModel() instanceof \ArrayAccess;
    }
}
