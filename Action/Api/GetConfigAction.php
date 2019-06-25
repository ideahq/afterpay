<?php
namespace IdeaHq\Afterpay\Action\Api;

use IdeaHq\Afterpay\Request\Api\GetConfig;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;

class GetConfigAction extends BaseApiAwareAction
{
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
