<?php
namespace IdeaHq\Afterpay;

use Http\Message\MessageFactory;
use Payum\Core\Exception\Http\HttpException;
use Payum\Core\HttpClientInterface;
use Psr\Http\Message\ResponseInterface;

class Api
{
    /**
     * @var HttpClientInterface
     */
    protected $client;

    /**
     * @var MessageFactory
     */
    protected $messageFactory;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @param array               $options
     * @param HttpClientInterface $client
     * @param MessageFactory      $messageFactory
     *
     * @throws \Payum\Core\Exception\InvalidArgumentException if an option is invalid
     */
    public function __construct(array $options, HttpClientInterface $client, MessageFactory $messageFactory)
    {
        $this->options = $options;
        $this->client = $client;
        $this->messageFactory = $messageFactory;
    }

    protected function getAuthorizationHeader(): string
    {
        return sprintf('Basic %s', base64_encode(sprintf('%s:%s', $this->options['merchant_id'], $this->options['secret_key'])));
    }

    protected function doRequest(string $method, string $subUri, array $data = []): array
    {
        $headers = [
            'Authorization' => $this->getAuthorizationHeader()
        ];

        if ($method === 'GET') {
            $data = http_build_query($data);
        } else {
            $data = json_encode($data);
            $headers['Content-Type'] = 'application/json';
        }

        $request = $this->messageFactory->createRequest($method, $this->getApiEndpoint($subUri), $headers, $data);
        $response = $this->client->send($request);

        if (false == ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300)) {
            throw HttpException::factory($request, $response);
        }

        return json_decode($response->getBody(), true);
    }

    protected function getApiEndpoint(string $subUri = ''): string
    {
        return ($this->options['sandbox'] ? 'https://api-sandbox.afterpay.com/v1' : 'https://api.afterpay.com/v1')
            . $subUri;
    }

    /**
     * https://docs.afterpay.com/nz-online-api-v1.html#get-configuration
     *
     * @return array
     */
    public function getConfig(): array
    {
        return $this->doRequest('GET', '/configuration')[0];
    }

    /**
     * https://docs.afterpay.com/nz-online-api-v1.html#create-order
     *
     * @param array $orderData
     * @return array
     */
    public function createOrder(array $orderData): array
    {
        return $this->doRequest('POST', '/orders', $orderData);
    }

    /**
     * https://docs.afterpay.com/nz-online-api-v1.html#capture-payment
     *
     * @param string $token
     * @param string $merchantReference
     * @return array
     */
    public function capturePayment(string $token, string $merchantReference): array
    {
        return $this->doRequest('POST', '/payments/capture'
            , ['token' => $token, 'merchantReference' => $merchantReference]);
    }
}
