<?php
namespace IdeaHq\Afterpay\Action\Api;

use IdeaHq\Afterpay\Request\Api\GetConfig;
use Payum\Core\ApiAwareTrait;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\LogicException;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareTrait;

class GetConfigAction extends BaseApiAwareAction
{
    use ApiAwareTrait;
    use GatewayAwareTrait;

    /**
     * {@inheritDoc}
     */
    public function execute($request)
    {
        /** @var $request GetConfig */
        RequestNotSupportedException::assertSupports($this, $request);
        $model = ArrayObject::ensureArrayObject($request->getModel());

        try {
            $config = $this->api->getConfig();

            $model->replace(['config' => $config]);
        } catch (\Exception $e) {
            $model->replace(['error' => $e->getMessage()]);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return $request instanceof GetConfig &&
            $request->getModel() instanceof \ArrayAccess;
    }
}
