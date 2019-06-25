<?php
namespace IdeaHq\Afterpay;

use IdeaHq\Afterpay\Action\Api\CapturePaymentAction;
use IdeaHq\Afterpay\Action\Api\CreateOrderAction;
use IdeaHq\Afterpay\Action\Api\GetConfigAction;
use IdeaHq\Afterpay\Action\AuthorizeAction;
use IdeaHq\Afterpay\Action\CancelAction;
use IdeaHq\Afterpay\Action\ConvertPaymentAction;
use IdeaHq\Afterpay\Action\CaptureAction;
use IdeaHq\Afterpay\Action\NotifyAction;
use IdeaHq\Afterpay\Action\RefundAction;
use IdeaHq\Afterpay\Action\StatusAction;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;

class AfterpayGatewayFactory extends GatewayFactory
{
    const FACTORY_NAME = 'afterpay';

    /**
     * {@inheritDoc}
     */
    protected function populateConfig(ArrayObject $config)
    {
        $config->defaults([
            'payum.factory_name' => self::FACTORY_NAME,
            'payum.factory_title' => 'Afterpay',
            'payum.action.capture' => new CaptureAction(),
            'payum.action.authorize' => new AuthorizeAction(),
            'payum.action.refund' => new RefundAction(),
            'payum.action.cancel' => new CancelAction(),
            'payum.action.notify' => new NotifyAction(),
            'payum.action.status' => new StatusAction(),
            'payum.action.convert_payment' => new ConvertPaymentAction(),
            'payum.action.get_config' => new GetConfigAction(),
            'payum.action.create_order' => new CreateOrderAction(),
            'payum.action.capture_payment_afterpay' => new CapturePaymentAction(),
        ]);

        if (false == $config['payum.api']) {
            $config['payum.default_options'] = array(
                'sandbox' => false,
                'merchant_id' => '',
                'secret_key' => ''
            );
            $config->defaults($config['payum.default_options']);
            $config['payum.required_options'] = [];

            $config['payum.api'] = function (ArrayObject $config) {
                $config->validateNotEmpty($config['payum.required_options']);

                return new Api((array) $config, $config['payum.http_client'], $config['httplug.message_factory']);
            };
        }
    }
}
