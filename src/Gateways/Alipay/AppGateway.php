<?php

namespace liuyuit\XyPaySdk\Gateways\Alipay;

use Symfony\Component\HttpFoundation\Response;
use liuyuit\XyPaySdk\Events;
use liuyuit\XyPaySdk\Exceptions\InvalidArgumentException;
use liuyuit\XyPaySdk\Exceptions\InvalidConfigException;
use liuyuit\XyPaySdk\Gateways\Alipay;

class AppGateway extends Gateway
{
    /**
     * Pay an order.
     *
     * @author yansongda <me@yansongda.cn>
     *
     * @param string $endpoint
     *
     * @throws InvalidConfigException
     * @throws InvalidArgumentException
     */
    public function pay($endpoint, array $payload): Response
    {
        $payload['method'] = 'alipay.trade.app.pay';

        $biz_array = json_decode($payload['biz_content'], true);
        if ((Alipay::MODE_SERVICE === $this->mode) && (!empty(Support::getInstance()->pid))) {
            $biz_array['extend_params'] = is_array($biz_array['extend_params']) ? array_merge(['sys_service_provider_id' => Support::getInstance()->pid], $biz_array['extend_params']) : ['sys_service_provider_id' => Support::getInstance()->pid];
        }
        $payload['biz_content'] = json_encode(array_merge($biz_array, ['product_code' => 'QUICK_MSECURITY_PAY']));
        $payload['sign'] = Support::generateSign($payload, $payload['sign_type']);

        Events::dispatch(new Events\PayStarted('Alipay', 'App', $endpoint, $payload));

        return new Response(http_build_query($payload));
    }
}
